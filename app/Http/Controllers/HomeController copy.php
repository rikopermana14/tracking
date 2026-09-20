<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Models\Vessel;
use App\Models\User;
use App\Models\Product;
use App\Models\Posisi;
use App\Models\ships;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use App\Models\Route;
use App\Models\RouteWaypoint;


class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // ================== DASHBOARD ==================
   public function index()
{
    $users = Auth::id();

    // Ambil semua kapal
    $locations = ships::where('status', 'MOVING')->get();

    // Hitung jumlah kapal berdasarkan status
    $movingCount = ships::where('status', 'MOVING')->count();
    $inactiveCount = ships::where('status', 'INACTIVE')->count();
    $idlingCount = ships::where('status', 'IDLING')->count();

    return view('index', compact(
        'users',
        'locations',
        'movingCount',
        'inactiveCount',
        'idlingCount'
    ));
}

    
// ================== ROUTE PLANNER ==================

public function generateRoute(Request $request)
{
    $shipLat = (float) $request->ship_lat;
    $shipLon = (float) $request->ship_lon;

    $destLat = (float) $request->dest_lat;
    $destLon = (float) $request->dest_lon;

    $bestRoute = $this->findBestRoute(
        $shipLat,
        $shipLon,
        $destLat,
        $destLon
    );

    if (!$bestRoute) {
        return response()->json([
            'error' => 'No route found'
        ], 404);
    }

    $route = $bestRoute['route'];
    $waypoints = $bestRoute['waypoints'];

    $startSnap = $bestRoute['shipSnap'];
    $endSnap   = $bestRoute['destSnap'];

    $startPos =
        $startSnap['index']
        +
        $startSnap['t'];

    $endPos =
        $endSnap['index']
        +
        $endSnap['t'];

    $routePath = [];

    $routePath[] = [
        'latitude'  => $startSnap['latitude'],
        'longitude' => $startSnap['longitude'],
        'type'      => 'start_snap'
    ];

    if ($startPos <= $endPos)
    {
        for (
            $i = $startSnap['index'] + 1;
            $i <= $endSnap['index'];
            $i++
        ) {
            if(isset($waypoints[$i]))
            {
                $routePath[] = [
                    'latitude'  => (float)$waypoints[$i]->latitude,
                    'longitude' => (float)$waypoints[$i]->longitude,
                    'sequence'  => $waypoints[$i]->sequence,
                    'route_id'  => $waypoints[$i]->route_id,
                    'type'      => 'waypoint'
                ];
            }
        }
    }
    else
    {
        for (
            $i = $startSnap['index'];
            $i >= $endSnap['index'] + 1;
            $i--
        ) {
            if(isset($waypoints[$i]))
            {
                $routePath[] = [
                    'latitude'  => (float)$waypoints[$i]->latitude,
                    'longitude' => (float)$waypoints[$i]->longitude,
                    'sequence'  => $waypoints[$i]->sequence,
                    'route_id'  => $waypoints[$i]->route_id,
                    'type'      => 'waypoint'
                ];
            }
        }
    }

    $routePath[] = [
        'latitude'  => $endSnap['latitude'],
        'longitude' => $endSnap['longitude'],
        'type'      => 'destination_snap'
    ];

    $distanceNm =
        $this->calculateRouteDistanceNm(
            $routePath
        );

    $offRouteDistanceNm =
        round(
            $startSnap['distance_km'] / 1.852,
            2
        );

    $corridorNm =
        (float) $request->input(
            'corridor_nm',
            5
        );

    $isOnRoute =
        $offRouteDistanceNm <= $corridorNm;

    return response()->json([
        'selected_route_id' =>
            $route->id,

        'selected_route_name' =>
            $route->route_name,

        'start_snap' =>
            $startSnap,

        'end_snap' =>
            $endSnap,

        'route_path' =>
            $routePath,

        'distance_nm' =>
            round($distanceNm,2),

        'off_route_distance_nm' =>
            $offRouteDistanceNm,

        'corridor_nm' =>
            $corridorNm,

        'is_on_route' =>
            $isOnRoute,

        'route_status' =>
            $isOnRoute
                ? 'ON_ROUTE'
                : 'OFF_ROUTE'
    ]);
}


private function findNearestRoute($lat,$lon)
{
    $routes = Route::all();

    $bestRoute = null;
    $bestDistance = PHP_FLOAT_MAX;

    foreach($routes as $route)
    {
        $waypoints = RouteWaypoint::where(
            'route_id',
            $route->id
        )
        ->orderBy('sequence')
        ->get();

    
        if($waypoints->count() < 2){
            continue;
        }

        $snap = $this->findNearestPointOnRoute(
            $waypoints,
            $lat,
            $lon
        );

        if(
            $snap &&
            $snap['distance_km'] < $bestDistance
        )
        {
            $bestDistance = $snap['distance_km'];

            $bestRoute = [
                'route' => $route,
                'snap' => $snap
            ];
        }
        \Log::info([
    'candidate_route' => $route->route_name,
    'distance_km' => $snap['distance_km']
]);
    }

    return $bestRoute;
}

private function findBestRoute($shipLat,$shipLon,$destLat,$destLon)
{
    $routes = Route::all();

    $bestRoute = null;
    $bestScore = PHP_FLOAT_MAX;

    foreach ($routes as $route)
    {
        $wps = RouteWaypoint::where(
            'route_id',
            $route->id
        )
        ->orderBy('sequence')
        ->get();

        if ($wps->count() < 2) {
            continue;
        }

        $shipSnap = $this->findNearestPointOnRoute(
            $wps,
            $shipLat,
            $shipLon
        );

        $destSnap = $this->findNearestPointOnRoute(
            $wps,
            $destLat,
            $destLon
        );

        $score =
            $shipSnap['distance_km']
            +
            $destSnap['distance_km'];

        if ($score < $bestScore)
        {
            $bestScore = $score;

            $bestRoute = [
                'route' => $route,
                'waypoints' => $wps,
                'shipSnap' => $shipSnap,
                'destSnap' => $destSnap
            ];
        }
    }

    return $bestRoute;
}


private function findNearestPointOnRoute($waypoints, $lat, $lon)
{
    $nearest = null;
    $minDistance = PHP_FLOAT_MAX;

    for ($i = 0; $i < $waypoints->count() - 1; $i++) {

        $a = $waypoints[$i];
        $b = $waypoints[$i + 1];

        $projection = $this->projectPointToSegment(
            (float) $lat,
            (float) $lon,
            (float) $a->latitude,
            (float) $a->longitude,
            (float) $b->latitude,
            (float) $b->longitude
        );

        if ($projection['distance_km'] < $minDistance) {
            $minDistance = $projection['distance_km'];

            $nearest = [
                'index' => $i,
                'from_sequence' => $a->sequence,
                'to_sequence' => $b->sequence,
                'latitude' => $projection['latitude'],
                'longitude' => $projection['longitude'],
                't' => $projection['t'],
                'distance_km' => $projection['distance_km']
            ];
        }
    }

    return $nearest;
}

private function projectPointToSegment(
    $pLat,
    $pLon,
    $aLat,
    $aLon,
    $bLat,
    $bLon
) {
    /*
     * Proyeksi sederhana lon/lat ke bidang datar.
     * Cukup aman untuk segmen pendek antar waypoint.
     */
    $lat0 = deg2rad(($aLat + $bLat + $pLat) / 3);

    $px = $pLon * cos($lat0);
    $py = $pLat;

    $ax = $aLon * cos($lat0);
    $ay = $aLat;

    $bx = $bLon * cos($lat0);
    $by = $bLat;

    $dx = $bx - $ax;
    $dy = $by - $ay;

    if ($dx == 0 && $dy == 0) {
        $nearestLat = $aLat;
        $nearestLon = $aLon;
        $t = 0;
    } else {
        $t = (($px - $ax) * $dx + ($py - $ay) * $dy) / (($dx * $dx) + ($dy * $dy));
        $t = max(0, min(1, $t));

        $nearestX = $ax + ($t * $dx);
        $nearestY = $ay + ($t * $dy);

        $nearestLat = $nearestY;
        $nearestLon = $nearestX / cos($lat0);
    }

    $distanceKm = $this->haversineGreatCircleDistance(
        $pLat,
        $pLon,
        $nearestLat,
        $nearestLon
    );

    return [
        'latitude' => $nearestLat,
        'longitude' => $nearestLon,
        't' => $t,
        'distance_km' => $distanceKm
    ];
}

private function calculateRouteDistanceNm($points)
{
    $totalKm = 0;

    for ($i = 0; $i < count($points) - 1; $i++) {
        $totalKm += $this->haversineGreatCircleDistance(
            $points[$i]['latitude'],
            $points[$i]['longitude'],
            $points[$i + 1]['latitude'],
            $points[$i + 1]['longitude']
        );
    }

    return $totalKm / 1.852;
}

    public function history()
    {
        $users = Auth::id();
        // $user = Vessel::where('id_user', $users)->get();
        // $data = Vessel::all();

        $locations = ships::all();
        // $inventory = Product::all();

        return view('history', compact('users','locations'));
    }

    // ================== API UNTUK MAP ==================
    public function getLastPosition()
    {
        $lastPosition = ships::orderBy('datetime_utc', 'desc')->first();
        return response()->json($lastPosition);
    }

    public function allLastPositions()
    {
        // Ambil lokasi terakhir per kapal (group by vname)
        $positions = ships::select(
                'id',
                'vname',
                'status',
                'latitude',
                'longitude',
                'speed',
                'direct',
                'mileage',
                'datetime_utc'
            )
            ->whereIn('id', function($query) {
                $query->selectRaw('MAX(id)')
                      ->from('ships')
                      ->groupBy('vname');
            })
            ->get();

        return response()->json($positions);
    }

    public function getCoordinatesByName(Request $request)
    {
        $destination = $request->query('destination');
        if (!$destination) {
            return response()->json(['error' => 'Destination required'], 400);
        }

        $url = "https://nominatim.openstreetmap.org/search?q=" . urlencode($destination) . "&format=json&limit=1";

        $response = Http::withHeaders([
            'User-Agent' => 'logindo-vdr-app/1.0 (support@logindo.com)'
        ])->get($url);

        if ($response->successful() && count($response->json()) > 0) {
            $data = $response->json()[0];
            return response()->json([
                'latitude' => $data['lat'],
                'longitude' => $data['lon']
            ]);
        }

        return response()->json(['error' => 'Location not found'], 404);
    }

    public function searchDestination(Request $request)
    {
        $query = urlencode($request->query('q'));

        $url = "https://nominatim.openstreetmap.org/search?q={$query}&format=json&limit=5";

        $response = Http::withHeaders([
            'User-Agent' => 'logindo-vdr-app/1.0 (support@logindo.com)'
        ])->get($url);

        if ($response->successful()) {
            return response()->json($response->json());
        }

        return response()->json([], 500);
    }

    public function filter(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate   = $request->input('end_date');
        $shipName  = $request->input('ship_name'); // filter nama kapal
    
        $query = ships::query();
    
        // Filter tanggal (pastikan full day: 00:00 - 23:59)
        if ($startDate && $endDate) {
            $query->whereBetween('datetime_utc', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ]);
        }
    
        // Filter nama kapal
        if ($shipName) {
            $query->where('vname', 'like', '%' . $shipName . '%');
        }
    
        // Ambil data urut berdasarkan waktu
        $positions = $query->orderBy('datetime_utc')->get();

        // hitung total jarak
        $totalDistance = 0;
        if ($positions->count() > 1) {
            for ($i = 0; $i < $positions->count() - 1; $i++) {
                $start = $positions[$i];
                $end   = $positions[$i + 1];

                $totalDistance += $this->haversineGreatCircleDistance(
                    $start->latitude, $start->longitude,
                    $end->latitude, $end->longitude
                );
            }
        }

        return response()->json([
            'positions'       => $positions,
            'total_distance'  => $totalDistance
        ]);
    }

    // ================== Haversine ==================
    function haversineGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371)
    {
        // convert derajat ke radian
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * $earthRadius;
    }

    // ================== USER MANAGEMENT ==================
    public function create()
    {
        $users = User::all();
        $roles = Role::all();
        return view('auth.user', compact('roles','users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'roles' => 'required|array',
        ], [
            'required' => 'The field cannot be empty.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $user->assignRole($request->roles);

        return redirect()->route('user')
            ->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $this->validate($request, [
            'edit_name3' => 'required|string|max:255',
            'edit_email3' => 'required|email',
            'edit_password3' => 'nullable|min:6',
        ], [
            'required' => 'The field cannot be empty.',
        ]);

        $user->name = $request->edit_name3;
        $user->email = $request->edit_email3;

        if ($request->edit_password3) {
            $user->password = bcrypt($request->edit_password3);
        }

        $user->save();
        $user->syncRoles($request->input('edit_roles3'));

        return redirect()->route('user')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('user')->with('success', 'User deleted successfully.');
    }
}

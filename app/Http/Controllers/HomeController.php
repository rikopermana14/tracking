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
use Illuminate\Support\Facades\DB;


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
        // $user = Vessel::where('id_user', $users)->get();
        // $data = Vessel::all();

        // ambil semua lokasi
        $locations = ships::all();

        // // stok untuk indikator
        // $inventory = Product::all();

        return view('index', compact('users','locations'));
    }

    // ================== ROUTE ESTIMATION PAGE ==================

public function routeEstimationPage()
{
    // Ambil daftar kapal unik berdasarkan nama kapal
    $ships = ships::select('id', 'vname')
        ->whereNotNull('vname')
        ->where('vname', '!=', '')
        ->orderBy('vname')
        ->get()
        ->unique('vname')
        ->values();

    return view('routes.route-estimation', compact('ships'));
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

    public function historicalLastPositions()
{
    // =====================================================
    // AMBIL WAKTU TERAKHIR DARI SETIAP KAPAL
    // =====================================================
    $latest = ships::select(
            'vname',
            DB::raw('MAX(datetime_utc) AS latest_datetime')
        )
        ->whereNotNull('vname')
        ->groupBy('vname');

    // =====================================================
    // AMBIL DATA LENGKAP DARI POSISI TERAKHIR
    // SETIAP KAPAL
    // =====================================================
    $locations = ships::joinSub(
            $latest,
            'latest',
            function ($join) {
                $join->on(
                    'ships.vname',
                    '=',
                    'latest.vname'
                )
                ->on(
                    'ships.datetime_utc',
                    '=',
                    'latest.latest_datetime'
                );
            }
        )
        ->select('ships.*')
        ->orderBy('ships.vname')
        ->get();

    return response()->json($locations);
}

    // ================== API UNTUK MAP ==================
    public function getLastPosition()
{
 $lastPosition = ships::orderBy('datetime_utc', 'desc')->first();
        return response()->json($lastPosition);
}

    public function allLastPositions()
{
    // =====================================================
    // AMBIL DATA MOVING TERAKHIR UNTUK SETIAP KAPAL
    // =====================================================
    $latestMoving = ships::select(
            'vname',
            DB::raw('MAX(datetime_utc) AS latest_datetime')
        )
        ->where('status', 'MOVING')
        ->where('speed', '>=', 1)
        ->groupBy('vname');

    // =====================================================
    // AMBIL RECORD LENGKAP BERDASARKAN
    // VNAME + DATETIME MOVING TERAKHIR
    // =====================================================
    $locations = ships::joinSub(
            $latestMoving,
            'latest_moving',
            function ($join) {
                $join->on(
                    'ships.vname',
                    '=',
                    'latest_moving.vname'
                )
                ->on(
                    'ships.datetime_utc',
                    '=',
                    'latest_moving.latest_datetime'
                );
            }
        )
        ->select('ships.*')
        ->get();

    return response()->json($locations);
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

    // ================== CALCULATE ROUTE ESTIMATION ==================

// ================== CALCULATE ROUTE ESTIMATION ==================

public function calculateRouteEstimation(Request $request)
{
    // =====================================================
    // 1. VALIDASI INPUT
    // =====================================================

    $request->validate([
        'ship_id'  => 'required',
        'ship_lat' => 'required|numeric',
        'ship_lon' => 'required|numeric',
        'dest_lat' => 'required|numeric',
        'dest_lon' => 'required|numeric',
    ]);


    // =====================================================
    // 2. AMBIL INPUT
    // =====================================================

    $shipId = $request->ship_id;

    $shipLat = (float) $request->ship_lat;
    $shipLon = (float) $request->ship_lon;

    $destLat = (float) $request->dest_lat;
    $destLon = (float) $request->dest_lon;


    // =====================================================
    // 3. AMBIL DATA KAPAL
    // =====================================================

    $selectedShip = ships::find($shipId);

    if (!$selectedShip) {

        return response()->json([
            'success' => false,
            'message' => 'Vessel not found.'
        ], 404);

    }

    $shipName = $selectedShip->vname;


    // =====================================================
    // 4. CARI DATA HISTORY KAPAL
    //    BERDASARKAN POSISI START TERDEKAT
    // =====================================================

    $shipHistory = ships::where(
            'vname',
            $shipName
        )
        ->whereNotNull('latitude')
        ->whereNotNull('longitude')
        ->get();


    if ($shipHistory->isEmpty()) {

        return response()->json([
            'success' => false,
            'message' =>
                'No historical position found for this vessel.'
        ], 404);

    }


    // =====================================================
    // 5. CARI RECORD HISTORY PALING DEKAT
    //    DENGAN POSISI START
    // =====================================================

    $nearestHistory = null;

    $nearestDistance = PHP_FLOAT_MAX;


    foreach ($shipHistory as $history) {

        $distanceKm =
            $this->haversineGreatCircleDistance(

                $shipLat,
                $shipLon,

                (float) $history->latitude,
                (float) $history->longitude

            );


        if ($distanceKm < $nearestDistance) {

            $nearestDistance =
                $distanceKm;

            $nearestHistory =
                $history;

        }

    }


    if (!$nearestHistory) {

        return response()->json([
            'success' => false,
            'message' =>
                'Unable to determine initial vessel position.'
        ], 404);

    }


    // =====================================================
    // 6. INITIAL SPEED
    //
    // SPEED DIAMBIL DARI RECORD HISTORY TERDEKAT
    // =====================================================

    $speedKnots =
        (float) $nearestHistory->speed;


    $historicalStartTime =
        $nearestHistory->datetime_utc;


    // =====================================================
    // VALIDASI SPEED
    // =====================================================

    if ($speedKnots <= 0) {

        return response()->json([

            'success' => false,

            'message' =>
                'Initial vessel speed must be greater than 0 knots.',

            'historical_start' => [

                'id' =>
                    $nearestHistory->id,

                'latitude' =>
                    (float) $nearestHistory->latitude,

                'longitude' =>
                    (float) $nearestHistory->longitude,

                'speed_knots' =>
                    $speedKnots,

                'datetime_utc' =>
                    $nearestHistory->datetime_utc,

            ],

        ], 422);

    }


    // =====================================================
    // 7. CARI ROUTE TERBAIK
    //
    // START + DESTINATION
    //        ↓
    // findBestRoute()
    //        ↓
    // ROUTE OTOMATIS
    // =====================================================

    $bestRoute =
        $this->findBestRoute(

            $shipLat,
            $shipLon,

            $destLat,
            $destLon

        );


    if (!$bestRoute) {

        return response()->json([
            'success' => false,
            'message' =>
                'No suitable route found.'
        ], 404);

    }


    // =====================================================
    // 8. AMBIL DATA ROUTE
    // =====================================================

    $route =
        $bestRoute['route'];

    $waypoints =
        $bestRoute['waypoints'];

    $startSnap =
        $bestRoute['shipSnap'];

    $endSnap =
        $bestRoute['destSnap'];


    // =====================================================
    // 9. POSISI START DAN DESTINATION
    //    PADA ROUTE
    // =====================================================

    $startPos =
        $startSnap['index']
        +
        $startSnap['t'];


    $endPos =
        $endSnap['index']
        +
        $endSnap['t'];


    // =====================================================
    // 10. BANGUN ROUTE PATH
    //
    // START SNAP
    //      ↓
    // WAYPOINT
    //      ↓
    // WAYPOINT
    //      ↓
    // DESTINATION SNAP
    // =====================================================

    $routePath = [];


    // =====================================================
    // START SNAP
    // =====================================================

    $routePath[] = [

        'latitude' =>
            $startSnap['latitude'],

        'longitude' =>
            $startSnap['longitude'],

        'type' =>
            'start_snap',

    ];


    // =====================================================
    // WAYPOINT
    // =====================================================

    if ($startPos <= $endPos) {

        for (

            $i =
                $startSnap['index'] + 1;

            $i <= $endSnap['index'];

            $i++

        ) {

            if (isset($waypoints[$i])) {

                $routePath[] = [

                    'latitude' =>
                        (float)
                        $waypoints[$i]->latitude,

                    'longitude' =>
                        (float)
                        $waypoints[$i]->longitude,

                    'sequence' =>
                        $waypoints[$i]->sequence,

                    'route_id' =>
                        $waypoints[$i]->route_id,

                    'type' =>
                        'waypoint',

                ];

            }

        }

    } else {

        for (

            $i =
                $startSnap['index'];

            $i >=
                $endSnap['index'] + 1;

            $i--

        ) {

            if (isset($waypoints[$i])) {

                $routePath[] = [

                    'latitude' =>
                        (float)
                        $waypoints[$i]->latitude,

                    'longitude' =>
                        (float)
                        $waypoints[$i]->longitude,

                    'sequence' =>
                        $waypoints[$i]->sequence,

                    'route_id' =>
                        $waypoints[$i]->route_id,

                    'type' =>
                        'waypoint',

                ];

            }

        }

    }


    // =====================================================
    // DESTINATION SNAP
    // =====================================================

    $routePath[] = [

        'latitude' =>
            $endSnap['latitude'],

        'longitude' =>
            $endSnap['longitude'],

        'type' =>
            'destination_snap',

    ];


    // =====================================================
    // 11. HITUNG JARAK ROUTE
    // =====================================================

    $distanceNm =
        $this->calculateRouteDistanceNm(
            $routePath
        );


    // =====================================================
    // 12. HITUNG WAKTU ESTIMASI
    //
    // Rumus:
    //
    // T = D / V
    //
    // D = nautical mile
    // V = knot
    // T = jam
    // =====================================================

    $travelHours =
        $distanceNm /
        $speedKnots;


    // =====================================================
    // 13. KONVERSI JAM KE DETIK
    // =====================================================

    $travelSeconds =
        (int) round(
            $travelHours * 3600
        );


    // =====================================================
    // 14. FORMAT DURASI
    // =====================================================

    $days =
        intdiv(
            $travelSeconds,
            86400
        );


    $remainingSeconds =
        $travelSeconds % 86400;


    $hours =
        intdiv(
            $remainingSeconds,
            3600
        );


    $remainingSeconds =
        $remainingSeconds % 3600;


    $minutes =
        intdiv(
            $remainingSeconds,
            60
        );


    $seconds =
        $remainingSeconds % 60;


    // =====================================================
    // 15. FORMAT DURASI UNTUK VIEW
    // =====================================================

    $durationParts = [];


    if ($days > 0) {

        $durationParts[] =
            $days .
            ' day' .
            ($days > 1 ? 's' : '');

    }


    if (
        $hours > 0 ||
        $days > 0
    ) {

        $durationParts[] =
            $hours . ' h';

    }


    if (
        $minutes > 0 ||
        $hours > 0 ||
        $days > 0
    ) {

        $durationParts[] =
            $minutes . ' m';

    }


    $durationParts[] =
        $seconds . ' s';


    $duration =
        implode(
            ' ',
            $durationParts
        );


    // =====================================================
    // 16. WAKTU MULAI ESTIMASI
    //
    // UNTUK MODE ESTIMATION:
    // menggunakan waktu saat perhitungan dilakukan.
    //
    // HISTORY TETAP DISIMPAN SEBAGAI REFERENSI
    // =====================================================

    $startTime =
    Carbon::parse($historicalStartTime);


    // =====================================================
    // 17. HITUNG ETA
    // =====================================================

    $eta =
        $startTime
            ->copy()
            ->addSeconds(
                $travelSeconds
            );


    // =====================================================
    // 18. RESPONSE
    // =====================================================

    return response()->json([

        'success' => true,


        // =================================================
        // HISTORY YANG DIGUNAKAN UNTUK INITIAL SPEED
        // =================================================

        'historical_start' => [

            'id' =>
                $nearestHistory->id,

            'latitude' =>
                (float)
                $nearestHistory->latitude,

            'longitude' =>
                (float)
                $nearestHistory->longitude,

            'speed_knots' =>
                $speedKnots,

            'datetime_utc' =>
                $historicalStartTime,

            'distance_from_input_km' =>
                round(
                    $nearestDistance,
                    4
                ),

        ],


        // =================================================
        // VESSEL
        // =================================================

        'ship' => [

            'id' =>
                $selectedShip->id,

            'name' =>
                $selectedShip->vname,

            'latitude' =>
                $shipLat,

            'longitude' =>
                $shipLon,

        ],


        // =================================================
        // ROUTE
        // =================================================

        'route' => [

            'id' =>
                $route->id,

            'name' =>
                $route->route_name,

        ],


        // =================================================
        // START
        // =================================================

        'start' => [

            'latitude' =>
                $shipLat,

            'longitude' =>
                $shipLon,

            'snap_latitude' =>
                $startSnap['latitude'],

            'snap_longitude' =>
                $startSnap['longitude'],

        ],


        // =================================================
        // DESTINATION
        // =================================================

        'destination' => [

            'latitude' =>
                $destLat,

            'longitude' =>
                $destLon,

            'snap_latitude' =>
                $endSnap['latitude'],

            'snap_longitude' =>
                $endSnap['longitude'],

        ],


        // =================================================
        // ROUTE PATH
        // =================================================

        'route_path' =>
            $routePath,


        // =================================================
        // DISTANCE
        // =================================================

        'distance_nm' =>
            round(
                $distanceNm,
                2
            ),


        // =================================================
        // INITIAL SPEED
        // =================================================

        'speed_knots' =>
            round(
                $speedKnots,
                2
            ),


        // =================================================
        // TRAVEL TIME
        // =================================================

        'travel_hours' =>
            round(
                $travelHours,
                4
            ),

        'travel_seconds' =>
            $travelSeconds,

        'duration' =>
            $duration,


        // =================================================
        // ETA
        // =================================================

        'start_time' =>
            $startTime->format(
                'Y-m-d H:i:s'
            ),

        'eta' =>
            $eta->format(
                'Y-m-d H:i:s'
            ),

        'eta_formatted' =>
            $eta->format(
                'd-m-Y H:i:s'
            ),

    ]);
}
}

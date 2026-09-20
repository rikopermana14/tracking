<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Route;
use App\Models\RouteWaypoint;
use Illuminate\Support\Facades\DB;

class RouteController extends Controller
{

    // =========================================================
// MENAMPILKAN ROUTE WAYPOINT EDITOR
// SEKALIGUS MENAMPILKAN ROUTE YANG SUDAH ADA
// =========================================================

public function create()
{
    // Mengambil seluruh route dari database
    // beserta waypoint-nya berdasarkan sequence.
    $routes = Route::with([
        'waypoints' => function ($query) {
            $query->orderBy('sequence');
        }
    ])
    ->orderBy('route_name')
    ->get();

    return view('routes.create', compact('routes'));
}


    // =========================================================
    // MENYIMPAN ROUTE + WAYPOINT
    // =========================================================

    public function store(Request $request)
{
    // =========================================================
    // VALIDASI DATA ROUTE
    // =========================================================

    $validated = $request->validate([
        'route_name' => 'required|string|max:255|unique:routes,route_name',

        'waypoints' => 'required|array|min:2',

        'waypoints.*.sequence' => 'required|integer|min:1',

        'waypoints.*.latitude' => [
            'required',
            'numeric',
            'between:-90,90'
        ],

        'waypoints.*.longitude' => [
            'required',
            'numeric',
            'between:-180,180'
        ],
    ]);


    // =========================================================
    // AMBIL WAYPOINT PERTAMA DAN TERAKHIR
    // =========================================================

    $firstWaypoint = $validated['waypoints'][0];

    $lastWaypoint = end($validated['waypoints']);


    // =========================================================
    // TENTUKAN ORIGIN DAN DESTINATION
    //
    // Karena tabel routes membutuhkan:
    // - origin
    // - destination
    //
    // kita gunakan nama route sebagai nilai sementara.
    //
    // Contoh:
    // "balikpapan-surabaya"
    //
    // origin      = Balikpapan
    // destination = Surabaya
    // =========================================================

    $routeName = trim($validated['route_name']);

    $routeParts = preg_split(
        '/\s*-\s*/',
        $routeName
    );


    $origin = $routeParts[0] ?? $routeName;

    $destination = $routeParts[1] ?? $routeName;


    // =========================================================
    // TRANSACTION DATABASE
    // =========================================================

    DB::beginTransaction();

    try {

        // =====================================================
        // SIMPAN ROUTE
        // =====================================================

        $route = Route::create([
            'route_name'  => $routeName,
            'origin'      => $origin,
            'destination' => $destination,
        ]);


        // =====================================================
        // SIMPAN SEMUA WAYPOINT
        // =====================================================

        foreach ($validated['waypoints'] as $waypoint) {

            RouteWaypoint::create([

                // PK tabel routes Anda adalah "id"
                'route_id' => $route->id,

                'sequence' => $waypoint['sequence'],

                'latitude' => $waypoint['latitude'],

                'longitude' => $waypoint['longitude'],
            ]);
        }


        // =====================================================
        // COMMIT
        // =====================================================

        DB::commit();


        // =====================================================
        // RESPONSE BERHASIL
        // =====================================================

        return response()->json([
            'success' => true,

            'message' => 'Route berhasil disimpan.',

            'route_id' => $route->id,

            'route_name' => $route->route_name,
        ]);


    } catch (\Throwable $e) {

        // =====================================================
        // ROLLBACK JIKA ERROR
        // =====================================================

        DB::rollBack();


        return response()->json([

            'success' => false,

            'message' => 'Gagal menyimpan route.',

            'error' => $e->getMessage(),

        ], 500);
    }
}

}
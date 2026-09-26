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
    // MENYIMPAN ROUTE BARU
    // =========================================================
    public function store(Request $request)
    {
        // =====================================================
        // VALIDASI
        // =====================================================
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


        // =====================================================
        // AMBIL WAYPOINT PERTAMA DAN TERAKHIR
        // =====================================================
        $firstWaypoint = $validated['waypoints'][0];
        $lastWaypoint = end($validated['waypoints']);


        // =====================================================
        // TENTUKAN NAMA ROUTE
        // =====================================================
        $routeName = trim($validated['route_name']);


        // Contoh:
        // LOGINDO STURDY - BATAM - BALIKPAPAN
        //
        // akan menjadi:
        // origin      = LOGINDO STURDY
        // destination = BATAM
        //
        // Karena nama route Anda dapat memiliki banyak tanda "-",
        // lebih aman mengambil bagian pertama dan terakhir.
        $routeParts = preg_split('/\s*-\s*/', $routeName);

        if (count($routeParts) >= 2) {
            $origin = trim($routeParts[0]);
            $destination = trim(end($routeParts));
        } else {
            $origin = $routeName;
            $destination = $routeName;
        }


        // =====================================================
        // TRANSACTION
        // =====================================================
        DB::beginTransaction();

        try {

            // =================================================
            // SIMPAN ROUTE
            // =================================================
            $route = Route::create([
                'route_name'  => $routeName,
                'origin'      => $origin,
                'destination' => $destination,
            ]);


            // =================================================
            // SIMPAN WAYPOINT
            // =================================================
            foreach ($validated['waypoints'] as $waypoint) {

                RouteWaypoint::create([
                    'route_id' => $route->id,
                    'sequence' => $waypoint['sequence'],
                    'latitude' => $waypoint['latitude'],
                    'longitude' => $waypoint['longitude'],
                ]);
            }


            // =================================================
            // COMMIT
            // =================================================
            DB::commit();


            return response()->json([
                'success' => true,
                'message' => 'Route berhasil disimpan.',
                'route_id' => $route->id,
                'route_name' => $route->route_name,
            ]);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan route.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    // =========================================================
    // UPDATE ROUTE YANG SUDAH ADA
    // =========================================================
    public function update(Request $request, Route $route)
    {
        // =====================================================
        // VALIDASI
        // =====================================================
        $validated = $request->validate([

            'route_name' => [
                'required',
                'string',
                'max:255',
                'unique:routes,route_name,' . $route->id,
            ],

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


        // =====================================================
        // NAMA ROUTE
        // =====================================================
        $routeName = trim($validated['route_name']);


        // =====================================================
        // TENTUKAN ORIGIN DAN DESTINATION
        // =====================================================
        $routeParts = preg_split('/\s*-\s*/', $routeName);

        if (count($routeParts) >= 2) {
            $origin = trim($routeParts[0]);
            $destination = trim(end($routeParts));
        } else {
            $origin = $routeName;
            $destination = $routeName;
        }


        // =====================================================
        // TRANSACTION
        // =====================================================
        DB::beginTransaction();

        try {

            // =================================================
            // UPDATE DATA ROUTE
            // =================================================
            $route->update([
                'route_name'  => $routeName,
                'origin'      => $origin,
                'destination' => $destination,
            ]);


            // =================================================
            // HAPUS WAYPOINT LAMA
            // =================================================
            RouteWaypoint::where('route_id', $route->id)->delete();


            // =================================================
            // MASUKKAN WAYPOINT BARU
            // =================================================
            foreach ($validated['waypoints'] as $waypoint) {

                RouteWaypoint::create([
                    'route_id' => $route->id,
                    'sequence' => $waypoint['sequence'],
                    'latitude' => $waypoint['latitude'],
                    'longitude' => $waypoint['longitude'],
                ]);
            }


            // =================================================
            // COMMIT
            // =================================================
            DB::commit();


            return response()->json([
                'success' => true,
                'message' => 'Route berhasil diperbarui.',
                'route_id' => $route->id,
                'route_name' => $route->route_name,
            ]);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui route.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
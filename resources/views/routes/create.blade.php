@extends('layout.index')

@section('content')

<div class="container-fluid">

    <!-- =====================================================
         HEADER
         ===================================================== -->

    <div class="card">

        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-route"></i>
                Route Waypoint Editor
            </h3>
        </div>


        <div class="card-body">

            <!-- =================================================
                 NAMA ROUTE
                 ================================================= -->

            <div class="form-group">

                <label>
                    Route Name
                </label>

                <input
                    type="text"
                    id="route_name"
                    class="form-control"
                    placeholder="Contoh: SEMARANG-SURABAYA"
                >

            </div>


            <!-- =================================================
                 TOOLBAR
                 ================================================= -->

            <div class="mb-2">

                <button
                    type="button"
                    id="btnUndo"
                    class="btn btn-warning btn-sm"
                >
                    <i class="fas fa-undo"></i>
                    Undo
                </button>


                <button
                    type="button"
                    id="btnClear"
                    class="btn btn-danger btn-sm"
                >
                    <i class="fas fa-trash"></i>
                    Clear
                </button>


                <button
                    type="button"
                    id="btnSave"
                    class="btn btn-success btn-sm"
                >
                    <i class="fas fa-save"></i>
                    Save Route
                </button>

            </div>


            <!-- =================================================
                 INFORMASI
                 ================================================= -->

            <div class="alert alert-info">

                <i class="fas fa-info-circle"></i>

                Klik pada peta untuk menambahkan waypoint.
                Waypoint dapat digeser dengan drag.

            </div>

            <!-- =====================================================
     ROUTE YANG SUDAH ADA
     ===================================================== -->

<div class="card mb-3">

    <div class="card-header">

        <strong>
            <i class="fas fa-route"></i>
            Route Existing
        </strong>

    </div>

    <div class="card-body">

        <div class="row">

            @forelse($routes as $route)

                <div class="col-md-3 mb-2">

                    <button
                        type="button"
                        class="btn btn-outline-primary btn-sm btn-block existing-route-btn"
                        data-route-id="{{ $route->id }}"
                    >

                        <i class="fas fa-route"></i>

                        {{ $route->route_name }}

                        <span class="badge badge-secondary">
                            {{ $route->waypoints->count() }} WP
                        </span>

                    </button>

                </div>

            @empty

                <div class="col-12">

                    <div class="alert alert-warning mb-0">

                        Belum ada route yang tersimpan.

                    </div>

                </div>

            @endforelse

        </div>

    </div>

</div>
            <!-- =================================================
                 MAP
                 ================================================= -->

            <div
                id="route-map"
                style="
                    height: 600px;
                    width: 100%;
                    border: 1px solid #ddd;
                    border-radius: 6px;
                "
            ></div>


            <!-- =================================================
                 INFORMASI WAYPOINT
                 ================================================= -->

            <div class="mt-3">

                <strong>
                    Jumlah Waypoint:
                </strong>

                <span id="waypoint-count">
                    0
                </span>

            </div>


            <!-- =================================================
                 TABLE WAYPOINT
                 ================================================= -->

            <div class="table-responsive mt-3">

                <table class="table table-bordered table-sm">

                    <thead>

                        <tr>
                            <th width="80">
                                Sequence
                            </th>

                            <th>
                                Latitude
                            </th>

                            <th>
                                Longitude
                            </th>

                            <th width="80">
                                Action
                            </th>
                        </tr>

                    </thead>

                    <tbody id="waypoint-table">

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>


<!-- =========================================================
     LEAFLET
     ========================================================= -->

<link
    rel="stylesheet"
    href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
/>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>

    // =========================================================
    // DATA ROUTE DARI DATABASE
    // =========================================================

    const existingRoutes = @json($routes);

</script>

<script>

// =========================================================
// INISIALISASI MAP
// =========================================================

// Membuat map Leaflet.
const routeMap = L.map('route-map').setView(
    [-2.5, 118],
    5
);


// =========================================================
// OPENSTREETMAP TILE
// =========================================================

// Menampilkan peta dasar.
L.tileLayer(
    'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
    {
        attribution: '© OpenStreetMap contributors'
    }
).addTo(routeMap);


// =========================================================
// VARIABEL ROUTE
// =========================================================
// =========================================================
// ROUTE YANG SEDANG DIBUAT
// =========================================================

let waypoints = [];

let waypointMarkers = [];

let routePolyline = null;


// =========================================================
// ROUTE YANG SUDAH ADA DI DATABASE
// =========================================================

// Menyimpan layer route existing yang sedang ditampilkan.
let existingRouteLayers = {};


// Menyimpan marker waypoint existing.
let existingWaypointLayers = {};


// =========================================================
// MEMBUAT ICON WAYPOINT
// =========================================================

function createWaypointIcon(sequence) {

    return L.divIcon({

        className: 'waypoint-icon',

        html: `
            <div style="
                width: 26px;
                height: 26px;
                background: #1976d2;
                border: 2px solid white;
                border-radius: 50%;
                color: white;
                font-size: 11px;
                font-weight: bold;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 2px 5px rgba(0,0,0,.4);
            ">
                ${sequence}
            </div>
        `,

        iconSize: [26, 26],

        iconAnchor: [13, 13]

    });
}


// =========================================================
// KLIK MAP → TAMBAH WAYPOINT
// =========================================================

routeMap.on('click', function(e) {

    addWaypoint(
        e.latlng.lat,
        e.latlng.lng
    );

});


// =========================================================
// TAMBAH WAYPOINT
// =========================================================

function addWaypoint(latitude, longitude) {

    const sequence =
        waypoints.length + 1;


    // Membuat object waypoint.
    const waypoint = {

        sequence: sequence,

        latitude: parseFloat(latitude),

        longitude: parseFloat(longitude)

    };


    // Menambahkan ke array.
    waypoints.push(waypoint);


    // Membuat marker.
    createWaypointMarker(waypoint);


    // Menggambar ulang route.
    redrawRoute();


    // Update table.
    updateWaypointTable();


    // Update jumlah waypoint.
    updateWaypointCount();

}


// =========================================================
// MEMBUAT MARKER WAYPOINT
// =========================================================

function createWaypointMarker(waypoint) {

    const marker = L.marker(
        [
            waypoint.latitude,
            waypoint.longitude
        ],
        {
            icon: createWaypointIcon(
                waypoint.sequence
            ),

            // Waypoint dapat digeser.
            draggable: true
        }
    ).addTo(routeMap);


    // =====================================================
    // POPUP WAYPOINT
    // =====================================================

    marker.bindPopup(`
        <b>Waypoint ${waypoint.sequence}</b>
        <br>
        Lat:
        ${waypoint.latitude.toFixed(6)}

        <br>

        Lon:
        ${waypoint.longitude.toFixed(6)}
    `);


    // =====================================================
    // EVENT DRAG WAYPOINT
    // =====================================================

    marker.on('dragend', function(e) {

        const position =
            e.target.getLatLng();


        // Update koordinat waypoint.
        waypoint.latitude =
            position.lat;

        waypoint.longitude =
            position.lng;


        // Gambar ulang garis.
        redrawRoute();


        // Update tabel.
        updateWaypointTable();

    });


    // Simpan marker.
    waypointMarkers.push(marker);

}


// =========================================================
// MENGGAMBAR POLYLINE ROUTE
// =========================================================

function redrawRoute() {

    // Hapus polyline lama.
    if (routePolyline) {

        routeMap.removeLayer(
            routePolyline
        );

    }


    // Belum cukup untuk membuat garis.
    if (waypoints.length < 2) {

        return;

    }


    // Mengambil koordinat semua waypoint.
    const coordinates =
        waypoints.map(function(point) {

            return [
                point.latitude,
                point.longitude
            ];

        });


    // =====================================================
    // MEMBUAT GARIS ROUTE
    // =====================================================

    routePolyline = L.polyline(
        coordinates,
        {
            color: 'blue',

            weight: 4,

            opacity: 0.8
        }
    ).addTo(routeMap);

}


// =========================================================
// UPDATE NOMOR WAYPOINT
// =========================================================

function renumberWaypoints() {

    waypoints.forEach(function(
        waypoint,
        index
    ) {

        waypoint.sequence =
            index + 1;

    });


    // Update icon marker.
    waypointMarkers.forEach(function(
        marker,
        index
    ) {

        marker.setIcon(
            createWaypointIcon(
                index + 1
            )
        );

    });

}

// =========================================================
// MENAMPILKAN SELURUH ROUTE YANG SUDAH ADA
// =========================================================

function displayExistingRoutes() {

    // Pastikan tidak ada route existing lama
    // yang masih tertinggal di map.
    Object.values(existingRouteLayers).forEach(
        function(layer) {

            routeMap.removeLayer(layer);

        }
    );


    // Hapus marker waypoint existing.
    Object.values(existingWaypointLayers).forEach(
        function(markers) {

            markers.forEach(
                function(marker) {

                    routeMap.removeLayer(marker);

                }
            );

        }
    );


    existingRouteLayers = {};

    existingWaypointLayers = [];


    // =========================================================
    // LOOP SELURUH ROUTE
    // =========================================================

    existingRoutes.forEach(
        function(route) {

            // Pastikan route memiliki waypoint.
            if (
                !route.waypoints ||
                route.waypoints.length < 2
            ) {

                return;

            }


            // =====================================================
            // MEMBUAT KOORDINAT POLYLINE
            // =====================================================

            const coordinates =
                route.waypoints.map(
                    function(point) {

                        return [
                            parseFloat(point.latitude),
                            parseFloat(point.longitude)
                        ];

                    }
                );


            // =====================================================
            // MEMBUAT POLYLINE EXISTING ROUTE
            // =====================================================

            const routeLayer =
                L.polyline(
                    coordinates,
                    {

                        // Warna abu-abu untuk route existing.
                        color: '#777',

                        weight: 3,

                        opacity: 0.6,

                        // Route existing berada
                        // di bawah route yang sedang dibuat.
                        interactive: true

                    }
                ).addTo(routeMap);


            // Simpan layer berdasarkan route_id.
            existingRouteLayers[
                route.route_id
            ] = routeLayer;


            // =====================================================
            // POPUP ROUTE
            // =====================================================

            routeLayer.bindPopup(`
                <div style="min-width:180px">

                    <strong>
                        ${route.route_name}
                    </strong>

                    <hr style="margin:5px 0">

                    <div>
                        Route ID:
                        ${route.route_id}
                    </div>

                    <div>
                        Waypoints:
                        ${route.waypoints.length}
                    </div>

                </div>
            `);


            // =====================================================
            // MARKER WAYPOINT EXISTING
            // =====================================================

            const markers = [];


            route.waypoints.forEach(
                function(point) {

                    const marker =
                        L.circleMarker(
                            [
                                parseFloat(point.latitude),
                                parseFloat(point.longitude)
                            ],
                            {

                                // Warna waypoint existing.
                                color: '#555',

                                fillColor: '#fff',

                                fillOpacity: 1,

                                radius: 3,

                                weight: 1

                            }
                        ).addTo(routeMap);


                    marker.bindTooltip(
                        `${route.route_name} - WP ${point.sequence}`,
                        {
                            direction: 'top'
                        }
                    );


                    markers.push(marker);

                }
            );


            existingWaypointLayers[
                route.route_id
            ] = markers;

        }
    );

}
// =========================================================
// LOAD ROUTE EXISTING SAAT HALAMAN DIBUKA
// =========================================================

displayExistingRoutes();
// =========================================================
// UPDATE TABLE WAYPOINT
// =========================================================

function updateWaypointTable() {

    const table =
        document.getElementById(
            'waypoint-table'
        );


    table.innerHTML = '';


    waypoints.forEach(function(
        waypoint,
        index
    ) {

        const row =
            document.createElement('tr');


        row.innerHTML = `

            <td>
                ${waypoint.sequence}
            </td>

            <td>
                ${waypoint.latitude.toFixed(6)}
            </td>

            <td>
                ${waypoint.longitude.toFixed(6)}
            </td>

            <td>

                <button
                    type="button"
                    class="btn btn-danger btn-xs"
                    onclick="deleteWaypoint(${index})"
                >
                    <i class="fas fa-trash"></i>
                </button>

            </td>

        `;


        table.appendChild(row);

    });

}


// =========================================================
// UPDATE JUMLAH WAYPOINT
// =========================================================

function updateWaypointCount() {

    document.getElementById(
        'waypoint-count'
    ).innerText =
        waypoints.length;

}


// =========================================================
// HAPUS WAYPOINT
// =========================================================

function deleteWaypoint(index) {

    // Hapus marker.
    if (waypointMarkers[index]) {

        routeMap.removeLayer(
            waypointMarkers[index]
        );

    }


    // Hapus waypoint.
    waypoints.splice(
        index,
        1
    );


    // Hapus marker dari array.
    waypointMarkers.splice(
        index,
        1
    );


    // Atur ulang sequence.
    renumberWaypoints();


    // Gambar ulang route.
    redrawRoute();


    // Update table.
    updateWaypointTable();


    // Update jumlah.
    updateWaypointCount();

}


// =========================================================
// UNDO WAYPOINT TERAKHIR
// =========================================================

document.getElementById(
    'btnUndo'
).addEventListener(
    'click',
    function() {

        if (
            waypoints.length === 0
        ) {

            return;

        }


        deleteWaypoint(
            waypoints.length - 1
        );

    }
);


// =========================================================
// CLEAR SEMUA WAYPOINT
// =========================================================

document.getElementById(
    'btnClear'
).addEventListener(
    'click',
    function() {

        if (
            !confirm(
                'Hapus semua waypoint?'
            )
        ) {

            return;

        }


        // Hapus semua marker.
        waypointMarkers.forEach(
            function(marker) {

                routeMap.removeLayer(
                    marker
                );

            }
        );


        // Reset array.
        waypoints = [];

        waypointMarkers = [];


        // Hapus polyline.
        if (routePolyline) {

            routeMap.removeLayer(
                routePolyline
            );

            routePolyline = null;

        }


        updateWaypointTable();

        updateWaypointCount();

    }
);


// =========================================================
// SIMPAN ROUTE KE DATABASE
// =========================================================

document.getElementById(
    'btnSave'
).addEventListener(
    'click',
    function() {

        const routeName =
            document.getElementById(
                'route_name'
            ).value.trim();


        // Validasi nama route.
        if (!routeName) {

            alert(
                'Nama route wajib diisi.'
            );

            return;

        }


        // Minimal dua waypoint.
        if (waypoints.length < 2) {

            alert(
                'Minimal harus ada 2 waypoint.'
            );

            return;

        }


        // =====================================================
        // KIRIM DATA KE LARAVEL
        // =====================================================

        fetch(
            "{{ route('routes.store') }}",
            {

                method: 'POST',

                headers: {

                    'Content-Type':
                        'application/json',

                    'X-CSRF-TOKEN':
                        document.querySelector(
                            'meta[name="csrf-token"]'
                        ).content,

                    'Accept':
                        'application/json'

                },

                body: JSON.stringify({

                    route_name:
                        routeName,

                    waypoints:
                        waypoints

                })

            }
        )

        .then(async response => {

            const data =
                await response.json();


            if (!response.ok) {

                throw data;

            }


            return data;

        })

        .then(data => {

            alert(
                'Route berhasil disimpan.'
            );


            console.log(
                'ROUTE SAVED:',
                data
            );

        })

        .catch(error => {

            console.error(
                'Save route error:',
                error
            );


            alert(
                error.message ||
                'Gagal menyimpan route.'
            );

        });

    }
);

</script>

@endsection
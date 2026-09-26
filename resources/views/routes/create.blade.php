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

let existingRouteLayers = {};
let existingWaypointLayers = {};

let selectedRouteId = null;
let selectedRoute = null;
let selectedRoutePolyline = null;
let selectedRouteMarkers = [];


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

    // Kalau sedang memilih/edit route existing,
    // klik kosong pada map tidak menambah waypoint.
    if (selectedRouteId !== null) {
        return;
    }

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

// =========================================================
// MENAMPILKAN ROUTE EXISTING
// =========================================================

function displayExistingRoutes() {

    // Hapus semua layer route lama
    Object.values(existingRouteLayers).forEach(function(layer) {

        if (routeMap.hasLayer(layer)) {
            routeMap.removeLayer(layer);
        }

    });


    // Hapus semua marker waypoint lama
    Object.values(existingWaypointLayers).forEach(function(markers) {

        markers.forEach(function(marker) {

            if (routeMap.hasLayer(marker)) {
                routeMap.removeLayer(marker);
            }

        });

    });


    existingRouteLayers = {};
    existingWaypointLayers = {};


    // =====================================================
    // TAMPILKAN SEMUA ROUTE
    // =====================================================

    existingRoutes.forEach(function(route) {

        console.log(
            'LOAD ROUTE:',
            route.id,
            route.route_name
        );


        if (
            !route.waypoints ||
            route.waypoints.length < 2
        ) {
            return;
        }


        // =================================================
        // KOORDINAT
        // =================================================

        const coordinates =
            route.waypoints.map(function(point) {

                return [
                    parseFloat(point.latitude),
                    parseFloat(point.longitude)
                ];

            });


        // =================================================
        // POLYLINE
        // =================================================

        const routeLayer =
            L.polyline(
                coordinates,
                {
                    color: '#777',
                    weight: 3,
                    opacity: 0.5
                }
            ).addTo(routeMap);


        // PENTING:
        // Laravel Route model menggunakan "id"
        existingRouteLayers[route.id] =
            routeLayer;


        // =================================================
        // KLIK ROUTE
        // =================================================

        routeLayer.on('click', function(e) {

            L.DomEvent.stopPropagation(e);

            console.log(
                'CLICK ROUTE:',
                route.id,
                route.route_name
            );

            selectExistingRoute(
                route.id
            );

        });


        // =================================================
        // MARKER WAYPOINT EXISTING
        // =================================================

        const markers = [];


        route.waypoints.forEach(function(point) {

            const marker =
                L.circleMarker(
                    [
                        parseFloat(point.latitude),
                        parseFloat(point.longitude)
                    ],
                    {
                        color: '#555',
                        fillColor: '#fff',
                        fillOpacity: 1,
                        radius: 4,
                        weight: 1
                    }
                ).addTo(routeMap);


            marker.bindTooltip(
                `${route.route_name} - WP ${point.sequence}`,
                {
                    direction: 'top'
                }
            );


            // Klik waypoint
            marker.on('click', function(e) {

                L.DomEvent.stopPropagation(e);

                console.log(
                    'CLICK WAYPOINT:',
                    route.id,
                    route.route_name
                );

                selectExistingRoute(
                    route.id
                );

            });


            markers.push(marker);

        });


        existingWaypointLayers[
            route.id
        ] = markers;

    });

}
// =========================================================
// PILIH SATU ROUTE UNTUK DIEDIT
// =========================================================

function selectExistingRoute(routeId) {

    console.log(
        'SELECT ROUTE ID:',
        routeId
    );


    // =====================================================
    // CARI ROUTE
    // =====================================================

    const route =
        existingRoutes.find(function(item) {

            return parseInt(item.id) ===
                   parseInt(routeId);

        });


    // =====================================================
    // JIKA TIDAK DITEMUKAN
    // =====================================================

    if (!route) {

        console.error(
            'Route tidak ditemukan:',
            routeId
        );

        console.log(
            'Available routes:',
            existingRoutes
        );

        return;
    }


    console.log(
        'ROUTE TERPILIH:',
        route.id,
        route.route_name
    );


   // =====================================================
// SIMPAN ROUTE AKTIF
// =====================================================

selectedRouteId = route.id;
selectedRoute = route;


// =====================================================
// ACTIVE BUTTON
// =====================================================

document.querySelectorAll('.existing-route-btn').forEach(function(button) {

    button.classList.remove('active');

});

const selectedButton =
    document.querySelector(
        `.existing-route-btn[data-route-id="${route.id}"]`
    );

if (selectedButton) {
    selectedButton.classList.add('active');
}
    // =====================================================
    // HILANGKAN ROUTE LAIN
    // =====================================================

    Object.keys(existingRouteLayers).forEach(
        function(id) {

            const layer =
                existingRouteLayers[id];


            if (
                parseInt(id) ===
                parseInt(routeId)
            ) {

                // Route aktif
                layer.setStyle({
                    color: '#007bff',
                    weight: 5,
                    opacity: 1
                });

            } else {

                // Route lain disembunyikan
                if (routeMap.hasLayer(layer)) {

                    routeMap.removeLayer(layer);

                }

            }

        }
    );


    // =====================================================
    // HILANGKAN WAYPOINT ROUTE LAIN
    // =====================================================

    Object.keys(existingWaypointLayers).forEach(
        function(id) {

            if (
                parseInt(id) ===
                parseInt(routeId)
            ) {
                return;
            }


            existingWaypointLayers[id].forEach(
                function(marker) {

                    if (
                        routeMap.hasLayer(marker)
                    ) {

                        routeMap.removeLayer(
                            marker
                        );

                    }

                }
            );

        }
    );


    // =====================================================
    // ROUTE NAME
    // =====================================================

    document.getElementById(
        'route_name'
    ).value =
        route.route_name;


    // =====================================================
    // LOAD WAYPOINT KE MODE EDIT
    // =====================================================

    loadRouteForEditing(route);


    // =====================================================
    // ZOOM KE ROUTE
    // =====================================================

    if (
        route.waypoints &&
        route.waypoints.length > 0
    ) {

        const bounds =
            L.latLngBounds(
                route.waypoints.map(
                    function(point) {

                        return [
                            parseFloat(point.latitude),
                            parseFloat(point.longitude)
                        ];

                    }
                )
            );


        routeMap.fitBounds(
            bounds,
            {
                padding: [30, 30]
            }
        );

    }

}
// =========================================================
// LOAD ROUTE EXISTING KE MODE EDIT
// =========================================================

function loadRouteForEditing(route) {

    // Hapus marker edit sebelumnya
    waypointMarkers.forEach(function(marker) {

        if (routeMap.hasLayer(marker)) {

            routeMap.removeLayer(marker);

        }

    });

    waypointMarkers = [];

    // Copy waypoint route
    waypoints =
        route.waypoints.map(function(point) {

            return {

                id: point.id,

                sequence:
                    parseInt(point.sequence),

                latitude:
                    parseFloat(point.latitude),

                longitude:
                    parseFloat(point.longitude),

                course:
                    point.course,

                distance_nm:
                    point.distance_nm

            };

        });

    // =====================================================
    // BUAT MARKER EDIT
    // =====================================================

    waypoints.forEach(function(waypoint) {

        createEditableWaypointMarker(
            waypoint
        );

    });

    // =====================================================
    // GAMBAR ROUTE
    // =====================================================

    redrawRoute();

    // =====================================================
    // UPDATE TABLE
    // =====================================================

    updateWaypointTable();

    updateWaypointCount();

}
// =========================================================
// MARKER WAYPOINT EDITABLE
// =========================================================

function createEditableWaypointMarker(waypoint) {

    const marker =
        L.marker(
            [
                waypoint.latitude,
                waypoint.longitude
            ],
            {
                icon:
                    createWaypointIcon(
                        waypoint.sequence
                    ),

                draggable: true
            }
        ).addTo(routeMap);

    marker.bindPopup(`
        <b>Waypoint ${waypoint.sequence}</b>
        <br>
        Latitude:
        <span class="popup-lat">
            ${waypoint.latitude.toFixed(6)}
        </span>

        <br>

        Longitude:
        <span class="popup-lon">
            ${waypoint.longitude.toFixed(6)}
        </span>
    `);

    // =====================================================
    // DRAG WAYPOINT
    // =====================================================

    marker.on(
        'dragend',
        function(e) {

            const position =
                e.target.getLatLng();

            waypoint.latitude =
                position.lat;

            waypoint.longitude =
                position.lng;

            // Update popup
            marker.setPopupContent(`
                <b>Waypoint ${waypoint.sequence}</b>
                <br>
                Latitude:
                ${waypoint.latitude.toFixed(6)}

                <br>

                Longitude:
                ${waypoint.longitude.toFixed(6)}
            `);

            // Gambar ulang route
            redrawRoute();

            // Update tabel
            updateWaypointTable();

        }
    );

    waypointMarkers.push(
        marker
    );

}
// =========================================================
// LOAD ROUTE EXISTING SAAT HALAMAN DIBUKA
// =========================================================

displayExistingRoutes();

// =========================================================
// KLIK BUTTON ROUTE EXISTING
// =========================================================

document.querySelectorAll('.existing-route-btn').forEach(function(button) {

    button.addEventListener('click', function() {

        const routeId =
            this.getAttribute('data-route-id');

        console.log(
            'BUTTON ROUTE DIKLIK:',
            routeId
        );

        if (!routeId) {
            console.error('Route ID tidak ditemukan.');
            return;
        }

        // Pilih route
        selectExistingRoute(
            parseInt(routeId)
        );

    });

});
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
        class="btn btn-primary btn-xs"
        onclick="focusWaypoint(${index})"
    >
        <i class="fas fa-crosshairs"></i>
    </button>

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

function focusWaypoint(index) {

    const waypoint =
        waypoints[index];

    if (!waypoint) {
        return;
    }

    routeMap.setView(
        [
            waypoint.latitude,
            waypoint.longitude
        ],
        14
    );

    if (waypointMarkers[index]) {

        waypointMarkers[index].openPopup();

    }

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
// SAVE / UPDATE ROUTE
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

        if (!routeName) {

            alert(
                'Nama route wajib diisi.'
            );

            return;

        }

        if (
            !selectedRouteId
        ) {

            alert(
                'Silakan klik route terlebih dahulu.'
            );

            return;

        }

        if (
            waypoints.length < 2
        ) {

            alert(
                'Minimal harus ada 2 waypoint.'
            );

            return;

        }

        fetch(
            `/routes/${selectedRouteId}`,
            {
                method: 'PUT',

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
                'Route berhasil diperbarui.'
            );

            console.log(
                'ROUTE UPDATED:',
                data
            );

        })

        .catch(error => {

            console.error(
                'Update route error:',
                error
            );

            alert(
                error.message ||
                'Gagal memperbarui route.'
            );

        });

    }
);

</script>

@endsection
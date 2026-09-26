@extends('layout.index')
@section('content')
<div class="wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Historical Route Vessel</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">Home</li>
                        <li class="breadcrumb-item active">Vessel Track Report</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <div class="card">
    <div class="card-header">
        <div class="row align-items-center">
            <!-- Export Dropdown -->
            <div class="col-md-4 mb-2">
                <select id="exportLink" class="form-control">
                    <option disabled selected>Export Data Table</option>
                    <option id="csv">Export as CSV</option>
                    <option id="excel">Export as XLS</option>
                    <option id="copy">Copy to Clipboard</option>
                    <option id="pdf">Export as PDF</option>
                    <option id="print">Print</option>
                </select>
            </div>

            <!-- Filter Form -->
            <div class="col-md-8">
                <form id="filter-form" class="form-inline flex-wrap">
                    <div class="form-group mb-2 mr-3">
                        <label for="start-date" class="mr-2">Start Date:</label>
                        <input type="date" id="start-date" name="start_date" class="form-control">
                    </div>

                    <div class="form-group mb-2 mr-3">
                        <label for="end-date" class="mr-2">End Date:</label>
                        <input type="date" id="end-date" name="end_date" class="form-control">
                    </div>

                    <!-- Filter Nama Kapal -->
<div class="form-group mb-2 mr-3">
    <label for="ship-name" class="mr-2">Ship Name:</label>

    <select id="ship-name"
            name="ship_name"
            class="form-control">

        <option value="">
            Semua Kapal
        </option>

    </select>
</div>

 <!-- Filter Nama Kapal -->
<div class="form-group mb-2 mr-3">
    <label for="status" class="mr-2">Status:</label>

    <select id="status"
            name="status"
            class="form-control">

        <option value="">
            Semua Status
        </option>

    </select>
</div>

                    <button type="button" onclick="filterByDate()" class="btn btn-primary mb-2">
                        Filter
                    </button>
                </form>
            </div>
        </div>
    </div>
     <!-- Map -->
     <div id="map"></div>
<!-- ==========================================
     DAFTAR SEMUA KAPAL
     ========================================== -->
<div class="historical-vessel-section">

    <div class="historical-vessel-header">
        <h4>
            <i class="fas fa-ship"></i>
            Vessel AIS
        </h4>

        <div class="historical-legend">

            <span>
                <span class="historical-dot moving"></span>
                Moving
            </span>

            <span>
                <span class="historical-dot idling"></span>
                Idling
            </span>

            <span>
                <span class="historical-dot inactive"></span>
                Inactive
            </span>

        </div>
    </div>

    <div
        id="historical-vessel-container"
        class="row">
        
        <div class="col-12 text-center">
            <div class="historical-loading">
                <i class="fas fa-spinner fa-spin"></i>
                Loading AIS...
            </div>
        </div>

    </div>

</div>
<!-- Legend -->
<div id="legend">
    <h6><b>Keterangan:</b></h6>
    <div>
        <span class="legend-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" 
                viewBox="0 0 24 24" fill="green">
                <path d="M12 2 L19 21 L12 17 L5 21 Z"/>
            </svg>
        </span> Titik Awal (Start)
    </div>
    <div>
        <span class="legend-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" 
                viewBox="0 0 24 24" fill="red">
                <path d="M12 2 L19 21 L12 17 L5 21 Z"/>
            </svg>
        </span> Titik Akhir (End / Last Position)
    </div>
    <div>
        <span class="legend-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" 
                viewBox="0 0 24 24" fill="orange">
                <path d="M12 2 L19 21 L12 17 L5 21 Z"/>
            </svg>
        </span> Titik Perjalanan (Intermediate)
    </div>
</div>
</div>

<div class="container">

    <div id="distance"></div>
    <div id="destination-distance"></div>
    <div id="estimated-time"></div>
    <div id="average-speed"></div>

   <!-- ==========================================
     TABLE HASIL DATA
     RESPONSIVE + DATATABLE
     ========================================== -->
<div class="mt-3">

    <h5 id="result-title">
    Hasil Data Filter
</h5>

    <div class="table-responsive">

        <table
            class="table table-bordered table-striped table-hover"
            id="result1"
            style="width:100%;"
        >

            <thead>
                <tr>
                    <th>No</th>
                    <th>Date Time (UTC)</th>
                    <th>Name</th>
                    <th>Latitude</th>
                    <th>Longitude</th>
                    <th>Speed (knots)</th>
                    <th>Mileage</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>
                <!-- DataTables mengisi data melalui JavaScript -->
            </tbody>

        </table>

    </div>

</div>

<style>
    
            /* =========================================================
   HISTORICAL VESSEL SECTION
   ========================================================= */

.historical-vessel-section {
    margin-top: 10px;
    margin-bottom: 15px;
}

.historical-vessel-header {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 15px;
    margin-bottom: 8px;
}

.historical-vessel-header h4 {
    margin: 0;
    font-size: 18px;
}

.historical-legend {
    display: flex;
    gap: 15px;
    font-size: 13px;
}

.historical-legend span {
    display: flex;
    align-items: center;
    gap: 5px;
}

.historical-dot {
    width: 11px;
    height: 11px;
    border-radius: 50%;
    display: inline-block;
}

.historical-dot.moving {
    background: #00c853;
}

.historical-dot.idling {
    background: #f44336;
}

.historical-dot.inactive {
    background: #757575;
}


/* =========================================================
   CARD KAPAL
   ========================================================= */

.historical-vessel-card {
    min-height: 88px;
    border-radius: 7px;
    padding: 6px;
    margin-bottom: 8px;
    color: white;
    box-shadow: 0 2px 5px rgba(0,0,0,0.15);
    transition: 0.2s;
    cursor: pointer;
}

.historical-vessel-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 12px rgba(0,0,0,0.20);
}


/* STATUS */

.historical-vessel-card.moving {
    background: #00c853;
}

.historical-vessel-card.idling {
    background: #f44336;
}

.historical-vessel-card.inactive {
    background: #757575;
}

.historical-vessel-card.unknown {
    background: #757575;
}


/* CONTENT */

.historical-vessel-icon {
    text-align: center;
    font-size: 24px;
    line-height: 24px;
    margin-bottom: 2px;
}

.historical-vessel-name {
    text-align: center;
    font-size: 11px;
    font-weight: 700;
    margin-bottom: 2px;
}

.historical-vessel-status {
    text-align: center;
    font-size: 9px;
    font-weight: 700;
    text-transform: uppercase;
    margin-bottom: 2px;
}

.historical-vessel-info {
    text-align: center;
    font-size: 8px;
    line-height: 1.3;
}

.historical-vessel-location {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 4px;
    font-size: 8px;
}

.historical-vessel-location-text {
    max-width: 90%;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.historical-loading,
.historical-empty {
    padding: 20px;
    color: #777;
    text-align: center;
}
    #map { 
        height: 500px; 
        margin-top: 20px; 
        position: relative;
    }
    #legend {
        position: absolute;
        bottom: 30px;
        right: 30px;
        background: white;
        padding: 10px 12px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 13px;
        line-height: 20px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.3);
        z-index: 1000;
    }
    #legend h6 { margin-bottom: 5px; font-size: 14px; }
    .legend-icon { 
        display: inline-block; 
        width: 18px; 
        text-align: center; 
        margin-right: 5px; 
    }
    #map { height: 250px; margin-top: 20px; }
    #filter-form { margin: 20px; position: relative; }
    /* =====================================================
   BARIS TABLE BISA DIKLIK
   ===================================================== */

#result1 tbody tr {
    cursor: pointer;
}

#result1 tbody tr:hover {
    background-color: #e8f4ff !important;
}


/* =====================================================
   MARKER YANG DIPILIH
   ===================================================== */

.selected-history-marker {
    z-index: 9999 !important;
}
    /* =========================================================
   RESPONSIVE DATATABLE
   ========================================================= */

.table-responsive {
    width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

/*
 * ID result1 TETAP dipakai DataTables.
 * min-width membuat 8 kolom tidak terlalu sempit.
 */
#result1 {
    width: 100% !important;
    min-width: 850px;
}

#result1 th,
#result1 td {
    white-space: nowrap;
    vertical-align: middle;
}
</style>

<!-- Leaflet -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://rawcdn.githack.com/bbecquet/Leaflet.RotatedMarker/master/leaflet.rotatedMarker.js"></script>


<script>
    var map = L.map('map').setView([0, 0], 2);
var table1;

// Marker khusus untuk posisi yang dipilih dari tabel
var selectedHistoryMarker = null;

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    function haversineDistance(lat1, lon1, lat2, lon2) {
        const toRadians = deg => deg * Math.PI / 180;
        const R = 6371;
        const dLat = toRadians(lat2 - lat1);
        const dLon = toRadians(lon2 - lon1);
        const a = Math.sin(dLat / 2) ** 2 +
                  Math.cos(toRadians(lat1)) * Math.cos(toRadians(lat2)) *
                  Math.sin(dLon / 2) ** 2;
        return 2 * R * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    }

    function createArrowIcon(color = "orange") {
        return L.divIcon({
            className: "custom-arrow",
            html: `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" 
                        viewBox="0 0 24 24" fill="${color}">
                        <path d="M12 2 L19 21 L12 17 L5 21 Z"/>
                   </svg>`,
            iconSize: [20, 20],
            iconAnchor: [10, 10]
        });
    }

    // ================== LOAD NAMA KAPAL DARI AIS ==================

function loadAISShipNames() {

    fetch('/track-ship/all-last-positions')
        .then(response => {

            if (!response.ok) {
                throw new Error('Gagal mengambil data AIS');
            }

            return response.json();
        })
        .then(positions => {

            const select =
                document.getElementById('ship-name');

            if (!select) {
                return;
            }

            // Reset dropdown
            select.innerHTML = `
                <option value="">
                    Semua Kapal
                </option>
            `;

            if (!Array.isArray(positions)) {
                return;
            }

            // Ambil nama kapal dan hilangkan duplikat
            const shipNames = [
                ...new Set(
                    positions
                        .map(pos => (pos.vname || '').trim())
                        .filter(name => name !== '')
                )
            ];

            // Urutkan berdasarkan nama
            shipNames.sort((a, b) =>
                a.localeCompare(b)
            );

            // Masukkan ke dropdown
            shipNames.forEach(shipName => {

                const option =
                    document.createElement('option');

                option.value = shipName;
                option.textContent = shipName;

                select.appendChild(option);
            });

        })
        .catch(error => {

            console.error(
                'AIS Ship Name Error:',
                error
            );

        });
}
 // ================== LOAD NAMA KAPAL DARI AIS ==================

function loadAISStatus() {

    fetch('/track-ship/all-last-positions')
        .then(response => {

            if (!response.ok) {
                throw new Error('Gagal mengambil data AIS');
            }

            return response.json();
        })
        .then(positions => {

            const select =
                document.getElementById('status');

            if (!select) {
                return;
            }

            // Reset dropdown
            select.innerHTML = `
                <option value="">
                    Semua Status
                </option>
            `;

            if (!Array.isArray(positions)) {
                return;
            }

            // Ambil nama kapal dan hilangkan duplikat
            const status1 = [
                ...new Set(
                    positions
                        .map(pos => (pos.status || '').trim())
                        .filter(status => status !== '')
                )
            ];

            // Urutkan berdasarkan nama
            status1.sort((a, b) =>
                a.localeCompare(b)
            );

            // Masukkan ke dropdown
            status1.forEach(status => {

                const option =
                    document.createElement('option');

                option.value = status;
                option.textContent = status;

                select.appendChild(option);
            });

        })
        .catch(error => {

            console.error(
                'AIS Status Error:',
                error
            );

        });
}
    // ================== LOAD SEMUA POSISI TERAKHIR ==================
    // =====================================================
// LOAD POSISI TERAKHIR SEMUA KAPAL
// Historical menggunakan endpoint khusus
// karena status terakhir boleh MOVING / IDLING / INACTIVE
// =====================================================

function loadAllLastPositions() {

    fetch('/track-ship/historical-last-positions')

        .then(response => {

            if (!response.ok) {
                throw new Error(
                    'Gagal mengambil posisi terakhir kapal'
                );
            }

            return response.json();
        })

        .then(positions => {

            if (
                !Array.isArray(positions) ||
                positions.length === 0
            ) {
                return;
            }

            let bounds = [];

            positions.forEach(pos => {

                const lat =
                    parseFloat(pos.latitude);

                const lon =
                    parseFloat(pos.longitude);

                if (
                    Number.isNaN(lat) ||
                    Number.isNaN(lon)
                ) {
                    return;
                }

                // ==========================================
                // WARNA MARKER BERDASARKAN STATUS
                // ==========================================

                const status =
                    String(pos.status || '')
                        .trim()
                        .toLowerCase();

                let markerColor = 'gray';

                if (status === 'moving') {
                    markerColor = 'green';
                }

                else if (status === 'idling') {
                    markerColor = 'red';
                }

                else if (status === 'inactive') {
                    markerColor = 'gray';
                }

                // ==========================================
                // MARKER KAPAL
                // ==========================================

                const marker = L.marker(
                    [lat, lon],
                    {
                        icon: createArrowIcon(markerColor),

                        rotationAngle:
                            parseFloat(pos.direct) || 0,

                        rotationOrigin:
                            "center center"
                    }
                ).addTo(map);

                // ==========================================
                // POPUP
                // ==========================================

                marker.bindPopup(`
                    <b>Date Time:</b>
                    ${pos.datetime_utc || '-'}
                    <br>

                    <b>Name:</b>
                    ${pos.vname || '-'}
                    <br>

                    <b>Status:</b>
                    ${pos.status || '-'}
                    <br>

                    <b>Speed:</b>
                    ${pos.speed || 0} knots
                    <br>

                    <b>Latitude:</b>
                    ${pos.latitude || '-'}
                    <br>

                    <b>Longitude:</b>
                    ${pos.longitude || '-'}
                `);

                bounds.push([lat, lon]);
            });

            // ==========================================
            // ZOOM KE SEMUA KAPAL
            // ==========================================

            if (bounds.length > 0) {

                map.fitBounds(bounds, {
                    padding: [30, 30]
                });

            }

        })

        .catch(error => {

            console.error(
                'Historical vessel error:',
                error
            );

        });
}
   
// =====================================================
// LOAD CARD SEMUA KAPAL
// Historical menampilkan status terakhir sebenarnya
// =====================================================

function loadHistoricalVessels() {

    fetch('/track-ship/historical-last-positions')

        .then(response => {

            if (!response.ok) {
                throw new Error(
                    'Gagal mengambil data kapal'
                );
            }

            return response.json();
        })

        .then(positions => {

            const container =
                document.getElementById(
                    'historical-vessel-container'
                );

            if (!container) {
                return;
            }

            container.innerHTML = '';

            if (
                !Array.isArray(positions) ||
                positions.length === 0
            ) {

                container.innerHTML = `
                    <div class="col-12">
                        <div class="historical-empty">
                            <i class="fas fa-ship"></i>
                            <br>
                            Tidak ada data kapal.
                        </div>
                    </div>
                `;

                return;
            }

            // ==========================================
            // LOOP SEMUA KAPAL
            // ==========================================

            positions.forEach(pos => {

                const vesselName =
                    pos.vname || 'Unknown Vessel';

                const status =
                    String(pos.status || '')
                        .trim()
                        .toLowerCase();

                const speed =
                    parseFloat(pos.speed) || 0;

                // ==========================================
                // TENTUKAN WARNA CARD
                // ==========================================

                let statusClass = 'unknown';
                let statusText =
                    pos.status || 'UNKNOWN';

                if (status === 'moving') {

                    statusClass = 'moving';
                    statusText = 'MOVING';

                }

                else if (status === 'idling') {

                    statusClass = 'idling';
                    statusText = 'IDLING';

                }

                else if (status === 'inactive') {

                    statusClass = 'inactive';
                    statusText = 'INACTIVE';

                }

                // ==========================================
                // CARD
                // ==========================================

                const col =
                    document.createElement('div');

                col.className =
                    'col-6 col-sm-4 col-md-3 mb-3';

                col.style.cursor = 'pointer';

                col.innerHTML = `
                    <div
                        class="historical-vessel-card ${statusClass}"
                    >

                        <div class="historical-vessel-icon">
                            <i class="fas fa-ship"></i>
                        </div>

                        <div class="historical-vessel-name">
                            ${escapeHistoricalHtml(
                                vesselName
                            )}
                        </div>

                        <div class="historical-vessel-status">
                            ${escapeHistoricalHtml(
                                statusText
                            )}
                        </div>

                        <div class="historical-vessel-info">

                            <div>
                                <i class="fas fa-tachometer-alt"></i>
                                Speed:
                                ${speed.toFixed(2)}
                                knots
                            </div>

                            <div
                                class="historical-vessel-location"
                            >
                                <i class="fas fa-map-marker-alt"></i>

                                <span
                                    class="historical-vessel-location-text"
                                >
                                    Mencari lokasi...
                                </span>

                            </div>

                        </div>

                    </div>
                `;

                container.appendChild(col);

                // ==========================================
                // KETIKA CARD DIKLIK
                // FOCUS KE KAPAL DI MAP
                // ==========================================

                // ==========================================
// KETIKA CARD DIKLIK
// FILTER DATA BERDASARKAN KAPAL
// ==========================================

col.addEventListener(
    'click',
    function () {

        console.log(
            'Kapal dipilih:',
            vesselName
        );

document.getElementById('result-title').innerText =
    `Data History - ${vesselName}`;
        // =================================================
        // PILIH NAMA KAPAL DI DROPDOWN
        // =================================================

        const shipSelect =
            document.getElementById('ship-name');

        if (shipSelect) {

            shipSelect.value = vesselName;

        }


        // =================================================
        // FILTER DATA KAPAL
        // =================================================

        filterByDate();


        // =================================================
        // FOCUS KE POSISI TERAKHIR KAPAL
        // =================================================

        const lat =
            parseFloat(pos.latitude);

        const lon =
            parseFloat(pos.longitude);


        if (
            !Number.isNaN(lat) &&
            !Number.isNaN(lon)
        ) {

            map.flyTo(
                [lat, lon],
                10,
                {
                    animate: true,
                    duration: 1.5
                }
            );

        }

    }
);

                // ==========================================
                // REVERSE GEOCODING
                // ==========================================

                const locationElement =
                    col.querySelector(
                        '.historical-vessel-location-text'
                    );

                getHistoricalLocation(
                    pos.latitude,
                    pos.longitude,
                    locationElement
                );

            });

        })

        .catch(error => {

            console.error(
                'Historical vessel card error:',
                error
            );

        });
}
// =====================================================
// LOKASI KAPAL
// =====================================================

function getHistoricalLocation(
    latitude,
    longitude,
    element
) {

    if (
        !latitude ||
        !longitude
    ) {

        element.innerText =
            'Lokasi tidak tersedia';

        return;
    }

    fetch(
        `https://nominatim.openstreetmap.org/reverse` +
        `?format=jsonv2` +
        `&lat=${encodeURIComponent(latitude)}` +
        `&lon=${encodeURIComponent(longitude)}` +
        `&zoom=10` +
        `&addressdetails=1`
    )

    .then(response => {

        if (!response.ok) {
            throw new Error(
                'Reverse geocoding gagal'
            );
        }

        return response.json();

    })

    .then(data => {

        if (
            !data ||
            !data.display_name
        ) {

            element.innerText =
                'Lokasi tidak ditemukan';

            return;
        }

        const address =
            data.address || {};

        let location = [];

        if (address.sea) {
            location.push(address.sea);
        }

        if (address.ocean) {
            location.push(address.ocean);
        }

        if (address.state) {
            location.push(address.state);
        }

        if (address.country) {
            location.push(address.country);
        }

        if (location.length === 0) {

            location.push(
                data.display_name
            );

        }

        element.innerText =
            location.join(', ');

    })

    .catch(error => {

        console.error(
            'Historical location error:',
            error
        );

        element.innerText =
            `${latitude}, ${longitude}`;

    });
}
function escapeHistoricalHtml(value) {

    const div =
        document.createElement('div');

    div.textContent = value;

    return div.innerHTML;
}

// =====================================================
// LOAD MAP + SEMUA KAPAL
// =====================================================

loadAllLastPositions();
loadHistoricalVessels();
loadAISShipNames();
loadAISStatus();

setInterval(loadAISShipNames, 300000);
setInterval(loadAISStatus, 300000);
setInterval(function () {

    loadHistoricalVessels();

}, 30000);
// =====================================================
// TAMPILKAN POSISI HISTORY YANG DIPILIH DARI TABLE
// =====================================================

function showHistoryPoint(
    latitude,
    longitude,
    datetime,
    shipName,
    speed,
    mileage,
    status
) {

    latitude = parseFloat(latitude);
    longitude = parseFloat(longitude);


    // =================================================
    // VALIDASI KOORDINAT
    // =================================================

    if (
        isNaN(latitude) ||
        isNaN(longitude)
    ) {

        alert(
            'Koordinat tidak valid.'
        );

        return;
    }


    // =================================================
    // HAPUS MARKER PILIHAN SEBELUMNYA
    // =================================================

    if (selectedHistoryMarker) {

        map.removeLayer(
            selectedHistoryMarker
        );

        selectedHistoryMarker = null;

    }


    // =================================================
    // BUAT MARKER BESAR
    // =================================================

    selectedHistoryMarker =
        L.circleMarker(
            [
                latitude,
                longitude
            ],
            {
                radius: 10,

                color: '#ffffff',

                weight: 3,

                fillColor: '#ff0000',

                fillOpacity: 1,

                className:
                    'selected-history-marker'
            }
        ).addTo(map);


    // =================================================
    // POPUP
    // =================================================

    selectedHistoryMarker.bindPopup(`

        <div style="min-width:220px;">

            <b style="font-size:15px;">
                ${shipName}
            </b>

            <hr style="margin:6px 0;">

            <b>Date Time:</b>
            ${datetime}

            <br>

            <b>Latitude:</b>
            ${latitude.toFixed(6)}

            <br>

            <b>Longitude:</b>
            ${longitude.toFixed(6)}

            <br>

            <b>Speed:</b>
            ${speed} knots

            <br>

            <b>Mileage:</b>
            ${mileage}

            <br>

            <b>Status:</b>
            ${status || '-'}

        </div>

    `);


    // =================================================
    // PINDAHKAN PETA KE TITIK
    // =================================================

    map.setView(
        [
            latitude,
            longitude
        ],
        14,
        {
            animate: true
        }
    );


    // =================================================
    // BUKA POPUP
    // =================================================

    selectedHistoryMarker.openPopup();

}
// =====================================================
// KLIK BARIS TABLE → TAMPILKAN POSISI DI MAP
// =====================================================

$('#result1 tbody').on(
    'click',
    'tr',
    function () {

        // Ambil data dari baris yang diklik
        const rowData =
            table1.row(this).data();


        // Tidak ada data
        if (!rowData) {

            return;

        }


        // Struktur rowData:
        //
        // [0] No
        // [1] Date Time
        // [2] Name
        // [3] Latitude
        // [4] Longitude
        // [5] Speed
        // [6] Mileage
        // [7] Status


        const datetime =
            rowData[1];

        const shipName =
            rowData[2];

        const latitude =
            rowData[3];

        const longitude =
            rowData[4];

        const speed =
            rowData[5];

        const mileage =
            rowData[6];

        const status =
            rowData[7];


        // =================================================
        // TAMPILKAN DI MAP
        // =================================================

        showHistoryPoint(

            latitude,

            longitude,

            datetime,

            shipName,

            speed,

            mileage,

            status

        );

    }
);
    // ================== FILTER ==================
function filterByDate() {
    var startDate = document.getElementById('start-date').value;
    var endDate = document.getElementById('end-date').value;
    var shipName = document.getElementById('ship-name').value;

    fetch(`/track-ship/filter?start_date=${startDate}&end_date=${endDate}&ship_name=${encodeURIComponent(shipName)}`)
        .then(response => response.json())
        .then(data => {
            // hapus marker lama
            map.eachLayer(function (layer) {
                if (layer instanceof L.Marker || layer instanceof L.Polyline) {
                    map.removeLayer(layer);
                }
            });

            // tambah tile layer lagi
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // reset DataTable
            table1.clear();

            let groupedByShip = {};
            data.positions.forEach(pos => {
                if (!groupedByShip[pos.vname]) {
                    groupedByShip[pos.vname] = [];
                }
                groupedByShip[pos.vname].push(pos);
            });

            let totalDistance = 0;

            Object.keys(groupedByShip).forEach(ship => {
                let positions = groupedByShip[ship];
                let latlngs = [];

                positions.forEach((position, index) => {
                    latlngs.push([position.latitude, position.longitude]);

                    let markerColor = "orange";
                    if (index === 0) markerColor = "green";   // start per kapal
                    if (index === positions.length - 1) markerColor = "red"; // end per kapal

                    L.marker([position.latitude, position.longitude], {
                        icon: createArrowIcon(markerColor),
                        rotationAngle: position.direct || 0,
                        rotationOrigin: "center center"
                    }).addTo(map)
                      .bindPopup(`
                          <b>Date Time:</b> ${position.datetime_utc}<br>
                          <b>Name:</b> ${position.vname}<br>
                          <b>Lat:</b> ${position.latitude}<br>
                          <b>Lon:</b> ${position.longitude}<br>
                          <b>Speed:</b> ${position.speed} knots<br>
                          <b>mileage:</b> ${position.mileage} <br>
                          <b>Status:</b> ${position.status || '-'}
                      `);

                    // hitung jarak
                    if (index > 0) {
                        const prevPos = positions[index - 1];
                        totalDistance += haversineDistance(
                            prevPos.latitude, prevPos.longitude,
                            position.latitude, position.longitude
                        );
                    }

                    // isi tabel
                    table1.row.add([
                        index + 1,
                        position.datetime_utc,
                        position.vname,
                        position.latitude,
                        position.longitude,
                        position.speed,
                        position.mileage,
                        position.status || '-'
                    ]);
                });

                // hanya gambar garis jika filter untuk 1 kapal
                if (shipName && latlngs.length > 1) {
                    var polyline = L.polyline(latlngs, { color: 'orange' }).addTo(map);
                    map.fitBounds(polyline.getBounds());
                }
            });

            // redraw DataTable
            table1.draw();

            // update total jarak (semua kapal)
            document.getElementById('distance').innerText =
                `Total Distance: ${totalDistance.toFixed(2)} km`;
        });
}
</script>
@endsection

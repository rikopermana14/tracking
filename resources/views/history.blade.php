@extends('layout.index')
@section('content')
<div class="wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Vessel Track Report</h1>
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

                    <button type="button" onclick="filterByDate()" class="btn btn-primary mb-2">
                        Filter
                    </button>
                </form>
            </div>
        </div>
    </div>
     <!-- Map -->
     <div id="map"></div>

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
   
        <style>
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
</style>
    <div id="distance"></div>
    <div id="destination-distance"></div>
    <div id="estimated-time"></div>
    <div id="average-speed"></div>

    <!-- Table -->
    <div class="mt-3">
        <h5>Hasil Data Filter</h5>
        <table class="table table-bordered table-striped" id="result1">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Date Time (UTC)</th>
                    <th>Name</th>
                    <th>Latitude</th>
                    <th>Longitude</th>
                    <th>Speed (knots)</th>
                    <th>mileage</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <!-- diisi via JS -->
            </tbody>
        </table>
    </div>
</div>

<style>
    #map { height: 250px; margin-top: 20px; }
    #filter-form { margin: 20px; position: relative; }
</style>

<!-- Leaflet -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://rawcdn.githack.com/bbecquet/Leaflet.RotatedMarker/master/leaflet.rotatedMarker.js"></script>


<script>
    var map = L.map('map').setView([0, 0], 2);
    var table1;

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
    // ================== LOAD SEMUA POSISI TERAKHIR ==================
    function loadAllLastPositions() {
        fetch('/track-ship/all-last-positions')
            .then(r => r.json())
            .then(positions => {
                if (Array.isArray(positions) && positions.length > 0) {
                    let bounds = [];
                    positions.forEach(pos => {
                        const marker = L.marker([pos.latitude, pos.longitude], {
                            icon: createArrowIcon("red"),
                            rotationAngle: pos.direct || 0,
                            rotationOrigin: "center center"
                        }).addTo(map);

                        marker.bindPopup(`
                            <b>Date Time:</b> ${pos.datetime_utc}<br>
                            <b>Name:</b> ${pos.vname}<br>
                            <b>Status:</b> ${pos.status}<br>
                            <b>Speed:</b> ${pos.speed} knots
                        `);
                        bounds.push([pos.latitude, pos.longitude]);
                    });
                    if (bounds.length > 0) map.fitBounds(bounds);
                }
            });
    }
    loadAllLastPositions();
loadAISShipNames();
setInterval(loadAISShipNames, 300000);

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

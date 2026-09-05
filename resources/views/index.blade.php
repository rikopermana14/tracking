@extends('layout.index')
@section('content')
<div class="container-fluid px-2">

    <!-- Map -->
    <div id="map"></div>

    <!-- INFO PANEL -->
<div id="route-info-panel">

    <div class="route-info-header">
        <div>
            <i class="fas fa-route"></i>
            <strong>Voyage Information</strong>
        </div>

        <div id="route-status-badge"
             class="route-status waiting">
            WAITING
        </div>
    </div>


    <div class="route-info-columns">

        <!-- CONTAINER KIRI -->
        <div class="route-info-column">

            <div class="route-info-item">
                <div class="route-info-icon">
                    <i class="fas fa-map-marker-alt"></i>
                </div>

                <div class="route-info-content">
                    <span class="route-info-label">
                        DESTINATION
                    </span>

                    <strong id="route-destination">
                        -
                    </strong>
                </div>
            </div>


            <div class="route-info-item">
                <div class="route-info-icon">
                    <i class="fas fa-clock"></i>
                </div>

                <div class="route-info-content">
                    <span class="route-info-label">
                        ETA
                    </span>

                    <strong id="estimated-time">
                        -
                    </strong>
                </div>
            </div>

        </div>


        <!-- CONTAINER KANAN -->
        <div class="route-info-column">

            <div class="route-info-item">
                <div class="route-info-icon">
                    <i class="fas fa-road"></i>
                </div>

                <div class="route-info-content">
                    <span class="route-info-label">
                        DISTANCE
                    </span>

                    <strong id="destination-distance">
                        -
                    </strong>
                </div>
            </div>


            <div class="route-info-item">
                <div class="route-info-icon">
                    <i class="fas fa-tachometer-alt"></i>
                </div>

                <div class="route-info-content">
                    <span class="route-info-label">
                        VESSEL SPEED
                    </span>

                    <strong id="average-speed">
                        -
                    </strong>
                </div>
            </div>

        </div>

    </div>


    <!-- STATUS ROUTE -->
    <div id="distance"
         class="route-position-status">

        <i class="fas fa-compass"></i>
        Waiting for route...

    </div>

</div>
<!-- ==========================================
     CONTAINER BAWAH - DAFTAR KAPAL DARI AIS
     ========================================== -->

<div class="container-fluid ais-vessel-section">
    <div class="ais-vessel-header">
        <h4>
            <i class="fas fa-ship"></i>
            Vessel AIS
        </h4>

        <div class="ais-legend">
            <span>
                <span class="legend-dot moving"></span>
                Moving
            </span>

            <span>
                <span class="legend-dot idling"></span>
                Idling
            </span>
        </div>
    </div>

    <div id="ais-vessel-container" class="row">
        <div class="col-12 text-center">
            <div class="ais-loading">
                <i class="fas fa-spinner fa-spin"></i>
                Loading AIS...
            </div>
        </div>
    </div>
</div>
</div>

<style>

/* =========================================================
   MAP
   ========================================================= */

#map {
    height: 205px;
    margin-top: 2px;
}


/* =========================================================
   INFO PANEL
   ========================================================= */

#route-info-panel {
    margin-top: 8px;
    margin-bottom: 8px;
    border: 1px solid #ddd;
    border-radius: 10px;
    background: #fff;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
    overflow: hidden;
}


/* HEADER */

.route-info-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 5px 10px;
    font-size: 12px;
    background: #f5f6f7;
    border-bottom: 1px solid #ddd;
}

.route-info-header i {
    margin-right: 5px;
}


/* STATUS BADGE */

.route-status {
    padding: 3px 9px;
    border-radius: 20px;
    font-size: 10px;
    font-weight: 700;
}

.route-status.waiting {
    background: #e0e0e0;
    color: #555;
}

.route-status.on-route {
    background: #d8f5df;
    color: #16803c;
}

.route-status.off-route {
    background: #ffe0e0;
    color: #c62828;
}


/* =========================================================
   INFO PANEL - 2 KOLOM
   ========================================================= */

.route-info-columns {
    display: grid;
    grid-template-columns: 1fr 1fr;
}

.route-info-column {
    padding: 5px 10px;
}

.route-info-column:first-child {
    border-right: 1px solid #ddd;
}


/* INFO ITEM */

.route-info-item {
    display: flex;
    align-items: center;
    min-height: 38px;
    padding: 4px 5px;
}

.route-info-column .route-info-item + .route-info-item {
    border-top: 1px solid #eee;
}


/* ICON */

.route-info-icon {
    width: 28px;
    min-width: 28px;
    font-size: 15px;
    text-align: center;
    color: #555;
}


/* CONTENT */

.route-info-content {
    min-width: 0;
    margin-left: 8px;
}

.route-info-label {
    display: block;
    font-size: 8px;
    color: #777;
    font-weight: 600;
    letter-spacing: 0.4px;
}

.route-info-content strong {
    display: block;
    font-size: 11px;
    color: #222;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}


/* DESTINATION */

#route-destination {
    max-width: 100%;
}


/* =========================================================
   ROUTE POSITION STATUS
   ========================================================= */

.route-position-status {
    padding: 6px 12px;
    border-top: 1px solid #eee;
    font-size: 11px;
    color: #555;
    background: #fafafa;
}

.route-position-status.on-route {
    color: #16803c;
    background: #f0fff4;
}

.route-position-status.off-route {
    color: #c62828;
    background: #fff5f5;
}


/* =========================================================
   AIS VESSEL HEADER
   ========================================================= */

.ais-vessel-header {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: row;
    margin-bottom: 5px;
    gap: 15px;
}

.ais-vessel-header h4 {
    margin: 0;
    font-size: 18px;
}


/* =========================================================
   AIS LEGEND
   ========================================================= */

.ais-legend {
    display: flex;
    gap: 15px;
    font-size: 14px;
}

.ais-legend span {
    display: flex;
    align-items: center;
    gap: 5px;
}

.legend-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    display: inline-block;
}

.legend-dot.moving {
    background-color: #00c853;
}

.legend-dot.idling {
    background-color: #f44336;
}


/* =========================================================
   AIS CONTAINER
   ========================================================= */

#ais-vessel-container {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    margin-left: -5px;
    margin-right: -5px;
}

#ais-vessel-container > div {
    padding-left: 5px;
    padding-right: 5px;
}


/* =========================================================
   AIS VESSEL CARD
   ========================================================= */

.ais-vessel-card {
    min-height: 88px;
    border-radius: 7px;
    padding: 5px;
    margin-bottom: 5px;
    color: white;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
    transition:
        transform 0.2s,
        box-shadow 0.2s;
    cursor: pointer;
}

.ais-vessel-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 12px rgba(0, 0, 0, 0.20);
}


/* STATUS WARNA */

.ais-vessel-card.moving {
    background-color: #00c853;
}

.ais-vessel-card.idling {
    background-color: #f44336;
}

.ais-vessel-card.unknown {
    background-color: #757575;
}


/* =========================================================
   AIS VESSEL CONTENT
   ========================================================= */

.ais-vessel-icon {
    text-align: center;
    font-size: 24px;
    line-height: 24px;
    margin-bottom: 1px;
}

.ais-vessel-name {
    text-align: center;
    font-size: 11px;
    margin-bottom: 1px;
    font-weight: 700;
    color: white;
}

.ais-vessel-status {
    text-align: center;
    font-size: 9px;
    font-weight: 700;
    text-transform: uppercase;
    margin-bottom: 1px;
}

.ais-vessel-info {
    text-align: center;
    font-size: 8px;
    line-height: 1.15;
}


/* =========================================================
   AIS LOCATION
   ========================================================= */

.ais-vessel-location {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 4px;
     font-size: 8px;
}

.vessel-location-text {
    max-width: 90%;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}


/* =========================================================
   LOADING / EMPTY
   ========================================================= */

.ais-loading {
    padding: 20px;
    font-size: 14px;
    color: #777;
}

.ais-empty {
    padding: 20px;
    text-align: center;
    color: #777;
}


/* =========================================================
   RESPONSIVE
   ========================================================= */

@media (max-width: 768px) {

    #map {
        height: 240px;
    }

    .route-info-columns {
        grid-template-columns: 1fr;
    }

    .route-info-column:first-child {
        border-right: none;
        border-bottom: 1px solid #ddd;
    }

    .ais-vessel-header {
        gap: 10px;
    }

    .ais-vessel-header h4 {
        font-size: 16px;
    }

    .ais-legend {
        font-size: 12px;
        gap: 10px;
    }
}


@media (max-width: 480px) {

    #map {
        height: 220px;
    }

    .route-info-header {
        font-size: 13px;
    }

    .route-info-item {
        min-height: 44px;
    }

    .route-info-content strong {
        font-size: 12px;
    }

    .ais-vessel-icon {
        font-size: 32px;
    }

    .ais-vessel-name {
        font-size: 12px;
    }

    .ais-vessel-status {
        font-size: 10px;
    }

    .ais-vessel-info {
        font-size: 9px;
    }
}
/* =========================================================
   VESSEL POPUP
   ========================================================= */

.vessel-popup {
    width: 100%;
    font-size: 11px;
}

.popup-title {
    font-size: 13px;
    font-weight: 700;
    margin-bottom: 5px;
    padding-bottom: 4px;
    border-bottom: 1px solid #ddd;
}

.popup-info {
    line-height: 1.35;
}

.vessel-popup hr {
    margin: 5px 0;
}

.vessel-popup label {
    display: block;
    margin-bottom: 3px;
}

.popup-destination {
    width: 100%;
    height: 28px;
    padding: 4px 7px;
    font-size: 11px;
    border: 1px solid #bbb;
    border-radius: 4px;
    box-sizing: border-box;
}

.popup-suggestions {
    max-height: 90px;
    overflow-y: auto;
    border: 1px solid #ccc;
    background: #fff;
    width: 100%;
    display: none;
    box-sizing: border-box;
}

.popup-suggestions div {
    padding: 5px 7px;
    cursor: pointer;
    font-size: 10px;
}
</style>

<!-- Leaflet -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://rawcdn.githack.com/bbecquet/Leaflet.RotatedMarker/master/leaflet.rotatedMarker.js"></script>

<script>
    var map = L.map('map').setView([0, 0], 5);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    let destMarker = null;
    let routeLine = null;
    let connectorLine = null;
    let vesselMarkers = {};

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

    function loadLastPositions() {
        fetch('/track-ship/all-last-positions')
            .then(r => r.json())
            .then(positions => {

                if (!Array.isArray(positions) || positions.length === 0) {
                    return;
                }

                let bounds = [];

                positions.forEach(pos => {

                    if (!pos.latitude || !pos.longitude) {
                        return;
                    }

                    const lat = parseFloat(pos.latitude);
                    const lon = parseFloat(pos.longitude);

                    bounds.push([lat, lon]);

                    const vesselKey =
    pos.mmsi ||
    pos.imo ||
    pos.vname ||
    `${lat}_${lon}`;

const marker = L.marker([lat, lon], {
    icon: createArrowIcon(
        String(pos.status || '').toLowerCase() === 'moving'
            ? 'green'
            : 'red'
    ),
    rotationAngle: pos.direct || 0,
    rotationOrigin: "center center"
}).addTo(map);

// Simpan marker agar bisa dipanggil dari container bawah
vesselMarkers[vesselKey] = marker;

                    marker.bindPopup(`
    <div class="vessel-popup">

        <div class="popup-title">
            <i class="fas fa-ship"></i>
            ${escapeAISHtml(pos.vname || 'Unknown Vessel')}
        </div>

        <div class="popup-info">
            <div>
                <b>Date Time:</b>
                ${pos.datetime_utc || '-'}
            </div>

            <div>
                <b>Status:</b>
                ${pos.status || '-'}
            </div>

            <div>
                <b>Lat:</b>
                ${pos.latitude || '-'}
            </div>

            <div>
                <b>Lon:</b>
                ${pos.longitude || '-'}
            </div>

            <div>
                <b>Speed:</b>
                ${pos.speed || 0} knots
            </div>
        </div>

        <hr>

        <label>
            <b>Destination Name:</b>
        </label>

        <input
            type="text"
            class="popup-destination"
            placeholder="Ketik lokasi..."
        >

        <div class="popup-suggestions"></div>

    </div>
`, {
    maxWidth: 250,
    minWidth: 230,
    maxHeight: 180,
    autoPan: true,
    autoPanPaddingTopLeft: [10, 50],
    autoPanPaddingBottomRight: [10, 20]
});
marker.on("popupopen", function(e) {

    initPopupAutocomplete(e, pos);

    setTimeout(function () {

        const popupElement = e.popup.getElement();

        if (!popupElement) {
            return;
        }

        const mapElement = document.getElementById('map');

        const popupRect = popupElement.getBoundingClientRect();
        const mapRect = mapElement.getBoundingClientRect();

        let moveY = 0;

        // Popup terlalu tinggi
        if (popupRect.top < mapRect.top + 10) {
            moveY = (mapRect.top + 10) - popupRect.top;
        }

        // Popup terlalu rendah
        if (popupRect.bottom > mapRect.bottom - 10) {
            moveY = (mapRect.bottom - 10) - popupRect.bottom;
        }

        if (moveY !== 0) {
            map.panBy([0, -moveY], {
                animate: true,
                duration: 0.3
            });
        }

    }, 150);

});
                });

                if (bounds.length > 0) {
                    map.fitBounds(bounds);
                }
            })
            .catch(err => {
                console.error("Load last positions error:", err);
            });
    }

    loadLastPositions();

    function initPopupAutocomplete(e, shipPos) {
        const container = e.popup.getElement();
        const inputPopup = container.querySelector(".popup-destination");
        const suggestionPopup = container.querySelector(".popup-suggestions");

       let searchTimeout;

inputPopup.addEventListener("input", function () {

    clearTimeout(searchTimeout);

    let query = this.value.trim();

    if (query.length < 3) {
        suggestionPopup.style.display = "none";
        return;
    }

    searchTimeout = setTimeout(() => {

        fetch(
            `/track-ship/search-destination?q=${encodeURIComponent(query)}`
        )
        .then(res => res.json())
        .then(data => {

            suggestionPopup.innerHTML = "";

            if (!Array.isArray(data) || data.length === 0) {
                suggestionPopup.style.display = "none";
                return;
            }

            data.forEach(place => {

                const div = document.createElement("div");
                div.textContent = place.display_name;

                div.onclick = function () {
                    inputPopup.value = place.display_name;
suggestionPopup.style.display = "none";

// Tampilkan destination di Info Panel
const destinationElement =
    document.getElementById('route-destination');

if (destinationElement) {
    destinationElement.innerText =
        place.display_name;
}

// Status sementara
const routeStatus =
    document.getElementById('route-status-badge');

if (routeStatus) {
    routeStatus.innerText = 'CALCULATING';
    routeStatus.className =
        'route-status waiting';
}

                            const destLat = parseFloat(place.lat);
                            const destLon = parseFloat(place.lon);

                            const shipLat = parseFloat(shipPos.latitude);
                            const shipLon = parseFloat(shipPos.longitude);

                            if (destMarker) {
                                map.removeLayer(destMarker);
                            }

                            if (routeLine) {
                                map.removeLayer(routeLine);
                            }

                            if (connectorLine) {
                                map.removeLayer(connectorLine);
                            }

                            destMarker = L.marker([destLat, destLon], {
                                icon: createArrowIcon("blue")
                            })
                            .addTo(map)
                            .bindPopup(place.display_name)
                            .openPopup();

                            fetch('/track-ship/generate-route', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector(
                                        'meta[name="csrf-token"]'
                                    ).content
                                },
                              body: JSON.stringify({


    ship_lat: shipLat,
    ship_lon: shipLon,

    dest_lat: destLat,
    dest_lon: destLon,

    corridor_nm: 5
})
                            })
                            .then(async res => {

                                const routeData = await res.json();

                                if (!res.ok) {
                                    console.error("Generate route HTTP error:", routeData);
                                    throw routeData;
                                }

                                return routeData;
                            })
                            .then(routeData => {

                                console.log("ROUTE DATA", routeData);
console.log(
    "SELECTED ROUTE:",
    routeData.selected_route_id,
    routeData.selected_route_name
);
                                if (routeData.error) {
                                    alert(routeData.error);
                                    return;
                                }

                                if (!routeData.route_path || routeData.route_path.length < 2) {
                                    console.error("Route path tidak valid:", routeData);
                                    alert("Route path tidak ditemukan atau point kurang dari 2.");
                                    return;
                                }

// let routeCoordinates = [];

// const isOnRoute = routeData.is_on_route === true;

// /*
//  * Kalau kapal masih dalam corridor route,
//  * garis biru dimulai dari posisi kapal.
//  */
// if (isOnRoute) {
//     routeCoordinates.push([
//         parseFloat(shipLat),
//         parseFloat(shipLon)
//     ]);
// }
let routeCoordinates = [];

routeData.route_path.forEach(point => {
    routeCoordinates.push([
        parseFloat(point.latitude),
        parseFloat(point.longitude)
    ]);
});

routeLine = L.polyline(routeCoordinates,{
    color:'blue',
    weight:4
}).addTo(map);

connectorLine = L.polyline([
    [
        parseFloat(shipLat),
        parseFloat(shipLon)
    ],
    [
        parseFloat(routeData.start_snap.latitude),
        parseFloat(routeData.start_snap.longitude)
    ]
],{
    color:'orange',
    weight:2,
    dashArray:'5,5'
}).addTo(map);

const offRouteDistanceNM =
    parseFloat(routeData.off_route_distance_nm) || 0;


                                map.fitBounds(routeLine.getBounds());

                                let distanceNM = parseFloat(routeData.distance_nm) || 0;

                                document.getElementById('destination-distance').innerText =
                                    `Distance To Destination : ${distanceNM.toFixed(2)} NM`;
const routeStatus =
    document.getElementById('route-status-badge');

const routePosition =
    document.getElementById('distance');

if (routeData.is_on_route === true || routeData.is_on_route == 1) {

    // Badge
    routeStatus.innerText = 'ON ROUTE';
    routeStatus.className =
        'route-status on-route';

    // Detail bawah
    routePosition.className =
        'route-position-status on-route';

    routePosition.innerHTML = `
        <i class="fas fa-check-circle"></i>
        Vessel is ON ROUTE
        &nbsp;•&nbsp;
        ${offRouteDistanceNM.toFixed(2)} NM
        from route centerline
    `;

} else {

    // Badge
    routeStatus.innerText = 'OFF ROUTE';
    routeStatus.className =
        'route-status off-route';

    // Detail bawah
    routePosition.className =
        'route-position-status off-route';

    routePosition.innerHTML = `
        <i class="fas fa-exclamation-triangle"></i>
        Vessel is OFF ROUTE
        &nbsp;•&nbsp;
        ${offRouteDistanceNM.toFixed(2)} NM
        from nearest route
    `;
}

                                const shipSpeed = parseFloat(shipPos.speed) || 0;

                                if (shipSpeed > 0) {

                                   const eta = distanceNM / shipSpeed;

const totalMinutes = Math.round(eta * 60);

const hours = Math.floor(totalMinutes / 60);
const minutes = totalMinutes % 60;

const days = Math.floor(hours / 24);
const remainingHours = hours % 24;

let etaDayText = '';

if (days > 0) {
    etaDayText =
        `${days}d ${remainingHours}h ${minutes}m`;
} else {
    etaDayText =
        `0d ${hours}h ${minutes}m`;
}

document.getElementById('estimated-time').innerText =
    `${hours}h ${minutes}m / ${etaDayText}`;

                                    document.getElementById('average-speed').innerText =
                                        `Average Speed: ${shipSpeed.toFixed(2)} knots`;

                                } else {

                                    document.getElementById('estimated-time').innerText =
                                        "ETA : kapal sedang diam / speed 0";

                                    document.getElementById('average-speed').innerText =
                                        "Average Speed: 0 knots";
                                }
                            })
                            .catch(err => {
                                console.error("Generate route error:", err);
                                alert("Gagal generate route. Cek console / network.");
                            });
                        };

                        suggestionPopup.appendChild(div);
                    });

                    suggestionPopup.style.display = "block";
                })
                .catch(err => {
                    console.error("Autocomplete error:", err);
                });
                 }, 1000);
        });
    }
</script>




<script>
    function getAISLocation(latitude, longitude, element) {

    if (!latitude || !longitude) {
        element.innerText = 'Lokasi tidak tersedia';
        return;
    }

    fetch(
        `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${encodeURIComponent(latitude)}&lon=${encodeURIComponent(longitude)}&zoom=10&addressdetails=1`,
        {
            headers: {
                'Accept': 'application/json'
            }
        }
    )
    .then(response => {

        if (!response.ok) {
            throw new Error('Reverse geocoding gagal');
        }

        return response.json();
    })
    .then(data => {

        if (!data || !data.display_name) {
            element.innerText = 'Lokasi tidak ditemukan';
            return;
        }

        const address = data.address || {};

        /*
         * Untuk kapal di laut biasanya kita prioritaskan:
         * sea/ocean → state/wilayah → country
         */

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

        /*
         * Jika address tidak memiliki informasi
         * yang cukup, gunakan display_name.
         */
        if (location.length === 0) {
            location.push(data.display_name);
        }

        element.innerText = location.join(', ');
    })
    .catch(error => {

        console.error(
            'Location lookup error:',
            error
        );

        element.innerText =
            `${latitude}, ${longitude}`;
    });
}
    function loadAISVessels() {

        fetch('/track-ship/all-last-positions')
            .then(response => {

                if (!response.ok) {
                    throw new Error('Gagal mengambil data AIS');
                }

                return response.json();
            })

            .then(positions => {

                const container =
                    document.getElementById('ais-vessel-container');

                container.innerHTML = '';

                if (!Array.isArray(positions) || positions.length === 0) {

                    container.innerHTML = `
                        <div class="col-12">
                            <div class="ais-empty">
                                <i class="fas fa-ship"></i>
                                <br>
                                Tidak ada data kapal AIS.
                            </div>
                        </div>
                    `;

                    return;
                }

                positions.forEach(pos => {

                    const vesselName =
                        pos.vname || 'Unknown Vessel';
                        const vesselKey =
    pos.mmsi ||
    pos.imo ||
    pos.vname ||
    `${pos.latitude}_${pos.longitude}`;

                    const status =
                        String(pos.status || '')
                            .trim()
                            .toLowerCase();

                    const speed =
                        parseFloat(pos.speed) || 0;

                    let statusClass = 'unknown';
                    let statusText = pos.status || 'UNKNOWN';

                    /*
                     * MOVING = HIJAU
                     * IDLING = MERAH
                     */

                    if (status === 'moving') {

                        statusClass = 'moving';
                        statusText = 'MOVING';

                    } else if (status === 'idling') {

                        statusClass = 'idling';
                        statusText = 'IDLING';
                    }

                    const latitude =
                        pos.latitude !== undefined
                            ? pos.latitude
                            : '-';

                    const longitude =
                        pos.longitude !== undefined
                            ? pos.longitude
                            : '-';

                    const col = document.createElement('div');

col.className =
    'col-6 col-sm-4 col-md-3 mb-3';

col.style.cursor = 'pointer';

                    col.innerHTML = `
                        <div class="ais-vessel-card ${statusClass}">

                            <div class="ais-vessel-icon">
                                <i class="fas fa-ship"></i>
                            </div>

                            <div class="ais-vessel-name">
                                ${escapeAISHtml(vesselName)}
                            </div>

                            <div class="ais-vessel-status">
                                ${escapeAISHtml(statusText)}
                            </div>

                            <div class="ais-vessel-info">

                                <div>
                                    <i class="fas fa-tachometer-alt"></i>
                                    Speed:
                                    ${speed.toFixed(2)} knots
                                </div>

                               <div class="ais-vessel-location">
    <i class="fas fa-map-marker-alt"></i>
    <span class="vessel-location-text">
        Mencari lokasi...
    </span>
</div>

                            </div>

                        </div>
                    `;

                    container.appendChild(col);
                    col.addEventListener('click', function () {

    const marker = vesselMarkers[vesselKey];

    if (!marker) {
        console.warn(
            'Marker kapal tidak ditemukan:',
            vesselKey
        );
        return;
    }

    const latLng = marker.getLatLng();

    // Pindahkan map ke kapal
    map.flyTo(
        [latLng.lat, latLng.lng],
        12,
        {
            animate: true,
            duration: 1.5
        }
    );

    // Buka popup kapal
    marker.openPopup();
});
                    const locationElement =
    col.querySelector('.vessel-location-text');

getAISLocation(
    latitude,
    longitude,
    locationElement
);
                });
            })

            .catch(error => {

                console.error(
                    'AIS Vessel Error:',
                    error
                );

                document.getElementById(
                    'ais-vessel-container'
                ).innerHTML = `
                    <div class="col-12">
                        <div class="ais-empty text-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                            <br>
                            Gagal mengambil data AIS.
                        </div>
                    </div>
                `;
            });
    }


    /*
     * Mencegah nama kapal dari AIS
     * memasukkan HTML ke halaman.
     */
    function escapeAISHtml(value) {

        const div = document.createElement('div');

        div.textContent = value;

        return div.innerHTML;
    }


    /*
     * Load pertama
     */
    loadAISVessels();


    /*
     * Refresh AIS setiap 30 detik
     */
    setInterval(loadAISVessels, 30000);
</script>

</div>


@endsection

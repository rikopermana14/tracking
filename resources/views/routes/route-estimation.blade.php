@extends('layout.index')

@section('content')

<style>

    /* =====================================================
       GENERAL
    ===================================================== */

    .route-estimation-card {
        margin-bottom: 20px;
    }

    .section-title {
        font-weight: 600;
        margin-bottom: 12px;
    }


    /* =====================================================
       INPUT
    ===================================================== */

    .history-button {
        margin-top: 30px;
    }


    /* =====================================================
       RESULT INFO BOX
    ===================================================== */

    .info-box {
        min-height: 90px;
        margin-bottom: 15px;
    }

    .info-box-number {
        font-size: 17px;
    }


    /* =====================================================
       MAP
    ===================================================== */

    #estimation-map {

        width: 100%;

        height: 450px;

        border-radius: 6px;

        border: 1px solid #ddd;

    }


    /* =====================================================
       HISTORY TABLE
    ===================================================== */

    #estimation-history-table tbody tr {

        cursor: pointer;

    }

    #estimation-history-table tbody tr:hover {

        background-color: #eaf4ff;

    }


    /* =====================================================
       SELECTED HISTORY ROW
    ===================================================== */

    .history-selected-row {

        background-color: #d9edf7 !important;

    }


    /* =====================================================
       RESULT DETAIL
    ===================================================== */

    .result-detail {

        background: #f8f9fa;

        border: 1px solid #dee2e6;

        border-radius: 5px;

        padding: 15px;

        margin-top: 15px;

    }

    .result-detail strong {

        min-width: 150px;

        display: inline-block;

    }


    /* =====================================================
       MAP LEGEND
    ===================================================== */

    .map-legend {

        background: white;

        padding: 10px;

        line-height: 20px;

        box-shadow: 0 0 5px rgba(0,0,0,0.25);

        border-radius: 5px;

    }

    .legend-item {

        margin-bottom: 4px;

    }


    /* =====================================================
       HISTORY INFORMATION
    ===================================================== */

    .selected-history-info {

        background: #f8f9fa;

        border-left: 4px solid #17a2b8;

        padding: 10px 15px;

        margin-top: 10px;

        display: none;

    }


</style>


<!-- =========================================================
     LEAFLET
     ========================================================= -->

<link
    rel="stylesheet"
    href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
/>


<div class="container-fluid">

    <!-- =====================================================
         MAIN CARD
         ===================================================== -->

    <div class="card shadow-sm route-estimation-card">


        <!-- =================================================
             HEADER
             ================================================= -->

        <div class="card-header">

            <h5 class="mb-0">

                <i class="fas fa-route"></i>

                Route Estimation

            </h5>

        </div>


        <!-- =================================================
             BODY
             ================================================= -->

        <div class="card-body">


            <!-- =================================================
                 VESSEL
                 ================================================= -->

            <div class="row">

                <div class="col-md-6">

                    <div class="form-group">

                        <label for="estimation_ship">

                            Vessel

                        </label>


                        <select
                            id="estimation_ship"
                            class="form-control"
                        >

                            <option value="">

                                -- Select Vessel --

                            </option>


                            @foreach($ships as $ship)

                                <option
                                    value="{{ $ship->id }}"
                                >

                                    {{ $ship->vname }}

                                </option>

                            @endforeach

                        </select>

                    </div>

                </div>

            </div>


            <hr>


            <!-- =================================================
                 START POINT
                 ================================================= -->

            <div class="section-title">

                <i class="fas fa-map-marker-alt text-success"></i>

                Start Point

            </div>


            <div class="row">


                <!-- SELECT HISTORY -->

                <div class="col-md-4">

                    <div class="form-group">

                        <label>

                            Historical Position

                        </label>


                        <button
                            type="button"
                            id="btn-select-start-history"
                            class="btn btn-info btn-block"
                        >

                            <i class="fas fa-history"></i>

                            Select Start from History

                        </button>

                    </div>

                </div>


                <!-- START LATITUDE -->

                <div class="col-md-4">

                    <div class="form-group">

                        <label for="start_lat">

                            Start Latitude

                        </label>


                        <input
                            type="number"
                            step="any"
                            id="start_lat"
                            class="form-control"
                            placeholder="Latitude"
                        >

                    </div>

                </div>


                <!-- START LONGITUDE -->

                <div class="col-md-4">

                    <div class="form-group">

                        <label for="start_lon">

                            Start Longitude

                        </label>


                        <input
                            type="number"
                            step="any"
                            id="start_lon"
                            class="form-control"
                            placeholder="Longitude"
                        >

                    </div>

                </div>

            </div>


            <!-- START HISTORY INFORMATION -->

            <div
                id="start-history-info"
                class="selected-history-info"
            >

                <strong>
                    Selected Start History
                </strong>

                <br>

                Date Time:
                <span id="start-history-datetime">
                    -
                </span>

                <br>

                Speed:
                <span id="start-history-speed">
                    -
                </span>

                knots

            </div>


            <hr>


            <!-- =================================================
                 DESTINATION
                 ================================================= -->

            <div class="section-title">

                <i class="fas fa-flag-checkered text-danger"></i>

                Destination

            </div>


            <div class="row">


                <!-- SELECT HISTORY -->

                <div class="col-md-4">

                    <div class="form-group">

                        <label>

                            Historical Position

                        </label>


                        <button
                            type="button"
                            id="btn-select-destination-history"
                            class="btn btn-danger btn-block"
                        >

                            <i class="fas fa-history"></i>

                            Select Destination from History

                        </button>

                    </div>

                </div>


                <!-- DESTINATION LATITUDE -->

                <div class="col-md-4">

                    <div class="form-group">

                        <label for="dest_lat">

                            Destination Latitude

                        </label>


                        <input
                            type="number"
                            step="any"
                            id="dest_lat"
                            class="form-control"
                            placeholder="Latitude"
                        >

                    </div>

                </div>


                <!-- DESTINATION LONGITUDE -->

                <div class="col-md-4">

                    <div class="form-group">

                        <label for="dest_lon">

                            Destination Longitude

                        </label>


                        <input
                            type="number"
                            step="any"
                            id="dest_lon"
                            class="form-control"
                            placeholder="Longitude"
                        >

                    </div>

                </div>

            </div>


            <!-- DESTINATION HISTORY INFORMATION -->

            <div
                id="destination-history-info"
                class="selected-history-info"
            >

                <strong>
                    Selected Destination History
                </strong>

                <br>

                Date Time:
                <span id="destination-history-datetime">
                    -
                </span>

                <br>

                Speed:
                <span id="destination-history-speed">
                    -
                </span>

                knots

            </div>


            <!-- =================================================
                 HIDDEN HISTORY IDS
                 ================================================= -->

            <input
                type="hidden"
                id="start_history_id"
                value=""
            >


            <input
                type="hidden"
                id="destination_history_id"
                value=""
            >


            <hr>


            <!-- =================================================
                 CALCULATE BUTTON
                 ================================================= -->

            <div class="row">

                <div class="col-md-4">

                    <button
                        type="button"
                        id="btn-route-estimation"
                        class="btn btn-primary btn-block"
                    >

                        <i class="fas fa-calculator"></i>

                        Calculate Estimation

                    </button>

                </div>

            </div>


            <!-- =================================================
                 RESULT
                 ================================================= -->

            <div
                id="route-estimation-result"
                style="display:none;"
            >

                <hr>


                <div class="section-title">

                    <i class="fas fa-chart-line"></i>

                    Estimation Result

                </div>


                <!-- =================================================
                     INFO BOXES
                     ================================================= -->

                <div class="row">


                    <!-- ROUTE -->

                    <div class="col-md-3">

                        <div class="info-box">

                            <span
                                class="info-box-icon bg-primary"
                            >

                                <i class="fas fa-route"></i>

                            </span>


                            <div class="info-box-content">

                                <span class="info-box-text">

                                    Selected Route

                                </span>


                                <span
                                    class="info-box-number"
                                    id="est_route_name"
                                >

                                    -

                                </span>

                            </div>

                        </div>

                    </div>


                    <!-- DISTANCE -->

                    <div class="col-md-3">

                        <div class="info-box">

                            <span
                                class="info-box-icon bg-info"
                            >

                                <i class="fas fa-ruler-horizontal"></i>

                            </span>


                            <div class="info-box-content">

                                <span class="info-box-text">

                                    Route Distance

                                </span>


                                <span
                                    class="info-box-number"
                                    id="est_distance"
                                >

                                    -

                                </span>

                            </div>

                        </div>

                    </div>


                    <!-- SPEED -->

                    <div class="col-md-3">

                        <div class="info-box">

                            <span
                                class="info-box-icon bg-success"
                            >

                                <i class="fas fa-tachometer-alt"></i>

                            </span>


                            <div class="info-box-content">

                                <span class="info-box-text">

                                    Initial Speed

                                </span>


                                <span
                                    class="info-box-number"
                                    id="est_speed"
                                >

                                    -

                                </span>

                            </div>

                        </div>

                    </div>


                    <!-- TIME -->

                    <div class="col-md-3">

                        <div class="info-box">

                            <span
                                class="info-box-icon bg-warning"
                            >

                                <i class="fas fa-clock"></i>

                            </span>


                            <div class="info-box-content">

                                <span class="info-box-text">

                                    Estimated Time

                                </span>


                                <span
                                    class="info-box-number"
                                    id="est_duration"
                                >

                                    -

                                </span>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- =================================================
                     DETAIL
                     ================================================= -->

                <div class="result-detail">


                    <strong>
                        Vessel:
                    </strong>

                    <span id="est_ship_name">
                        -
                    </span>

                    <br>


                    <strong>
                        Route:
                    </strong>

                    <span id="est_route_name_detail">
                        -
                    </span>

                    <br>


                    <strong>
                        Start:
                    </strong>

                    <span id="est_start_position">
                        -
                    </span>

                    <br>


                    <strong>
                        Destination:
                    </strong>

                    <span id="est_destination_position">
                        -
                    </span>

                    <br>


                    <strong>
                        Initial Speed:
                    </strong>

                    <span id="est_initial_speed_detail">
                        -
                    </span>

                    <br>


                    <strong>
                        Historical Start:
                    </strong>

                    <span id="est_historical_start">
                        -
                    </span>

                    <br>


                    <strong>
                        ETA:
                    </strong>

                    <span id="est_eta">
                        -
                    </span>


                </div>


                <br>


                <!-- =================================================
                     MAP
                     ================================================= -->

                <div class="section-title">

                    <i class="fas fa-map"></i>

                    Route Map

                </div>


                <div id="estimation-map"></div>


            </div>

        </div>

    </div>

</div>


<!-- =========================================================
     HISTORY MODAL
     ========================================================= -->

<div
    class="modal fade"
    id="historyModal"
    tabindex="-1"
    role="dialog"
    aria-hidden="true"
>


    <div
        class="modal-dialog modal-xl"
        role="document"
    >


        <div class="modal-content">


            <!-- =================================================
                 MODAL HEADER
                 ================================================= -->

            <div class="modal-header">

                <h5 class="modal-title">

                    <i class="fas fa-history"></i>

                    Select Historical Position

                </h5>


                <button
                    type="button"
                    class="close"
                    data-dismiss="modal"
                >

                    <span>
                        &times;
                    </span>

                </button>

            </div>


            <!-- =================================================
                 MODAL BODY
                 ================================================= -->

            <div class="modal-body">


                <!-- SEARCH HISTORY -->

                <div class="row">

                    <div class="col-md-4">

                        <div class="form-group">

                            <label>
                                Search
                            </label>

                            <input
                                type="text"
                                id="history-search"
                                class="form-control"
                                placeholder="Search date, latitude, longitude..."
                            >

                        </div>

                    </div>

                </div>


                <div class="table-responsive">


                    <table
                        class="table table-bordered table-hover"
                        id="estimation-history-table"
                    >


                        <thead class="thead-light">

                            <tr>

                                <th>
                                    Date Time
                                </th>

                                <th>
                                    Latitude
                                </th>

                                <th>
                                    Longitude
                                </th>

                                <th>
                                    Speed
                                </th>

                                <th>
                                    Mileage
                                </th>

                                <th>
                                    Status
                                </th>

                                <th>
                                    Action
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <tr>

                                <td
                                    colspan="7"
                                    class="text-center"
                                >

                                    Select vessel first.

                                </td>

                            </tr>

                        </tbody>


                    </table>

                </div>

            </div>

        </div>

    </div>

</div>


<!-- =========================================================
     LEAFLET JS
     ========================================================= -->

<script
    src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js">
</script>


<script>

$(document).ready(function () {


    // =====================================================
    // VARIABLES
    // =====================================================

    let historySelectionType = null;

    let historyData = [];

    let estimationMap = null;

    let routeLayer = null;

    let startMarker = null;

    let destinationMarker = null;

    let selectedHistoryMarker = null;


    // =====================================================
    // INITIALIZE MAP
    // =====================================================

    function initializeMap() {

        if (estimationMap) {

            return;

        }


        estimationMap =
            L.map('estimation-map');


        L.tileLayer(
            'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
            {
                maxZoom: 19,

                attribution:
                    '&copy; OpenStreetMap contributors'
            }
        ).addTo(
            estimationMap
        );


        estimationMap.setView(
            [
                -2.5,
                118
            ],
            5
        );

    }


    // =====================================================
    // VESSEL CHANGED
    // =====================================================

    $('#estimation_ship').on(
        'change',
        function () {

            // Hapus history yang sebelumnya dipilih

            $('#start_history_id')
                .val('');

            $('#destination_history_id')
                .val('');


            $('#start-history-info')
                .hide();

            $('#destination-history-info')
                .hide();


            $('#start_lat')
                .val('');

            $('#start_lon')
                .val('');

            $('#dest_lat')
                .val('');

            $('#dest_lon')
                .val('');

        }
    );


    // =====================================================
    // MANUAL START INPUT
    // =====================================================

    $('#start_lat, #start_lon').on(
        'input',
        function () {

            // Kalau user mengetik manual,
            // jangan gunakan history ID lama.

            $('#start_history_id')
                .val('');

            $('#start-history-info')
                .hide();

        }
    );


    // =====================================================
    // MANUAL DESTINATION INPUT
    // =====================================================

    $('#dest_lat, #dest_lon').on(
        'input',
        function () {

            $('#destination_history_id')
                .val('');

            $('#destination-history-info')
                .hide();

        }
    );


    // =====================================================
    // SELECT START HISTORY
    // =====================================================

    $('#btn-select-start-history').on(
        'click',
        function () {

            if (
                !$('#estimation_ship').val()
            ) {

                alert(
                    'Please select vessel first.'
                );

                return;

            }


            historySelectionType =
                'start';


            loadEstimationHistory();

        }
    );


    // =====================================================
    // SELECT DESTINATION HISTORY
    // =====================================================

    $('#btn-select-destination-history').on(
        'click',
        function () {

            if (
                !$('#estimation_ship').val()
            ) {

                alert(
                    'Please select vessel first.'
                );

                return;

            }


            historySelectionType =
                'destination';


            loadEstimationHistory();

        }
    );


    // =====================================================
    // LOAD HISTORY
    // =====================================================

    function loadEstimationHistory()
    {

        const shipName =
            $('#estimation_ship option:selected')
                .text()
                .trim();


        $('#estimation-history-table tbody')
            .html(`

                <tr>

                    <td
                        colspan="7"
                        class="text-center"
                    >

                        <i
                            class="fas fa-spinner fa-spin"
                        ></i>

                        Loading history...

                    </td>

                </tr>

            `);


        $('#historyModal').modal('show');


        fetch(

            "{{ route('track-ship.route-estimation-history') }}" +

            "?ship_name=" +

            encodeURIComponent(shipName)

        )


        .then(
            response => {

                if (!response.ok) {

                    throw new Error(
                        'Failed to load history.'
                    );

                }

                return response.json();

            }
        )


        .then(
            data => {

                if (
                    !data.success
                ) {

                    throw new Error(
                        data.message ||
                        'Failed to load history.'
                    );

                }


                historyData =
                    data.data || [];


                renderHistoryTable(
                    historyData
                );

            }
        )


        .catch(
            error => {

                console.error(
                    error
                );


                $('#estimation-history-table tbody')
                    .html(`

                        <tr>

                            <td
                                colspan="7"
                                class="text-center text-danger"
                            >

                                ${error.message}

                            </td>

                        </tr>

                    `);

            }
        );

    }


    // =====================================================
    // RENDER HISTORY TABLE
    // =====================================================

    function renderHistoryTable(
        data
    ) {

        const tbody =
            $('#estimation-history-table tbody');


        tbody.empty();


        if (
            !data ||
            data.length === 0
        ) {

            tbody.html(`

                <tr>

                    <td
                        colspan="7"
                        class="text-center"
                    >

                        No historical data found.

                    </td>

                </tr>

            `);

            return;

        }


        data.forEach(
            row => {

                const latitude =
                    parseFloat(
                        row.latitude
                    );

                const longitude =
                    parseFloat(
                        row.longitude
                    );


                tbody.append(`

                    <tr
                        data-history-id="${row.id}"
                    >

                        <td>
                            ${row.datetime_utc ?? '-'}
                        </td>

                        <td>
                            ${row.latitude ?? '-'}
                        </td>

                        <td>
                            ${row.longitude ?? '-'}
                        </td>

                        <td>
                            ${row.speed ?? '-'}
                        </td>

                        <td>
                            ${row.mileage ?? '-'}
                        </td>

                        <td>
                            ${row.status ?? '-'}
                        </td>

                        <td>

                            <button
                                type="button"
                                class="btn btn-sm btn-primary btn-select-history"
                                data-id="${row.id}"
                            >

                                <i
                                    class="fas fa-check"
                                ></i>

                                Select

                            </button>

                        </td>

                    </tr>

                `);

            }
        );

    }


    // =====================================================
    // SEARCH HISTORY
    // =====================================================

    $('#history-search').on(
        'input',
        function () {

            const keyword =
                $(this)
                    .val()
                    .toLowerCase()
                    .trim();


            if (!keyword) {

                renderHistoryTable(
                    historyData
                );

                return;

            }


            const filtered =
                historyData.filter(
                    row => {

                        const text = [

                            row.datetime_utc,

                            row.latitude,

                            row.longitude,

                            row.speed,

                            row.mileage,

                            row.status

                        ]
                        .join(' ')
                        .toLowerCase();


                        return text.includes(
                            keyword
                        );

                    }
                );


            renderHistoryTable(
                filtered
            );

        }
    );


    // =====================================================
    // SELECT HISTORY BUTTON
    // =====================================================

    $(document).on(
        'click',
        '.btn-select-history',
        function () {

            const id =
                $(this)
                    .data('id');


            const row =
                historyData.find(
                    item =>
                        String(item.id) ===
                        String(id)
                );


            if (!row) {

                alert(
                    'Historical record not found.'
                );

                return;

            }


            selectHistoryPoint(
                row
            );

        }
    );


    // =====================================================
    // SELECT HISTORY POINT
    // =====================================================

    function selectHistoryPoint(
        row
    ) {

        const latitude =
            parseFloat(
                row.latitude
            );

        const longitude =
            parseFloat(
                row.longitude
            );


        if (
            isNaN(latitude) ||
            isNaN(longitude)
        ) {

            alert(
                'Invalid coordinates.'
            );

            return;

        }


        // =================================================
        // START
        // =================================================

        if (
            historySelectionType ===
            'start'
        ) {

            $('#start_history_id')
                .val(row.id);


            $('#start_lat')
                .val(latitude);


            $('#start_lon')
                .val(longitude);


            $('#start-history-datetime')
                .text(
                    row.datetime_utc || '-'
                );


            $('#start-history-speed')
                .text(
                    row.speed ?? '-'
                );


            $('#start-history-info')
                .show();


            // Tampilkan titik di map

            initializeMap();


            showSelectedHistoryPoint(
                latitude,
                longitude,
                row,
                'start'
            );

        }


        // =================================================
        // DESTINATION
        // =================================================

        if (
            historySelectionType ===
            'destination'
        ) {

            $('#destination_history_id')
                .val(row.id);


            $('#dest_lat')
                .val(latitude);


            $('#dest_lon')
                .val(longitude);


            $('#destination-history-datetime')
                .text(
                    row.datetime_utc || '-'
                );


            $('#destination-history-speed')
                .text(
                    row.speed ?? '-'
                );


            $('#destination-history-info')
                .show();


            initializeMap();


            showSelectedHistoryPoint(
                latitude,
                longitude,
                row,
                'destination'
            );

        }


        // Tutup modal

        $('#historyModal')
            .modal('hide');

    }


    // =====================================================
    // SHOW SELECTED HISTORY POINT
    // =====================================================

    function showSelectedHistoryPoint(
        latitude,
        longitude,
        row,
        type
    ) {

        initializeMap();


        const markerColor =
            type === 'start'
                ? '#28a745'
                : '#dc3545';


        if (
            type === 'start'
        ) {

            if (startMarker) {

                estimationMap
                    .removeLayer(
                        startMarker
                    );

            }


            startMarker =
                L.circleMarker(
                    [
                        latitude,
                        longitude
                    ],
                    {

                        radius: 9,

                        color: '#ffffff',

                        weight: 3,

                        fillColor:
                            markerColor,

                        fillOpacity: 1

                    }
                )
                .addTo(
                    estimationMap
                );


            startMarker.bindPopup(`

                <b>START</b>

                <hr>

                Vessel:
                ${row.vname ?? '-'}

                <br>

                Date Time:
                ${row.datetime_utc ?? '-'}

                <br>

                Latitude:
                ${latitude.toFixed(6)}

                <br>

                Longitude:
                ${longitude.toFixed(6)}

                <br>

                Speed:
                ${row.speed ?? '-'} knots

            `);


            startMarker.openPopup();

        }


        if (
            type === 'destination'
        ) {

            if (destinationMarker) {

                estimationMap
                    .removeLayer(
                        destinationMarker
                    );

            }


            destinationMarker =
                L.circleMarker(
                    [
                        latitude,
                        longitude
                    ],
                    {

                        radius: 9,

                        color: '#ffffff',

                        weight: 3,

                        fillColor:
                            markerColor,

                        fillOpacity: 1

                    }
                )
                .addTo(
                    estimationMap
                );


            destinationMarker.bindPopup(`

                <b>DESTINATION</b>

                <hr>

                Vessel:
                ${row.vname ?? '-'}

                <br>

                Date Time:
                ${row.datetime_utc ?? '-'}

                <br>

                Latitude:
                ${latitude.toFixed(6)}

                <br>

                Longitude:
                ${longitude.toFixed(6)}

                <br>

                Speed:
                ${row.speed ?? '-'} knots

            `);


            destinationMarker.openPopup();

        }


        estimationMap.setView(
            [
                latitude,
                longitude
            ],
            12
        );

    }


    // =====================================================
    // CALCULATE ESTIMATION
    // =====================================================

    $('#btn-route-estimation').on(
        'click',
        function () {

            const button =
                $(this);


            const shipId =
                $('#estimation_ship')
                    .val();


            const shipLat =
                $('#start_lat')
                    .val();


            const shipLon =
                $('#start_lon')
                    .val();


            const destLat =
                $('#dest_lat')
                    .val();


            const destLon =
                $('#dest_lon')
                    .val();


            const startHistoryId =
                $('#start_history_id')
                    .val();


            const destinationHistoryId =
                $('#destination_history_id')
                    .val();


            // =================================================
            // VALIDATION
            // =================================================

            if (!shipId) {

                alert(
                    'Please select vessel.'
                );

                return;

            }


            if (
                shipLat === '' ||
                shipLon === ''
            ) {

                alert(
                    'Please enter or select start position.'
                );

                return;

            }


            if (
                destLat === '' ||
                destLon === ''
            ) {

                alert(
                    'Please enter or select destination.'
                );

                return;

            }


            // =================================================
            // LOADING
            // =================================================

            button
                .prop(
                    'disabled',
                    true
                )
                .html(`

                    <i
                        class="fas fa-spinner fa-spin"
                    ></i>

                    Calculating...

                `);


            // =================================================
            // REQUEST
            // =================================================

            fetch(
                "{{ route('track-ship.calculate-route-estimation') }}",
                {

                    method: 'POST',

                    headers: {

                        'Content-Type':
                            'application/json',

                        'X-CSRF-TOKEN':
                            '{{ csrf_token() }}',

                        'Accept':
                            'application/json'

                    },

                    body:
                        JSON.stringify({

                            ship_id:
                                shipId,

                            ship_lat:
                                parseFloat(
                                    shipLat
                                ),

                            ship_lon:
                                parseFloat(
                                    shipLon
                                ),

                            dest_lat:
                                parseFloat(
                                    destLat
                                ),

                            dest_lon:
                                parseFloat(
                                    destLon
                                ),

                            start_history_id:
                                startHistoryId ||
                                null,

                            destination_history_id:
                                destinationHistoryId ||
                                null

                        })

                }
            )


            // =================================================
            // RESPONSE
            // =================================================

            .then(
                response => {

                    return response
                        .json()
                        .then(
                            data => {

                                if (
                                    !response.ok
                                ) {

                                    throw new Error(
                                        data.message ||
                                        'Server error.'
                                    );

                                }


                                return data;

                            }
                        );

                }
            )


            .then(
                data => {

                    if (
                        !data.success
                    ) {

                        throw new Error(
                            data.message ||
                            'Route estimation failed.'
                        );

                    }


                    // =========================================
                    // RESULT
                    // =========================================

                    $('#est_ship_name')
                        .text(
                            data.ship.name
                        );


                    $('#est_route_name')
                        .text(
                            data.route.name
                        );


                    $('#est_route_name_detail')
                        .text(
                            data.route.name
                        );


                    $('#est_distance')
                        .text(

                            Number(
                                data.distance_nm
                            ).toFixed(2)

                            + ' NM'

                        );


                    $('#est_speed')
                        .text(

                            Number(
                                data.speed_knots
                            ).toFixed(2)

                            + ' kn'

                        );


                    $('#est_initial_speed_detail')
                        .text(

                            Number(
                                data.speed_knots
                            ).toFixed(2)

                            + ' knots'

                        );


                    $('#est_duration')
                        .text(
                            data.duration
                        );


                    $('#est_start_position')
                        .text(

                            Number(
                                data.start.latitude
                            ).toFixed(6)

                            + ', '

                            +

                            Number(
                                data.start.longitude
                            ).toFixed(6)

                        );


                    $('#est_destination_position')
                        .text(

                            Number(
                                data.destination.latitude
                            ).toFixed(6)

                            + ', '

                            +

                            Number(
                                data.destination.longitude
                            ).toFixed(6)

                        );


                    // =========================================
                    // HISTORICAL START
                    // =========================================

                    if (
                        data.historical_start
                    ) {

                        $('#est_historical_start')
                            .text(

                                data.historical_start
                                    .datetime_utc

                                +

                                ' | Speed: '

                                +

                                Number(
                                    data.historical_start
                                        .speed_knots
                                ).toFixed(2)

                                +

                                ' kn'

                            );

                    } else {

                        $('#est_historical_start')
                            .text('-');

                    }


                    // =========================================
                    // ETA
                    // =========================================

                    $('#est_eta')
                        .text(
                            data.eta_formatted
                        );


                    // =========================================
                    // SHOW RESULT
                    // =========================================

                    $('#route-estimation-result')
                        .slideDown();


                    // =========================================
                    // MAP
                    // =========================================

                    initializeMap();


                    setTimeout(
                        function () {

                            estimationMap
                                .invalidateSize();


                            drawEstimationRoute(
                                data
                            );

                        },
                        300
                    );

                }
            )


            // =================================================
            // ERROR
            // =================================================

            .catch(
                error => {

                    console.error(
                        'Route estimation error:',
                        error
                    );


                    alert(
                        error.message ||
                        'Failed to calculate route estimation.'
                    );

                }
            )


            // =================================================
            // FINISH
            // =================================================

            .finally(
                function () {

                    button
                        .prop(
                            'disabled',
                            false
                        )
                        .html(`

                            <i
                                class="fas fa-calculator"
                            ></i>

                            Calculate Estimation

                        `);

                }
            );

        }
    );


    // =====================================================
    // DRAW ROUTE
    // =====================================================

    function drawEstimationRoute(
        data
    ) {

        initializeMap();


        // =================================================
        // HAPUS ROUTE LAMA
        // =================================================

        if (routeLayer) {

            estimationMap
                .removeLayer(
                    routeLayer
                );

            routeLayer = null;

        }


        // =================================================
        // HAPUS MARKER LAMA
        // =================================================

        if (startMarker) {

            estimationMap
                .removeLayer(
                    startMarker
                );

            startMarker = null;

        }


        if (destinationMarker) {

            estimationMap
                .removeLayer(
                    destinationMarker
                );

            destinationMarker = null;

        }


        // =================================================
        // ROUTE PATH
        // =================================================

        if (
            !data.route_path ||
            data.route_path.length === 0
        ) {

            return;

        }


        const routeCoordinates =
            data.route_path.map(
                point => [

                    parseFloat(
                        point.latitude
                    ),

                    parseFloat(
                        point.longitude
                    )

                ]
            );


        // =================================================
        // ROUTE LINE
        // =================================================

        routeLayer =
            L.polyline(
                routeCoordinates,
                {

                    weight: 5,

                    opacity: 0.85

                }
            )
            .addTo(
                estimationMap
            );


        // =================================================
        // START MARKER
        // =================================================

        const startLat =
            parseFloat(
                data.start.latitude
            );

        const startLon =
            parseFloat(
                data.start.longitude
            );


        startMarker =
            L.circleMarker(
                [
                    startLat,
                    startLon
                ],
                {

                    radius: 9,

                    color: '#ffffff',

                    weight: 3,

                    fillColor: '#28a745',

                    fillOpacity: 1

                }
            )
            .addTo(
                estimationMap
            );


        startMarker.bindPopup(`

            <b>START</b>

            <hr>

            Vessel:
            ${data.ship.name}

            <br>

            Latitude:
            ${startLat.toFixed(6)}

            <br>

            Longitude:
            ${startLon.toFixed(6)}

            <br>

            Initial Speed:
            ${Number(
                data.speed_knots
            ).toFixed(2)} knots

        `);


        // =================================================
        // DESTINATION MARKER
        // =================================================

        const destLat =
            parseFloat(
                data.destination.latitude
            );

        const destLon =
            parseFloat(
                data.destination.longitude
            );


        destinationMarker =
            L.circleMarker(
                [
                    destLat,
                    destLon
                ],
                {

                    radius: 9,

                    color: '#ffffff',

                    weight: 3,

                    fillColor: '#dc3545',

                    fillOpacity: 1

                }
            )
            .addTo(
                estimationMap
            );


        destinationMarker.bindPopup(`

            <b>DESTINATION</b>

            <hr>

            Latitude:
            ${destLat.toFixed(6)}

            <br>

            Longitude:
            ${destLon.toFixed(6)}

        `);


        // =================================================
        // ROUTE WAYPOINT MARKERS
        // =================================================

        data.route_path.forEach(
            point => {

                if (
                    point.type !==
                    'waypoint'
                ) {

                    return;

                }


                L.circleMarker(
                    [

                        parseFloat(
                            point.latitude
                        ),

                        parseFloat(
                            point.longitude
                        )

                    ],
                    {

                        radius: 3,

                        stroke: false,

                        fillOpacity: 0.7

                    }

                )
                .bindPopup(`

                    <b>Route Waypoint</b>

                    <br>

                    Sequence:
                    ${point.sequence ?? '-'}

                    <br>

                    Latitude:
                    ${Number(
                        point.latitude
                    ).toFixed(6)}

                    <br>

                    Longitude:
                    ${Number(
                        point.longitude
                    ).toFixed(6)}

                `)

                .addTo(
                    estimationMap
                );

            }
        );


        // =================================================
        // FIT MAP
        // =================================================

        estimationMap.fitBounds(
            routeLayer.getBounds(),
            {

                padding: [
                    30,
                    30
                ]

            }
        );

    }


});
</script>


@endsection
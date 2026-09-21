@extends('layout.index')

@section('content')

<div class="container-fluid">

    <!-- ===================================================== -->
    <!-- HEADER -->
    <!-- ===================================================== -->

    <div class="card shadow-sm">

        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-route"></i>
                Route Estimation
            </h5>
        </div>

        <div class="card-body">

            <!-- ================================================= -->
            <!-- INPUT -->
            <!-- ================================================= -->

            <div class="row">

                <!-- VESSEL -->
                <div class="col-md-4">

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


                <!-- START LATITUDE -->
                <div class="col-md-2">

                    <div class="form-group">

                        <label>
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
                <div class="col-md-2">

                    <div class="form-group">

                        <label>
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


                <!-- DEST LATITUDE -->
                <div class="col-md-2">

                    <div class="form-group">

                        <label>
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


                <!-- DEST LONGITUDE -->
                <div class="col-md-2">

                    <div class="form-group">

                        <label>
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


            <!-- ================================================= -->
            <!-- BUTTON -->
            <!-- ================================================= -->

            <div class="row">

                <div class="col-md-3">

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


            <hr>


            <!-- ================================================= -->
            <!-- RESULT -->
            <!-- ================================================= -->

            <div
                id="route-estimation-result"
                style="display:none;"
            >

                <div class="row">

                    <!-- ROUTE -->
                    <div class="col-md-3">

                        <div class="info-box">

                            <span class="info-box-icon bg-primary">

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

                            <span class="info-box-icon bg-info">

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

                            <span class="info-box-icon bg-success">

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


                    <!-- DURATION -->
                    <div class="col-md-3">

                        <div class="info-box">

                            <span class="info-box-icon bg-warning">

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


                <!-- ================================================= -->
                <!-- DETAIL -->
                <!-- ================================================= -->

                <div class="alert alert-light">

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
                        ETA:
                    </strong>

                    <span id="est_eta">
                        -
                    </span>

                </div>

            </div>

        </div>

    </div>

</div>


<!-- ========================================================= -->
<!-- JAVASCRIPT -->
<!-- ========================================================= -->

<script>

$(document).ready(function () {


    $('#btn-route-estimation').on('click', function () {


        // =====================================================
        // AMBIL INPUT
        // =====================================================

        const shipId =
            $('#estimation_ship').val();

        const shipLat =
            $('#start_lat').val();

        const shipLon =
            $('#start_lon').val();

        const destLat =
            $('#dest_lat').val();

        const destLon =
            $('#dest_lon').val();


        // =====================================================
        // VALIDASI
        // =====================================================

        if (!shipId) {

            alert('Please select vessel.');

            return;
        }


        if (
            shipLat === '' ||
            shipLon === ''
        ) {

            alert('Please enter start position.');

            return;
        }


        if (
            destLat === '' ||
            destLon === ''
        ) {

            alert('Please enter destination.');

            return;
        }


        // =====================================================
        // LOADING
        // =====================================================

        const button = $(this);


        button
            .prop('disabled', true)
            .html(
                '<i class="fas fa-spinner fa-spin"></i> Calculating...'
            );


        // =====================================================
        // REQUEST
        // =====================================================

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

                body: JSON.stringify({

                    ship_id: shipId,

                    ship_lat:
                        parseFloat(shipLat),

                    ship_lon:
                        parseFloat(shipLon),

                    dest_lat:
                        parseFloat(destLat),

                    dest_lon:
                        parseFloat(destLon)

                })

            }
        )


        // =====================================================
        // RESPONSE
        // =====================================================

        .then(response => {

            if (!response.ok) {

                return response.json()
                    .then(error => {

                        throw new Error(
                            error.message ||
                            'Server error'
                        );

                    });

            }

            return response.json();

        })


        .then(data => {


            if (!data.success) {

                alert(
                    data.message ||
                    'Route estimation failed.'
                );

                return;
            }


            // =================================================
            // VESSEL
            // =================================================

            $('#est_ship_name').text(
                data.ship.name
            );


            // =================================================
            // ROUTE
            // =================================================

            $('#est_route_name').text(
                data.route.name
            );

            $('#est_route_name_detail').text(
                data.route.name
            );


            // =================================================
            // DISTANCE
            // =================================================

            $('#est_distance').text(

                Number(
                    data.distance_nm
                ).toFixed(2)

                + ' NM'

            );


            // =================================================
            // SPEED
            // =================================================

            $('#est_speed').text(

                Number(
                    data.speed_knots
                ).toFixed(2)

                + ' kn'

            );


            // =================================================
            // DURATION
            // =================================================

            $('#est_duration').text(
                data.duration
            );


            // =================================================
            // START
            // =================================================

            $('#est_start_position').text(

                Number(
                    data.start.latitude
                ).toFixed(5)

                + ', '

                +

                Number(
                    data.start.longitude
                ).toFixed(5)

            );


            // =================================================
            // DESTINATION
            // =================================================

            $('#est_destination_position').text(

                Number(
                    data.destination.latitude
                ).toFixed(5)

                + ', '

                +

                Number(
                    data.destination.longitude
                ).toFixed(5)

            );


            // =================================================
            // ETA
            // =================================================

            $('#est_eta').text(
                data.eta_formatted
            );


            // =================================================
            // SHOW RESULT
            // =================================================

            $('#route-estimation-result')
                .slideDown();

        })


        // =====================================================
        // ERROR
        // =====================================================

        .catch(error => {

            console.error(
                'Route estimation error:',
                error
            );

            alert(
                error.message ||
                'Failed to calculate route estimation.'
            );

        })


        // =====================================================
        // FINISH
        // =====================================================

        .finally(() => {

            button
                .prop('disabled', false)
                .html(
                    '<i class="fas fa-calculator"></i> ' +
                    'Calculate Estimation'
                );

        });

    });

});

</script>

@endsection
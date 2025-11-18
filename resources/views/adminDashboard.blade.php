@extends('layout', ['pageId' => '0' ,'grupId' => '0' ])

@section('content')
    <style>
        .gradient-icon {
            background: linear-gradient(to right, #0099ff, #f1008d);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .shop-count {
            display: inline-block;
            width: 30px; /* Set width and height for a circular shape */
            height: 30px;
            border-radius: 50%;
            background-color: #ffae00;
            color: #000;
            text-align: center;
            line-height: 30px; /* Center the text vertically */
            box-shadow: 0px 0px 3px rgba(0, 0, 0, 0.5); /* Add shadow effect */
            font-size: 15px;
            font-weight: bold;
            font-family: 'Lato', sans-serif;
            letter-spacing: 1px;
        }


        .vertical-center {
            /* Your default styles here (without flex and align-items) */
        }

        /* Media query for screens larger than 768px (typical tablets and desktops) */
        @media (min-width: 768px) {
            .vertical-center {
                display: flex;
                align-items: center; /* Vertically align items */
            }
        }

        /* emoji gray css */
        .black-and-white-emoji {
            filter: grayscale(100%);
        }
    </style>

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-12">
            <h2><b>Dashboard</b></h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="/admindashboard">Home</a>
                </li>
                <li class="breadcrumb-item active">
                    <strong>Dashboard</strong>
                </li>
            </ol>
        </div>
    </div>
    <br>

    <div class="row">
        {{-- <div class="col-sm-12">
            <h2 class="font-bold">Dashboard</h2><br>
        </div> --}}
        @php
            $userRole = session('user_type');
        @endphp

        @if ($userRole == '1')
            <div class="col-sm-12">
                <input type="hidden" id="csrf_token" value="{{ csrf_token() }}">

                <div class="row">
                    <div class="col-lg-4">
                        <div class="ibox ">
                            <div class="ibox-title">
                                <h3 style="font-family: 'Lato', sans-serif; color: #000; text-transform: uppercase;">Sales Representative</h3>
                            </div>
                            <div class="ibox-content">
                                @if(!empty($salesRep->sales_rep_name))
                                    <h2 class="no-margins"><i class="fa fa-user fa-2x gradient-icon"></i>&nbsp;&nbsp;
                                    {{ $salesRep->sales_rep_name }}</h2>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="ibox ">
                            <div class="ibox-title">
                                <h3 style="font-family: 'Lato', sans-serif; color: #000; text-transform: uppercase;">Vehicle</h3>
                            </div>
                            <div class="ibox-content">
                                @if(!empty($vehicle->reg_number))
                                    <h2 class="no-margins"><i class="fa fa-bus fa-2x gradient-icon"></i>&nbsp;&nbsp;
                                    {{ $vehicle->reg_number }}</h2>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="ibox ">
                            <div class="ibox-title">
                                <h3 style="font-family: 'Lato', sans-serif; color: #000; text-transform: uppercase;">Driver</h3>
                            </div>
                            <div class="ibox-content">
                                @if(!empty($driver->driver_name))
                                    <h2 class="no-margins"><i class="fa fa-id-card fa-2x gradient-icon"></i>&nbsp;&nbsp;
                                    {{ $driver->driver_name }} </h2>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-12">
                <div class="ibox" style="font-family: 'Lato', sans-serif;">
                    <div class="ibox-title">
                        @if(!empty($route->route_name))
                        <h2 class="font-bold vertical-center">
                            <span class="shop-count">{{ $shops->count() }}</span>&nbsp; Shops in &nbsp;
                            <span style="color: #000">{{ $route->route_name }}</span>&nbsp; Route
                        </h2>
                        @endif
                    </div>

                    <div class="ibox-content">
                        @if(!empty($shops))
                            @foreach ($shops as $shop)
                                @if (in_array($shop->id, $invoiceCustomers))
                                    <p>
                                        🏪
                                        <a style="font-family: 'Roboto', sans-serif;" data-target="#activity" data-toggle="modal" onclick="loadDeliveryModal({{ $salesRep->id }},{{ $shop->id }},{{ $deliveryId}})">
                                            <span class="label label-info" style="font-size: 14px">{{ $shop->customer_name }}</span>
                                        </a>
                                    </p>
                                @else
                                    <p style="cursor: default;">
                                        <span class="black-and-white-emoji">🏪</span>
                                        @if ($shop->location_link != null)
                                            <span class="label" style="font-size: 14px; color: #5c5c5c, 149, 125); font-family: 'Roboto', sans-serif;">{{ $shop->customer_name }} &nbsp;
                                                <a href="{{ $shop->location_link }}" class="btn btn-dark btn-xs" style="font-size: 9px; margin-top: -3px; margin-right: -4px; height: 19px; color: #08f100;" target="_blank">Location &nbsp;<i class="fa fa-external-link-square"></i></a>
                                            </span>
                                        @else
                                            <span class="label" style="font-size: 14px; color: #5c5c5c; font-family: 'Roboto', sans-serif;">{{ $shop->customer_name }}</span>
                                        @endif
                                    </p>
                                @endif
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>

            {{-- start modal --}}
            <div class="modal fade" id="activity" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document" id="deliveryModalContent">

                </div>
            </div>
        @endif
    </div>
@endsection


<script>
    function loadDeliveryModal(repId, cusId, deliveryId) {
        var csrf_token = $("#csrf_token").val();
        jQuery.ajax({
            url: "{{ url('/loadDeliveryModalDashboard') }}",
            type: "POST",
            data: {
                "_token": csrf_token,
                "repId": repId,
                "cusId":cusId,
                "deliveryId":deliveryId
            },
            beforeSend: function() {
                showLder();
            },
            complete: function() {
            },
            error: function(data) {
            },
            success: function(data) {
                $('#deliveryModalContent').html(data);
                hideLder();
            }
        });
    }
</script>
@section('footer')
@endsection

<!DOCTYPE html>
<html>

@php
    use App\STATIC_DATA_MODEL;
    $company_name = STATIC_DATA_MODEL::$company_name;
    $company_logo = STATIC_DATA_MODEL::$company_logo;
    $company_logo_without_bg = STATIC_DATA_MODEL::$company_logo_without_bg;
@endphp

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $company_name }}</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="css/plugins/dataTables/datatables.min.css" rel="stylesheet">
    <link href="css/plugins/select2/select2.min.css" rel="stylesheet">
    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/sweetalert.css" rel="stylesheet">
    <link href="css/plugins/datapicker/datepicker3.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ $company_logo }}" />

    <!--Google fonts - Lato-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato&display=swap" rel="stylesheet">
    <!--Google fonts - Source Code Pro-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Source+Code+Pro&family=Ubuntu+Mono&display=swap" rel="stylesheet">
    <!--Google fonts - Ubunto Mono-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu+Mono&display=swap" rel="stylesheet">
    <!--Google fonts - Comfortaa-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Comfortaa&display=swap" rel="stylesheet">
    <!--Google fonts - Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300&display=swap" rel="stylesheet">
    <!--Google fonts - Orbitron -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@600&display=swap" rel="stylesheet">
    <!--Google fonts - Frank Ruhl Libre -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Frank+Ruhl+Libre&display=swap" rel="stylesheet">
    <style>
        #bell_icon:hover:before {
          content: "\f0f3";
          font-family: FontAwesome;
          font-size: 1.1em;
          transition: all 0.2s ease-in-out;
        }

        #bell_icon:before {
          content: "\f0a2";
          font-family: FontAwesome;
          font-size: 1.1em;
          color: #4a4846;
        }

        .dropdown-menu .dropdown-item:hover {
            background-color: #ffdf9e;
            /* color: #fff; */
        }

        .roundedCircle {
            display: inline-block;
            width: 16px;
            height: 16px;
            text-align: center;
            border-radius: 50%;
        }

        /* ----LOADING GIF CSS---- */
        #loader {
            display: none; /* Hide initially */
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8); /* Dark overlay with slight opacity */
            z-index: 9999; /* Ensure it's on top of everything */
            justify-content: center;
            align-items: center;
        }
        #loader img {
            width: 300px; /* Adjust loader size */
            height: 300px;
            animation: spin 2s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
      </style>
</head>

<body>
    <input type="hidden" id="csrf_token" value="{{ csrf_token() }}">
    <div id="wrapper">

        <div id="loader">
            <img src="img/loader3.gif" alt="Loading...">
        </div>

        <nav class="navbar-default navbar-static-side" role="navigation" style="background-color: #4d3521">
            <div class="sidebar-collapse">
                <ul class="nav metismenu" id="side-menu">
                    <li class="nav-header" style="background: linear-gradient(to left, #ffffff, #ffffff);">
                        <div class="dropdown profile-element">
                            <img src="{{ $company_logo_without_bg }}" style="width: 100%; border-radius: 100%;">
                        </div>
                        <div class="logo-element" style="background: linear-gradient(to left, #ffffff, #ffffff);">
                            <img src="{{ $company_logo_without_bg }}" style="width: 65px; border-radius: 3%;">
                            {{-- <h3 style="color: #293846; font-size: 22px; font-family: 'Lato', sans-serif; letter-spacing: 3px;">RF</h3> --}}
                        </div>

        {{--        <div class="dropdown profile-element">
                            <center>
                                {{-- <img alt="image" class="rounded-circle" src="" /> --}}
                        {{-- <a data-toggle="dropdown" class="dropdown-toggle" href="#"> --}}
                        {{-- <span class="block m-t-xs font-bold">David Williams</span> --}}
                        {{-- <span class="text-muted text-xs block">Art Director <b class="caret"></b></span> --}}
                        {{-- </a> --}}
                        {{-- </center> --}}
                        {{-- </div> --}}
        <!--        <div class="logo-element">
                        IN+
                    </div>-->
                    </li>
                    <li style="color: #ff9900; font-family: 'Lato', sans-serif; letter-spacing: 1px;">
                        <a href="/admindashboard"><i class="fa fa-th-large"></i> <span
                                class="nav-label">Dashboard</span></a>
                    </li>
                    @php
                        $privilageId = \DB::table('um_user')
                            ->select('um_user.*')
                            ->where('id', session('logged_user_id'))
                            ->first();
                        $privilagesGroups = \DB::table('um_user_has_interface_components')
                            ->select('pm_interface_topic.topic_name', 'pm_interface_topic.id', 'pm_interface_topic.menu_icon')
                            ->distinct('um_user_has_interface_components.id')
                            ->join('pm_interface_components', 'um_user_has_interface_components.pm_interface_components_id', '=', 'pm_interface_components.id')
                            ->join('pm_interfaces', 'pm_interface_components.pm_interfaces_id', '=', 'pm_interfaces.id')
                            ->join('pm_interface_topic', 'pm_interfaces.pm_interface_topic_id', '=', 'pm_interface_topic.id')
                            ->where('um_user_has_interface_components.um_user_id', session('logged_user_id'))
                            ->get();
                    @endphp
                    {{-- --------------------- TOPICS --------------------- --}}
                    @foreach ($privilagesGroups as $groups)
                        @if ($groups->id == ucfirst($grupId))
                            <li class="nav_ancor active" style="color: #ffae00; font-family: 'Lato', sans-serif; letter-spacing: 0.5px;">
                                <a><i class="{{ $groups->menu_icon }}"></i> <span class="nav-label">{{ $groups->topic_name }}</span><span class="fa arrow"></span></a>
                        @else
                            <li class="nav_ancor" style="color: #ffae00; font-family: 'Lato', sans-serif; letter-spacing: 0.5px;">
                                <a><i class="{{ $groups->menu_icon }}"></i> <span class="nav-label">{{ $groups->topic_name }}</span><span class="fa arrow"></span></a>
                        @endif

                        @php
                            $privilagesInterfaces = \DB::table('um_user_has_interface_components')
                                ->select(
                                    'pm_interfaces.path',
                                    'pm_interfaces.interface_name',
                                    'pm_interface_topic.topic_name',
                                    'pm_interface_topic.id',
                                    'pm_interfaces.id
                        AS interfaceId',
                                )
                                ->distinct('um_user_has_interface_components.id')
                                ->join('pm_interface_components', 'um_user_has_interface_components.pm_interface_components_id', '=', 'pm_interface_components.id')
                                ->join('pm_interfaces', 'pm_interface_components.pm_interfaces_id', '=', 'pm_interfaces.id')
                                ->join('pm_interface_topic', 'pm_interfaces.pm_interface_topic_id', '=', 'pm_interface_topic.id')
                                ->where('um_user_has_interface_components.um_user_id', session('logged_user_id'))
                                ->where('pm_interfaces.pm_interface_topic_id', $groups->id)
                                ->get();
                        @endphp
                        <ul class="nav nav-second-level collapse">
                            {{-- --------------------- INTERFACES --------------------- --}}
                            @foreach ($privilagesInterfaces as $Interfaces)
                                @if (ucfirst($pageId) == $Interfaces->interfaceId)
                                    <li style="background-color: #ffae00;"><a href="/{{ $Interfaces->path }}" class="active">{{ $Interfaces->interface_name }}</a></li>
                                @else
                                    <li style="background-color: #3a2a21;"><a href="/{{ $Interfaces->path }}" class="">{{ $Interfaces->interface_name }}</a></li>
                                @endif
                            @endforeach
                        </ul>
                        </li>
                    @endforeach
                </ul>
            </div>
        </nav>


        <div id="page-wrapper" class="gray-bg">
            <div class="row border-bottom">
                {{-- <nav class="navbar navbar-static-top white-bg" role="navigation" style="margin-bottom: 0">
                    <div class="navbar-header">
                        <a class="navbar-minimalize minimalize-styl-2 btn" style="background-color: #ff9900" href="#"><i
                                class="fa fa-bars" style="color: #ffffff"></i> </a>

                {{-- <div class="row">
                            <div class="col-lg-12">
                                <div class="ibox ">
                                    <div class="ibox-content">

                                    </div>
                                </div>
                            </div>
                        </div> --}}
                {{-- </div> --}}
                {{-- </nav> --}}

                <nav class="navbar navbar-static-top white-bg" role="navigation" style="margin-bottom: 0">
                    <div class="navbar-header">
                        <a class="navbar-minimalize minimalize-styl-2 btn" style="background-color: #ff9900"
                            href="#"><i class="fa fa-bars" style="color: #ffffff"></i> </a>
                    </div>
                    <ul class="nav navbar-top-links navbar-right">
                        <li style="padding-top: 3px;">
                            @php
                               $logged_user = session('logged_user_id');
                               $user_OBJ = App\User::find($logged_user);
                               @endphp
                            <span class="m-r-sm welcome-message" style="color: #ff8c00; font-family: 'Comfortaa', cursive;"><i class="fa fa-user" aria-hidden="true"></i> {{ $user_OBJ->first_name }} {{ $user_OBJ->last_name }}</span>
                        </li>

                        <li style="padding-top: 3px;">
                            <span class="m-r-sm welcome-message" style="color: #000; font-family: 'Comfortaa', cursive;">Welcome to BAKERYMATE ePortal</span>
                        </li>

                        <li>
                            <span class="m-r-sm"> &nbsp;</span>
                        </li>

                        <li>
                            <!--DATE DISPLAY-->
                            @php
                                use Carbon\Carbon;
                                $date = Carbon::now(); // Get the current date
                                $formattedDate = $date->format('l, F j, Y');
                            @endphp
                            <span style="font-family: 'Comfortaa', cursive; font-weight: bold; font-size: 11px;">
                                <img src="img/Calendar-icon.png" style="width: 16px; height: 16px;"> &nbsp;
                                <span id="date_span" style="background: linear-gradient(to right, #ff8c00, #050300); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">{{ $formattedDate }}</span>
                            </span>&nbsp;&nbsp;
                            <!--CLOCK DISPLAY-->
                            @php
                                $current_time = Carbon::now()->format('g:i A');
                            @endphp
                            <span style="font-family: 'Orbitron', sans-serif; font-weight: bold; font-size: 13px; letter-spacing: 1px;">
                                <img src="img/Clock-icon.png" style="width: 16px; height: 16px;"> &nbsp;
                                <span id="clock_span" style="background: linear-gradient(to right, #ff8c00, #050300); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">{{ $current_time }}</span>
                            </span>
                        </li>

                        <li>
                            <span class="m-r-sm">&nbsp;</span>
                        </li>

                        <li class="dropdown" id="notificationArea">
                            {{-- <a class="dropdown-toggle count-info" data-toggle="dropdown" href="#" style="color: black">
                                <i class="fa fa-bell-o" id="bell_icon" style="color: #4a4846;"></i> <span class="label label-primary roundedCircle">0</span>
                            </a> --}}
                            <div style="margin: 13px;">
                                <img style="margin: auto; z-index: 1000; position: absolute; scale: 0.13; margin-left: -105px; margin-top: -107px;" src="img/loader2.gif">
                            </div>
                        </li>

                        <li>
                            <a style="color: #6a6a6a;" href="/logout" onmouseover="this.style.color='black'" onmouseout="this.style.color='#6a6a6a'"><i class="fa fa-sign-out"></i> Log out</a>
                        </li>
                    </ul>
                </nav>

            </div>
            <br>
            <div>
                @yield('content')
            </div>
        </div>
    </div>
    <!-- Mainly scripts -->
    <script src="js/jquery-3.1.1.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/plugins/metisMenu/jquery.metisMenu.js"></script>
    <script src="js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
    <script type="text/javascript" src="js/sweetalert.min.js"></script>
    <!-- Custom and plugin javascript -->
    <script src="js/inspinia.js"></script>
    <!-- Select2 -->
    <script src="js/plugins/select2/select2.full.min.js"></script>
    <!-- Data picker -->
    <script src="js/plugins/datapicker/bootstrap-datepicker.js"></script>
    {{-- data table --}}
    <script src="js/plugins/dataTables/datatables.min.js"></script>
    <script src="js/plugins/dataTables/dataTables.bootstrap4.min.js"></script>
    <script type="text/javascript" src="js/sweetalert.min.js"></script>
    <!-- Data picker -->
    {{-- <script src="public/js/plugins/datapicker/bootstrap-datepicker.js"></script> --}}

    {{-- Date and Time refresh function --}}
    <script>
        setInterval(function() {
            var currentDate = new Date().toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
            var currentTime = new Date().toLocaleTimeString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true });

            document.getElementById('date_span').textContent = currentDate;
            document.getElementById('clock_span').textContent = currentTime;
        }, 1000);
    </script>


    <script>
        $(document).ready(function() {
            loadNotifications();

            $(document).on('submit', 'form', function() {
                $('button').attr('disabled', 'disabled');
                showLder();
            });
        });


        function loadNotifications() {
            var csrf_token = $("#csrf_token").val();
            setTimeout(function () {
                jQuery.ajax({
                    url: "{{ url('/loadReorderNotifications') }}",
                    type: "POST",
                    data: {
                        "_token": csrf_token,
                    },
                    beforeSend: function() {
                        // showLder();
                    },
                    complete: function() {
                        // hideLder();
                    },
                    error: function(data) {
                    },
                    success: function(data) {
                        $('#notificationArea').html(data);
                    }
                });
            }, 500);
        }


        // function showLder() {
        //     document.getElementById("loader").innerHTML = '<div style="display: block; position: fixed; z-index: 10000; background-color: #000; opacity: 0.9; background-position: center; left: 0; bottom: 0; right: 0; top: 0; align-content: center;"> <img style="margin: auto; z-index: 1000; position : absolute; top : 35%; left : 45%; scale: 1;" src="img/loader.gif"> </div>';
        //     // document.getElementById("loader").innerHTML = '<div style="display: block; position: fixed; z-index: 10000; background-color: #000; opacity: 0.9; background-position: center; left: 0; bottom: 0; right: 0; top: 0; align-content: center;"> <img style="margin: auto; z-index: 1000; position : absolute; top : 25%; left : 35%; scale: 1.4;" src="img/loader3.gif"> </div>';
        // }

        // function hideLder() {
        //     document.getElementById("loader").innerHTML = '';
        // }


        function showLder() {
            document.getElementById("loader").style.display = "flex";
        }

        function hideLder() {
            document.getElementById("loader").style.display = "none";
        }
    </script>

    @yield('footer')
</body>

</html>

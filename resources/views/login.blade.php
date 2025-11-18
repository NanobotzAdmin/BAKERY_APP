<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BAKERYMATE</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="img/bakerymate.png" />
    {{-- google fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto&family=Roboto+Slab&display=swap" rel="stylesheet">
    <style>
        /*input placeholder css*/
        input::placeholder {
            font-size: 10px;
            color: #dedede;
            font-family: 'Roboto', sans-serif;
            /* font-weight: bold; */
            font-style: italic;
            /* text-transform: uppercase; */
        }
    </style>
</head>

<body class="gray-bg">
    <div class="middle-box  loginscreen animated fadeInDown">
        <div>
            <div>

                <!--<h1 class="logo-name">Richvil Bakers</h1>-->

            </div>
            {{-- <center>
                <img src="img/logo.png" style="width: 100%;height: 100%">
            </center> --}}
            {{-- <center>
                <h2 style="color: #000000;font-weight: bold">Login</h2>
                <p>Use Your Credentials to login to the system </p>
            </center> --}}
            @include('include.flash')
            @include('include.errors')
            <div class="ibox">
                <div class="ibox-content">
                    <img src="img/logo.png" style="width: 100%;height: 100%">
                    <form action="adminLogin" method="POST">
                        {{ csrf_field() }}
                        <div class="form-group"><label>Username</label> <input type="text"
                                placeholder="Enter username here.." class="form-control" name="username"></div>
                        <div class="form-group"><label>Password</label> <input type="password"
                                placeholder="Enter password here.." class="form-control" name="password"></div>
                        <div>
                            <button class="btn btn-sm btn-primary btn-block  m-t-n-xs" type="submit"><strong>LOGIN</strong></button><br>
                            {{-- <label> <input type="checkbox" class="i-checks"> Remember me </label> --}}
                        </div>

                        <div>
                            <button type="button" class="btn btn-sm btn-danger btn-block" data-toggle="modal" data-target="#forgetpass">Reset Password</button>
                        </div>
                    </form>
                </div>
            </div>

            <center>
                <p class="m-t"> <small>@ BAKERYMATE ePortal {{ date('Y') }}</small> </p>
            </center>
        </div>
    </div>




    <!-- Modal -->
    <div class="modal fade" id="forgetpass" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel">Forgot Password</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group"><label>Email</label>
                        <input type="email" placeholder="Enter email" class="form-control" name=''>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning">Change Password</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

</body>

<!-- Mainly scripts -->
<script src="js/jquery-3.1.1.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.js"></script>
<script src="js/plugins/metisMenu/jquery.metisMenu.js"></script>
<script src="js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

</html>

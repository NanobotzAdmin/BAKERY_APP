@extends('layout')
@section('content')

<style>
    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 34px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 26px;
        width: 26px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    input:checked+.slider {
        background-color: #2196F3;
    }

    input:focus+.slider {
        box-shadow: 0 0 1px #2196F3;
    }

    input:checked+.slider:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(26px);
    }

    /* Rounded sliders */
    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }
</style>

<div class="row">
    <div class="col-sm-12">
        <h2 class="font-bold">Privillage Management</h2><br>

        <div class="ibox">
            <div class="ibox-content">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="tabs-container">
                            <ul class="nav nav-tabs" role="tablist">
                                <li><a class="nav-link active" data-toggle="tab" href="#tab-1"> User Role Has
                                        Permission</a></li>
                                <li><a class="nav-link" data-toggle="tab" href="#tab-2">User Has Permission</a></li>
                            </ul>
                            <div class="tab-content">
                                <div role="tabpanel" id="tab-1" class="tab-pane active">
                                    <div class="panel-body">

                                        <div class="row">
                                            <div class="col-xs-12 col-sm-4">
                                                <div class="form-group">
                                                    <div class="col-xs-12">
                                                        <label class="control-label">Select User-Role</label>
                                                        <select class="select2 form-control" id="">
                                                            <option selected="" disabled="" value="">--Select--
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xs-12 col-sm-4">
                                                <div class="form-group">
                                                    <div class="col-xs-12">
                                                        <label class="control-label">Select Interface Topic</label>
                                                        <select class="select2 form-control" id="">
                                                            <option selected="" disabled="" value="">--Select--
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="portlet light">
                                                    <div class="portlet-title">
                                                        <h3 class="caption-subject"
                                                            style="color: lightseagreen;font-weight: bold">Interfaces
                                                        </h3>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="portlet light">
                                                    <div class="portlet-title">
                                                        <h3 class="caption-subject"
                                                            style="color: lightseagreen;font-weight: bold">Components
                                                        </h3>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row" style="border-bottom: #888; border-bottom-style: ridge">
                                            <div class="col-xs-12 col-sm-6">
                                                <div class="ibox">
                                                    <div class="ibox-content">
                                                        <form class="form">
                                                            <div class=" form-group row">
                                                                <div class="col-sm-9"
                                                                    style="border-bottom: #cccccc; border-bottom-style: ridge">
                                                                    <label class=" control-label"
                                                                        style="margin-top: 10px;">Interface 1</label>
                                                                </div>
                                                                <div class="col-sm-1">
                                                                    <button type="button"
                                                                        class="btn btn-success btn-sm">View</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="col-xs-12 col-sm-6">
                                                <div class="ibox">
                                                    <div class="ibox-content">
                                                        <div class="form-group row">
                                                            <label class="col-sm-8 control-label">Interface
                                                            </label>

                                                            <label class="switch">
                                                                <input type="checkbox">
                                                                <span class="slider round"></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div role="tabpanel" id="tab-2" class="tab-pane">
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-xs-12 col-sm-4">
                                                <div class="form-group">
                                                    <div class="col-xs-12">
                                                        <label class="control-label">Select User-Role</label>
                                                        <select class="select2 form-control" id="">
                                                            <option selected="" disabled="" value="">--Select--
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xs-12 col-sm-4">
                                                <div class="form-group">
                                                    <div class="col-xs-12">
                                                        <label class="control-label">Select Use</label>
                                                        <div id="">
                                                            <select class="select2 form-control" id="">
                                                                <option selected="" disabled="" value="">--Select--
                                                                </option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xs-12 col-sm-4">
                                                <div class="form-group">
                                                    <div class="col-xs-12">
                                                        <label class="control-label">Select Interface Topic</label>
                                                        <select class="select2 form-control" id="">
                                                            <option selected="" disabled="" value="">--Select--
                                                            </option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="portlet light">
                                                    <div class="portlet-title">
                                                        <h3 class="caption-subject"
                                                            style="color: lightseagreen;font-weight: bold">Interfaces
                                                        </h3>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="portlet light">
                                                    <div class="portlet-title">
                                                        <h3 class="caption-subject"
                                                            style="color: lightseagreen;font-weight: bold">Components
                                                        </h3>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row" style="border-bottom: #888; border-bottom-style: ridge">
                                            <div class="col-xs-12 col-sm-6">
                                                <div class="ibox">
                                                    <div class="ibox-content">
                                                        <form class="form">
                                                            <div class=" form-group row">
                                                                <div class="col-sm-9"
                                                                    style="border-bottom: #cccccc; border-bottom-style: ridge">
                                                                    <label class=" control-label"
                                                                        style="margin-top: 10px;">Interface 1</label>
                                                                </div>
                                                                <div class="col-sm-1">
                                                                    <button type="button"
                                                                        class="btn btn-success btn-sm">View</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="col-xs-12 col-sm-6">
                                                <div class="ibox">
                                                    <div class="ibox-content">
                                                        <div class="form-group row">
                                                            <label class="col-sm-8 control-label">Interface
                                                            </label>

                                                            <label class="switch">
                                                                <input type="checkbox">
                                                                <span class="slider round"></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>



@endsection


@section('footer')

<script>
    $(document).ready(function(){
                $('.dataTables-example').DataTable({
                    pageLength: 10,
                    responsive: true,
                    dom: '<"html5buttons"B>lTfgitp',
                    buttons: [
                
                    ]
    
                });
    
            });
    
</script>

@endsection
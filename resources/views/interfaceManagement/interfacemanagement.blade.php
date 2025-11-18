@php
    $privilageId = \DB::table('pm_interfaces')
    ->select('pm_interfaces.id AS pageId','pm_interface_topic.id AS grupId')
    ->join('pm_interface_topic', 'pm_interfaces.pm_interface_topic_id', '=', 'pm_interface_topic.id')
    ->where('pm_interfaces.path','adminInterfaceManagement')
    ->first();
@endphp

@extends('layout', ['pageId' => $privilageId->pageId ,'grupId' => $privilageId->grupId ])
@section('content')

<div class="">
    @include('include.flash')
    @include('include.errors')
    <input type="hidden" id="csrf_token" value="{{ csrf_token() }}">

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2><b>Interface Management</b></h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="/admindashboard">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <a>Admin Settings</a>
                </li>
                <li class="breadcrumb-item active">
                    <strong>Interface Management</strong>
                </li>
            </ol>
        </div>
        <div class="col-lg-2">

        </div>
    </div>

    <div class="row wrapper wrapper-content">
        <div class="col-lg-12">
            <!------- ///////////// ADD INTERFACE TOPIC ////////////  -->
            <div class="row">
                <div class="col-lg-4">
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>Add Interface Topic</h5>
                        </div>
                        <div class="ibox-content">
                            <form action="/saveInterfaceTopic" method="POST">
                                {{ csrf_field() }}
                            <div class="form-group">
                                <label>Topic Name <span style="color: red">*</span></label>
                                <input type="text" class="form-control" name="InterfaceTopic_Name" value="{{old('InterfaceTopic_Name')}}">
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <label>Topic Icon <span style="color: red">*</span></label>
                                    </div>
                                    <div class="col-sm-6">
                                        <span><small style="color: #005c4b;">(Use Font Awesome icon class names)</small></span>
                                    </div>
                                </div>
                                <input type="text" class="form-control" name="InterfaceTopic_Icon" value="{{old('InterfaceTopic_Icon')}}">
                            </div>
                            <div class="form-group">
                                <label>Section Class <span style="color: red">*</span></label>
                                <input type="text" class="form-control"  name="InterfaceTopic_Section" value="{{old('InterfaceTopic_Section')}}">
                            </div>
                            <div class="form-group">
                                <button  type='submit' class="btn btn-primary btn-sm pull-right"><i class="fa fa-floppy-o" aria-hidden="true"></i> | Save</button>
                            </div>
                            </form>
                            <br>
                        </div>
                    </div>
                </div>

                <!------- ///////////// ADD INTERFACE ////////////  -->
                <div class="col-lg-4">
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>Add Interface </h5>
                        </div>
                        <div class="ibox-content">
                                <form action="/saveInterface" method="POST">
                                    {{ csrf_field() }}
                            <div class="form-group">
                                <label>Topic Name <span style="color: red">*</span></label>
                                <select class="select2 form-control" name="topic" value="{{old('topic')}}">
                                    <option value="0">-- Select One --</option>
                                    @foreach ($interfaceTopics as $interfaceTopic)
                                    <option value="{{ $interfaceTopic->id }}">{{ $interfaceTopic->topic_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Interface Name <span style="color: red">*</span></label>
                                <input type="text" class="form-control" name="interface_name" value="{{old('interface_name')}}">
                            </div>
                            <div class="form-group">
                                <label>Interface URL <span style="color: red">*</span></label>
                                <input type="text" class="form-control" value="{{old('interface_url')}}" name="interface_url">
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <label>Icon Class <span style="color: red">*</span></label>
                                    </div>
                                    <div class="col-sm-6">
                                        <span><small style="color: #005c4b;">(Use Font Awesome icon class names)</small></span>
                                    </div>
                                </div>
                                <input type="text" class="form-control" value="{{old('interface_icon')}}" name="interface_icon">
                            </div>
                            <div class="form-group">
                                <label>Title Class <span style="color: red">*</span></label>
                                <input type="text" class="form-control" value="{{old('interface_title')}}" name="interface_title">
                            </div>
                            <div class="form-group">
                                <button class="btn btn-primary btn-sm pull-right" type="submit"><i class="fa fa-floppy-o" aria-hidden="true"></i> | Save</button>
                            </div>
                                </form>
                            <br>
                        </div>
                    </div>
                </div>

                        <!------- ///////////// ADD INTERFACE COMPONENT ////////////  -->

                <div class="col-lg-4">
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>Add Interface Component</h5>
                        </div>
                        <div class="ibox-content">
                                <form action="/saveInterfaceComponent" method="POST">
                                    {{ csrf_field() }}
                            <div class="form-group">
                                <label>Topic Name</label>
                                <select class="select2 form-control" value="{{old('component_topic')}}" name="component_topic" onchange="loadInterfaces()" id="component_topic">
                                    <option value="0">-- Select One --</option>
                                        @foreach ($interfaceTopics as $interfaceTopicCompo)
                                        <option value="{{ $interfaceTopicCompo->id }}">{{ $interfaceTopicCompo->topic_name }}</option>
                                        @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Interface Name <span style="color: red">*</span></label>
                                <select class="select2 form-control" value="{{old('component_interface')}}" name="component_interface" id='component_interface'>
                                <option value="0">-- Select One --</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Interface Component Name <span style="color: red">*</span></label>
                                <input type="text" class="form-control" value="{{old('component_name')}}" name="component_name">
                            </div>
                            <div class="form-group">
                                <label>Interface Component ID <span style="color: red">*</span></label>
                                <input type="text" class="form-control" value="{{old('component_id')}}" name="component_id">
                            </div>
                            <div class="form-group">
                                <button class="btn btn-primary btn-sm pull-right" type="submit"><i class="fa fa-floppy-o" aria-hidden="true"></i> | Save</button>
                            </div>
                                </form>
                            <br>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <div class="ibox-title">
                <h5>Update Interface Topic</h5>
            </div>
            <div class="ibox-content">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover dataTables-example">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Topic Name</th>
                                <th>Topic Icon</th>
                                <th>Section Class</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($interfaceTopics as $interfaceTopic)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $interfaceTopic->topic_name }}</td>
                                <td>{{ $interfaceTopic->menu_icon }}</td>
                                <td>{{ $interfaceTopic->section_class }}</td>
                                <td>
                                    <button type="button" class="btn btn-outline-info btn-xs" data-toggle="modal" data-target="#update_interfaceTopic_modal" onclick='loadDataToModal({{ $interfaceTopic->id }},"interfaceTopic")'><i class="fa fa-eye" aria-hidden="true"></i> | View</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="update_interfaceTopic_modal" tabindex="-1" role="dialog"
                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-sm" role="document">
                        <div class="modal-content" id="DIV_interfaceTopic_updateModal">


                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <div class="ibox-title">
                <h5>Update Interfaces</h5>
            </div>
            <div class="ibox-content">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover dataTables-example">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Topic Name</th>
                                <th>Interface Name</th>
                                <th>Interface URL</th>
                                <th>Icon Class</th>
                                <th>Title Class</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($interfaces as $interface)
                                @php
                                    $interfaceTopic = App\InterfaceTopics::find($interface->pm_interface_topic_id);
                                @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $interfaceTopic->topic_name }}</td>
                                <td>{{ $interface->interface_name }}</td>
                                <td>{{ $interface->path }}</td>
                                <td>{{ $interface->icon_class }}</td>
                                <td>{{ $interface->tile_class }}</td>
                                <td>
                                    <button type="button" class="btn btn-outline-info btn-xs" data-toggle="modal" data-target="#update_interface_modal" onclick='loadDataToModal({{ $interface->id }},"interface")'><i class="fa fa-eye" aria-hidden="true"></i> | View</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="update_interface_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-bg" role="document">
                        <div class="modal-content" id="DIV_interface_updateModal">

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <div class="ibox-title">
                <h5>Update Interface Component</h5>
            </div>
            <div class="ibox-content">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover dataTables-example">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Topic Name</th>
                                <th>Interface Name</th>
                                <th>Interface Component Name</th>
                                <th>Interface Component ID</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($interfaceComponents as $interfaceComponent)

                    <?php
                       $interfaceDetails = \DB::table('pm_interface_topic')
                        ->select('pm_interface_topic.topic_name', 'pm_interfaces.interface_name')
                        ->join('pm_interfaces', 'pm_interfaces.pm_interface_topic_id', '=', 'pm_interface_topic.id')
                        ->join('pm_interface_components', 'pm_interfaces.id', '=', 'pm_interface_components.pm_interfaces_id')
                        ->where('pm_interface_components.id', $interfaceComponent->id)
                        ->first();
                    ?>
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $interfaceDetails->topic_name }}</td>
                                <td>{{ $interfaceDetails->interface_name }}</td>
                                <td>{{ $interfaceComponent->components_name }}</td>
                                <td>{{ $interfaceComponent->component_id }}</td>
                                <td>
                                    <button type="button" class="btn btn-outline-info btn-xs" data-toggle="modal" data-target="#update_interfaceComponent_modal" onclick='loadDataToModal({{ $interfaceComponent->id }},"interfaceComponent")'><i class="fa fa-eye" aria-hidden="true"></i> | View</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="update_interfaceComponent_modal" tabindex="-1" role="dialog"
                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-bg" role="document">
                        <div class="modal-content" id="DIV_interfaceComponent_updateModal">

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="interfaceComponent" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content" id="ModalContent">

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
            buttons: []
        });
    });

    // load Interface options by Topic ID
    function loadInterfaces() {
        var csrf_token = $("#csrf_token").val();
        var topicId = $('#component_topic').val();

        if (topicId == 0) {
            swal("", "Please select a Topic !", "warning");
            $("#component_interface").empty();
            $('#component_interface').append("<option value='0'>-- Select One --</option>");
        } else {
            jQuery.ajax({
                url: "{{ url('/loadInterfaces') }}",
                type: "POST",
                data: {
                    "_token": csrf_token,
                    "topicId":topicId,
                },
                beforeSend: function () {
                    showLder();
                },
                complete: function () {
                },
                error: function (data) {
                },
                success: function (data) {
                    hideLder();
                    $("#component_interface").empty();
                    $('#component_interface').append("<option value='0'>-- Select One --</option>");
                    var html = '';
                    $.each(data.intefaceCompo, function (key, val) {
                        html += '<option value = ' + val.id + '>' + val.interface_name + '</option>';
                    });
                    $('#component_interface').append(html);
                }
            });
        }
    }

    // load Interface options by Topic ID
    function loadInterfaces_2() {
        var csrf_token = $("#csrf_token").val();
        var topicId = $('#updateInterfaceComponent_topic').val();

        if (topicId == 0) {
            swal("", "Please select a Topic !", "warning");
            $("#updateInterfaceComponent_interface").empty();
            $('#updateInterfaceComponent_interface').append("<option value='0'>-- Select One --</option>");
        } else {
            jQuery.ajax({
                url: "{{ url('/loadInterfaces') }}",
                type: "POST",
                data: {
                    "_token": csrf_token,
                    "topicId":topicId,
                },
                beforeSend: function () {
                    showLder();
                },
                complete: function () {
                },
                error: function (data) {
                },
                success: function (data) {
                    hideLder();
                    $("#updateInterfaceComponent_interface").empty();
                    $('#updateInterfaceComponent_interface').append("<option value='0'>-- Select One --</option>");
                    var html = '';
                    $.each(data.intefaceCompo, function (key, val) {
                        html += '<option value=' + val.id + '>' + val.interface_name + '</option>';
                    });
                    $('#updateInterfaceComponent_interface').append(html);
                }
            });
        }
    }


    // view modal data
    function loadDataToModal(id, section) {
        var csrf_token = $("#csrf_token").val();
        var url = "";

        if (section == "interfaceTopic") {
            url = "{{ url('/loadInterfaceTopicDetailsToModal') }}";
        } else if (section == "interface") {
            url = "{{ url('/loadInterfaceDetailsToModal') }}";
        } else if (section == "interfaceComponent") {
            url = "{{ url('/loadInterfaceComponentDetailsToModal') }}";
        }

        jQuery.ajax({
            url: url,
            type: "POST",
            data: {
                "_token": csrf_token,
                "section":section,
                "id":id,
            },
            beforeSend: function () {
                showLder();
            },
            complete: function () {
            },
            error: function (data) {
            },
            success: function (data) {
                hideLder();
                if (section == "interfaceTopic") {
                    $('#DIV_interfaceTopic_updateModal').html(data);
                } else if (section == "interface") {
                    $('#DIV_interface_updateModal').html(data);
                } else if (section == "interfaceComponent") {
                    $('#DIV_interfaceComponent_updateModal').html(data);
                }
            }
        });
    }


    // update INTERFACE TOPIC
    function updateInterfaceTopicModalData() {
        var csrf_token = $("#csrf_token").val();
        var topic_id = $("#updateInterfaceTopic_interfaceTopicID").val();
        var topic_name = $("#updateInterfaceTopic_topic_name").val();
        var menu_icon = $("#updateInterfaceTopic_menu_icon").val();
        var section_class = $("#updateInterfaceTopic_section_class").val();

        jQuery.ajax({
            url: "{{ url('/updateInterfaceTopic') }}",
            type: "POST",
            data: {
                "_token": csrf_token,
                "topic_id":topic_id,
                "topic_name":topic_name,
                "menu_icon":menu_icon,
                "section_class":section_class,
            },
            beforeSend: function () {
                showLder();
            },
            complete: function () {
            },
            error: function (response) {
                hideLder();
                let errorMessage = 'Something went wrong.';

                if (response.responseJSON && response.responseJSON.message) {
                    errorMessage = response.responseJSON.message;
                }
                // Validation Error
                if (response.responseJSON && response.responseJSON.type === 'validation') {
                    swal("Validation Failed!", errorMessage, "warning");
                } else {
                    swal("Error", errorMessage, "error");
                }
            },
            success: function (response) {
                hideLder();
                if (response.status === 'success') {
                    // Close the modal
                    $('#update_interfaceTopic_modal').modal('hide');
                    swal({
                        title: "Updated",
                        text: response.message,
                        type: "success"
                    }, function() {
                        location.reload();  // Reload the page after "OK" is pressed
                    });
                }
            }
        });
    }

    // update INTERFACE
    function updateInterfaceModalData() {
        var csrf_token = $("#csrf_token").val();
        var interface_id = $("#updateInterface_interfaceID").val();
        var topic_id = $("#updateInterface_topic").val();
        var interface_name = $("#updateInterface_interface_name").val();
        var interface_URL = $("#updateInterface_interface_URL").val();
        var icon_class = $("#updateInterface_icon_class").val();
        var tile_class = $("#updateInterface_title_class").val();

        jQuery.ajax({
            url: "{{ url('/updateInterface') }}",
            type: "POST",
            data: {
                "_token": csrf_token,
                "interface_id":interface_id,
                "topic_id":topic_id,
                "interface_name":interface_name,
                "interface_URL":interface_URL,
                "icon_class":icon_class,
                "tile_class":tile_class,
            },
            beforeSend: function () {
                showLder();
            },
            complete: function () {
            },
            error: function (response) {
                hideLder();
                let errorMessage = 'Something went wrong.';

                if (response.responseJSON && response.responseJSON.message) {
                    errorMessage = response.responseJSON.message;
                }
                // Validation Error
                if (response.responseJSON && response.responseJSON.type === 'validation') {
                    swal("Validation Failed!", errorMessage, "warning");
                } else {
                    swal("Error", errorMessage, "error");
                }
            },
            success: function (response) {
                hideLder();
                if (response.status === 'success') {
                    // Close the modal
                    $('#update_interface_modal').modal('hide');
                    swal({
                        title: "Updated",
                        text: response.message,
                        type: "success"
                    }, function() {
                        location.reload();  // Reload the page after "OK" is pressed
                    });
                }
            }
        });
    }

    // update INTERFACE COMPONENT
    function updateInterfaceComponentModalData() {
        var csrf_token = $("#csrf_token").val();
        var component_id = $("#updateInterfaceComponent_componentID").val();
        var topic_id = $("#updateInterfaceComponent_topic").val();
        var interface_id = $("#updateInterfaceComponent_interface").val();
        var interface_component_name = $("#updateInterfaceComponent_interfaceComponentName").val();
        var interface_component_id = $("#updateInterfaceComponent_interfaceComponentID").val();

        jQuery.ajax({
            url: "{{ url('/updateInterfaceComponent') }}",
            type: "POST",
            data: {
                "_token": csrf_token,
                "component_id":component_id,
                "topic_id":topic_id,
                "interface_id":interface_id,
                "interface_component_name":interface_component_name,
                "interface_component_id":interface_component_id
            },
            beforeSend: function () {
                showLder();
            },
            complete: function () {
            },
            error: function (response) {
                hideLder();
                let errorMessage = 'Something went wrong.';

                if (response.responseJSON && response.responseJSON.message) {
                    errorMessage = response.responseJSON.message;
                }
                // Validation Error
                if (response.responseJSON && response.responseJSON.type === 'validation') {
                    swal("Validation Failed!", errorMessage, "warning");
                } else {
                    swal("Error", errorMessage, "error");
                }
            },
            success: function (response) {
                hideLder();
                if (response.status === 'success') {
                    // Close the modal
                    $('#update_interfaceComponent_modal').modal('hide');
                    swal({
                        title: "Updated",
                        text: response.message,
                        type: "success"
                    }, function() {
                        location.reload();  // Reload the page after "OK" is pressed
                    });
                }
            }
        });
    }
</script>

@endsection

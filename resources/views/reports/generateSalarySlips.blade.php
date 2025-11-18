@php


$privilageId = \DB::table('pm_interfaces')
->select('pm_interfaces.id AS pageId','pm_interface_topic.id AS grupId')
->join('pm_interface_topic', 'pm_interfaces.pm_interface_topic_id', '=', 'pm_interface_topic.id')
->where('pm_interfaces.path','adminGenerateSalarySlips')
->first();


@endphp


@extends('layout', ['pageId' => $privilageId->pageId ,'grupId' => $privilageId->grupId ])

@section('content')


<div class="row">
    <div class="col-sm-12">
        <h2 class="font-bold">Generate Salary Slips</h2>
        <div class="ibox">
            <div class="ibox-content">
                <div class="row mt-4">
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label>Select User</label>
                            <select class="select2_demo_3 form-control" id="user">
                                <option value="0">Select One</option>
                             </select>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="form-group" id="data_1">
                            <label>Date From</label>
                            <div class="input-group date">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input type="text"
                                    class="form-control form-control-sm" id="dateFrom">
                            </div>
                        </div>
                    </div>
                     <div class="col-lg-4">
                        <div class="form-group" id="data_1">
                            <label>Date To</label>
                            <div class="input-group date">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input type="text"
                                    class="form-control form-control-sm" id="dateTo">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group ">
                    <button type="button" class="btn btn-primary pull-right">Generate</button>
                  </div>
                <br>
             </div>
        </div>


<div class="ibox">
    <div class="ibox-content">
        <button type="button" class="btn btn-sm btn-success btn-block">Print Salary Slip </button>
            <br>
            <div class="row">
            <div class="col-sm-3"></div>
                <div class="col-sm-6">
                    <center>
                    <img src="img/logo.png" alt="logo" style="width: 40%"><br><br>
                    <label style="font-size: medium">Batuwaththa, Halamada</label><br>
                    <label style="font-size: medium">0702243051 / 0372243051</label><br>
                    <label style="font-size: medium">Reg Number</label><br><br>
                    <h2><b>Monthly Salary Slip</b></h2>

                   <br>

                     <table style="font-size: medium;width: 55%">
                        <tbody>
                            <tr style="height: 35px">
                                <td>Name </td>
                                <td> :</td>
                                <td>&nbsp;&nbsp;&nbsp;Buddhika Dasanayaka</td>
                            </tr>
                            <tr style="height: 35px">
                                <td>Month </td>
                                <td> :</td>
                                <td>&nbsp;&nbsp;&nbsp;May</td>
                            </tr>
                            <tr style="height: 35px">
                                <td>Working Days </td>
                                <td> :</td>
                                <td>&nbsp;&nbsp;&nbsp;14</td>
                            </tr>
                            <tr style="height: 35px">
                                <td>Payment </td>
                                <td> :</td>
                                <td>&nbsp;&nbsp;&nbsp;14</td>
                            </tr>
                            <tr style="height: 35px">
                                <td>Comission </td>
                                <td> :</td>
                                <td>&nbsp;&nbsp;&nbsp;10%</td>
                            </tr>
                            <tr style="height: 35px">
                                <td>Total </td>
                                <td> :</td>
                                <td>&nbsp;&nbsp;&nbsp;<u style="text-decoration-style: double">000000</u></td>
                            </tr>
                        </tbody>
                    </table>

                    </center>

                </div>
                <div class="col-sm-3"></div>
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


            $(".select2_demo_3").select2({
                placeholder: "Select a state",
                allowClear: true
            });

            var mem = $('#data_1 .input-group.date').datepicker({
                todayBtn: "linked",
                keyboardNavigation: false,
                forceParse: false,
                calendarWeeks: true,
                autoclose: true
            });

</script>

@endsection

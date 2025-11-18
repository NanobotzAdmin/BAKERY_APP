@php

$privilageId = \DB::table('pm_interfaces')
    ->select('pm_interfaces.id AS pageId', 'pm_interface_topic.id AS grupId')
    ->join('pm_interface_topic', 'pm_interfaces.pm_interface_topic_id', '=', 'pm_interface_topic.id')
    ->where('pm_interfaces.path', 'adminProfitAndLossStatement')
    ->first();

@endphp


@extends('layout', ['pageId' => $privilageId->pageId, 'grupId' => $privilageId->grupId])

@section('content')
    <div class="row">


        <div class="col-sm-6">
            <h2 class="font-bold">Profit And Loss Statement of Richwil Bakers</h2>

            <div class="ibox">
                <div class="ibox-content">
                  
                     <div class="form-group row mt-4">
                            <label for="" class="col-sm-2"><b>From :</b></label>
                            <label for="" class="col-sm-2">(From Date)</label>
                            <label for="" class="col-sm-1"><b>To :</b></label>
                            <label for="" class="col-sm-2">(To Date)</label>
                        </div>

                        <br>

                        <div class="table-responsive">
<table class="table table-bordered table-striped table-advance">
    <tbody>
        <tr>
            <td>Sales</td>
            <td></td>
            <td style="text-align: center">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; xxxxx</td>
           </tr>
        <tr>
            <td>
                <ul>
                    <li>Item Cost</li><hr>
                    <li>Return Cost</li>
                </ul>
            </td>
            <td>
                <ul style="list-style-type: none;">
                    <li style="text-align: center">xxxx</li><hr>
                    <li style="text-align: center">xxxx</li>
                </ul>    
            </td>
            <td>
                <ul style="list-style-type: none;">
                    <li style="text-align: center;color: white">xxx</li><hr>
                    <li style="text-align: center">xxxx</li>
                </ul>    
            </td>
           </tr>

           <tr>
            <td>Administration</td>
            <td></td>
            <td style="text-align: center">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; xxxxx</td>
           </tr>
        <tr>
            <td>
                <ul>
                    <li>Item Cost</li><hr>
                    <li>Return Cost</li>
                </ul>
            </td>
            <td>
                <ul style="list-style-type: none;">
                    <li style="text-align: center">xxxx</li><hr>
                    <li style="text-align: center">xxxx</li>
                </ul>    
            </td>
            <td>
                <ul style="list-style-type: none;">
                    <li style="text-align: center;color: white">xxx</li><hr>
                    <li style="text-align: center">xxxx</li>
                </ul>    
            </td>
           </tr>
           <tr>
            <td>Net Profit</td>
            <td></td>
            <td style="text-align: center">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; xxxxx</td>
           </tr>
    </tbody>
</table>
                        </div>
                  
              
                </div>
            </div>
        </div>
    </div>

   
@endsection


@section('footer')
    <script>
        $(document).ready(function() {
            $('.dataTables-example').DataTable({
                pageLength: 10,
                responsive: true,
                dom: '<"html5buttons"B>lTfgitp',
                buttons: []
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

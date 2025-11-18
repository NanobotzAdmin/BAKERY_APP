@php

    $privilageId = \DB::table('pm_interfaces')
        ->select('pm_interfaces.id AS pageId', 'pm_interface_topic.id AS grupId')
        ->join('pm_interface_topic', 'pm_interfaces.pm_interface_topic_id', '=', 'pm_interface_topic.id')
        ->where('pm_interfaces.path', 'adminStockInReport')
        ->first();

@endphp


@extends('layout', ['pageId' => $privilageId->pageId, 'grupId' => $privilageId->grupId])

@section('content')


<style>
    /* --- Table CSS begins --- */
    .styled-table th:first-child {
        border-radius: 5px 0 0 0;
    }
    .styled-table th:last-child {
        border-radius: 0 5px 0 0;
    }
    .styled-table {
        border-collapse: collapse;
        margin: 25px 0;
        font-size: 14px;
        font-family: Verdana, Geneva, sans-serif;
        min-width: 400px;
    }
    .styled-table thead tr {
        background-color: #5c3d23;
        color: #ffffff;
        text-align: left;
        font-size: 14px;
        font-weight: bold;
        font-family: Verdana, Geneva, sans-serif;
    }
    .styled-table th,
    .styled-table td {
        padding: 12px 15px;
    }
    .styled-table tbody tr {
        border-bottom: 1px solid #dddddd;
    }
    .styled-table tbody tr:nth-of-type(even) {
        background-color: #ffffff;
    }
    .styled-table tbody tr:last-of-type {
        border-bottom: 2px solid #5c3d23;
    }
    .styled-table tbody tr:hover td {
        background-color: #faf6ec;
    }
</style>

    <div class="row">
        <div class="col-sm-12">
            <h2 class="font-bold">Stock In Report</h2>
            <div class="ibox">
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-sm styled-table" style="width: 60%;">
                            <thead>
                                <tr>
                                    <th style="text-align: center;">#</th>
                                    <th>Product</th>
                                    <th style="text-align: center; padding-right: 30px;">Stock In Quantity</th>
                                    <th style="text-align: center; padding-right: 30px;">Stock Out Quantity</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($stock as $pro)
                                    <tr>
                                        <td style="text-align: center;">{{ $loop->iteration }}</td>
                                        <td>{{ $pro->proname }}</td>
                                        <td style="text-align: center; padding-right: 30px; width: 200px;">{{ $pro->in_qty }}</td>
                                        <td style="text-align: center; padding-right: 30px; width: 200px;">{{ $pro->out_qty }}</td>
                                    </tr>
                                @endforeach
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

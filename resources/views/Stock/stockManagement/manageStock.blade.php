@php
    $privilageId = \DB::table('pm_interfaces')
        ->select('pm_interfaces.id AS pageId', 'pm_interface_topic.id AS grupId')
        ->join('pm_interface_topic', 'pm_interfaces.pm_interface_topic_id', '=', 'pm_interface_topic.id')
        ->where('pm_interfaces.path', 'adminManageProducts')
        ->first();
@endphp

@extends('layout', ['pageId' => $privilageId->pageId, 'grupId' => $privilageId->grupId])
@section('content')

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-12">
        <h2><b>Stock Management</b></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="/admindashboard">Home</a>
            </li>
            <li class="breadcrumb-item">
                <a>Product Management</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Stock Management</strong>
            </li>
        </ol>
    </div>
</div>
<br>

<input type="hidden" id="csrf_token" value="{{ csrf_token() }}">

<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <div class="ibox-title">
                <h5>Manage Stock</h5>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="form-group col-md-2">
                        <label for="">Product Category</label>
                        <select class="select2_demo_3 form-control" id="CategoryList" onchange="loadProductSubCategories()">
                            <option value="0">-- All --</option>
                            @foreach ($Category as $catogories)
                            <option value="{{ $catogories->id }}">{{ $catogories->main_category_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-2">
                        <label for="">Product</label>
                        <div id="ContentProList">
                            <select class="select2_demo_3 form-control" id="proList">
                                <option value="0">-- All --</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-md-2">
                        <label for="">&nbsp;</label><br>
                        <button type="button" class="btn btn-sm btn-info" onclick="loadStockBatchDetails()"><i class="fa fa-search" aria-hidden="true"></i> &nbsp; Search</button>
                    </div>
                </div>
                <br>
            </div>
        </div>
    </div>
</div>

<div class="row" id="BatchDetailsContent"> </div>

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
            placeholder: "-- Select One --",
            allowClear: false
        });


        // load Products to Select menu by Category
        function loadProductSubCategories() {
            var csrf_token = $("#csrf_token").val();
            var categoryId = $("#CategoryList").val();
            if (categoryId == 0) {
                // swal("", "Please select a Category.", "warning");
                // $('#proList').val(0);
                $('#ContentProList').html('\
                    <select class="select2_demo_3 form-control">\
                        <option value="0" selected>-- All --</option>\
                    </select>\
                ');
                // Reinitialize Select2
                $('.select2_demo_3').select2();
            } else {
                jQuery.ajax({
                    url: "{{ url('/loadProductsToCategory') }}",
                    type: "POST",
                    data: {
                        "_token": csrf_token,
                        "categoryId": categoryId
                    },
                    beforeSend: function() {
                        showLder();
                    },
                    complete: function() {
                    },
                    error: function(data) {
                    },
                    success: function(data) {
                        hideLder();
                        $('#ContentProList').html(data);
                    }
                });
            }
        }


        // get Stock Batches with filters
        function loadStockBatchDetails() {
            var csrf_token = $("#csrf_token").val();
            var productMainCategoryId = $("#CategoryList").val();
            var productSubCategoryId = $("#proList").val();
            // if (categoryId == 0) {
            //     swal("", "Please select a Category.", "warning");
            // } else if (productSubCategoryId == 0) {
            //     swal("", "Please select a product.", "warning");
            // } else {
                jQuery.ajax({
                    url: "{{ url('/loadProductBatches') }}",
                    type: "POST",
                    data: {
                        "_token": csrf_token,
                        "productMainCategoryId": productMainCategoryId,
                        "productSubCategoryId": productSubCategoryId
                    },
                    beforeSend: function() {
                        showLder();
                    },
                    complete: function() {
                    },
                    error: function(data) {
                    },
                    success: function(data) {
                        hideLder();
                        $('#BatchDetailsContent').html(data);
                    }
                });
            // }
        }
    </script>
@endsection

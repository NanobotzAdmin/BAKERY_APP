@php
    $privilageId = \DB::table('pm_interfaces')
        ->select('pm_interfaces.id AS pageId', 'pm_interface_topic.id AS grupId')
        ->join('pm_interface_topic', 'pm_interfaces.pm_interface_topic_id', '=', 'pm_interface_topic.id')
        ->where('pm_interfaces.path', 'adminStockIn')
        ->first();
@endphp

@extends('layout', ['pageId' => $privilageId->pageId, 'grupId' => $privilageId->grupId])
@section('content')

    <Style>
        input::placeholder {
            font: 10px sans-serif;
            font-style: italic;
            color: #dbd8d0 !important;
            letter-spacing: 1.1px;
        }
    </Style>

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-12">
            <h2><b>Stock In</b></h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="/admindashboard">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <a>Product Management</a>
                </li>
                <li class="breadcrumb-item active">
                    <strong>Stock In</strong>
                </li>
            </ol>
        </div>
    </div>
    <br>

    <div class="row">
        <input type="hidden" id="csrf_token" value="{{ csrf_token() }}">
        <div class="col-sm-12">

            @include('include.flash')
            @include('include.errors')

            <div class="row">
                <div class="col-sm-12">
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>Stock In Search</h5>
                        </div>
                        <div class="ibox-content">
                            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#newStock">Add New Stock</button>
                        </div>
                        <div class="ibox-content">
                            <br>
                            <form class="form-inline">
                                <div class="form-group mb-2">
                                    <label for="" class="col-sm-2">Date</label>
                                    <div class="form-group" id="data_1">
                                        <div class="input-group date">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input type="text" class="form-control form-control-sm" id="dateSelect" placeholder="Choose a date..." maxlength="10" oninput="this.value = this.value.replace(/[^0-9/]/g, '').replace(/(\..*?)\..*/g, '$1');" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-info mb-2" onclick="searchByDateStockIn()"><i class="fa fa-search" aria-hidden="true"></i> &nbsp; Search</button>
                            </form>
                            <br>
                        </div>
                    </div>

                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>Stock In &nbsp;<small><b>(Today)</b></small></h5>
                        </div>
                        <div class="ibox-content">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover dataTables-example">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Batch Number</th>
                                            <th>Sub Category</th>
                                            <th>Product Status</th>
                                            <th style="max-width: 140px;">Available Quantity</th>
                                            <th style="max-width: 140px;">Selling Price <small>(LKR)</small></th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="stockInTbody">
                                        <?php $id = 0; ?>
                                        @foreach ($stockBatch as $batch)
                                            <?php
                                                $id++;
                                                $subCategory = App\SubCategory::find($batch->pm_product_sub_category_id);
                                                $proStatus = App\ProductItemState::find($batch->pm_product_item_state_id);
                                                $mainCategory = App\MainCategory::find($subCategory->pm_product_main_category_id);
                                            ?>
                                            <tr>
                                                <td>{{ $id }}</td>
                                                <td>{{ $batch->batch_code }}</td>
                                                <td>{{ $subCategory->sub_category_name }}</td>
                                                <td>{{ $proStatus->item_name }}</td>
                                                <td style="text-align: right;">{{ $batch->available_quantity }}</td>
                                                <td style="text-align: right;">{{ number_format((float)$batch->selling_price, 2, '.', '') }}</td>
                                                <td>
                                                    <button type="button" class="btn btn-warning btn-xs" data-toggle="modal" data-target="#updateStock" onclick="loadstockUpdateModal({{ $batch->id }} ,{{ $mainCategory->id }})"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>&nbsp; Update</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Modal -->
                            <div class="modal fade" id="updateStock" tabindex="-1" role="dialog"
                                aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content" id="stockUpdateArea">

                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="newStock" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form action="saveStock" method="POST">
                {{ csrf_field() }}
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="exampleModalLabel">Add New Stock</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="">Main Category</label>
                                <select class="select2 form-control" name="MainCategory" onchange="loadSubCategories('savePage')" id="MainCategory">
                                    <option value="0">-- Select One --</option>
                                    @foreach ($categoryList as $category)
                                        <option value="{{ $category->id }}">{{ $category->main_category_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="">Sub Category</label>
                                <select class="selec2 form-control" name="subCategory" id="subCategory" onchange="loadProductDetails('savePage')">
                                    <option value="0">-- Select One --</option>
                                </select>
                            </div>
                        </div>

                        <div id="productDetailsLoadingDiv"></div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="">State</label>
                                <select class="selec2 form-control" name="itemStatus">
                                    <option value="0">-- Select One --</option>
                                    @foreach ($Status as $StatusList)
                                        <option value="{{ $StatusList->id }}">{{ $StatusList->item_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="">Adding Quantity</label>
                                <input type="text" class="form-control allow_decimal" style="color: #010ec5;" name="addingQty" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" maxlength="15" autocomplete="off">
                            </div>
                        </div>

                        {{-- <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="">Selling Price</label>
                                <input type="text" class="form-control" name="sellingPrice">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="">Actual Cost</label>
                                <input type="text" class="form-control" name="actualCost">
                            </div>
                        </div>
                        <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="">Retail Price</label>
                                    <input type="text" class="form-control" name="retailPrice">
                                </div>
                        </div> --}}

                        <div class="form-row">
                            <div class="col-md-6" id="productExpiryDateLoadingDiv">

                            </div>
                        </div>

                        {{-- <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="visibleCheck" name="visibleCheck">
                            <label class="form-check-label" for="exampleCheck1">Is visible</label>
                        </div> --}}
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save New Stock</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>

                </div>
            </form>
        </div>
    </div>
    {{-- modal ends --}}

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


        var mem = $('#data_1 .input-group.date').datepicker({
            todayBtn: "linked",
            keyboardNavigation: false,
            forceParse: false,
            calendarWeeks: true,
            autoclose: true
        });


        $(".allow_decimal").on("input", function(evt) {
            var self = $(this);
            self.val(self.val().replace(/[^0-9\.]/g, ''));
            if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) {
                evt.preventDefault();
            }
        });



        function loadSubCategories(type) {
            // save page
            if (type == 'savePage') {
                var MainCategory = $('#MainCategory option:selected').val();
                var csrf_token = $("#csrf_token").val();
                $('#productCode').val('');
                $('#batchCode').val('');
                $('#expiryDate').val('');
                $('#batchCodeHidden').val('');

                if (MainCategory == 0) {
                    swal("", "Please select a Main Category.", "warning");
                } else {
                    jQuery.ajax({
                        url: "{{ url('/loadSubCategories') }}",
                        type: "POST",
                        data: {
                            "_token": csrf_token,
                            "MainCategory": MainCategory,
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
                            $("#subCategory").empty();
                            $('#subCategory').append("<option value='0'>-- Select One --</option>");
                            var html = '';
                            $.each(data.products, function(key, val) {
                                html += '<option value =' + val.id + '>' + val.sub_category_name + '</option>';
                            });
                            $('#subCategory').append(html);
                        }
                    });
                }

            } else { // update page
                var MainCategory = $('#MODAL_MAIN_CATEGORY option:selected').val();
                var csrf_token = $("#csrf_token").val();
                $('#productCode').val('');
                $('#batchCode').val('');
                $('#expiryDate').val('');
                $('#batchCodeHidden').val('');

                if (MainCategory == 0) {
                    swal("", "Please select a Main Category.", "warning");
                } else {
                    jQuery.ajax({
                        url: "{{ url('/loadSubCategories') }}",
                        type: "POST",
                        data: {
                            "_token": csrf_token,
                            "MainCategory": MainCategory,
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
                            $("#MODAL_SUB_CATEGORY").empty();
                            $('#MODAL_SUB_CATEGORY').append("<option value='0'>-- Select One --</option>");
                            var html = '';
                            $.each(data.products, function(key, val) {
                                html += '<option value =' + val.id + '>' + val.sub_category_name + '</option>';
                            });
                            $('#MODAL_SUB_CATEGORY').append(html);
                        }
                    });
                }
            }
        }


        function loadProductDetails(pageType) {
            var csrf_token = $("#csrf_token").val();
            // save page
            if (pageType == 'savePage') {
                var subCategory = $('#subCategory option:selected').val();
                var MainCategory = $('#MainCategory option:selected').val();

                if (subCategory == 0) {
                    swal("Sorry!", "Select Product!", "warning");
                } else if (MainCategory == 0) {
                    swal("Sorry!", "Select Main Category!", "warning");
                } else {
                    jQuery.ajax({
                        url: "{{ url('/loadProductDetails') }}",
                        type: "POST",
                        data: {
                            "_token": csrf_token,
                            "subCategory": subCategory,
                            "MainCategory": MainCategory,
                            "url": "Stock.ajaxStock.loadProductDetails",
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
                            $('#productDetailsLoadingDiv').html(data);
                            loadExpiryDate(subCategory, MainCategory, pageType);
                        }
                    });
                }
            } else { // update page
                var subCategory = $('#MODAL_SUB_CATEGORY option:selected').val();
                var MainCategory = $('#MODAL_MAIN_CATEGORY option:selected').val();

                if (subCategory == 0) {
                    swal("Sorry!", "Select Product!", "warning");
                } else if (MainCategory == 0) {
                    swal("Sorry!", "Select Main Category!", "warning");
                } else {
                    jQuery.ajax({
                        url: "{{ url('/loadProductDetails') }}",
                        type: "POST",
                        data: {
                            "_token": csrf_token,
                            "subCategory": subCategory,
                            "MainCategory": MainCategory,
                            "url": "Stock.ajaxStock.loadProductDetails",
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
                            $('#MODAL_productDetailsLoadingDiv').html(data);
                            loadExpiryDate(subCategory, MainCategory, pageType);
                        }
                    });
                }
            }
        }


        function loadExpiryDate(subCategory, MainCategory, pageType) {
            var csrf_token = $("#csrf_token").val();
            if (pageType == 'savePage') {
                jQuery.ajax({
                    url: "{{ url('/loadProductDetails') }}",
                    type: "POST",
                    data: {
                        "_token": csrf_token,
                        "subCategory": subCategory,
                        "MainCategory": MainCategory,
                        "url": "Stock.ajaxStock.loadProductExpiryDate",
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
                        $('#productExpiryDateLoadingDiv').html(data);
                    }
                });
            } else {
                jQuery.ajax({
                    url: "{{ url('/loadProductDetails') }}",
                    type: "POST",
                    data: {
                        "_token": csrf_token,
                        "subCategory": subCategory,
                        "MainCategory": MainCategory,
                        "url": "Stock.ajaxStock.loadProductExpiryDate",
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
                        $('#MODAL_productExpiryDateLoadingDiv').html(data);
                    }
                });
            }
        }


        function loadstockUpdateModal(batchId, mainCategoryId) {
            var csrf_token = $("#csrf_token").val();
            jQuery.ajax({
                url: "{{ url('/loadstockUpdateData') }}",
                type: "POST",
                data: {
                    "_token": csrf_token,
                    "batchId": batchId,
                    "mainCategoryId": mainCategoryId,
                    "url": "Stock.ajaxStock.loadStockUpdateDataToModal",
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
                    $('#stockUpdateArea').html(data);
                }
            });
        }


        function searchByDateStockIn() {
            var csrf_token = $("#csrf_token").val();
            var dateSelect = $("#dateSelect").val();

            if (dateSelect == '') {
                swal("", "Please select a Date.", "warning");
            } else {
                jQuery.ajax({
                    url: "{{ url('/searchByDateStockIn') }}",
                    type: "POST",
                    data: {
                        "_token": csrf_token,
                        "dateSelect": dateSelect,
                    },
                    beforeSend: function() {
                        showLder();
                    },
                    complete: function() {
                    },
                    error: function(data) {
                    },
                    success: function(data) {
                        $('#stockInTbody').html(data);
                        hideLder();
                    }
                });
            }
        }
    </script>
@endsection

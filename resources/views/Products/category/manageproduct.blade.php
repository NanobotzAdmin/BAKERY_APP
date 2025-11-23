@php
    $privilageId = \DB::table('pm_interfaces')
        ->select('pm_interfaces.id AS pageId', 'pm_interface_topic.id AS grupId')
        ->join('pm_interface_topic', 'pm_interfaces.pm_interface_topic_id', '=', 'pm_interface_topic.id')
        ->where('pm_interfaces.path', 'adminProductManagement')
        ->first();
@endphp

@extends('layout', ['pageId' => $privilageId->pageId, 'grupId' => $privilageId->grupId])

@section('content')

    <style>
        .table-hover tbody tr:hover {
            background-color: #faf6ec;
            color: #000;
            /* Light blue color - adjust as needed */
            transition: background-color 0.2s;
            /* Add a smooth transition effect */
        }

        .table th {
            text-align: center; /* Horizontally center the text */
            vertical-align: middle !important; /* Vertically center the text */
        }
    </style>

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-12">
            <h2><b>Manage Products</b></h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="/admindashboard">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <a>Product Management</a>
                </li>
                <li class="breadcrumb-item active">
                    <strong>Manage Products</strong>
                </li>
            </ol>
        </div>
    </div>
    <br>

    <div class="row">
        <div class="col-sm-12">
            <input type="hidden" id="csrf_token" value="{{ csrf_token() }}">
            @include('include.flash')
            @include('include.errors')

            <!-- Modal -->
            <div class="modal fade" id="mainProduct" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="exampleModalLabel">Create New Main Category</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="saveMainCategory" method="POST">
                            {{ csrf_field() }}
                            <div class="modal-body">

                                <div class="form-group">
                                    <label for="category"> Main Category Name</label>
                                    <input type="text" class="form-control" id="category" name="category" value="{{ old('category') }}" autocomplete="off">
                                </div>

                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Save</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Manage Main Categories</h5>
                </div>
                <div class="ibox-content">
                    <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#mainProduct">Create New Main Category</button>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <div class="row mb-3">
                            <div class="col-sm-12">
                                <a href="{{ url('/adminCategoryVariationManagement') }}" class="btn btn-info">Category & Variation Management</a>
                            </div>
                        </div>
                        <table class="table table-bordered table-hover dataTables-example" style="font-family: 'Lato', sans-serif;">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Main Category Name</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $id = 0; ?>
                                @foreach ($MainCategory as $category)
                                    <?php $id++; ?>
                                    <tr>
                                        <td><?php echo $id; ?></td>
                                        <td>{{ $category->main_category_name }}</td>
                                        @if ($category->is_active == 1)
                                            <td style="min-width: 90px; color: #1ab394; text-align: center;"><span
                                                    class="badge"
                                                    style="color: #28a745; background-color: #e2f5e6;">Active</span></td>
                                        @else
                                            <td style="min-width: 90px; color: #e70000; text-align: center;"><span
                                                    class="badge"
                                                    style="color: #dc3545; background-color: #fceff0;">Inactive</span></td>
                                        @endif
                                        <td>
                                            <button type="button" class="btn btn-outline-warning btn-xs"
                                                data-toggle="modal" data-target="#updateMainCategory"
                                                onclick="showCategoryUpdateModal({{ $category->id }},'mainCategory')"><i
                                                    class="fa fa-pencil-square-o" aria-hidden="true"></i>&nbsp;
                                                Update</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Modal -->
                    <div class="modal fade" id="updateMainCategory" tabindex="-1" role="dialog"
                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content" id="modelContentCategory">

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!------------ ////////////// SUB CATEGORY BEGIN HERE ///////////////////////// -->




    <div class="row">
        <div class="col-sm-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Manage Products</h5>
                </div>
                <div class="ibox-content">
                    <a href="{{ url('/adminProductRegistration') }}" class="btn btn-sm btn-primary">Product Registration</a>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <div class="row mb-3">
                            <div class="col-sm-12">
                                <a href="{{ url('/adminCategoryVariationManagement') }}" class="btn btn-info">Category & Variation Management</a>
                            </div>
                        </div>
                        <table class="table table-bordered table-hover dataTables-example" style="font-family: 'Lato', sans-serif;">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Product Name</th>
                                    <th>Main Category</th>
                                    <th>Sub Category</th>
                                    <th>Variation</th>
                                    <th>Variation Value</th>
                                    <th>Product Code</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $id = 0; ?>
                                @foreach ($productItems as $productItem)
                                    <?php $id++; ?>
                                    <tr>
                                        <td>{{ $id }}</td>
                                        <td>{{ $productItem->product_name }}</td>
                                        <td>{{ $productItem->mainCategory ? $productItem->mainCategory->main_category_name : 'N/A' }}</td>
                                        <td>{{ $productItem->subCategory ? $productItem->subCategory->sub_category_name : 'N/A' }}</td>
                                        <td>{{ $productItem->variation ? $productItem->variation->variation_name : 'N/A' }}</td>
                                        <td>{{ $productItem->variationValue ? ($productItem->variationValue->variation_value_name ? $productItem->variationValue->variation_value_name : $productItem->variationValue->variation_value) : 'N/A' }}</td>
                                        <td>{{ $productItem->product_code }}</td>
                                        @if ($productItem->status == App\STATIC_DATA_MODEL::$Active)
                                            <td style="min-width: 90px; color: #1ab394; text-align: center;"><span
                                                    class="badge"
                                                    style="color: #28a745; background-color: #e2f5e6;">Active</span></td>
                                        @else
                                            <td style="min-width: 90px; color: #e70000; text-align: center;"><span
                                                    class="badge"
                                                    style="color: #dc3545; background-color: #fceff0;">Inactive</span></td>
                                        @endif
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
                pageLength: 25,
                responsive: true,
                dom: '<"html5buttons"B>lTfgitp',
                buttons: []
            });
        });


        function showCategoryUpdateModal(CategoryId, categoryType) {
            var csrf_token = $("#csrf_token").val();
            jQuery.ajax({
                url: "{{ url('/loadCategoryDataToModal') }}",
                type: "POST",
                data: {
                    "_token": csrf_token,
                    "CategoryId": CategoryId,
                    "categoryType": categoryType
                },
                beforeSend: function() {
                    showLder();
                },
                complete: function() {},
                error: function(data) {},
                success: function(data) {
                    hideLder();
                    $('#modelContentCategory').html(data);
                }
            });
        }



    </script>
@endsection

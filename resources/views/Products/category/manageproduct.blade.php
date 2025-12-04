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
            transition: background-color 0.2s;
        }

        .table th {
            text-align: center;
            vertical-align: middle !important;
        }

        .product-type-selling {
            background-color: #e7f3ff;
            border-left: 4px solid #007bff;
        }

        .product-type-semifinished {
            background-color: #e7f3ff;
            border-left: 4px solid #007bff;
        }

        .badge-semifinished {
            background-color: #15850dff;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: 500;
        }


        .product-type-raw {
            background-color: #fff4e6;
            border-left: 4px solid #ff9800;
        }

        .badge-selling {
            background-color: #007bff;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: 500;
        }

        .badge-raw {
            background-color: #ff9800;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: 500;
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
        </div>
    </div>




    <div class="row">
        <div class="col-sm-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Manage Products</h5>
                </div>
                <div class="ibox-content">
                    <div class="row mb-3">
                        <div class="col-sm-6">
                            <a href="{{ url('/adminProductRegistration') }}" class="btn btn-sm btn-primary">
                                <i class="fa fa-plus"></i> Product Registration
                            </a>
                            <a href="{{ url('/adminCategoryVariationManagement') }}" class="btn btn-sm btn-info">
                                <i class="fa fa-cog"></i> Category & Variation Management
                            </a>
                        </div>
                        <div class="col-sm-6 text-right">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-outline-primary"
                                    onclick="filterProducts('all')">All</button>
                                <button type="button" class="btn btn-sm btn-outline-primary"
                                    onclick="filterProducts('selling')">Selling Products</button>
                                <button type="button" class="btn btn-sm btn-outline-primary"
                                    onclick="filterProducts('raw')">Raw Materials</button>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover dataTables-example" id="productsTable"
                            style="font-family: 'Lato', sans-serif;">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Product Type</th>
                                    <th>Product Item Name</th>
                                    <th>Bin Code</th>
                                    <th>Main Category</th>
                                    <th>Sub Category</th>
                                    <th>Variation</th>
                                    <th>Variation Value</th>
                                    <th>Selling Price</th>
                                    <th>Cost Price</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                                                $id = 0;
    $productItemTypes = [];
    foreach (\App\STATIC_DATA_MODEL::$productItemTypes as $type) {
        $productItemTypes[$type['id']] = $type['name'];
    }
                                                            ?>
                                @foreach ($productItems as $productItem)
                                                            <?php
                                    $id++;
                                    $productTypeId = $productItem->pm_product_item_type_id;
                                    $productTypeName = isset($productItemTypes[$productTypeId]) ? $productItemTypes[$productTypeId] : 'N/A';

                                    $isSelling = ($productTypeId == 1);
                                    $isRaw = ($productTypeId == 2); // Assuming type 2 is Raw Material based on original 'else' logic
                                    $isSemiFinished = ($productTypeId == 3);

                                    $rowClass = '';
                                    $badgeText = '';
                                    $badgeClass = '';

                                    if ($isSelling) {
                                        $rowClass = 'product-type-selling';
                                        $badgeText = 'Selling Product';
                                        $badgeClass = 'badge-selling';
                                    } elseif ($isRaw) {
                                        $rowClass = 'product-type-raw';
                                        $badgeText = 'Raw Material';
                                        $badgeClass = 'badge-raw';
                                    } elseif ($isSemiFinished) {
                                        $rowClass = 'product-type-semifinished'; // Add this class to your <style> block
                                        $badgeText = 'Semi-Finished Product';
                                        $badgeClass = 'badge-semifinished'; // Add this class to your <style> block
                                    } else {
                                        // Default for other types, if any
                                        $rowClass = '';
                                        $badgeText = 'Other';
                                        $badgeClass = 'badge-secondary';
                                    }
                                                                                                                                                                                                                                                                    ?>
                                                            <tr class="{{ $rowClass }}"
                                                                data-product-type="{{ $isSelling ? 'selling' : ($isRaw ? 'raw' : ($isSemiFinished ? 'semifinished' : 'other')) }}">
                                                                <td>{{ $id }}</td>
                                                                <td>
                                                                    <span class="{{ $badgeClass }}">{{ $badgeText }}</span>
                                                                </td>
                                                                <td><strong>{{ $productItem->product_item_name }}</strong></td>
                                                                <td>{{ $productItem->bin_code }}</td>
                                                                <td>{{ $productItem->mainCategory ? $productItem->mainCategory->main_category_name : 'N/A' }}
                                                                </td>
                                                                <td>{{ $productItem->subCategory ? $productItem->subCategory->sub_category_name : 'N/A' }}
                                                                </td>
                                                                <td>{{ $productItem->variation ? $productItem->variation->variation_name : 'N/A' }}</td>
                                                                <td>{{ $productItem->variationValue ? ($productItem->variationValue->variation_value_name ? $productItem->variationValue->variation_value_name : $productItem->variationValue->variation_value) : 'N/A' }}
                                                                </td>
                                                                <td>
                                                                    @if($isSelling && $productItem->selling_price)
                                                                        {{ number_format($productItem->selling_price, 2) }}
                                                                    @else
                                                                        <span class="text-muted">-</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if($isSelling && $productItem->cost_price)
                                                                        {{ number_format($productItem->cost_price, 2) }}
                                                                    @else
                                                                        <span class="text-muted">-</span>
                                                                    @endif
                                                                </td>
                                                                @if ($productItem->status == App\STATIC_DATA_MODEL::$Active)
                                                                    <td style="min-width: 90px; color: #1ab394; text-align: center;"><span class="badge"
                                                                            style="color: #28a745; background-color: #e2f5e6;">Active</span></td>
                                                                @else
                                                                    <td style="min-width: 90px; color: #e70000; text-align: center;"><span class="badge"
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
        var table;
        $(document).ready(function () {
            table = $('#productsTable').DataTable({
                pageLength: 25,
                responsive: true,
                dom: '<"html5buttons"B>lTfgitp',
                buttons: [],
                order: [[1, 'asc']] // Sort by Product Type
            });
        });

        function filterProducts(type) {
            if (type === 'all') {
                table.column(1).search('').draw();
            } else if (type === 'selling') {
                table.column(1).search('Selling Product').draw();
            } else if (type === 'raw') {
                table.column(1).search('Raw Material').draw();
            }
        }
    </script>
@endsection
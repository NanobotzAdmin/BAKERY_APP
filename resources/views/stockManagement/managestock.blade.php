@php
    $pageMeta = isset($privilageId) ? $privilageId : (object) ['pageId' => 'inventoryPage', 'grupId' => 'invGroup'];

    // DUMMY DATA: Simulating Database Logic
    // 'allocated' is calculated from pending orders (e.g., 5 orders need 5kg of flour total)
    $inventory = [
        [
            'id' => 101, 'name' => 'Wheat Flour (All Purpose)', 'category' => 'Dry Goods', 
            'current_stock' => 500, 'unit' => 'kg', 
            'allocated' => 45.5, // Reduced by active orders
            'reorder_level' => 100,
            'last_updated' => '2023-11-25'
        ],
        [
            'id' => 102, 'name' => 'White Sugar', 'category' => 'Dry Goods', 
            'current_stock' => 120, 'unit' => 'kg', 
            'allocated' => 30, 
            'reorder_level' => 100, // This will trigger LOW STOCK warning
            'last_updated' => '2023-11-24'
        ],
        [
            'id' => 103, 'name' => 'Butter (Unsalted)', 'category' => 'Cold Storage', 
            'current_stock' => 15, 'unit' => 'kg', 
            'allocated' => 12, 
            'reorder_level' => 20, // This will trigger CRITICAL warning
            'last_updated' => '2023-11-25'
        ],
        [
            'id' => 104, 'name' => 'Eggs (Large)', 'category' => 'Cold Storage', 
            'current_stock' => 1500, 'unit' => 'pcs', 
            'allocated' => 200, 
            'reorder_level' => 300,
            'last_updated' => '2023-11-25'
        ],
        [
            'id' => 105, 'name' => 'Vanilla Essence', 'category' => 'Flavoring', 
            'current_stock' => 5, 'unit' => 'L', 
            'allocated' => 0.2, 
            'reorder_level' => 2,
            'last_updated' => '2023-10-20'
        ],
    ];
@endphp

@extends('layout', ['pageId' => $pageMeta->pageId, 'grupId' => $pageMeta->grupId])

@section('content')

    <style>
        /* Status Indicators */
        .status-ok { border-left: 4px solid #1ab394; }
        .status-warning { border-left: 4px solid #f8ac59; background-color: #fdfbf7; }
        .status-critical { border-left: 4px solid #ed5565; background-color: #fff6f6; }
        
        /* Metric Badges */
        .badge-allocated { background-color: #e3f2fd; color: #0d47a1; border: 1px solid #bbdefb; font-weight: 600; padding: 4px 8px; border-radius: 4px; }
        .badge-stock { font-weight: 700; font-size: 1.1em; color: #333; }
        
        /* Progress Bar for Reorder Level */
        .progress-mini { height: 6px; margin-top: 5px; background-color: #e9ecef; border-radius: 3px; }
        .progress-bar { border-radius: 3px; }
    </style>

    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2><b>Material Stock & Reorder</b></h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/dashboard">Home</a></li>
                <li class="breadcrumb-item active"><strong>Inventory Management</strong></li>
            </ol>
        </div>
    </div>

    <div class="wrapper wrapper-content animated fadeInRight">
        
        {{-- KPI CARDS --}}
        <div class="row mb-4">
            <div class="col-lg-3">
                <div class="widget style1 navy-bg">
                    <div class="row">
                        <div class="col-4"><i class="fa fa-cubes fa-4x"></i></div>
                        <div class="col-8 text-right">
                            <span> Total Items </span>
                            <h2 class="font-bold">245</h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="widget style1 yellow-bg">
                    <div class="row">
                        <div class="col-4"><i class="fa fa-exclamation-triangle fa-4x"></i></div>
                        <div class="col-8 text-right">
                            <span> Low Stock </span>
                            <h2 class="font-bold">12 Items</h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="widget style1 lazur-bg">
                    <div class="row">
                        <div class="col-4"><i class="fa fa-shopping-cart fa-4x"></i></div>
                        <div class="col-8 text-right">
                            <span> Pending Allocation </span>
                            <h2 class="font-bold">Active</h2>
                        </div>
                    </div>
                </div>
            </div>
             <div class="col-lg-3">
                <div class="widget style1 red-bg">
                    <div class="row">
                        <div class="col-4"><i class="fa fa-ban fa-4x"></i></div>
                        <div class="col-8 text-right">
                            <span> Critical </span>
                            <h2 class="font-bold">3 Items</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- MAIN INVENTORY TABLE --}}
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Stock Balance Sheet</h5>
                        <div class="ibox-tools">
                            <a class="btn btn-primary btn-xs" href="#">Generate Purchase Order</a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        
                        {{-- Filters --}}
                        <div class="row mb-3">
                            <div class="col-sm-4">
                                <input type="text" placeholder="Search Material Name..." class="form-control">
                            </div>
                            <div class="col-sm-3">
                                <select class="form-control">
                                    <option value="">Status: All</option>
                                    <option value="critical">Critical Stock</option>
                                    <option value="low">Low Stock</option>
                                    <option value="ok">In Stock</option>
                                </select>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="25%">Raw Material</th>
                                        <th width="15%" class="text-center">Current Stock</th>
                                        <th width="15%" class="text-center">Allocated<br><small class="text-muted">(Pending Orders)</small></th>
                                        <th width="15%" class="text-center">Net Available</th>
                                        <th width="15%" class="text-center">Reorder Level</th>
                                        <th width="15%" class="text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($inventory as $item)
                                        @php
                                            // Calculation Logic
                                            $netAvailable = $item['current_stock'] - $item['allocated'];
                                            
                                            // Status Logic
                                            $rowClass = 'status-ok';
                                            $statusText = 'In Stock';
                                            $statusBadge = 'badge-primary';
                                            
                                            if ($netAvailable <= 0) {
                                                $rowClass = 'status-critical'; // Out of stock logic based on allocation
                                                $statusText = 'Over Allocated!';
                                                $statusBadge = 'badge-danger';
                                            } elseif ($netAvailable <= $item['reorder_level']) {
                                                $rowClass = 'status-warning';
                                                $statusText = 'Low Stock';
                                                $statusBadge = 'badge-warning';
                                            } elseif ($netAvailable <= ($item['reorder_level'] * 1.2)) {
                                                // Approaching reorder level logic could go here
                                                $rowClass = 'status-warning';
                                            }

                                            // Progress bar calculation
                                            $percent = ($item['current_stock'] > 0) 
                                                ? ($netAvailable / ($item['reorder_level'] * 3)) * 100 
                                                : 0;
                                            if($percent > 100) $percent = 100;
                                        @endphp

                                        <tr class="{{ $rowClass }}">
                                            <td>
                                                <h5 class="mb-0">{{ $item['name'] }}</h5>
                                                <small class="text-muted">{{ $item['category'] }} | Updated: {{ $item['last_updated'] }}</small>
                                            </td>
                                            
                                            {{-- Current Physical Stock --}}
                                            <td class="text-center" style="vertical-align: middle;">
                                                <span class="badge-stock">{{ $item['current_stock'] }}</span> <small>{{ $item['unit'] }}</small>
                                            </td>
                                            
                                            {{-- Allocated (Reduction from Orders) --}}
                                            <td class="text-center" style="vertical-align: middle;">
                                                @if($item['allocated'] > 0)
                                                    <span class="badge-allocated">
                                                        <i class="fa fa-arrow-down"></i> -{{ $item['allocated'] }} {{ $item['unit'] }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>

                                            {{-- Net Available --}}
                                            <td class="text-center" style="vertical-align: middle;">
                                                <span class="font-bold {{ $netAvailable < $item['reorder_level'] ? 'text-danger' : 'text-navy' }}" style="font-size: 1.2em;">
                                                    {{ $netAvailable }}
                                                </span> 
                                                <small>{{ $item['unit'] }}</small>
                                            </td>

                                            {{-- Reorder Level Visuals --}}
                                            <td class="text-center" style="vertical-align: middle;">
                                                <small>Min: <strong>{{ $item['reorder_level'] }} {{ $item['unit'] }}</strong></small>
                                                <div class="progress progress-mini">
                                                    <div style="width: {{ $percent }}%;" class="progress-bar {{ $netAvailable < $item['reorder_level'] ? 'bg-danger' : 'bg-primary' }}"></div>
                                                </div>
                                                @if($netAvailable < $item['reorder_level'])
                                                    <small class="text-danger font-bold"><i class="fa fa-warning"></i> Reorder Now</small>
                                                @endif
                                            </td>

                                            {{-- Actions --}}
                                            <td class="text-right" style="vertical-align: middle;">
                                                <button class="btn btn-white btn-sm" onclick="openAdjustModal({{ $item['id'] }}, '{{ $item['name'] }}', {{ $item['current_stock'] }}, '{{ $item['unit'] }}')">
                                                    <i class="fa fa-pencil"></i> Adjust
                                                </button>
                                                @if($netAvailable < $item['reorder_level'])
                                                    <button class="btn btn-outline btn-danger btn-sm">
                                                        <i class="fa fa-shopping-cart"></i> Order
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- STOCK ADJUSTMENT MODAL --}}
    <div class="modal fade" id="adjustStockModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Adjust Stock Level</h4>
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                </div>
                <form action="" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="product_id" id="modalProductId">
                        
                        <div class="text-center mb-3">
                            <h3 id="modalProductName" class="font-bold m-0">Product Name</h3>
                            <small class="text-muted">Current System Stock: <span id="modalCurrentStock">0</span> <span id="modalUnit">kg</span></small>
                        </div>

                        <div class="form-group">
                            <label>Adjustment Type</label>
                            <select class="form-control" name="type" id="adjustmentType">
                                <option value="add">Add Stock (Purchase/Return)</option>
                                <option value="subtract">Reduce Stock (Damage/Spillage)</option>
                                <option value="set">Set New Physical Count (Audit)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Quantity</label>
                            <input type="number" step="0.01" class="form-control" name="quantity" placeholder="Enter amount" required>
                        </div>

                        <div class="form-group">
                            <label>Reason / Note</label>
                            <textarea class="form-control" rows="2" placeholder="e.g. Expired goods removed"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Adjustment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openAdjustModal(id, name, stock, unit) {
            document.getElementById('modalProductId').value = id;
            document.getElementById('modalProductName').innerText = name;
            document.getElementById('modalCurrentStock').innerText = stock;
            document.getElementById('modalUnit').innerText = unit;
            
            $('#adjustStockModal').modal('show');
        }
    </script>

@endsection
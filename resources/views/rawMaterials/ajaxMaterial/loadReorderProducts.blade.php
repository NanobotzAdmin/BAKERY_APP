@php
    $privilageId = \DB::table('pm_interfaces')
    ->select('pm_interfaces.id AS pageId','pm_interface_topic.id AS grupId')
    ->join('pm_interface_topic', 'pm_interfaces.pm_interface_topic_id', '=', 'pm_interface_topic.id')
    ->where('pm_interfaces.path','adminRawMaterialManagement')
    ->first();
@endphp

@extends('layout', ['pageId' => $privilageId->pageId ,'grupId' => $privilageId->grupId ])
@section('content')

<div class="col-sm-12">
    <div class="ibox">
        <div class="ibox-content">
            <div class="table-responsive">
                <table class="table table-striped table-hover" style="font-family: Verdana, Geneva, sans-serif;">
                    <thead class="bg-info">
                        <tr>
                            <th>#</th>
                            <th>Material Name</th>
                            <th>Product</th>
                            <th>Available Quantity</th>
                            <th>Reorder Level</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $id = 0; ?>
                        @foreach ($materialList as $materials)
                        <?php
                            $id++;
                            $product = App\SubCategory::find($materials->pm_product_sub_category_id);
                        ?>
                        <tr>
                            <td>{{ $id }}</td>
                            <td>{{ $materials->material_name }}</td>
                            <td>{{ $product->sub_category_name }}</td>
                            <td>{{ $materials->available_count }}</td>
                            <td>{{ $materials->reorder_count }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@section('footer')
@endsection

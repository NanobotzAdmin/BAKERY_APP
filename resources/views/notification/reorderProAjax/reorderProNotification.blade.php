<a class="dropdown-toggle count-info" data-toggle="dropdown" href="#" style="color: black">
    <i class="fa fa-bell-o" id="bell_icon" style="color: #4a4846;"></i> <span class="label label-primary roundedCircle">{{ $notificationCount }}</span>
</a>

<ul class="dropdown-menu dropdown-alerts">
    @foreach ($materialList as $materials)
        <?php
        $product = App\SubCategory::find($materials->pm_product_sub_category_id);
        ?>
        <li>
            <a href="/adminLoadReorderProducts" class="dropdown-item">
                <div>
                    <i class="fa fa-envelope fa-fw"></i> {{ $product->sub_category_name }}
                    <span class="float-right text-muted small">{{ $materials->available_count }}</span>
                </div>
            </a>
        </li>
        <li class="dropdown-divider"></li>
    @endforeach
</ul>
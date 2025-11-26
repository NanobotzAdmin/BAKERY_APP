@php
    $pageMeta = isset($privilageId) ? $privilageId : (object) ['pageId' => 'posPage', 'grupId' => 'salesGroup'];

    // DUMMY PRODUCT DATA
    $products = [
        ['id' => 101, 'name' => 'Fish Bun', 'category' => 'short_eats', 'price' => 80, 'image' => 'fa-adjust', 'color' => '#f8ac59'],
        ['id' => 102, 'name' => 'Chicken Roll', 'category' => 'short_eats', 'price' => 120, 'image' => 'fa-circle', 'color' => '#f8ac59'],
        ['id' => 103, 'name' => 'Veggie Patty', 'category' => 'short_eats', 'price' => 90, 'image' => 'fa-leaf', 'color' => '#1ab394'],
        ['id' => 201, 'name' => 'Sandwich Bread', 'category' => 'breads', 'price' => 180, 'image' => 'fa-bars', 'color' => '#f8ac59'],
        ['id' => 202, 'name' => 'Roast Paan', 'category' => 'breads', 'price' => 100, 'image' => 'fa-fire', 'color' => '#ed5565'],
        ['id' => 301, 'name' => 'Butter Cake (500g)', 'category' => 'cakes', 'price' => 850, 'image' => 'fa-birthday-cake', 'color' => '#23c6c8'],
        ['id' => 302, 'name' => 'Choco Eclair', 'category' => 'cakes', 'price' => 150, 'image' => 'fa-star', 'color' => '#23c6c8'],
        ['id' => 401, 'name' => 'Iced Coffee', 'category' => 'beverages', 'price' => 200, 'image' => 'fa-coffee', 'color' => '#1c84c6'],
    ];
@endphp

@extends('layout', ['pageId' => $pageMeta->pageId, 'grupId' => $pageMeta->grupId])

@section('content')

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
{{-- SweetAlert2 CSS --}}
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

<style>
    /* MODERN POS CSS OVERRIDES */
    :root {
        --primary-color: #4f46e5; /* Indigo 600 */
        --primary-hover: #4338ca; /* Indigo 700 */
        --bg-color: #f3f4f6;
        --card-bg: #ffffff;
        --text-main: #111827;
        --text-muted: #6b7280;
        --success-color: #10b981;
        --danger-color: #ef4444;
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        --radius-lg: 16px;
        --radius-xl: 24px;
    }

    

    .pos-container {
        height: calc(100vh - 60px); /* Adjust based on your header height */
        padding: 20px;
    }

    /* Left Side: Catalog */
    .catalog-section {
        height: 100%;
        display: flex;
        flex-direction: column;
        padding-right: 20px;
    }

    .search-bar-container {
        background: var(--card-bg);
        padding: 16px;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-md);
        margin-bottom: 24px;
        border: 1px solid rgba(229, 231, 235, 0.5);
    }

    #posSearch {
        border: 1px solid #e5e7eb;
        background: #f9fafb;
        padding: 12px 16px;
        border-radius: 12px;
        width: 100%;
        font-size: 0.95rem;
        transition: all 0.2s;
    }
    #posSearch:focus { 
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        background: white;
    }

    .category-scroll {
        display: flex;
        gap: 12px;
        overflow-x: auto;
        padding-bottom: 5px;
        margin-bottom: 0;
    }
    
    .category-scroll::-webkit-scrollbar { height: 0px; } /* Hide scrollbar for cleaner look */

    .category-btn {
        border-radius: 50px;
        padding: 8px 24px;
        font-size: 0.9rem;
        font-weight: 500;
        border: 1px solid transparent;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        white-space: nowrap;
        cursor: pointer;
    }
    
    .category-btn.active {
        background-color: var(--primary-color);
        color: white;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        transform: translateY(-1px);
    }
    
    .category-btn:not(.active) {
        background-color: white;
        color: var(--text-muted);
        border: 1px solid #e5e7eb;
    }
    .category-btn:hover:not(.active) {
        background-color: #f9fafb;
        border-color: #d1d5db;
        color: var(--text-main);
    }

    .product-scroll-area {
        flex: 1;
        overflow-y: auto;
        padding-right: 5px;
        /* Custom Scrollbar */
        scrollbar-width: thin;
        scrollbar-color: #d1d5db transparent;
    }
    .product-scroll-area::-webkit-scrollbar { width: 6px; }
    .product-scroll-area::-webkit-scrollbar-track { background: transparent; }
    .product-scroll-area::-webkit-scrollbar-thumb { background-color: #d1d5db; border-radius: 20px; }

    /* Modern Product Card */
    .product-card {
        background: var(--card-bg);
        border-radius: var(--radius-lg);
        border: 1px solid rgba(229, 231, 235, 0.5);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        height: 100%;
        position: relative;
        overflow: hidden;
        box-shadow: var(--shadow-sm);
    }

    .product-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
        border-color: rgba(79, 70, 229, 0.2);
    }

    .product-card:active { transform: scale(0.96); }

    .product-img-box {
        height: 110px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        color: white;
        position: relative;
    }
    
    .product-img-box::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 40%;
        background: linear-gradient(to top, rgba(0,0,0,0.1), transparent);
    }

    .product-details {
        padding: 16px;
        text-align: center;
    }

    .product-name {
        font-weight: 600;
        color: var(--text-main);
        font-size: 1rem;
        margin-bottom: 6px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .product-price {
        color: var(--primary-color);
        font-weight: 700;
        font-size: 1.1rem;
    }

    /* Right Side: Cart */
    .cart-panel {
        background: var(--card-bg);
        border-radius: var(--radius-xl);
        height: 100%;
        display: flex;
        flex-direction: column;
        box-shadow: var(--shadow-lg);
        overflow: hidden;
        border: 1px solid rgba(229, 231, 235, 0.5);
    }

    .cart-header {
        padding: 24px;
        border-bottom: 1px solid #f3f4f6;
        background: #fff;
    }

    .cart-items {
        flex: 1;
        overflow-y: auto;
        padding: 0 24px;
        scrollbar-width: thin;
        /* max-height: 400px; */
    }

    .cart-item-row {
        display: flex;
        align-items: center;
        padding: 16px 0;
        border-bottom: 1px dashed #e5e7eb;
        animation: slideInRight 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .qty-btn {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        border: 1px solid #e5e7eb;
        background: white;
        color: var(--text-main);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 0.8rem;
    }
    .qty-btn:hover { 
        background: var(--primary-color); 
        border-color: var(--primary-color); 
        color: white; 
        transform: scale(1.1);
    }

    .cart-footer {
        padding: 24px;
        background: #f9fafb;
        border-top: 1px solid #e5e7eb;
    }

    .total-row {
        font-size: 1.4rem;
        font-weight: 800;
        color: var(--text-main);
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
        align-items: center;
    }

    #btnPay {
        background: var(--success-color);
        border: none;
        border-radius: 16px;
        padding: 16px;
        font-size: 1.1rem;
        font-weight: 600;
        letter-spacing: 0.5px;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        transition: all 0.3s;
        width: 100%;
    }
    #btnPay:hover:not(:disabled) { 
        background: #059669; 
        transform: translateY(-2px); 
        box-shadow: 0 6px 15px rgba(16, 185, 129, 0.4);
    }
    #btnPay:disabled { 
        background: #d1d5db; 
        box-shadow: none; 
        transform: none; 
        cursor: not-allowed;
    }

    .btn-trash {
        border-radius: 16px;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #fee2e2;
        background: #fef2f2;
        color: var(--danger-color);
        transition: all 0.2s;
    }
    .btn-trash:hover {
        background: var(--danger-color);
        color: white;
        border-color: var(--danger-color);
    }

    @keyframes slideInRight {
        from { opacity: 0; transform: translateX(20px); }
        to { opacity: 1; transform: translateX(0); }
    }
</style>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row h-100 pos-container">
        
        {{-- LEFT COLUMN: CATALOG --}}
        <div class="col-md-8 catalog-section">
            
            {{-- Search & Categories --}}
            <div class="search-bar-container">
                <div class="row align-items-center">
                    <div class="col-md-5">
                        <div class="position-relative">
                            <i class="fa fa-search position-absolute text-muted" style="top:14px; left:16px;"></i>
                            <input type="text" id="posSearch" class="form-control" style="padding-left: 44px;" placeholder="Search item or scan barcode...">
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="category-scroll text-right">
                            <button class="btn category-btn active" onclick="filterPos('all')">All Items</button>
                            <button class="btn category-btn" onclick="filterPos('short_eats')">🍔 Short Eats</button>
                            <button class="btn category-btn" onclick="filterPos('breads')">🍞 Breads</button>
                            <button class="btn category-btn" onclick="filterPos('cakes')">🍰 Cakes</button>
                            <button class="btn category-btn" onclick="filterPos('beverages')">🥤 Drinks</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Product Grid --}}
            <div class="product-scroll-area" id="productGrid">
                <div class="row">
                    @foreach($products as $p)
                        <div class="col-lg-3 col-md-4 col-6 mb-4 product-item" data-category="{{ $p['category'] }}" data-name="{{ strtolower($p['name']) }}">
                            <div class="product-card" onclick="addToCart({{ $p['id'] }}, '{{ $p['name'] }}', {{ $p['price'] }})">
                                {{-- Using the dynamic color with a slight transparency for the background --}}
                                <div class="product-img-box" style="background: linear-gradient(135deg, {{ $p['color'] }} 0%, {{ $p['color'] }}dd 100%);">
                                    <i class="fa {{ $p['image'] }}"></i>
                                </div>
                                <div class="product-details">
                                    <div class="product-name" title="{{ $p['name'] }}">{{ $p['name'] }}</div>
                                    <div class="product-price">Rs {{ number_format($p['price'], 0) }}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN: BILLING CART --}}
        <div class="col-md-4 h-100">
            <div class="cart-panel">
                
                {{-- Header --}}
                <div class="cart-header">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="m-0 font-weight-bold" style="font-size: 1.2rem;">Current Order</h5>
                        <span class="badge badge-light border text-dark p-2 rounded-pill">#882</span>
                    </div>
                    <select class="form-control border-0 bg-light rounded-pill px-3" style="font-size: 0.9rem; height: 40px;">
                        <option>👤 Normal Customer</option>
                        <option>⭐ Loyalty Customer</option>
                        <option>🛵 UberEats</option>
                    </select>
                </div>

                {{-- Items List --}}
                <div class="cart-items" id="cartItemsContainer">
                    {{-- Empty State --}}
                    <div class="text-center text-muted h-100 d-flex flex-column justify-content-center align-items-center">
                        <div style="background:#f3f4f6; padding:30px; border-radius:50%; margin-bottom:20px;">
                            <i class="fa fa-shopping-basket fa-3x text-secondary" style="opacity: 0.3;"></i>
                        </div>
                        <p class="font-weight-bold mb-1" style="font-size: 1.1rem;">Your cart is empty</p>
                        <small>Tap on items to start your order</small>
                    </div>
                </div>

                {{-- Footer / Totals --}}
                <div class="cart-footer">
                    <div class="d-flex justify-content-between mb-2 text-muted" style="font-size: 0.95rem;">
                        <span>Subtotal</span>
                        <span id="subTotal" style="font-weight: 500;">0.00</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 text-muted" style="font-size: 0.95rem;">
                        <span>Tax / VAT (0%)</span>
                        <span>0.00</span>
                    </div>
                    
                    <div class="total-row">
                        <span>Total</span>
                        <span style="color: var(--primary-color);">Rs <span id="grandTotal">0.00</span></span>
                    </div>
                    
                    <div class="row">
                        <div class="col-3 pr-1">
                            <button class="btn btn-trash btn-block" onclick="clearCart()">
                                <i class="fa fa-trash-alt fa-lg"></i>
                            </button>
                        </div>
                        <div class="col-9 pl-1">
                            <button class="btn btn-primary btn-block font-bold" data-toggle="modal" data-target="#paymentModal" id="btnPay" disabled>
                                PAY NOW <i class="fa fa-chevron-right ml-2"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- PAYMENT MODAL (Cleaned up) --}}
<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="border-radius: 24px; border:none; overflow: hidden;">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title font-weight-bold">Checkout</h5>
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body px-4 pb-4">
                <div class="text-center mb-4 mt-2">
                    <small class="text-uppercase text-muted font-weight-bold" style="letter-spacing: 1px;">Total Payable</small>
                    <h1 class="text-success font-weight-bold m-0 mt-1">Rs <span id="modalTotal">0.00</span></h1>
                </div>
                
                <div class="form-group mb-4">
                    <label class="small font-weight-bold text-muted mb-2">PAYMENT METHOD</label>
                    <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                        <label class="btn btn-outline-secondary active py-2" style="border-radius: 12px 0 0 12px;">
                            <input type="radio" name="options" id="option1" checked> 💵 Cash
                        </label>
                        <label class="btn btn-outline-secondary py-2" style="border-radius: 0 12px 12px 0;">
                            <input type="radio" name="options" id="option2"> 💳 Card
                        </label>
                    </div>
                </div>

                <div class="form-group mb-4">
                    <label class="small font-weight-bold text-muted mb-2">CASH RECEIVED</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-light border-0 font-weight-bold">Rs</span>
                        </div>
                        <input type="number" class="form-control form-control-lg text-center font-weight-bold border-light bg-light" id="cashReceived" placeholder="0.00" style="border-radius: 0 12px 12px 0;">
                    </div>
                </div>
                
                <div class="p-3 bg-light rounded-lg text-center" style="border-radius: 16px;">
                    <small class="text-muted">Change to Return</small>
                    <h3 class="font-weight-bold m-0 text-dark mt-1">Rs <span id="balanceAmount">0.00</span></h3>
                </div>
            </div>
            <div class="modal-footer border-0 px-4 pb-4 pt-0">
                <button type="button" class="btn btn-primary btn-block btn-lg font-weight-bold" style="border-radius: 16px; padding: 14px;" onclick="completeOrder()">Complete Order</button>
            </div>
        </div>
    </div>
</div>

{{-- JAVASCRIPT LOGIC --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
{{-- SweetAlert2 JS --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Global Cart Array
    var cart = [];

    // 1. ADD TO CART
    function addToCart(id, name, price) {
        let existingItem = cart.find(item => item.id === id);

        if (existingItem) {
            existingItem.qty += 1;
        } else {
            cart.push({ id: id, name: name, price: price, qty: 1 });
        }
        renderCart();
        
        // Optional: Toast notification
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 1500,
            timerProgressBar: false,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        })

        Toast.fire({
            icon: 'success',
            title: name + ' added'
        })
    }

    // 2. RENDER CART HTML
    function renderCart() {
        let html = '';
        let total = 0;

        if (cart.length === 0) {
            $('#cartItemsContainer').html(`
                <div class="text-center text-muted h-100 d-flex flex-column justify-content-center align-items-center">
                    <div style="background:#f3f4f6; padding:30px; border-radius:50%; margin-bottom:20px;">
                        <i class="fa fa-shopping-basket fa-3x text-secondary" style="opacity: 0.3;"></i>
                    </div>
                    <p class="font-weight-bold mb-1" style="font-size: 1.1rem;">Your cart is empty</p>
                    <small>Tap on items to start your order</small>
                </div>
            `);
            $('#subTotal').text('0.00');
            $('#grandTotal').text('0.00');
            $('#btnPay').prop('disabled', true);
            return;
        }

        cart.forEach((item, index) => {
            let itemTotal = item.price * item.qty;
            total += itemTotal;
            
            html += `
                <div class="cart-item-row">
                    <div style="flex:1;">
                        <div style="font-weight:600; font-size: 0.95rem; color: var(--text-main);">${item.name}</div>
                        <div style="font-size:12px; color: var(--text-muted);">Rs ${item.price} / unit</div>
                    </div>
                    <div style="display:flex; align-items:center; gap: 10px;">
                        <div class="qty-btn" onclick="updateQty(${index}, -1)"><i class="fa fa-minus"></i></div>
                        <span style="font-weight:bold; min-width:24px; text-align:center;">${item.qty}</span>
                        <div class="qty-btn" onclick="updateQty(${index}, 1)"><i class="fa fa-plus"></i></div>
                    </div>
                    <div style="width: 80px; text-align:right; font-weight:bold; color: var(--text-main);">
                        ${itemTotal.toFixed(0)}
                    </div>
                    <div style="margin-left: 15px;">
                        <i class="fa fa-times text-danger" style="cursor:pointer; opacity:0.5; transition:0.2s;" onmouseover="this.style.opacity=1" onmouseout="this.style.opacity=0.5" onclick="removeItem(${index})"></i>
                    </div>
                </div>
            `;
        });

        $('#cartItemsContainer').html(html);
        $('#subTotal').text(total.toFixed(2));
        $('#grandTotal').text(total.toFixed(2));
        $('#modalTotal').text(total.toFixed(2));
        $('#btnPay').prop('disabled', false);
    }

    // 3. UPDATE QUANTITY
    function updateQty(index, delta) {
        cart[index].qty += delta;
        if (cart[index].qty <= 0) {
            removeItem(index); // Use removeItem to trigger confirmation if needed, or just splice
            return;
        }
        renderCart();
    }

    // 4. REMOVE ITEM
    function removeItem(index) {
        // Optional: Confirm before removing single item? Maybe too annoying for POS.
        // Just remove it for speed.
        cart.splice(index, 1);
        renderCart();
    }

    // 5. CLEAR CART
    function clearCart() {
        if (cart.length === 0) return;

        Swal.fire({
            title: 'Clear Order?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, clear it!',
            borderRadius: '16px'
        }).then((result) => {
            if (result.isConfirmed) {
                cart = [];
                renderCart();
                Swal.fire(
                    'Cleared!',
                    'The order has been cleared.',
                    'success'
                )
            }
        })
    }

    // 6. FILTER LOGIC
    function filterPos(category) {
        $('.category-btn').removeClass('active');
        $(event.target).addClass('active');

        if (category === 'all') {
            $('.product-item').fadeIn(200);
        } else {
            $('.product-item').hide();
            $('.product-item[data-category="' + category + '"]').fadeIn(200);
        }
    }

    // 7. SEARCH LOGIC
    $('#posSearch').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $(".product-item").filter(function() {
            $(this).toggle($(this).data('name').indexOf(value) > -1)
        });
    });

    // 8. CALCULATE BALANCE (MODAL)
    $('#cashReceived').on('keyup', function() {
        let total = parseFloat($('#modalTotal').text().replace(/,/g, ''));
        let received = parseFloat($(this).val()) || 0;
        let balance = received - total;
        $('#balanceAmount').text(balance.toFixed(2));
        
        if(balance < 0) $('#balanceAmount').addClass('text-danger').removeClass('text-success');
        else $('#balanceAmount').removeClass('text-danger').addClass('text-success');
    });

    // 9. COMPLETE ORDER
    function completeOrder() {
        // Close Modal
        $('#paymentModal').modal('hide');

        Swal.fire({
            title: 'Order Completed!',
            text: 'Receipt has been sent to printer.',
            icon: 'success',
            confirmButtonColor: '#10b981',
            confirmButtonText: 'Start New Order'
        }).then((result) => {
            if (result.isConfirmed) {
                cart = [];
                renderCart();
                // Reset modal inputs
                $('#cashReceived').val('');
                $('#balanceAmount').text('0.00').removeClass('text-danger text-success');
            }
        });
    }

</script>
@endsection
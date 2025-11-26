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

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

<style>
    /* MODERN POS CSS OVERRIDES */
    body {
        font-family: 'Poppins', sans-serif;
        background-color: #f3f4f6;
        overflow: hidden; /* Prevent body scroll, handle inside containers */
        height: 100vh;
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
        padding-right: 15px;
    }

    .search-bar-container {
        background: white;
        padding: 15px;
        border-radius: 15px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        margin-bottom: 20px;
    }

    #posSearch {
        border: none;
        background: #f9fafb;
        padding: 12px 15px;
        border-radius: 10px;
        width: 100%;
        font-size: 0.95rem;
    }
    #posSearch:focus { outline: 2px solid #6366f1; }

    .category-scroll {
        display: flex;
        gap: 10px;
        overflow-x: auto;
        padding-bottom: 5px;
        margin-bottom: 15px;
    }
    
    .category-scroll::-webkit-scrollbar { height: 4px; }
    .category-scroll::-webkit-scrollbar-thumb { background: #ccc; border-radius: 4px; }

    .category-btn {
        border-radius: 25px;
        padding: 8px 20px;
        font-size: 0.9rem;
        font-weight: 500;
        border: 1px solid transparent;
        transition: all 0.3s ease;
        white-space: nowrap;
    }
    
    .category-btn.active {
        background-color: #3b82f6; /* Modern Blue */
        color: white;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
    }
    
    .category-btn:not(.active) {
        background-color: white;
        color: #6b7280;
        border: 1px solid #e5e7eb;
    }
    .category-btn:hover:not(.active) {
        background-color: #f3f4f6;
        transform: translateY(-1px);
    }

    .product-scroll-area {
        flex: 1;
        overflow-y: auto;
        padding-right: 5px;
    }

    /* Modern Product Card */
    .product-card {
        background: white;
        border-radius: 16px;
        border: none;
        transition: all 0.25s ease;
        cursor: pointer;
        height: 100%;
        position: relative;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }

    .product-card:active { transform: scale(0.98); }

    .product-img-box {
        height: 100px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        color: white;
        opacity: 0.9;
    }

    .product-details {
        padding: 12px;
        text-align: center;
    }

    .product-name {
        font-weight: 600;
        color: #374151;
        font-size: 0.95rem;
        margin-bottom: 4px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .product-price {
        color: #3b82f6;
        font-weight: 700;
        font-size: 1rem;
    }

    /* Right Side: Cart */
    .cart-panel {
        background: white;
        border-radius: 20px;
        height: 100%;
        display: flex;
        flex-direction: column;
        box-shadow: -5px 0 25px rgba(0,0,0,0.05);
        overflow: hidden;
    }

    .cart-header {
        padding: 20px;
        border-bottom: 1px solid #f3f4f6;
        background: #fff;
    }

    .cart-items {
        flex: 1;
        overflow-y: auto;
        padding: 0 20px;
    }

    .cart-item-row {
        display: flex;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px dashed #e5e7eb;
        animation: fadeIn 0.3s ease;
    }

    .qty-btn {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        border: 1px solid #e5e7eb;
        background: white;
        color: #374151;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: 0.2s;
    }
    .qty-btn:hover { background: #f3f4f6; border-color: #d1d5db; }

    .cart-footer {
        padding: 20px;
        background: #f9fafb;
        border-top: 1px solid #e5e7eb;
    }

    .total-row {
        font-size: 1.2rem;
        font-weight: 700;
        color: #111827;
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
    }

    #btnPay {
        background: #10b981; /* Emerald Green */
        border: none;
        border-radius: 12px;
        padding: 15px;
        font-size: 1rem;
        letter-spacing: 0.5px;
        box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3);
        transition: 0.3s;
    }
    #btnPay:hover { background: #059669; transform: translateY(-2px); }
    #btnPay:disabled { background: #d1d5db; box-shadow: none; transform: none; }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateX(10px); }
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
                            <i class="fa fa-search position-absolute text-muted" style="top:12px; left:15px;"></i>
                            <input type="text" id="posSearch" class="form-control" style="padding-left: 40px;" placeholder="Search item or scan barcode...">
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
                                <div class="product-img-box" style="background: linear-gradient(135deg, {{ $p['color'] }} 0%, {{ $p['color'] }}aa 100%);">
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
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="m-0 font-weight-bold">Current Order</h5>
                        <span class="badge badge-light border text-dark p-2">#882</span>
                    </div>
                    <select class="form-control border-0 bg-light" style="font-size: 0.9rem;">
                        <option>👤 Walk-in Customer</option>
                        <option>⭐ Loyalty Customer</option>
                        <option>🛵 UberEats</option>
                    </select>
                </div>

                {{-- Items List --}}
                <div class="cart-items" id="cartItemsContainer">
                    {{-- Empty State --}}
                    <div class="text-center text-muted h-100 d-flex flex-column justify-content-center align-items-center">
                        <div style="background:#f3f4f6; padding:30px; border-radius:50%; margin-bottom:20px;">
                            <i class="fa fa-shopping-basket fa-3x text-secondary"></i>
                        </div>
                        <p class="font-weight-bold">No items added yet</p>
                        <small>Click on items to add them to cart</small>
                    </div>
                </div>

                {{-- Footer / Totals --}}
                <div class="cart-footer">
                    <div class="d-flex justify-content-between mb-1 text-muted" style="font-size: 0.9rem;">
                        <span>Subtotal</span>
                        <span id="subTotal">0.00</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 text-muted" style="font-size: 0.9rem;">
                        <span>Tax / VAT (0%)</span>
                        <span>0.00</span>
                    </div>
                    
                    <div class="total-row">
                        <span>Total</span>
                        <span>Rs <span id="grandTotal">0.00</span></span>
                    </div>
                    
                    <div class="row">
                        <div class="col-4 pr-1">
                            <button class="btn btn-outline-danger btn-block py-3" style="border-radius: 12px;" onclick="clearCart()">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                        <div class="col-8 pl-1">
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
        <div class="modal-content" style="border-radius: 20px; border:none;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title font-weight-bold">Checkout</h5>
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <small class="text-uppercase text-muted font-weight-bold">Total Payable</small>
                    <h1 class="text-success font-weight-bold m-0">Rs <span id="modalTotal">0.00</span></h1>
                </div>
                
                <div class="form-group">
                    <label class="small font-weight-bold text-muted">PAYMENT METHOD</label>
                    <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                        <label class="btn btn-outline-secondary active">
                            <input type="radio" name="options" id="option1" checked> 💵 Cash
                        </label>
                        <label class="btn btn-outline-secondary">
                            <input type="radio" name="options" id="option2"> 💳 Card
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="small font-weight-bold text-muted">CASH RECEIVED</label>
                    <input type="number" class="form-control form-control-lg text-center font-weight-bold" id="cashReceived" placeholder="0.00">
                </div>
                
                <div class="p-3 bg-light rounded text-center">
                    <small class="text-muted">Change to Return</small>
                    <h3 class="font-weight-bold m-0 text-dark">Rs <span id="balanceAmount">0.00</span></h3>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-primary btn-block btn-lg" style="border-radius: 12px;" onclick="alert('Receipt Printed! Order Saved.')">Complete Order</button>
            </div>
        </div>
    </div>
</div>

{{-- JAVASCRIPT LOGIC --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
    }

    // 2. RENDER CART HTML
    function renderCart() {
        let html = '';
        let total = 0;

        if (cart.length === 0) {
            $('#cartItemsContainer').html(`
                <div class="text-center text-muted h-100 d-flex flex-column justify-content-center align-items-center">
                    <div style="background:#f3f4f6; padding:30px; border-radius:50%; margin-bottom:20px;">
                        <i class="fa fa-shopping-basket fa-3x text-secondary"></i>
                    </div>
                    <p class="font-weight-bold">No items added yet</p>
                    <small>Click on items to add them to cart</small>
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
                        <div style="font-weight:600; font-size: 0.95rem;">${item.name}</div>
                        <div style="font-size:11px; color:#888;">Rs ${item.price} / unit</div>
                    </div>
                    <div style="display:flex; align-items:center; gap: 8px;">
                        <div class="qty-btn" onclick="updateQty(${index}, -1)"><i class="fa fa-minus" style="font-size:10px;"></i></div>
                        <span style="font-weight:bold; min-width:20px; text-align:center;">${item.qty}</span>
                        <div class="qty-btn" onclick="updateQty(${index}, 1)"><i class="fa fa-plus" style="font-size:10px;"></i></div>
                    </div>
                    <div style="width: 70px; text-align:right; font-weight:bold; color: #374151;">
                        ${itemTotal.toFixed(0)}
                    </div>
                    <div style="margin-left: 10px;">
                        <i class="fa fa-times text-danger" style="cursor:pointer; opacity:0.5;" onmouseover="this.style.opacity=1" onmouseout="this.style.opacity=0.5" onclick="removeItem(${index})"></i>
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
            cart.splice(index, 1);
        }
        renderCart();
    }

    // 4. REMOVE ITEM
    function removeItem(index) {
        cart.splice(index, 1);
        renderCart();
    }

    // 5. CLEAR CART
    function clearCart() {
        if(confirm('Are you sure you want to clear the order?')) {
            cart = [];
            renderCart();
        }
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

</script>
@endsection
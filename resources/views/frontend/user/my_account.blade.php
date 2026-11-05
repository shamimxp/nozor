@extends('frontend.layouts.app')
@section('content')
    @php
        $orderCount = $orders->count();
        $pendingCount = $orders->whereIn('status', ['pending', 'processing', 'confirmed'])->count();
        $latestOrder = $orders->first();
    @endphp

    <div class="page-header breadcrumb-wrap">
        <div class="container">
            <div class="breadcrumb">
                <a href="{{ url('/') }}" rel="nofollow"><i class="fi-rs-home mr-5"></i>Home</a>
                <span></span> Pages <span></span> My Account
            </div>
        </div>
    </div>

    <section class="wm-account-page">
        <div class="container">
            <div class="wm-account-shell">
                <aside class="wm-account-nav" aria-label="Account navigation">
                    <div class="wm-account-nav__brand">
                        <img
                            src="{{ asset('admin/app-assets/images/logo/edited_red_letters.svg') }}"
                            alt="WOOD Machinery"
                            class="wm-account-nav__logo"
                        >
                        <p class="wm-account-nav__chip">Customer account</p>
                        <h2 class="wm-account-nav__name">{{ $user->name ?? 'User' }}</h2>
                        <p class="wm-account-nav__meta">{{ $user->phone ?? '' }}</p>
                    </div>

                    <ul class="nav flex-column wm-account-menu" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="dashboard-tab" data-bs-toggle="tab" href="#dashboard" role="tab" aria-controls="dashboard" aria-selected="true">
                                <i class="fi-rs-settings-sliders"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="orders-tab" data-bs-toggle="tab" href="#orders" role="tab" aria-controls="orders" aria-selected="false">
                                <i class="fi-rs-shopping-bag"></i>
                                <span>Orders</span>
                                @if($orderCount > 0)
                                    <em class="wm-account-menu__count">{{ $orderCount }}</em>
                                @endif
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="track-orders-tab" data-bs-toggle="tab" href="#track-orders" role="tab" aria-controls="track-orders" aria-selected="false">
                                <i class="fi-rs-shopping-cart-check"></i>
                                <span>Track Your Order</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="account-detail-tab" data-bs-toggle="tab" href="#account-detail" role="tab" aria-controls="account-detail" aria-selected="false">
                                <i class="fi-rs-user"></i>
                                <span>Account details</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link wm-account-menu__logout" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fi-rs-sign-out"></i>
                                <span>Logout</span>
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </aside>

                <div class="wm-account-main">
                    <div class="tab-content account dashboard-content">
                        <div class="tab-pane fade active show" id="dashboard" role="tabpanel" aria-labelledby="dashboard-tab">
                            <div class="wm-account-panel">
                                <div class="wm-account-panel__header">
                                    <p class="wm-account-kicker">Overview</p>
                                    <h3>Hello {{ $user->name ?? 'User' }}!</h3>
                                    <p class="wm-account-lead">
                                        From your account dashboard you can check
                                        <a href="#orders" data-bs-toggle="tab" data-bs-target="#orders" class="wm-account-inline-link" onclick="document.getElementById('orders-tab').click(); return false;">recent orders</a>,
                                        <a href="#track-orders" data-bs-toggle="tab" data-bs-target="#track-orders" class="wm-account-inline-link" onclick="document.getElementById('track-orders-tab').click(); return false;">track deliveries</a>,
                                        and
                                        <a href="#account-detail" data-bs-toggle="tab" data-bs-target="#account-detail" class="wm-account-inline-link" onclick="document.getElementById('account-detail-tab').click(); return false;">edit your account details</a>.
                                    </p>
                                </div>

                                <div class="wm-account-stats">
                                    <article class="wm-account-stat">
                                        <span class="wm-account-stat__label">Total orders</span>
                                        <strong class="wm-account-stat__value">{{ $orderCount }}</strong>
                                    </article>
                                    <article class="wm-account-stat">
                                        <span class="wm-account-stat__label">In progress</span>
                                        <strong class="wm-account-stat__value">{{ $pendingCount }}</strong>
                                    </article>
                                    <article class="wm-account-stat wm-account-stat--wide">
                                        <span class="wm-account-stat__label">Latest order</span>
                                        <strong class="wm-account-stat__value wm-account-stat__value--sm">
                                            @if($latestOrder)
                                                {{ $latestOrder->invoice_no }}
                                            @else
                                                No orders yet
                                            @endif
                                        </strong>
                                    </article>
                                </div>

                                <div class="wm-account-actions">
                                    <button type="button" class="wm-account-action" onclick="document.getElementById('orders-tab').click();">
                                        <span class="wm-account-action__icon" aria-hidden="true"><i class="fi-rs-shopping-bag"></i></span>
                                        <span>
                                            <strong>View orders</strong>
                                            <small>Invoice history &amp; status</small>
                                        </span>
                                    </button>
                                    <button type="button" class="wm-account-action" onclick="document.getElementById('track-orders-tab').click();">
                                        <span class="wm-account-action__icon" aria-hidden="true"><i class="fi-rs-shopping-cart-check"></i></span>
                                        <span>
                                            <strong>Track an order</strong>
                                            <small>Enter your invoice number</small>
                                        </span>
                                    </button>
                                    <button type="button" class="wm-account-action" onclick="document.getElementById('account-detail-tab').click();">
                                        <span class="wm-account-action__icon" aria-hidden="true"><i class="fi-rs-user"></i></span>
                                        <span>
                                            <strong>Account details</strong>
                                            <small>Update name &amp; email</small>
                                        </span>
                                    </button>
                                    <a href="{{ route('shop') }}" class="wm-account-action wm-account-action--shop">
                                        <span class="wm-account-action__icon" aria-hidden="true"><i class="fi-rs-shopping-cart"></i></span>
                                        <span>
                                            <strong>Continue shopping</strong>
                                            <small>Browse machinery &amp; parts</small>
                                        </span>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="orders" role="tabpanel" aria-labelledby="orders-tab">
                            <div class="wm-account-panel">
                                <div class="wm-account-panel__header">
                                    <p class="wm-account-kicker">History</p>
                                    <h3>Your Orders</h3>
                                    <p class="wm-account-lead">Review invoices, payment method, and current order status.</p>
                                </div>

                                <div class="table-responsive wm-account-table-wrap">
                                    <table class="table wm-account-table">
                                        <thead>
                                            <tr>
                                                <th>Order</th>
                                                <th>Date</th>
                                                <th>Status</th>
                                                <th>Total</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($orders as $order)
                                                <tr>
                                                    <td data-title="Order"><strong>{{ $order->invoice_no }}</strong></td>
                                                    <td data-title="Date">{{ $order->created_at->format('F d, Y') }}</td>
                                                    <td data-title="Status">
                                                        <span class="wm-account-status wm-account-status--{{ Str::slug($order->status) }}">{{ ucfirst($order->status) }}</span>
                                                    </td>
                                                    <td data-title="Total">৳{{ number_format($order->total, 2) }}</td>
                                                    <td data-title="Actions">
                                                        <a href="#" class="wm-account-btn-sm" data-bs-toggle="modal" data-bs-target="#orderModal{{ $order->id }}">View</a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="wm-account-empty">
                                                        No orders found yet.
                                                        <a href="{{ route('shop') }}">Start shopping</a>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="track-orders" role="tabpanel" aria-labelledby="track-orders-tab">
                            <div class="wm-account-panel">
                                <div class="wm-account-panel__header">
                                    <p class="wm-account-kicker">Delivery</p>
                                    <h3>Orders tracking</h3>
                                    <p class="wm-account-lead">Enter the Order ID from your receipt or confirmation email, then press Track.</p>
                                </div>

                                <form class="wm-account-track-form" action="{{ route('my-account.track-order') }}" method="post">
                                    @csrf
                                    <div class="wm-account-field">
                                        <label for="track-invoice">Order Number</label>
                                        <input id="track-invoice" name="invoice_no" placeholder="Enter invoice no" type="text" required />
                                    </div>
                                    <button class="wm-account-submit" type="submit">Track order</button>
                                </form>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="account-detail" role="tabpanel" aria-labelledby="account-detail-tab">
                            <div class="wm-account-panel">
                                <div class="wm-account-panel__header">
                                    <p class="wm-account-kicker">Profile</p>
                                    <h3>Account Details</h3>
                                    <p class="wm-account-lead">Keep your contact information up to date for faster checkout and delivery updates.</p>
                                </div>

                                @if(session('success'))
                                    <div class="wm-account-alert">{{ session('success') }}</div>
                                @endif

                                <form method="post" action="{{ route('my-account.update') }}" name="enq" class="wm-account-profile-form">
                                    @csrf
                                    <div class="wm-account-field">
                                        <label for="account-name">Full Name <span class="required">*</span></label>
                                        <input id="account-name" required class="form-control" name="name" type="text" value="{{ old('name', $user->name) }}" />
                                    </div>
                                    <div class="wm-account-field">
                                        <label for="account-phone">Phone Number <span class="required">*</span></label>
                                        <input id="account-phone" required class="form-control" name="phone" type="text" value="{{ $user->phone }}" readonly />
                                    </div>
                                    <div class="wm-account-field">
                                        <label for="account-email">Email Address</label>
                                        <input id="account-email" class="form-control" name="email" type="email" value="{{ old('email', $user->email) }}" />
                                    </div>
                                    <button type="submit" class="wm-account-submit" name="submit" value="Submit">Save changes</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @foreach($orders as $order)
    <div class="modal fade custom-modal" id="orderModal{{ $order->id }}" tabindex="-1" aria-labelledby="orderModalLabel{{ $order->id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content wm-account-modal">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="modal-body">
                    <h4 class="wm-account-modal__title">Order Details — {{ $order->invoice_no }}</h4>
                    <div class="row mb-30">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Date:</strong> {{ $order->created_at->format('F d, Y h:i A') }}</p>
                            <p class="mb-1"><strong>Status:</strong> {{ ucfirst($order->status) }}</p>
                            <p class="mb-1"><strong>Payment Method:</strong> {{ strtoupper($order->payment_method) }}</p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            @if($order->address)
                            <h5 class="mb-2">Shipping Address</h5>
                            <address class="mb-0">
                                {{ $order->address->name }}, {{ $order->address->phone }}<br>
                                {{ $order->address->address }}<br>
                                {{ $order->address->note }}
                            </address>
                            @endif
                        </div>
                    </div>

                    <h5 class="mt-20 mb-10">Items</h5>
                    <div class="table-responsive">
                        <table class="table wm-account-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Qty</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr>
                                    <td>{{ $item->product ? $item->product->name : 'Unknown Product' }}
                                        @if($item->color) <br><small>Color: {{ $item->color }}</small> @endif
                                        @if($item->size) <br><small>Size: {{ $item->size }}</small> @endif
                                    </td>
                                    <td>৳{{ number_format($item->price, 2) }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>৳{{ number_format($item->price * $item->quantity, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-end">Subtotal</th>
                                    <td>৳{{ number_format($order->subtotal, 2) }}</td>
                                </tr>
                                <tr>
                                    <th colspan="3" class="text-end">Discount</th>
                                    <td>৳{{ number_format($order->discount, 2) }}</td>
                                </tr>
                                <tr>
                                    <th colspan="3" class="text-end">Shipping</th>
                                    <td>৳{{ number_format($order->shipping_charge, 2) }}</td>
                                </tr>
                                <tr>
                                    <th colspan="3" class="text-end">Total</th>
                                    <td><strong>৳{{ number_format($order->total, 2) }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    @if(session('trackedOrder'))
    <div class="modal fade custom-modal" id="trackedOrderModal" tabindex="-1" aria-labelledby="trackedOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content wm-account-modal">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="modal-body">
                    <h4 class="wm-account-modal__title">Order Details — {{ session('trackedOrder')->invoice_no }}</h4>
                    <div class="row mb-30">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Date:</strong> {{ session('trackedOrder')->created_at->format('F d, Y h:i A') }}</p>
                            <p class="mb-1"><strong>Status:</strong> {{ ucfirst(session('trackedOrder')->status) }}</p>
                            <p class="mb-1"><strong>Payment Method:</strong> {{ strtoupper(session('trackedOrder')->payment_method) }}</p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            @if(session('trackedOrder')->address)
                            <h5 class="mb-2">Shipping Address</h5>
                            <address class="mb-0">
                                {{ session('trackedOrder')->address->name }}, {{ session('trackedOrder')->address->phone }}<br>
                                {{ session('trackedOrder')->address->address }}<br>
                                {{ session('trackedOrder')->address->note }}
                            </address>
                            @endif
                        </div>
                    </div>

                    <h5 class="mt-20 mb-10">Items</h5>
                    <div class="table-responsive">
                        <table class="table wm-account-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Qty</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(session('trackedOrder')->items as $item)
                                <tr>
                                    <td>{{ $item->product ? $item->product->name : 'Unknown Product' }}
                                        @if($item->color) <br><small>Color: {{ $item->color }}</small> @endif
                                        @if($item->size) <br><small>Size: {{ $item->size }}</small> @endif
                                    </td>
                                    <td>৳{{ number_format($item->price, 2) }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>৳{{ number_format($item->price * $item->quantity, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-end">Subtotal</th>
                                    <td>৳{{ number_format(session('trackedOrder')->subtotal, 2) }}</td>
                                </tr>
                                <tr>
                                    <th colspan="3" class="text-end">Discount</th>
                                    <td>৳{{ number_format(session('trackedOrder')->discount, 2) }}</td>
                                </tr>
                                <tr>
                                    <th colspan="3" class="text-end">Shipping</th>
                                    <td>৳{{ number_format(session('trackedOrder')->shipping_charge, 2) }}</td>
                                </tr>
                                <tr>
                                    <th colspan="3" class="text-end">Total</th>
                                    <td><strong>৳{{ number_format(session('trackedOrder')->total, 2) }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        @if(session('active_tab'))
        var tabElement = document.getElementById('{{ session('active_tab') }}-tab');
        if (tabElement) {
            var tab = new bootstrap.Tab(tabElement);
            tab.show();
        }
        @endif
        @if(session('trackedOrder'))
        var trackedModal = new bootstrap.Modal(document.getElementById('trackedOrderModal'));
        trackedModal.show();
        @endif
    });
</script>
@endpush

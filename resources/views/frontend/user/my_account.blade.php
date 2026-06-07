@extends('frontend.layouts.app')
@section('content')
    <main class="main pages">
        <div class="page-header breadcrumb-wrap">
            <div class="container">
                <div class="breadcrumb">
                    <a href="{{Url('/')}}" rel="nofollow"><i class="fi-rs-home mr-5"></i>Home</a>
                    <span></span> Pages <span></span> My Account
                </div>
            </div>
        </div>
        <div class="page-content pt-15 pb-15">
            <div class="container">
                <div class="row">
                    <div class="col-lg-10 m-auto">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="dashboard-menu">
                                    <ul class="nav flex-column" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="dashboard-tab" data-bs-toggle="tab" href="#dashboard" role="tab" aria-controls="dashboard" aria-selected="false"><i class="fi-rs-settings-sliders mr-10"></i>Dashboard</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="orders-tab" data-bs-toggle="tab" href="#orders" role="tab" aria-controls="orders" aria-selected="false"><i class="fi-rs-shopping-bag mr-10"></i>Orders</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="track-orders-tab" data-bs-toggle="tab" href="#track-orders" role="tab" aria-controls="track-orders" aria-selected="false"><i class="fi-rs-shopping-cart-check mr-10"></i>Track Your Order</a>
                                        </li>
{{--                                        <li class="nav-item">--}}
{{--                                            <a class="nav-link" id="address-tab" data-bs-toggle="tab" href="#address" role="tab" aria-controls="address" aria-selected="true"><i class="fi-rs-marker mr-10"></i>My Address</a>--}}
{{--                                        </li>--}}
                                        <li class="nav-item">
                                            <a class="nav-link" id="account-detail-tab" data-bs-toggle="tab" href="#account-detail" role="tab" aria-controls="account-detail" aria-selected="true"><i class="fi-rs-user mr-10"></i>Account details</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fi-rs-sign-out mr-10"></i>Logout</a>
                                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                                @csrf
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="tab-content account dashboard-content pl-50">
                                    <div class="tab-pane fade active show" id="dashboard" role="tabpanel" aria-labelledby="dashboard-tab">
                                        <div class="card">
                                            <div class="card-header">
                                                <h3 class="mb-0">Hello {{ $user->name ?? 'User' }}!</h3>
                                            </div>
                                            <div class="card-body">
                                                <p>
                                                    From your account dashboard. you can easily check &amp; view your <a href="#">recent orders</a>,<br />
                                                    manage your <a href="#">shipping and billing addresses</a> and <a href="#">edit your password and account details.</a>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="orders" role="tabpanel" aria-labelledby="orders-tab">
                                        <div class="card">
                                            <div class="card-header">
                                                <h3 class="mb-0">Your Orders</h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table">
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
                                                            <td>{{ $order->invoice_no }}</td>
                                                            <td>{{ $order->created_at->format('F d, Y') }}</td>
                                                            <td>{{ ucfirst($order->status) }}</td>
                                                            <td>৳{{ number_format($order->total, 2) }}</td>
                                                            <td><a href="#" class="btn-small d-block" data-bs-toggle="modal" data-bs-target="#orderModal{{ $order->id }}">View</a></td>
                                                        </tr>
                                                        @empty
                                                        <tr><td colspan="5">No orders found.</td></tr>
                                                        @endforelse
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="track-orders" role="tabpanel" aria-labelledby="track-orders-tab">
                                        <div class="card">
                                            <div class="card-header">
                                                <h3 class="mb-0">Orders tracking</h3>
                                            </div>
                                            <div class="card-body contact-from-area">
                                                <p>To track your order please enter your OrderID in the box below and press "Track" button. This was given to you on your receipt and in the confirmation email you should have received.</p>
                                                <div class="row">
                                                    <div class="col-lg-8">
                                                        <form class="contact-form-style mt-30 mb-50" action="{{ route('my-account.track-order') }}" method="post">
                                                            @csrf
                                                            <div class="input-style mb-20">
                                                                <label>Order Number</label>
                                                                <input name="invoice_no" placeholder="enter invoice no" type="text" required />
                                                            </div>
                                                            <button class="submit submit-auto-width" type="submit">Track</button>
                                                        </form>


                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="address" role="tabpanel" aria-labelledby="address-tab">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="card mb-3 mb-lg-0">
                                                    <div class="card-header">
                                                        <h3 class="mb-0">Billing Address</h3>
                                                    </div>
                                                    <div class="card-body">
                                                        <address>
                                                            3522 Interstate<br />
                                                            75 Business Spur,<br />
                                                            Sault Ste. <br />Marie, MI 49783
                                                        </address>
                                                        <p>New York</p>
                                                        <a href="#" class="btn-small">Edit</a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h5 class="mb-0">Shipping Address</h5>
                                                    </div>
                                                    <div class="card-body">
                                                        <address>
                                                            4299 Express Lane<br />
                                                            Sarasota, <br />FL 34249 USA <br />Phone: 1.941.227.4444
                                                        </address>
                                                        <p>Sarasota</p>
                                                        <a href="#" class="btn-small">Edit</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="account-detail" role="tabpanel" aria-labelledby="account-detail-tab">
                                        <div class="card">
                                            <div class="card-header">
                                                <h5>Account Details</h5>
                                            </div>
                                            <div class="card-body">
                                                <form method="post" action="{{ route('my-account.update') }}" name="enq">
                                                    @csrf
                                                    <div class="row">
                                                        <div class="form-group col-md-12">
                                                            <label>Full Name <span class="required">*</span></label>
                                                            <input required="" class="form-control" name="name" type="text" value="{{ $user->name }}" />
                                                        </div>
                                                         <div class="form-group col-md-12">
                                                            <label>Phone Number <span class="required">*</span></label>
                                                            <input required="" class="form-control" name="" type="text" value="{{ $user->phone }}" readonly />
                                                        </div>
                                                        <div class="form-group col-md-12">
                                                            <label>Email Address</label>
                                                            <input class="form-control" name="email" type="email" value="{{ $user->email }}" />
                                                        </div>
                                                        <div class="col-md-12">
                                                            <button type="submit" class="btn btn-fill-out submit font-weight-bold" name="submit" value="Submit">Save Change</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Order Details Modals -->
    @foreach($orders as $order)
    <div class="modal fade custom-modal" id="orderModal{{ $order->id }}" tabindex="-1" aria-labelledby="orderModalLabel{{ $order->id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="position: absolute; right: 15px; top: 15px; z-index: 10;"></button>
                <div class="modal-body p-30">
                    <h4 class="mb-15 border-bottom pb-2">Order Details - {{ $order->invoice_no }}</h4>
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
                        <table class="table">
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
            <div class="modal-content">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="position: absolute; right: 15px; top: 15px; z-index: 10;"></button>
                <div class="modal-body p-30">
                    <h4 class="mb-15 border-bottom pb-2">Order Details - {{ session('trackedOrder')->invoice_no }}</h4>
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
                        <table class="table">
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

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NOZOR || POS</title>
    <!-- bootstarp link -->
    <link rel="stylesheet" href="{{asset('admin/pos/assets/css/bootstrap.min.css')}}">
    <!-- font awesome -->
    <link rel="stylesheet" href="{{asset('admin/pos/assets/css/all.min.css')}}">
    <link rel="stylesheet" href="{{asset('admin/pos/assets/css/fontawesome.min.css')}}">
    <!-- css file link -->
    <link rel="stylesheet" href="{{asset('admin/pos/assets/css/global.css')}}">
    <link rel="stylesheet" href="{{asset('admin/pos/assets/css/main.css')}}">
    <link rel="stylesheet" href="{{asset('admin/pos/assets/css/responsive.css')}}">
    <!-- Select2 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        /* Product box — base overrides (layout in main.css) */
        .product__box {
            transition: all 0.22s ease;
            cursor: pointer;
            position: relative;
        }

        /* Hover: lift + border highlight + image zoom */
        .product__box:not(.stock__out):hover {
            border-color: #001f3f;
            box-shadow: 0 6px 18px rgba(0, 31, 63, 0.14);
            transform: translateY(-3px);
        }



        /* Active: press-down */
        .product__box:not(.stock__out):active {
            transform: scale(0.96);
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.08);
            background-color: #f5f7fa;
        }

        /* Out of stock */
        .product__box.stock__out {
            /* filter: grayscale(1); */
            cursor: not-allowed;
            opacity: 0.78;
        }

        .product__box.stock__out .product_thumb {
            filter: blur(2px);
        }

        .product__box.stock__out .product_title {
            color: #aaa;
        }

        .product__box.stock__out span {
            position: absolute;
            top: 40px;
            left: 50%;
            transform: translateX(-50%) rotate(-12deg);
            background: rgba(220, 53, 69, 0.92);
            color: #fff;
            padding: 3px 10px;
            font-weight: 700;
            font-size: 11px;
            text-transform: uppercase;
            z-index: 5;
            border-radius: 4px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.22);
            white-space: nowrap;
            letter-spacing: 0.5px;
        }

        .pos__product_wrapper {
            overflow-y: auto;
            max-height: calc(100vh - 250px);
            padding-bottom: 20px;
        }

        /* Select2 POS Styling */
        .select2-container .select2-selection--single {
            height: 38px !important;
            border-radius: 0 !important;
            border: 1px solid #dee2e6 !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
        }
        .btn-navy {
            background-color: #001f3f !important;
            color: white !important;
        }
    </style>
</head>

<body class="main_body">
<!-- pos header -->
<header class="pos_header">
    <!-- container -->
    <div class="container-fluid">
        <!-- row -->
        <div class="row gy-2 align-items-center">
            <!-- header left -->
            <div class="col-sm-6">
                <div class="header_right d-flex gap-2 align-items-center">
                    <p id="time" class="text-white"></p>
                    <script>
                        function updateTime() {
                            var currentDate = new Date();
                            var dateString = currentDate.toLocaleString();
                            document.getElementById('time').innerHTML = dateString;
                        }
                        setInterval(updateTime, 1000);
                        updateTime();
                    </script>
                </div>
            </div>
            <!-- header right -->
            <div class="col-sm-6">
                <!-- all button -->
                <div class="header_btnwrapper">
                    <div class="d-flex align-items-stretch justify-content-end gap-2 header___left">
                        <!-- add expense -->
                        <button type="button" title="Add Expense" class="btn_main header_btn bg-purple"
                                data-bs-toggle="modal" data-bs-target="#add_expense">
                            <span><i class="fa fas fa-minus-circle"></i> Add Expense</span>
                        </button>
                        <!-- add expence modal -->
                        <div class="modal fade" id="add_expense">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <!-- modal header -->
                                    <div class="modal-header">
                                        <h2 class="modal-title">Add Expense</h2>
                                        <button class="btn btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <!-- modal body -->
                                    <div class="modal-body">
                                        <!-- expenese -->
                                        <div class="expense_row">
                                            <div class="row gy-3">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="" class="form-label">Business
                                                            Location:</label>
                                                        <!-- select location -->
                                                        <select name="" id="" class="form-select rounded-0">
                                                            <option value="" hidden selected>Please Select
                                                            </option>
                                                            <option value="">Tateeghar Branch</option>
                                                            <option value="">Tateeghar Branch</option>
                                                            <option value="">Tateeghar Branch</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="" class="form-label">Expense
                                                            Category:</label>
                                                        <!-- select category -->
                                                        <select name="" id="" class="form-select rounded-0">
                                                            <option value="" hidden selected>Please Select
                                                            </option>
                                                            <option value="">Hasiful Islam</option>
                                                            <option value="">Rashedul</option>
                                                            <option value="">Jeasmine Soap</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="" class="form-label">Reference No:</label>
                                                        <!-- Reference no -->
                                                        <input type="text" class="form-control rounded-0">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="" class="form-label">Date:</label>
                                                        <!-- date -->
                                                        <input type="date" class="form-control rounded-0">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="" class="form-label">Expense for:</label>
                                                        <!-- Expense -->
                                                        <select name="" id="" class="form-select rounded-0">
                                                            <option value="" hidden selected>Please Select
                                                            </option>
                                                            <option value="">None</option>
                                                            <option value="">Sky Mart</option>
                                                            <option value="">Test</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="" class="form-label">Applicable Tax:</label>
                                                        <!-- tax -->
                                                        <select name="" id="" class="form-select rounded-0">
                                                            <option value="" hidden selected>Please Select
                                                            </option>
                                                            <option value="">None</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="" class="form-label">Total amount:</label>
                                                        <!-- total amount -->
                                                        <input type="number" class="form-control rounded-0">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="" class="form-label">Expense note:</label>
                                                        <!-- note -->
                                                        <textarea name="" id=""
                                                                  class="form-control rounded-0"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- payment -->
                                        <div class="payment_row mt-4">
                                            <div class="row gy-3">
                                                <div class="col-md-6">
                                                    <label for="" class="form-label">Amount:</label>
                                                    <div class="input-group">
                                                        <button class="input-group-text rounded-0">
                                                            <i class="fa-solid fa-money-bill"></i>
                                                        </button>
                                                        <!-- amount -->
                                                        <input type="number" class="form-control rounded-0">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="" class="form-label">Paid on:</label>
                                                        <input type="date" class="form-control rounded-0">
                                                    </div>
                                                </div>
                                                <!-- payment method -->
                                                <div class="col-md-6">
                                                    <label for="" class="form-label">Payment Method:</label>
                                                    <select name="" id="paymentMethod"
                                                            class="paymentMethod form-select rounded-0">
                                                        <option value="" selected hidden>Select Payment</option>
                                                        <option value="Cash">Cash</option>
                                                        <option value="Card">Card</option>
                                                        <option value="Cheque">Cheque</option>
                                                        <option value="Bank Transfer">Bank Transfer</option>
                                                        <option value="Other">Other</option>
                                                        <option value="Customer Payment 1">Customer Payment 1
                                                        </option>
                                                        <option value="Customer Payment 2">Customer Payment 2
                                                        </option>
                                                    </select>
                                                </div>
                                                <!-- card payment row -->
                                                <div class="cardpayment_row">
                                                    <div class="row gy-3">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="" class="form-label">Card
                                                                    Number:</label>
                                                                <!-- Reference no -->
                                                                <input type="text" class="form-control rounded-0"
                                                                       placeholder="Card Number">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="" class="form-label">Card holder
                                                                    name:</label>
                                                                <!-- Reference no -->
                                                                <input type="text" class="form-control rounded-0"
                                                                       placeholder="Card holder name">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="" class="form-label">Card
                                                                    Transaction No:</label>
                                                                <!-- Reference no -->
                                                                <input type="text" class="form-control rounded-0"
                                                                       placeholder="Card Transaction No">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="" class="form-label">Card
                                                                    Type:</label>
                                                                <!-- Reference no -->
                                                                <select name="" id="" class="form-select rounded-0">
                                                                    <option value="" selected hidden>Select Card
                                                                    </option>
                                                                    <option value="">Credit Card</option>
                                                                    <option value="">Debit Card</option>
                                                                    <option value="">Visa</option>
                                                                    <option value="">Master Card</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="" class="form-label">Month</label>
                                                                <!-- Reference no -->
                                                                <input type="text" class="form-control rounded-0"
                                                                       placeholder="Month">
                                                            </div>
                                                        </div>
                                                        <!-- payment note -->
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="" class="form-label">Year:</label>
                                                                <input type="text" class="form-control rounded-0"
                                                                       placeholder="Year">
                                                            </div>
                                                        </div>
                                                        <!-- payment note -->
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="" class="form-label">Security
                                                                    Code:</label>
                                                                <input type="text" class="form-control rounded-0"
                                                                       placeholder="Security Code">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- checque payment row -->
                                                <div class="cheque_payment_row">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label for="" class="form-label">Cheque
                                                                    No.</label>
                                                                <input type="text" class="form-control rounded-0">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- checque payment row -->
                                                <div class="bank_payment_row">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label for="" class="form-label">Bank Account
                                                                    No.</label>
                                                                <input type="text" class="form-control rounded-0">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- cutomer payment row -->
                                                <div class="cutomer_payment_row">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label for="" class="form-label">Transaction
                                                                    No.</label>
                                                                <input type="text" class="form-control rounded-0">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- payment note -->
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="" class="form-label">Payment note:</label>
                                                        <textarea class="form-control rounded-0" rows="" id=""
                                                                  name="" cols="50"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- hr -->
                                        <hr class="my-3">
                                        <div class="expense_payment_content text-end">
                                            <strong>Payment due:</strong>
                                            <span id="expense_payment_due">0.00</span>
                                        </div>
                                    </div>
                                    <!-- modal footer -->
                                    <div class="modal-footer">
                                        <button type="submit"
                                                class="btn_main footer_innerbtn misty-color">Save</button>
                                        <button type="button" class="btn_main footer_innerbtn bg-navy"
                                                data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- suspend button -->
                        <button type="button" data-bs-target="#suspend_modal" data-bs-toggle="modal"
                                title="View Suspended Details" class="btn_main header_btn bg-yellow">
                            <span><i class="fa fa-pause-circle fa-lg"></i></span>
                        </button>
                        <!-- suspend modal -->
                        <div class="modal fade" id="suspend_modal">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <!-- modal header -->
                                    <div class="modal-header">
                                        <h2 class="modal-title">Suspended Sales</h2>
                                        <button class="btn btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <!-- modal body -->
                                    <div class="modal-body">
                                        <div class="suspended_row">
                                            <div class="row gy-3">
                                                <div class="col-md-6">
                                                    <!-- suspend item-->
                                                    <div class="suspend_noteItem">
                                                        <!-- content -->
                                                        <div class="suspend_note_content">
                                                            <!-- title -->
                                                            <h4 class="suspend_note_title"> Not Sale This
                                                                product</h4>
                                                            <!-- user category -->
                                                            <p class="suspend_user_category"> Walk-In Customer
                                                            </p>
                                                            <p class="suspend_total_item">Total Items:
                                                                <span>4</span>
                                                            </p>
                                                            <p class="suspend_total_amount">Total:
                                                                <span>31055</span>৳
                                                            </p>
                                                            <p class="suspend_userid">0066</p>
                                                        </div>
                                                        <!-- footer -->
                                                        <div class="suspend_note_footer">
                                                            <div
                                                                class="d-flex align-items-center justify-content-between">
                                                                <p class="suspend_date">12-08-2023</p>
                                                                <!-- action -->
                                                                <div class="suspend_note_action">
                                                                    <button
                                                                        class="btn_main footer_innerbtn misty-color">
                                                                            <span><i
                                                                                    class="fa-solid fa-pen-to-square"></i></span>
                                                                    </button>
                                                                    <button
                                                                        class="btn_main footer_innerbtn bg-navy">
                                                                            <span><i
                                                                                    class="fa-solid fa-trash"></i></span>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <!-- suspend item-->
                                                    <div class="suspend_noteItem">
                                                        <!-- content -->
                                                        <div class="suspend_note_content">
                                                            <!-- title -->
                                                            <h4 class="suspend_note_title"> Not Sale This
                                                                product</h4>
                                                            <!-- user category -->
                                                            <p class="suspend_user_category"> Walk-In Customer
                                                            </p>
                                                            <p class="suspend_total_item">Total Items:
                                                                <span>4</span>
                                                            </p>
                                                            <p class="suspend_total_amount">Total:
                                                                <span>31055</span>৳
                                                            </p>
                                                            <p class="suspend_userid">0066</p>
                                                        </div>
                                                        <!-- footer -->
                                                        <div class="suspend_note_footer">
                                                            <div
                                                                class="d-flex align-items-center justify-content-between">
                                                                <p class="suspend_date">12-08-2023</p>
                                                                <!-- action -->
                                                                <div class="suspend_note_action">
                                                                    <button
                                                                        class="btn_main footer_innerbtn misty-color">
                                                                            <span><i
                                                                                    class="fa-solid fa-pen-to-square"></i></span>
                                                                    </button>
                                                                    <button
                                                                        class="btn_main footer_innerbtn bg-navy">
                                                                            <span><i
                                                                                    class="fa-solid fa-trash"></i></span>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <!-- suspend item-->
                                                    <div class="suspend_noteItem">
                                                        <!-- content -->
                                                        <div class="suspend_note_content">
                                                            <!-- title -->
                                                            <h4 class="suspend_note_title"> Not Sale This
                                                                product</h4>
                                                            <!-- user category -->
                                                            <p class="suspend_user_category"> Walk-In Customer
                                                            </p>
                                                            <p class="suspend_total_item">Total Items:
                                                                <span>4</span>
                                                            </p>
                                                            <p class="suspend_total_amount">Total:
                                                                <span>31055</span>৳
                                                            </p>
                                                            <p class="suspend_userid">0066</p>
                                                        </div>
                                                        <!-- footer -->
                                                        <div class="suspend_note_footer">
                                                            <div
                                                                class="d-flex align-items-center justify-content-between">
                                                                <p class="suspend_date">12-08-2023</p>
                                                                <!-- action -->
                                                                <div class="suspend_note_action">
                                                                    <button
                                                                        class="btn_main footer_innerbtn misty-color">
                                                                            <span><i
                                                                                    class="fa-solid fa-pen-to-square"></i></span>
                                                                    </button>
                                                                    <button
                                                                        class="btn_main footer_innerbtn bg-navy">
                                                                            <span><i
                                                                                    class="fa-solid fa-trash"></i></span>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <!-- suspend item-->
                                                    <div class="suspend_noteItem">
                                                        <!-- content -->
                                                        <div class="suspend_note_content">
                                                            <!-- title -->
                                                            <h4 class="suspend_note_title"> Not Sale This
                                                                product</h4>
                                                            <!-- user category -->
                                                            <p class="suspend_user_category"> Walk-In Customer
                                                            </p>
                                                            <p class="suspend_total_item">Total Items:
                                                                <span>4</span>
                                                            </p>
                                                            <p class="suspend_total_amount">Total:
                                                                <span>31055</span>৳
                                                            </p>
                                                            <p class="suspend_userid">0066</p>
                                                        </div>
                                                        <!-- footer -->
                                                        <div class="suspend_note_footer">
                                                            <div
                                                                class="d-flex align-items-center justify-content-between">
                                                                <p class="suspend_date">12-08-2023</p>
                                                                <!-- action -->
                                                                <div class="suspend_note_action">
                                                                    <button
                                                                        class="btn_main footer_innerbtn misty-color">
                                                                            <span><i
                                                                                    class="fa-solid fa-pen-to-square"></i></span>
                                                                    </button>
                                                                    <button
                                                                        class="btn_main footer_innerbtn bg-navy">
                                                                            <span><i
                                                                                    class="fa-solid fa-trash"></i></span>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- modal footer -->
                                    <div class="modal-footer">
                                        <button type="button" class="btn_main footer_innerbtn bg-navy"
                                                data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--  fullsecreen button -->
                        <button type="button" title="Click Fullsecreen" class="btn_main header_btn bg-primary"
                                id="fullsecreen_btn">
                            <span><i class="fa-solid fa-expand"></i></span>
                        </button>

                        <!-- calculator -->
                        <div class="position-relative">
                            <!--  calculator button -->
                            <button type="button" title="Calculator" class="btn_main header_btn bg-info"
                                    id="calculator_btn">
                                <span><i class="fa-solid fa-calculator"></i></span>
                            </button>
                            <!-- calculator -->
                            <div class="calculator" id="calculator">
                                <div class="display">
                                    <span id="current-calc"></span>
                                    <span id="result">0</span>
                                </div>
                                <div class="cal_row">
                                    <span data-key="Backspace" id="other">&#8592;</span>
                                    <span data-key="?" id="other">&plusmn;</span>
                                    <span data-key="%" id="operator">&percnt;</span>
                                    <span data-key="/" id="operator">&divide;</span>
                                </div>
                                <div class="cal_row">
                                    <span data-key="7" id="num">7</span>
                                    <span data-key="8" id="num">8</span>
                                    <span data-key="9" id="num">9</span>
                                    <span data-key="*" id="operator">&times;</span>
                                </div>
                                <div class="cal_row">
                                    <span data-key="4" id="num">4</span>
                                    <span data-key="5" id="num">5</span>
                                    <span data-key="6" id="num">6</span>
                                    <span data-key="-" id="operator">&minus;</span>
                                </div>
                                <div class="cal_row">
                                    <span data-key="1" id="num">1</span>
                                    <span data-key="2" id="num">2</span>
                                    <span data-key="3" id="num">3</span>
                                    <span data-key="+" id="operator">&plus;</span>
                                </div>
                                <div class="cal_row">
                                    <span data-key="Delete" id="del">CE</span>
                                    <span data-key="0" id="num">0</span>
                                    <span data-key="." id="other">.</span>
                                    <span data-key="Enter" id="equ">&equals;</span>
                                </div>
                            </div>
                        </div>

                        <!--  register details -->
                        <button type="button" title="Register Details" class="btn_main header_btn btn-success"
                                data-bs-target="#register_details" data-bs-toggle="modal">
                            <span><i class="fa-solid fa-briefcase"></i></span>
                        </button>
                        <!-- register modal -->
                        <div class="modal fade" id="register_details">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <!-- modal header -->
                                    <div class="modal-header">
                                        <h2 class="modal-title">Register Details ( 9th Aug, 2023 10:29 AM - 12th
                                            Aug, 2023 03:26 PM )</h2>
                                        <button class="btn btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <!-- modal body -->
                                    <div class="modal-body">
                                        <div class="col-sm-12">
                                            <table class="table register_modaltable table-bordered table-striped">
                                                <tbody>
                                                <tr>
                                                    <th>Payment Method</th>
                                                    <th>Sell</th>
                                                    <th>Expense</th>
                                                </tr>
                                                <tr>
                                                    <td> Cash in hand: </td>
                                                    <td>
                                                        <span class="display_currency">300.00 ৳</span>
                                                    </td>
                                                    <td>--</td>
                                                </tr>
                                                <tr>
                                                    <td> Cash Payment: </td>
                                                    <td>
                                                        <span class="display_currency">61,683.75 ৳</span>
                                                    </td>
                                                    <td>
                                                        <span class="display_currency">0.00 ৳</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td> Cheque Payment: </td>
                                                    <td>
                                                        <span class="display_currency">0.00 ৳</span>
                                                    </td>
                                                    <td>
                                                        <span class="display_currency">0.00 ৳</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td> Card Payment: </td>
                                                    <td>
                                                        <span class="display_currency">1,883.75 ৳</span>
                                                    </td>
                                                    <td>
                                                        <span class="display_currency">0.00 ৳</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td> Bank Transfer: </td>
                                                    <td>
                                                        <span class="display_currency">0.00 ৳</span>
                                                    </td>
                                                    <td>
                                                        <span class="display_currency">0.00 ৳</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td> Advance payment: </td>
                                                    <td>
                                                        <span class="display_currency">0.00 ৳</span>
                                                    </td>
                                                    <td>
                                                        <span class="display_currency">0.00 ৳</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td> Custom Payment 1: </td>
                                                    <td>
                                                        <span class="display_currency">0.00 ৳</span>
                                                    </td>
                                                    <td>
                                                        <span class="display_currency">0.00 ৳</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td> Custom Payment 2: </td>
                                                    <td>
                                                        <span class="display_currency">0.00 ৳</span>
                                                    </td>
                                                    <td>
                                                        <span class="display_currency">0.00 ৳</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td> Custom Payment 3: </td>
                                                    <td>
                                                        <span class="display_currency">0.00 ৳</span>
                                                    </td>
                                                    <td>
                                                        <span class="display_currency">0.00 ৳</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td> Custom Payment 4: </td>
                                                    <td>
                                                        <span class="display_currency">0.00 ৳</span>
                                                    </td>
                                                    <td>
                                                        <span class="display_currency">0.00 ৳</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td> Custom Payment 5: </td>
                                                    <td>
                                                        <span class="display_currency">0.00 ৳</span>
                                                    </td>
                                                    <td>
                                                        <span class="display_currency">0.00 ৳</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td> Custom Payment 6: </td>
                                                    <td>
                                                        <span class="display_currency">0.00 ৳</span>
                                                    </td>
                                                    <td>
                                                        <span class="display_currency">0.00 ৳</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td> Custom Payment 7: </td>
                                                    <td>
                                                        <span class="display_currency">0.00 ৳</span>
                                                    </td>
                                                    <td>
                                                        <span class="display_currency">0.00 ৳</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td> Other Payments: </td>
                                                    <td>
                                                        <span class="display_currency">0.00 ৳</span>
                                                    </td>
                                                    <td>
                                                        <span class="display_currency">0.00 ৳</span>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                            <hr class="my-3">
                                            <table class="table tableregsiter_sale table-bordered table-striped">
                                                <tbody>
                                                <tr>
                                                    <td> Total Sales: </td>
                                                    <td>
                                                        <span class="display_currency">63,567.50 ৳</span>
                                                    </td>
                                                </tr>
                                                <tr class="danger">
                                                    <th>Total Refund</th>
                                                    <td>
                                                        <span class="display_currency">0.00 ৳</span>
                                                    </td>
                                                </tr>
                                                <tr class="success">
                                                    <th>Total Payment</th>
                                                    <td>
                                                        <span class="display_currency">61,983.75 ৳</span>
                                                    </td>
                                                </tr>
                                                <tr class="success">
                                                    <th> Credit Sales: </th>
                                                    <td>
                                                        <b><span class="display_currency">31,055.00
                                                                        ৳</span></b>
                                                    </td>
                                                </tr>
                                                <tr class="success">
                                                    <th> Total Sales: </th>
                                                    <td>
                                                        <b><span class="display_currency">94,622.50
                                                                        ৳</span></b>
                                                    </td>
                                                </tr>
                                                <tr class="danger">
                                                    <th> Total Expense: </th>
                                                    <td>
                                                        <b><span class="display_currency">0.00 ৳</span></b>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                            <hr class="my-3">
                                            <div class="col-md-12">
                                                <h3 class="register_soldtable_title">Details of products sold</h3>
                                                <table class="table register_soldtable table-bordered">
                                                    <tbody>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Brands</th>
                                                        <th>Quantity</th>
                                                        <th>Total amount</th>
                                                    </tr>
                                                    <tr>
                                                        <td>1.</td>
                                                        <td></td>
                                                        <td> 23.0000</td>
                                                        <td>
                                                            <span class="display_currency">94,622.50 ৳</span>
                                                        </td>
                                                    </tr>
                                                    <!-- Final details -->
                                                    <tr class="success">
                                                        <th>#</th>
                                                        <th></th>
                                                        <th>23</th>
                                                        <th>
                                                            Grand Total:
                                                            <span class="display_currency"
                                                                  data-currency_symbol="true">94,622.50 ৳</span>
                                                        </th>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <hr class="my-3">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="register_ruleauth">
                                                        <ul>
                                                            <li>
                                                                <span class="register_ruleauth_title">User :</span>
                                                                <span>Sky Mart</span>
                                                            </li>
                                                            <li>
                                                                <span class="register_ruleauth_title">Email :</span>
                                                                <span>admin@gmail.com</span>
                                                            </li>
                                                            <li>
                                                                    <span class="register_ruleauth_title">Business
                                                                        Location :</span>
                                                                <span>SKY MART</span>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- modal footer -->
                                    <div class="modal-footer">
                                        <button type="button" onclick="window.print()"
                                                class="btn_main footer_innerbtn misty-color">Print</button>
                                        <button type="button" class="btn_main footer_innerbtn bg-navy"
                                                data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- close register -->
                        <button type="button" title="Close Register" data-bs-target="#close_register"
                                data-bs-toggle="modal" class="btn_main header_btn misty-color">
                            <span><i class="fa-solid fa-xmark"></i></span>
                        </button>

                        <!-- close register modal -->
                        <div class="modal fade" id="close_register">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <!-- modal header -->
                                    <div class="modal-header">
                                        <h2 class="modal-title">Current Register ( 9th Aug, 2023 10:29 AM - 12th
                                            Aug, 2023 05:07 PM)</h2>
                                        <button class="btn btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <!-- modal body -->
                                    <div class="modal-body">
                                        <div class="col-sm-12">
                                            <table class="table register_modaltable table-bordered table-striped">
                                                <tbody>
                                                <tr>
                                                    <th>Payment Method</th>
                                                    <th>Sell</th>
                                                    <th>Expense</th>
                                                </tr>
                                                <tr>
                                                    <td> Cash in hand: </td>
                                                    <td>
                                                        <span class="display_currency">300.00 ৳</span>
                                                    </td>
                                                    <td>--</td>
                                                </tr>
                                                <tr>
                                                    <td> Cash Payment: </td>
                                                    <td>
                                                        <span class="display_currency">61,683.75 ৳</span>
                                                    </td>
                                                    <td>
                                                        <span class="display_currency">0.00 ৳</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td> Cheque Payment: </td>
                                                    <td>
                                                        <span class="display_currency">0.00 ৳</span>
                                                    </td>
                                                    <td>
                                                        <span class="display_currency">0.00 ৳</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td> Card Payment: </td>
                                                    <td>
                                                        <span class="display_currency">1,883.75 ৳</span>
                                                    </td>
                                                    <td>
                                                        <span class="display_currency">0.00 ৳</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td> Bank Transfer: </td>
                                                    <td>
                                                        <span class="display_currency">0.00 ৳</span>
                                                    </td>
                                                    <td>
                                                        <span class="display_currency">0.00 ৳</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td> Advance payment: </td>
                                                    <td>
                                                        <span class="display_currency">0.00 ৳</span>
                                                    </td>
                                                    <td>
                                                        <span class="display_currency">0.00 ৳</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td> Custom Payment 1: </td>
                                                    <td>
                                                        <span class="display_currency">0.00 ৳</span>
                                                    </td>
                                                    <td>
                                                        <span class="display_currency">0.00 ৳</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td> Custom Payment 2: </td>
                                                    <td>
                                                        <span class="display_currency">0.00 ৳</span>
                                                    </td>
                                                    <td>
                                                        <span class="display_currency">0.00 ৳</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td> Custom Payment 3: </td>
                                                    <td>
                                                        <span class="display_currency">0.00 ৳</span>
                                                    </td>
                                                    <td>
                                                        <span class="display_currency">0.00 ৳</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td> Custom Payment 4: </td>
                                                    <td>
                                                        <span class="display_currency">0.00 ৳</span>
                                                    </td>
                                                    <td>
                                                        <span class="display_currency">0.00 ৳</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td> Custom Payment 5: </td>
                                                    <td>
                                                        <span class="display_currency">0.00 ৳</span>
                                                    </td>
                                                    <td>
                                                        <span class="display_currency">0.00 ৳</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td> Custom Payment 6: </td>
                                                    <td>
                                                        <span class="display_currency">0.00 ৳</span>
                                                    </td>
                                                    <td>
                                                        <span class="display_currency">0.00 ৳</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td> Custom Payment 7: </td>
                                                    <td>
                                                        <span class="display_currency">0.00 ৳</span>
                                                    </td>
                                                    <td>
                                                        <span class="display_currency">0.00 ৳</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td> Other Payments: </td>
                                                    <td>
                                                        <span class="display_currency">0.00 ৳</span>
                                                    </td>
                                                    <td>
                                                        <span class="display_currency">0.00 ৳</span>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                            <hr class="my-3">
                                            <table class="table tableregsiter_sale table-bordered table-striped">
                                                <tbody>
                                                <tr>
                                                    <td> Total Sales: </td>
                                                    <td>
                                                        <span class="display_currency">63,567.50 ৳</span>
                                                    </td>
                                                </tr>
                                                <tr class="danger">
                                                    <th>Total Refund</th>
                                                    <td>
                                                        <span class="display_currency">0.00 ৳</span>
                                                    </td>
                                                </tr>
                                                <tr class="success">
                                                    <th>Total Payment</th>
                                                    <td>
                                                        <span class="display_currency">61,983.75 ৳</span>
                                                    </td>
                                                </tr>
                                                <tr class="success">
                                                    <th> Credit Sales: </th>
                                                    <td>
                                                        <b><span class="display_currency">31,055.00
                                                                        ৳</span></b>
                                                    </td>
                                                </tr>
                                                <tr class="success">
                                                    <th> Total Sales: </th>
                                                    <td>
                                                        <b><span class="display_currency">94,622.50
                                                                        ৳</span></b>
                                                    </td>
                                                </tr>
                                                <tr class="danger">
                                                    <th> Total Expense: </th>
                                                    <td>
                                                        <b><span class="display_currency">0.00 ৳</span></b>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                            <hr class="my-3">
                                            <div class="col-md-12">
                                                <h3 class="register_soldtable_title">Details of products sold</h3>
                                                <table class="table register_soldtable table-bordered">
                                                    <tbody>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Brands</th>
                                                        <th>Quantity</th>
                                                        <th>Total amount</th>
                                                    </tr>
                                                    <tr>
                                                        <td>1.</td>
                                                        <td></td>
                                                        <td> 23.0000</td>
                                                        <td>
                                                            <span class="display_currency">94,622.50 ৳</span>
                                                        </td>
                                                    </tr>
                                                    <!-- Final details -->
                                                    <tr class="success">
                                                        <th>#</th>
                                                        <th></th>
                                                        <th>23</th>
                                                        <th>
                                                            Grand Total:
                                                            <span class="display_currency"
                                                                  data-currency_symbol="true">94,622.50 ৳</span>
                                                        </th>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <hr class="my-3">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="" class="form-label">Total Cash:</label>
                                                        <!-- Expense -->
                                                        <input type="text" class="form-control rounded-0"
                                                               value="61,983.75">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="" class="form-label">Total Card Slips:</label>
                                                        <!-- Expense -->
                                                        <input type="text" class="form-control rounded-0" value="0">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="" class="form-label">Total cheques:</label>
                                                        <!-- Expense -->
                                                        <input type="text" class="form-control rounded-0" value="0">
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="" class="form-label">Closing Note:</label>
                                                        <textarea class="form-control rounded-0" rows="" id=""
                                                                  name="" cols="50"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr class="my-3">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="register_ruleauth">
                                                        <ul>
                                                            <li>
                                                                <span class="register_ruleauth_title">User :</span>
                                                                <span>Sky Mart</span>
                                                            </li>
                                                            <li>
                                                                <span class="register_ruleauth_title">Email :</span>
                                                                <span>admin@gmail.com</span>
                                                            </li>
                                                            <li>
                                                                    <span class="register_ruleauth_title">Business
                                                                        Location :</span>
                                                                <span>SKY MART</span>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- modal footer -->
                                    <div class="modal-footer">
                                        <button type="button"
                                                class="btn_main footer_innerbtn misty-color">Cancel</button>
                                        <button type="button" class="btn_main footer_innerbtn bg-navy"
                                                data-bs-dismiss="modal">Close Register</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- back button -->
                        <button type="button" title="Go Back" class="btn_main header_btn opacity-color"
                                id="back__btn">
                            <span><i class="fa-solid fa-left"></i></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
<!-- pos header end -->

<!-- content main -->
<div class="content_main py-4">
    <!-- container -->
    <div class="container-fluid">
        <div class="row flex-row-reverse">
            <!-- cart column -->
            <div class="col-lg-4 col-xl-4 col-xxl-4">
                <!-- cart box -->
                <div class="cart__box">
                    <!-- card box header -->
                    <div class="card_box_header mb-3">
                        <div class="row gx-2">
                            <div class="col-md-5 col-lg-6 col-xl-5 col-12 col-sm-6">
                                <!-- select customer -->
                                <div class="input-group">
                                    <span class="input-group-text rounded-0"><i class="fa-solid fa-user"></i></span>
                                    <!-- select -->
                                    <select name="" id="" class="form-select">
                                        <option value="">Walk-In Customer</option>
                                        <option value="">Customer One</option>
                                        <option value="">Customer Two</option>
                                        <option value="">Customer Three</option>
                                        <option value="">Customer Four</option>
                                    </select>
                                    <!-- add user btn -->
                                    <button class="input-group-text rounded-0 bg-navy add_btn"
                                            data-bs-target="#addcustomer_modal" data-bs-toggle="modal">
                                        <i class="fa-solid fa-circle-plus"></i>
                                    </button>
                                    <!-- add user modal -->
                                    <div class="modal fade" id="addcustomer_modal">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <!-- modal header -->
                                                <div class="modal-header">
                                                    <h2 class="modal-title">Add a new contact</h2>
                                                    <button class="btn btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <!-- modal body -->
                                                <div class="modal-body">
                                                    <div class="addcontact_row">
                                                        <div class="row gy-3">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="" class="form-label">Contact
                                                                        ID:</label>
                                                                    <!-- select location -->
                                                                    <input type="text"
                                                                           class="rounded-0 form-control"
                                                                           placeholder="Contact ID">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="" class="form-label">Customer
                                                                        Group:</label>
                                                                    <!-- select category -->
                                                                    <select name="" id=""
                                                                            class="form-select rounded-0">
                                                                        <option value="" hidden="" selected="">
                                                                            Please Select
                                                                        </option>
                                                                        <option value="">None</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="" class="form-label">First
                                                                        Name:</label>
                                                                    <!-- Reference no -->
                                                                    <input type="text"
                                                                           class="form-control rounded-0"
                                                                           placeholder="First Name">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="" class="form-label">Last
                                                                        Name:</label>
                                                                    <!-- Reference no -->
                                                                    <input type="text"
                                                                           class="form-control rounded-0"
                                                                           placeholder="Last Name">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="" class="form-label">Mobile:</label>
                                                                    <!-- date -->
                                                                    <input type="tel" class="form-control rounded-0"
                                                                           placeholder="Mobile Number">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="" class="form-label">Email:</label>
                                                                    <!-- date -->
                                                                    <input type="tel" class="form-control rounded-0"
                                                                           placeholder="Mobile Number">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- modal footer -->
                                                <div class="modal-footer">
                                                    <button type="button"
                                                            class="btn_main footer_innerbtn misty-color">Save</button>
                                                    <button type="button" class="btn_main footer_innerbtn bg-navy"
                                                            data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-7 col-lg-6 col-xl-7 col-12 col-sm-6">
                                <!-- Search Proeduct -->
                                <div class="input-group">
                                    <button class="input-group-text rounded-0 add_btn" data-bs-toggle="modal"
                                            data-bs-target="#search_add">
                                        <i class="fa-solid fa-magnifying-glass"></i>
                                    </button>
                                    <!-- search modal -->
                                    <div class="modal fade" id="search_add">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <!-- modal header -->
                                                <div class="modal-header">
                                                    <h2 class="modal-title">Search products by</h2>
                                                    <button class="btn btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <!-- modal body -->
                                                <div class="modal-body">
                                                    <div class="search_modal_row">
                                                        <div class="row gy-3">
                                                            <div class="col-md-6">
                                                                <div class="search_checked">
                                                                    <!-- inner check -->
                                                                    <label class="check_inner">
                                                                        <input type="checkbox">
                                                                        <span class="checkmark"></span>
                                                                    </label>
                                                                    <span>Product Name</span>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="search_checked">
                                                                    <!-- inner check -->
                                                                    <label class="check_inner">
                                                                        <input type="checkbox">
                                                                        <span class="checkmark"></span>
                                                                    </label>
                                                                    <span>SKU</span>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="search_checked">
                                                                    <!-- inner check -->
                                                                    <label class="check_inner">
                                                                        <input type="checkbox">
                                                                        <span class="checkmark"></span>
                                                                    </label>
                                                                    <span>Custom Field1</span>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="search_checked">
                                                                    <!-- inner check -->
                                                                    <label class="check_inner">
                                                                        <input type="checkbox">
                                                                        <span class="checkmark"></span>
                                                                    </label>
                                                                    <span>Custom Field2</span>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="search_checked">
                                                                    <!-- inner check -->
                                                                    <label class="check_inner">
                                                                        <input type="checkbox">
                                                                        <span class="checkmark"></span>
                                                                    </label>
                                                                    <span>Custom Field3</span>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="search_checked">
                                                                    <!-- inner check -->
                                                                    <label class="check_inner">
                                                                        <input type="checkbox">
                                                                        <span class="checkmark"></span>
                                                                    </label>
                                                                    <span> Custom Field4</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- modal footer -->
                                                <div class="modal-footer">
                                                    <button type="button"
                                                            class="btn_main footer_innerbtn misty-color">Save</button>
                                                    <button type="button" class="btn_main footer_innerbtn bg-navy"
                                                            data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- search input -->
                                    <input class="form-control" type="text"
                                           placeholder="Enter Product Name / SKU / Product bar Code">
                                    <button class="input-group-text rounded-0 bg-navy add_btn"
                                            data-bs-target="#add_product" data-bs-toggle="modal">
                                        <i class="fa-solid fa-circle-plus"></i>
                                    </button>
                                    <!-- add new product -->
                                    <div class="modal fade" id="add_product">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <!-- modal header -->
                                                <div class="modal-header">
                                                    <h2 class="modal-title">Add new product</h2>
                                                    <button class="btn btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <!-- modal body -->
                                                <div class="modal-body">
                                                    <form action="" class="addproduct_modalrow">
                                                        <div class="row gy-3">
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="" class="form-label">Product
                                                                        Name:</label>
                                                                    <!-- Reference no -->
                                                                    <input type="text"
                                                                           class="form-control rounded-0">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="" class="form-label">SKU:</label>
                                                                    <!-- Reference no -->
                                                                    <input type="text"
                                                                           class="form-control rounded-0">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="" class="form-label">Barcode
                                                                        Type:</label>
                                                                    <!-- select location -->
                                                                    <select name="" id=""
                                                                            class="form-select rounded-0">
                                                                        <option value="" hidden="" selected="">
                                                                            Please Select
                                                                        </option>
                                                                        <option value="">A</option>
                                                                        <option value="">B</option>
                                                                        <option value="">C</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="" class="form-label">Unit:</label>
                                                                    <!-- select location -->
                                                                    <select name="" id=""
                                                                            class="form-select rounded-0">
                                                                        <option value="" hidden="" selected="">
                                                                            Please Select
                                                                        </option>
                                                                        <option value="">Pieces</option>
                                                                        <option value="">Box</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="" class="form-label">Brand:</label>
                                                                    <!-- select location -->
                                                                    <select name="" id=""
                                                                            class="form-select rounded-0">
                                                                        <option value="" hidden="" selected="">
                                                                            Please Select
                                                                        </option>
                                                                        <option value="">China</option>
                                                                        <option value="">Itali</option>
                                                                        <option value="">USA</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for=""
                                                                           class="form-label">Category:</label>
                                                                    <!-- select location -->
                                                                    <select name="" id=""
                                                                            class="form-select rounded-0">
                                                                        <option value="" hidden="" selected="">
                                                                            Please Select
                                                                        </option>
                                                                        <option value="">Silk</option>
                                                                        <option value="">Reshmi</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for=""
                                                                           class="form-label">Quantity:</label>
                                                                    <!-- Reference no -->
                                                                    <input type="number"
                                                                           class="form-control rounded-0">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="" class="form-label">Selling
                                                                        Price:</label>
                                                                    <!-- Reference no -->
                                                                    <input type="number"
                                                                           class="form-control rounded-0">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="" class="form-label">Product
                                                                        Image:</label>
                                                                    <!-- Reference no -->
                                                                    <input type="file"
                                                                           class="form-control rounded-0">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label for="" class="form-label">Product
                                                                        Descreption:</label>
                                                                    <!-- Reference no -->
                                                                    <textarea name="" id="" rows="5"
                                                                              class="form-control rounded-0"></textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                                <!-- modal footer -->
                                                <div class="modal-footer">
                                                    <button type="button"
                                                            class="btn_main footer_innerbtn misty-color">Save</button>
                                                    <button type="button" class="btn_main footer_innerbtn bg-navy"
                                                            data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- product table content -->
                    <div class="product__table_content">
                        <!-- product table -->
                        <div class="product__table">
                            <!-- table -->
                            <table class="table table-condensed table-bordered table-responsive">
                                <!-- table header -->
                                <thead>
                                <tr>
                                    <th class="text-start">Product</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-center">Subtotal</th>
                                    <th class="text-center">Action</th>
                                </tr>
                                </thead>
                                <tbody id="cart_table_body">
                                    <tr>
                                        <td colspan="4" class="text-center p-4 text-muted">No items in cart</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- Final Summary Section -->
                        <div class="pos-final-summary p-3 border-top">
                            <div class="mb-2">
                                <span class="fw-bold">Items:</span>
                                <span id="total_quantity" class="fw-bold">0</span>
                            </div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-secondary">Sub total :</span>
                                <span class="text-secondary fw-bold">৳ <span id="summary_subtotal">0.00</span></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-secondary">Product Discount:</span>
                                <span class="text-secondary fw-bold">- ৳ <span id="summary_product_discount">0.00</span></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-secondary">Extra Discount:</span>
                                <div class="d-flex align-items-center gap-2">
                                    <button type="button" class="btn btn-sm p-0 border-0" data-bs-toggle="modal" data-bs-target="#discount_modal">
                                        <i class="fa-solid fa-edit text-dark small"></i>
                                    </button>
                                    <span class="text-secondary fw-bold">- ৳ <span id="summary_extra_discount">0.00</span></span>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-secondary">Tax :</span>
                                <span class="text-secondary fw-bold">৳ <span id="summary_tax">0.00</span></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-secondary">Delivery Charge :</span>
                                <span class="text-secondary fw-bold">৳ <span id="summary_delivery_charge">0.00</span></span>
                            </div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between align-items-center mt-3 mb-4">
                                <h3 class="fw-bold m-0">Total :</h3>
                                <h3 class="fw-bold m-0 text-navy">৳ <span id="summary_total" class="total-amount">0.00</span></h3>
                                <input type="hidden" id="summary_total_input" value="0">
                            </div>

                            <div class="payment-methods-section mb-3">
                                <p class="small text-secondary fw-bold mb-2">Payment Method</p>
                                <div class="d-flex gap-2 payment_methods_wrapper">
                                    <button type="button" class="btn btn-navy btn-sm px-3 py-2 rounded-0 active payment-method-btn" data-method="Cash">Cash</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm px-3 py-2 rounded-0 payment-method-btn" data-method="Bank">Bank</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm px-3 py-2 rounded-0 payment-method-btn" data-method="Nagad">Nagad</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm px-3 py-2 rounded-0 payment-method-btn" data-method="Bkash">Bkash</button>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-secondary">Paid Amount :</span>
                                <input type="number" class="form-control form-control-sm text-end w-50 bg-light border-0 py-2" id="summary_paid_amount" value="0">
                            </div>

                             <div class="d-flex justify-content-between align-items-center mb-3">
                                 <span class="text-secondary">Due Amount :</span>
                                 <span class="text-secondary fw-bold">৳ <span id="summary_due_amount">0.00</span></span>
                             </div>

                             <div class="d-flex justify-content-between align-items-center mb-4">
                                 <span class="text-secondary">Change Amount :</span>
                                 <span class="text-secondary fw-bold">৳ <span id="summary_change_amount">0.00</span></span>
                             </div>

                            <div class="d-flex gap-2 mt-3 mb-2">
                                <button type="button" id="cancel_order" class="btn btn-outline-danger w-50 py-2 rounded-0">Cancel Order</button>
                                <button type="button" id="place_order" class="btn btn-navy w-50 py-2 text-white rounded-0" style="background-color: #001f3f;">Place Order</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- product column -->
            <div class="col-lg-8 col-xl-9 col-xxl-8">
                <!-- pos product -->
                <div class="pos__productcontent">
                    <div class="product__header__filter">
                        <div class="single__filter">
                            <select name="category_id" id="category_filter" class="form-select select2">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="single__filter">
                            <select name="sub_category_id" id="sub_category_filter" class="form-select select2">
                                <option value="">All Sub-Categories</option>
                            </select>
                        </div>
                        <div class="single__filter">
                            <div class="position-relative w-100">
                                <input type="search" id="product_search" class="form-control" placeholder="Search product...">
                                <i class="fa fa-search position-absolute" style="right: 10px; top: 7px; pointer-events: none;"></i>
                            </div>
                        </div>
                    </div>
                    <!-- pos product -->
                    <div class="pos__product_wrapper mt-2" id="pos_product_list">
                        <!-- Products will be loaded here via AJAX -->
                    </div>
                    <div id="load_more_loader" class="text-center my-3 d-none">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- content main end-->

<!-- footer modal list -->

<!-- suspend modal -->
<div class="modal fade" id="suspend_modalnote">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- modal header -->
            <div class="modal-header">
                <h2 class="modal-title">Suspend Sale</h2>
                <button class="btn btn-close" data-bs-dismiss="modal"></button>
            </div>
            <!-- modal body -->
            <div class="modal-body">
                <div class="suspend_notebody">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="" class="form-label">Suspend Note:</label>
                                <textarea class="form-control rounded-0" rows="" id="" name="" cols="50"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- modal footer -->
            <div class="modal-footer">
                <button type="submit" class="btn_main footer_innerbtn misty-color">Save</button>
                <button type="button" class="btn_main footer_innerbtn bg-navy"
                        data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- card modal -->
<div class="modal fade" id="card_payment">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- modal header -->
            <div class="modal-header">
                <h2 class="modal-title">Suspend Sale</h2>
                <button class="btn btn-close" data-bs-dismiss="modal"></button>
            </div>
            <!-- modal body -->
            <div class="modal-body">
                <div class="cardpayment_body">
                    <div class="row gy-3">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="" class="form-label">Card Number:</label>
                                <!-- Reference no -->
                                <input type="text" class="form-control rounded-0" placeholder="Card Number">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="" class="form-label">Card holder name:</label>
                                <!-- Reference no -->
                                <input type="text" class="form-control rounded-0" placeholder="Card holder name">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="" class="form-label">Card Transaction No.</label>
                                <!-- Reference no -->
                                <input type="text" class="form-control rounded-0"
                                       placeholder="Card Transaction No.">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="" class="form-label">Card Type</label>
                                <!-- card type no -->
                                <select name="" id="" class="rounded-0 form-select">
                                    <option value="">Visa</option>
                                    <option value="">Master Card</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="" class="form-label">Month</label>
                                <!-- Reference no -->
                                <input type="text" class="form-control rounded-0" placeholder="Month">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="" class="form-label">Year</label>
                                <!-- Reference no -->
                                <input type="text" class="form-control rounded-0" placeholder="Year">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="" class="form-label">Security Code</label>
                                <!-- Reference no -->
                                <input type="text" class="form-control rounded-0" placeholder="Security Code">
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <!-- modal footer -->
            <div class="modal-footer">
                <button type="submit" class="btn_main footer_innerbtn misty-color">Save</button>
                <button type="button" class="btn_main footer_innerbtn bg-navy"
                        data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- multiple payment -->
<div class="modal fade" id="multiple_payment">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- modal header -->
            <div class="modal-header">
                <h2 class="modal-title">Payment</h2>
                <button class="btn btn-close" data-bs-dismiss="modal"></button>
            </div>
            <!-- modal body -->
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-9">
                        <!-- payment -->
                        <div class="payment_row mt-4">
                            <div class="row gy-3">
                                <div class="col-md-6">
                                    <label for="" class="form-label">Amount:</label>
                                    <div class="input-group">
                                        <button class="input-group-text rounded-0">
                                            <i class="fa-solid fa-money-bill"></i>
                                        </button>
                                        <!-- amount -->
                                        <input type="number" class="form-control rounded-0">
                                    </div>
                                </div>
                                <!-- payment method -->
                                <div class="col-md-6">
                                    <label for="" class="form-label">Payment Method:</label>
                                    <select name="" id="paymentMethod" class="paymentMethod form-select rounded-0">
                                        <option value="" selected hidden>Select Payment</option>
                                        <option value="Cash">Cash</option>
                                        <option value="Bank">Bank</option>
                                        <option value="Nagad">Nagad</option>
                                        <option value="Bkash">Bkash</option>
                                        <option value="Card">Card</option>
                                        <option value="Cheque">Cheque</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                <!-- card payment row -->
                                <div class="cardpayment_row">
                                    <div class="row gy-3">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="" class="form-label">Card
                                                    Number:</label>
                                                <!-- Reference no -->
                                                <input type="text" class="form-control rounded-0"
                                                       placeholder="Card Number">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="" class="form-label">Card holder
                                                    name:</label>
                                                <!-- Reference no -->
                                                <input type="text" class="form-control rounded-0"
                                                       placeholder="Card holder name">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="" class="form-label">Card
                                                    Transaction No:</label>
                                                <!-- Reference no -->
                                                <input type="text" class="form-control rounded-0"
                                                       placeholder="Card Transaction No">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="" class="form-label">Card
                                                    Type:</label>
                                                <!-- Reference no -->
                                                <select name="" id="" class="form-select rounded-0">
                                                    <option value="" selected hidden>Select Card
                                                    </option>
                                                    <option value="">Credit Card</option>
                                                    <option value="">Debit Card</option>
                                                    <option value="">Visa</option>
                                                    <option value="">Master Card</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="" class="form-label">Month</label>
                                                <!-- Reference no -->
                                                <input type="text" class="form-control rounded-0"
                                                       placeholder="Month">
                                            </div>
                                        </div>
                                        <!-- payment note -->
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="" class="form-label">Year:</label>
                                                <input type="text" class="form-control rounded-0"
                                                       placeholder="Year">
                                            </div>
                                        </div>
                                        <!-- payment note -->
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="" class="form-label">Security
                                                    Code:</label>
                                                <input type="text" class="form-control rounded-0"
                                                       placeholder="Security Code">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- checque payment row -->
                                <div class="cheque_payment_row">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="" class="form-label">Cheque
                                                    No.</label>
                                                <input type="text" class="form-control rounded-0">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- checque payment row -->
                                <div class="bank_payment_row">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="" class="form-label">Bank Account
                                                    No.</label>
                                                <input type="text" class="form-control rounded-0">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- cutomer payment row -->
                                <div class="cutomer_payment_row">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="" class="form-label">Transaction
                                                    No.</label>
                                                <input type="text" class="form-control rounded-0">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- payment note -->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="" class="form-label">Payment note:</label>
                                        <textarea class="form-control rounded-0" rows="" id="" name=""
                                                  cols="50"></textarea>
                                    </div>
                                </div>

                                <!-- add payment row -->
                                <div class="add_payment_row mb-3">
                                    <button class="btn_main bg-navy w-100 p-2">Add Payment Row</button>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="" class="form-label">Sell note:</label>
                                            <textarea class="form-control rounded-0" rows="" id="" name=""
                                                      cols="50"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="" class="form-label">Staff note:</label>
                                            <textarea class="form-control rounded-0" rows="" id="" name=""
                                                      cols="50"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="box box-solid bg-orange">
                            <div class="paybox_body row">
                                <div class="col-12">
                                    <strong>
                                        Total Items:
                                    </strong>
                                    <br>
                                    <span class="lead text-bold total_quantity">1.00</span>
                                </div>
                                <div class="col-12">
                                    <hr>
                                    <strong>
                                        Total Payable:
                                    </strong>
                                    <br>
                                    <span class="lead text-bold total_payable_span">1,521.25 ৳</span>
                                </div>
                                <div class="col-12">
                                    <hr>
                                    <strong>
                                        Total Paying:
                                    </strong>
                                    <br>
                                    <span class="lead text-bold total_paying">1,521.25 ৳</span>
                                </div>
                                <div class="col-12">
                                    <hr>
                                    <strong>
                                        Change Return:
                                    </strong>
                                    <br>
                                    <span class="lead text-bold change_return_span">0.00 ৳</span>
                                </div>
                                <div class="col-12">
                                    <hr>
                                    <strong>
                                        Balance:
                                    </strong>
                                    <br>
                                    <span class="lead text-bold balance_due">0.00 ৳</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- modal footer -->
            <div class="modal-footer">
                <button type="submit" class="btn_main footer_innerbtn misty-color">Save</button>
                <button type="button" class="btn_main footer_innerbtn bg-navy"
                        data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- pos footer -->
<footer class="pos_footer">
    <!-- container -->
    <div class="container-fluid">
        <!-- wrapper -->
        <div class="pos_footer_wrapper">
            <!-- pos footer button -->
            <div class="pos_footer_btn d-flex align-items-center gap-2 flex-wrap">
                <!-- draft btn -->
                <button class="btn_main bg-info footer_innerbtn">
                    <span><i class="fa-solid fa-notes"></i></span> Draft </button>
                <!-- Quotation btn -->
                <button class="btn_main bg-purple footer_innerbtn">
                    <span><i class="fa-solid fa-edit"></i></span> Quotation </button>
                <!-- suspend btn -->
                <button class="btn_main misty-color footer_innerbtn" data-bs-target="#suspend_modalnote"
                        data-bs-toggle="modal">
                    <span><i class="fas fa-pause"></i></span> Suspend
                </button>
                <!-- Credit sale btn -->
                <button class="btn_main bg-yellow footer_innerbtn">
                    <span><i class="fa-solid fa-check"></i></span> Credit Sale </button>
                <!-- card btn -->
                <button class="btn_main bg-navy footer_innerbtn" data-bs-target="#card_payment"
                        data-bs-toggle="modal">
                    <span><i class="fa-solid fa-credit-card"></i></span> Card </button>
                <!-- multiple payment -->
                <button class="btn_main btn-success footer_innerbtn" data-bs-target="#multiple_payment"
                        data-bs-toggle="modal">
                    <span><i class="fas fa-money-check-alt"></i></span> Multiple Payment </button>
                <!-- cash payment -->
                <button class="btn_main bg-primary footer_innerbtn">
                    <span><i class="fas fa-money-check-alt"></i></span> Cash </button>
                <!-- total payable -->
                <div class="total_payable">
                    <span>Total Payable:</span>
                    <strong id="total_payment">000</strong>
                </div>
                <!-- cencel -->
                <button class="btn_main misty-color footer_innerbtn">
                    <span><i class="fa-solid fa-xmark"></i></span> Cencel </button>
            </div>
            <!-- recent transection -->
            <button class="btn_main bg-navy footer_innerbtn">
                <span><i class="fas fa-clock"></i></span> Recent Transactions </button>
        </div>
    </div>
</footer>
<!-- pos footer end -->
<!-- Discount Modal -->
<div class="modal fade" id="discount_modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <!-- modal header -->
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Update Discount</h5>
                <button class="btn btn-close" data-bs-dismiss="modal"></button>
            </div>
            <!-- modal body -->
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="form-label text-secondary small">Discount</label>
                            <input type="number" id="modal_discount_amount" class="form-control" value="0">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="form-label text-secondary small">Type</label>
                            <select id="modal_discount_type" class="form-select">
                                <option value="amount">Amount (৳)</option>
                                <option value="percentage">Percentage (%)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <!-- modal footer -->
            <div class="modal-footer border-0 justify-content-end gap-2">
                <button type="button" id="reset_discount" class="btn btn-light px-4 py-2 rounded">Reset</button>
                <button type="button" id="submit_discount" class="btn btn-navy px-4 py-2 text-white rounded" style="background-color: #001f3f;">Submit</button>
            </div>
        </div>
    </div>
</div>

<!-- jquery link -->
<script src="{{asset('admin/pos/assets/js/jquery.min.js')}}"></script>
<!-- bootstrap js -->
<script src="{{asset('admin/pos/assets/js/bootstrap.bundle.min.js')}}"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- main js -->
<script src="{{asset('admin/pos/assets/js/calculator.js')}}"></script>
<script src="{{asset('admin/pos/assets/js/app.js')}}"></script>
<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        width: '100%'
    });

    let offset = 0;
    const limit = 10;
    let isLoading = false;
    let hasMore = true;
    let totalProductsLoaded = 0;
    const maxProducts = 55;

    function loadProducts(append = false) {
        if (isLoading || (!hasMore && append)) return;
        if (append && totalProductsLoaded >= maxProducts) return;

        isLoading = true;
        $('#load_more_loader').removeClass('d-none');

        const category_id = $('#category_filter').val();
        const sub_category_id = $('#sub_category_filter').val();
        const search = $('#product_search').val();

        $.ajax({
            url: "{{ route('admin.pos.getProducts') }}",
            type: "GET",
            data: {
                category_id: category_id,
                sub_category_id: sub_category_id,
                search: search,
                offset: append ? offset : 0
            },
            success: function(response) {
                if (append) {
                    $('#pos_product_list').append(response.html);
                    offset += response.count;
                    totalProductsLoaded += response.count;
                } else {
                    $('#pos_product_list').html(response.html);
                    offset = response.count;
                    totalProductsLoaded = response.count;
                }

                hasMore = response.count === limit;

                if (totalProductsLoaded >= maxProducts) {
                    hasMore = false;
                }

                isLoading = false;
                $('#load_more_loader').addClass('d-none');
            },
            error: function() {
                isLoading = false;
                $('#load_more_loader').addClass('d-none');
            }
        });
    }

    // Initial load
    loadProducts();

    // Filters
    $('#category_filter').on('change', function() {
        const category_id = $(this).val();

        // Load subcategories
        if (category_id) {
            $.ajax({
                url: "{{ route('admin.pos.getSubcategories', ['id' => ':id']) }}".replace(':id', category_id),
                type: "GET",
                success: function(subcategories) {
                    let options = '<option value="">All Sub-Categories</option>';
                    subcategories.forEach(sub => {
                        options += `<option value="${sub.id}">${sub.name}</option>`;
                    });
                    $('#sub_category_filter').html(options).trigger('change.select2');
                }
            });
        } else {
            $('#sub_category_filter').html('<option value="">All Sub-Categories</option>').trigger('change.select2');
        }

        loadProducts();
    });

    $('#sub_category_filter').on('change', function() {
        loadProducts();
    });

    let searchTimer;
    $('#product_search').on('input', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function() {
            loadProducts();
        }, 500);
    });

    // Cart Logic
    let cart = {};
    let extraDiscount = {
        amount: 0,
        type: 'amount'
    };
    let selectedPaymentMethod = 'Cash';

    // Add to cart
    $(document).on('click', '.product__box:not(.stock__out)', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        const price = parseFloat($(this).data('price'));
        const stock = parseInt($(this).data('stock'));
        const discountType = $(this).data('discount-type');
        const discountAmount = parseFloat($(this).data('discount-amount')) || 0;

        if (cart[id]) {
            if (cart[id].quantity < stock) {
                cart[id].quantity++;
            } else {
                alert('No more stock available!');
            }
        } else {
            cart[id] = {
                id: id,
                name: name,
                price: price,
                quantity: 1,
                stock: stock,
                discountType: discountType,
                discountAmount: discountAmount
            };
        }
        updateCart();
    });

    function updateCart() {
        let html = '';
        let totalItems = 0;
        let subtotal = 0;
        let totalProductDiscount = 0;

        for (let id in cart) {
            const item = cart[id];
            const itemOriginalTotal = item.price * item.quantity;

            // Calculate item discount
            let itemDiscount = 0;
            if (item.discountType === 'percentage') {
                itemDiscount = (itemOriginalTotal * item.discountAmount) / 100;
            } else {
                itemDiscount = item.discountAmount * item.quantity;
            }

            subtotal += itemOriginalTotal;
            totalProductDiscount += itemDiscount;
            totalItems += item.quantity;

            html += `
                <tr class="cart__product" data-id="${item.id}">
                    <td style="width: 35%;">
                        <h4 class="cart__product_title">${item.name}</h4>
                        <p class="product_cart_price">
                            <span class="product_price_amount">${item.price.toFixed(2)}</span> ৳
                            ${item.discountAmount > 0 ? `<small class="text-success ms-1">(-${item.discountType === 'percentage' ? item.discountAmount+'%' : '৳'+item.discountAmount})</small>` : ''}
                        </p>
                    </td>
                    <td style="width: 30%;">
                        <div class="input-group">
                            <button class="input-group-text rounded-0 bg-navy add_btn decress_quantity" data-id="${item.id}">
                                <i class="fa-solid fa-minus text-white"></i>
                            </button>
                            <input class="form-control text-center quantity_input" type="text" value="${item.quantity}" readonly>
                            <button class="input-group-text rounded-0 bg-navy add_btn incress_quantity" data-id="${item.id}">
                                <i class="fa-solid fa-plus text-white"></i>
                            </button>
                        </div>
                    </td>
                    <td style="width:25%">
                        <p class="product_item_subtotal text-center">
                            <span class="subtotal__amount">${itemOriginalTotal.toFixed(2)}</span> ৳
                        </p>
                    </td>
                    <td style="width: 10%;" class="text-center">
                        <button class="cart_actionBtn btn_main misty-color remove_item" data-id="${item.id}">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        }

        $('#cart_table_body').html(html || '<tr><td colspan="4" class="text-center p-4 text-muted">No items in cart</td></tr>');
        $('#total_quantity').text(totalItems);
        calculateSummary(subtotal, totalProductDiscount);
    }

    function calculateSummary(subtotal, productDiscount) {
        let tax = 0;
        let deliveryCharge = 0;

        let totalExtraDiscount = 0;
        const discountableAmount = subtotal - productDiscount;

        if (extraDiscount.type === 'percentage') {
            totalExtraDiscount = (discountableAmount * extraDiscount.amount) / 100;
        } else {
            totalExtraDiscount = extraDiscount.amount;
        }

        let total = discountableAmount - totalExtraDiscount + tax + deliveryCharge;
        if (total < 0) total = 0;


        $('#summary_subtotal').text(subtotal.toLocaleString(undefined, {minimumFractionDigits: 2}));
        $('#summary_product_discount').text(productDiscount.toLocaleString(undefined, {minimumFractionDigits: 2}));
        $('#summary_extra_discount').text(totalExtraDiscount.toLocaleString(undefined, {minimumFractionDigits: 2}));
        $('#summary_tax').text(tax.toFixed(2));
        $('#summary_delivery_charge').text(deliveryCharge.toFixed(2));
        $('#summary_total').text(total.toFixed(2));
        $('#summary_total_input').val(total);

        // Ensure change is updated whenever total changes
        updateDynamicChange();
    }

    // // Change Calculation
    // $('#summary_paid_amount').on('keyup', function() {
    //     updateDynamicChange();
    // });

    // function updateDynamicChange() {
    //     const total = cartTotal; // Use the raw numeric total
    //     const paid = parseFloat($('#summary_paid_amount').val()) || 0;

    //     // Change is calculated if paid > total
    //     const change = paid - total;
    //     $('#summary_change_amount').text(change >= 0 ? change.toFixed(2) : '0.00');

    //     // Due is calculated if total > paid
    //     const due = total - paid;
    //     $('#summary_due_amount').text(due >= 0 ? due.toFixed(2) : '0.00');
    // }

// Corrected Event Listener for Paid Amount input
$('#summary_paid_amount').on('input', function () {
    updateDynamicChange();
});

// Corrected Calculation Function with proper DOM targeting
function updateDynamicChange() {
    // Get the raw total from the hidden input (set in calculateSummary)
    let total = parseFloat($('#summary_total_input').val()) || 0;
    // Get the paid amount from the input field, default to 0 if empty or invalid
    let paid = parseFloat($('#summary_paid_amount').val()) || 0;

    // Format numbers to 2 decimal places for consistent calculation
    total = parseFloat(total.toFixed(2));
    paid = parseFloat(paid.toFixed(2));

    let change = 0;
    let due = 0;

    // Core logic: If paid amount is greater than or equal to the total
    if (paid >= total) {
        change = paid - total;
        due = 0;
    } 
    // If paid amount is less than the total
    else {
        change = 0;
        due = total - paid;
    }

    // Update the HTML elements with the calculated values
    $('#summary_change_amount').text(change.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
    $('#summary_due_amount').text(due.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));

    // --- Visual Feedback (Color Coding) ---
    // Remove existing color classes from both elements
    $('#summary_change_amount').removeClass('text-success fw-bold');
    $('#summary_due_amount').removeClass('text-danger fw-bold');
    
    // Add back default text-secondary class
    $('#summary_change_amount').addClass('text-secondary');
    $('#summary_due_amount').addClass('text-secondary');

    // Apply special styling based on the result
    if (due > 0) {
        $('#summary_due_amount').removeClass('text-secondary').addClass('text-danger fw-bold');
    } else if (change > 0) {
        $('#summary_change_amount').removeClass('text-secondary').addClass('text-success fw-bold');
    }
}

    // Quantity actions
    $(document).on('click', '.incress_quantity', function() {
        const id = $(this).data('id');
        if (cart[id].quantity < cart[id].stock) {
            cart[id].quantity++;
            updateCart();
        } else {
            alert('No more stock available!');
        }
    });

    $(document).on('click', '.decress_quantity', function() {
        const id = $(this).data('id');
        if (cart[id].quantity > 1) {
            cart[id].quantity--;
            updateCart();
        }
    });

    $(document).on('click', '.remove_item', function() {
        const id = $(this).data('id');
        delete cart[id];
        updateCart();
    });

    // Discount Modal
    $('#submit_discount').on('click', function() {
        extraDiscount.amount = parseFloat($('#modal_discount_amount').val()) || 0;
        extraDiscount.type = $('#modal_discount_type').val();
        $('#discount_modal').modal('hide');
        updateCart();
    });

    $('#reset_discount').on('click', function() {
        $('#modal_discount_amount').val(0);
        $('#modal_discount_type').val('amount');
        extraDiscount.amount = 0;
        extraDiscount.type = 'amount';
        $('#discount_modal').modal('hide');
        updateCart();
    });

    // Payment Method
    $(document).on('click', '.payment-method-btn', function() {
        $('.payment-method-btn').removeClass('active btn-navy').addClass('btn-outline-secondary').css({'background-color': '', 'color': ''});
        $(this).removeClass('btn-outline-secondary').addClass('active btn-navy').css({'background-color': '#001f3f', 'color': 'white'});
        selectedPaymentMethod = $(this).data('method');
    });



    // Cancel Order
    $('#cancel_order').on('click', function() {
        if (confirm('Are you sure you want to clear the cart?')) {
            cart = {};
            extraDiscount = { amount: 0, type: 'amount' };
            $('#summary_paid_amount').val(0);
            updateCart();
        }
    });

    // Place Order Placeholder
    $('#place_order').on('click', function() {
        if (Object.keys(cart).length === 0) {
            alert('Cart is empty!');
            return;
        }
        alert('Order placed successfully! (Backend integration pending)');
        cart = {};
        updateCart();
    });
});
</script>
</body>

</html>

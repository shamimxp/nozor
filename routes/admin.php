<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Admin\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Admin\Auth\NewPasswordController;
use App\Http\Controllers\Admin\Auth\PasswordResetLinkController;
use App\Http\Controllers\Admin\Auth\RegisteredUserController;
use App\Http\Controllers\Admin\Auth\VerifyEmailController;
use App\Http\Controllers\Admin\RoleController;
use Illuminate\Support\Facades\Route;

Route::get('run-migrate', function () {
    \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    return \Illuminate\Support\Facades\Artisan::output();
});

//Route::group(['middleware' => ['guest:admin'],'prefix'=>'admin','as'=>'admin.'],function(){
//    Route::group(['middleware' => ['auth:admin']], function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('admin.register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('admin.login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
//});

Route::group(['middleware' => ['auth:admin']], function () {
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
});

Route::group(['middleware' => ['auth:admin']], function () {
    Route::get('/home', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
    Route::get('/admin_profile', [\App\Http\Controllers\Admin\ProfileController::class, 'edit'])->name('admin.profile.edit');
    Route::post('/update/admin_profile/{id}', [\App\Http\Controllers\Admin\ProfileController::class, 'update_general'])->name('admin.update_general');
    Route::post('/admin_profile/password/{id}', [\App\Http\Controllers\Admin\ProfileController::class, 'update_password'])->name('admin.update.password');
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('admin.logout');

    //admin user module
    Route::get('/user/list',[AdminController::class,'allUser'])->name('admin.user');
    Route::get('/user/create',[AdminController::class,'userCreate'])->name('admin.user.create');
    Route::post('/user/store',[AdminController::class,'storeAdminUser'])->name('admin.user.store');
    Route::get('/user/edit/{id}', [AdminController::class,'editAdminUser'])->name('admin.user.edit');
    Route::post('/user/update/{id}', [AdminController::class, 'updateAdminUser'])->name('admin.user.update');
    Route::delete('/user/trash/{id}', [AdminController::class,  'trashAdminUser'])->name('admin.user.delete');
    Route::get('/user/deleted', [AdminController::class, 'adminUserDeleted'])->name('admin-user.deleted');
    Route::delete('/user/delete/{id}', [AdminController::class, 'adminUserDelete'])->name('admin-user.delete.forever');
    Route::put('/user/restore/{id}', [AdminController::class, 'adminUserRestore'])->name('admin-user.restore');

    //Roles module
    Route::resource('/roles', RoleController::class);

    //Category module
    Route::resource('category', \App\Http\Controllers\Admin\CategoryController::class, ['as' => 'admin']);
    Route::post('/category/status', [\App\Http\Controllers\Admin\CategoryController::class, 'getStatus'])->name('admin.category.status');

    //SubCategory module
    Route::resource('sub-category', \App\Http\Controllers\Admin\SubCategoryController::class, ['as' => 'admin']);
    Route::post('/sub-category/status', [\App\Http\Controllers\Admin\SubCategoryController::class, 'getStatus'])->name('admin.sub_category.status');

    //Product Attribute module
    Route::resource('product-attribute', \App\Http\Controllers\Admin\ProductAttributeController::class, ['as' => 'admin']);
    Route::post('/product-attribute/status', [\App\Http\Controllers\Admin\ProductAttributeController::class, 'getStatus'])->name('admin.product_attribute.status');

    //Product module
    Route::get('/product/out-of-stock', [\App\Http\Controllers\Admin\ProductController::class, 'outOfStock'])->name('admin.product.out-of-stock');
    Route::resource('product', \App\Http\Controllers\Admin\ProductController::class, ['as' => 'admin']);
    Route::post('/product/status', [\App\Http\Controllers\Admin\ProductController::class, 'getStatus'])->name('admin.product.status');
    Route::post('/product/featured-status', [\App\Http\Controllers\Admin\ProductController::class, 'getFeaturedStatus'])->name('admin.product.featured-status');
    Route::post('/product/update-price-stock', [\App\Http\Controllers\Admin\ProductController::class, 'updatePriceStock'])->name('admin.product.update-price-stock');
    Route::get('/get-subcategory/{category_id}', [\App\Http\Controllers\Admin\ProductController::class, 'getSubCategory'])->name('admin.product.get_subcategory');

    //Unit module
    Route::resource('unit', \App\Http\Controllers\Admin\UnitController::class, ['as' => 'admin']);
    Route::post('/unit/status', [\App\Http\Controllers\Admin\UnitController::class, 'getStatus'])->name('admin.unit.status');

    //Customer module
    Route::resource('customer', \App\Http\Controllers\Admin\CustomerController::class, ['as' => 'admin']);

    //Vendor module
    Route::resource('vendor', \App\Http\Controllers\Admin\VendorController::class, ['as' => 'admin']);

    //Fabric module
    Route::resource('fabric', \App\Http\Controllers\Admin\FabricController::class, ['as' => 'admin']);

    //Fabric Price module
    Route::resource('fabric-price', \App\Http\Controllers\Admin\FabricPriceController::class, ['as' => 'admin']);

    //Purchase module
    Route::get('/purchase/vendor-history-pdf', [\App\Http\Controllers\Admin\PurchaseController::class, 'vendorHistoryPdf'])->name('admin.purchase.vendor-history-pdf');
    Route::get('/purchase/vendor-history', [\App\Http\Controllers\Admin\PurchaseController::class, 'vendorHistory'])->name('admin.purchase.vendor-history');
    Route::get('/purchase/due-list', [\App\Http\Controllers\Admin\PurchaseController::class, 'dueList'])->name('admin.purchase.due-list');
    Route::get('/purchase/vendor-payments/{id}', [\App\Http\Controllers\Admin\PurchaseController::class, 'vendorPaymentDetails'])->name('admin.purchase.vendor-payment-details');
    Route::post('/purchase/add-payment', [\App\Http\Controllers\Admin\PurchaseController::class, 'addPayment'])->name('admin.purchase.add-payment');
    Route::get('/purchase/export-excel', [\App\Http\Controllers\Admin\PurchaseController::class, 'exportExcel'])->name('admin.purchase.export-excel');
    Route::get('/purchase/export-list-pdf', [\App\Http\Controllers\Admin\PurchaseController::class, 'exportListPdf'])->name('admin.purchase.export-list-pdf');
    Route::resource('purchase', \App\Http\Controllers\Admin\PurchaseController::class, ['as' => 'admin']);

    //Custom Order module
    Route::get('/custom-order/export-pdf/{id}', [\App\Http\Controllers\Admin\CustomOrderController::class, 'exportPdf'])->name('admin.custom-order.export-pdf');
    Route::get('/custom-order/export-excel', [\App\Http\Controllers\Admin\CustomOrderController::class, 'exportExcel'])->name('admin.custom-order.export-excel');
    Route::get('/custom-order/export-list-pdf', [\App\Http\Controllers\Admin\CustomOrderController::class, 'exportListPdf'])->name('admin.custom-order.export-list-pdf');
    Route::post('/custom-order/assign-vendor', [\App\Http\Controllers\Admin\CustomOrderController::class, 'assignVendor'])->name('admin.custom-order.assign-vendor');
    Route::post('/custom-order/update-status', [\App\Http\Controllers\Admin\CustomOrderController::class, 'updateStatus'])->name('admin.custom-order.update-status');
    Route::get('/custom-order-fabric-prices', [\App\Http\Controllers\Admin\CustomOrderController::class, 'getFabricPrices'])->name('admin.custom-order.fabric-prices');
    Route::get('/custom-order/due-list', [\App\Http\Controllers\Admin\CustomOrderController::class, 'dueList'])->name('admin.custom-order.due-list');
    Route::resource('custom-order', \App\Http\Controllers\Admin\CustomOrderController::class, ['as' => 'admin']);

    //POS Order module
    Route::get('/pos-order/analysis', [\App\Http\Controllers\Admin\PosOrderController::class, 'analysis'])->name('admin.pos-order.analysis');
    Route::get('/pos-order/export-pdf/{id}', [\App\Http\Controllers\Admin\PosOrderController::class, 'exportPdf'])->name('admin.pos-order.export-pdf');
    Route::post('/pos-order/cancel/{id}', [\App\Http\Controllers\Admin\PosOrderController::class, 'cancel'])->name('admin.pos-order.cancel');
    Route::get('/pos-order/due-list', [\App\Http\Controllers\Admin\PosOrderController::class, 'dueList'])->name('admin.pos-order.due-list');
    Route::resource('pos-order', \App\Http\Controllers\Admin\PosOrderController::class, ['as' => 'admin']);

    // Due Collection / Customer Payment module
    Route::get('/due-collection', [\App\Http\Controllers\Admin\CustomerPaymentController::class, 'index'])->name('admin.due-collection.index');
    Route::post('/due-collection/store', [\App\Http\Controllers\Admin\CustomerPaymentController::class, 'store'])->name('admin.due-collection.store');
    Route::get('/due-collection/customer-history/{id}', [\App\Http\Controllers\Admin\CustomerPaymentController::class, 'customerHistory'])->name('admin.due-collection.customer-history');
    //Inventory Purchase module
    Route::get('/inventory-purchase/receive/{id}', [\App\Http\Controllers\Admin\InventoryPurchaseController::class, 'receive'])->name('admin.inventory-purchase.receive');
    Route::post('/inventory-purchase/receive/{id}', [\App\Http\Controllers\Admin\InventoryPurchaseController::class, 'receiveStore'])->name('admin.inventory-purchase.receive.store');
    Route::post('/inventory-purchase/{id}/payment', [\App\Http\Controllers\Admin\InventoryPurchaseController::class, 'addPayment'])->name('admin.inventory-purchase.payment.store');
    Route::get('/inventory-purchase/{id}/payment-history', [\App\Http\Controllers\Admin\InventoryPurchaseController::class, 'paymentHistory'])->name('admin.inventory-purchase.payment-history');
    Route::resource('inventory-purchase', \App\Http\Controllers\Admin\InventoryPurchaseController::class, ['as' => 'admin']);

    //Stock Adjustment module
    Route::post('/stock-adjustment/{id}/receive', [\App\Http\Controllers\Admin\StockAdjustmentController::class, 'receive'])->name('admin.stock-adjustment.receive');
    Route::resource('stock-adjustment', \App\Http\Controllers\Admin\StockAdjustmentController::class, ['as' => 'admin']);

    // Report module
    Route::get('/report/custom-order-sales', [\App\Http\Controllers\Admin\ReportController::class, 'customOrderReport'])->name('admin.report.custom-order-report');
    Route::get('/report/pos-order-sales', [\App\Http\Controllers\Admin\ReportController::class, 'posOrderReport'])->name('admin.report.pos-order-report');
    Route::get('/report/custom-profit-loss', [\App\Http\Controllers\Admin\ReportController::class, 'customProfitLossReport'])->name('admin.report.custom-profit-loss');
    Route::get('/report/pos-profit-loss', [\App\Http\Controllers\Admin\ReportController::class, 'posProfitLossReport'])->name('admin.report.pos-profit-loss');
    Route::get('/report/product-stock', [\App\Http\Controllers\Admin\ReportController::class, 'productStockReport'])->name('admin.report.product-stock');
    Route::get('/report/export-product-stock-excel', [\App\Http\Controllers\Admin\ReportController::class, 'exportProductStockExcel'])->name('admin.report.export-product-stock-excel');
    Route::get('/report/export-product-stock-pdf', [\App\Http\Controllers\Admin\ReportController::class, 'exportProductStockPdf'])->name('admin.report.export-product-stock-pdf');
    Route::get('/report/export-custom-profit-loss-excel', [\App\Http\Controllers\Admin\ReportController::class, 'exportCustomProfitLossExcel'])->name('admin.report.export-custom-profit-loss-excel');
    Route::get('/report/export-custom-profit-loss-pdf', [\App\Http\Controllers\Admin\ReportController::class, 'exportCustomProfitLossPdf'])->name('admin.report.export-custom-profit-loss-pdf');
    Route::get('/report/export-pos-profit-loss-excel', [\App\Http\Controllers\Admin\ReportController::class, 'exportPosProfitLossExcel'])->name('admin.report.export-pos-profit-loss-excel');
    Route::get('/report/export-pos-profit-loss-pdf', [\App\Http\Controllers\Admin\ReportController::class, 'exportPosProfitLossPdf'])->name('admin.report.export-pos-profit-loss-pdf');
    Route::get('/report/export-custom-sales-excel', [\App\Http\Controllers\Admin\ReportController::class, 'exportCustomSalesExcel'])->name('admin.report.export-custom-sales-excel');
    Route::get('/report/export-custom-sales-pdf', [\App\Http\Controllers\Admin\ReportController::class, 'exportCustomSalesPdf'])->name('admin.report.export-custom-sales-pdf');
    Route::get('/report/export-pos-sales-excel', [\App\Http\Controllers\Admin\ReportController::class, 'exportPosSalesExcel'])->name('admin.report.export-pos-sales-excel');
    Route::get('/report/export-pos-sales-pdf', [\App\Http\Controllers\Admin\ReportController::class, 'exportPosSalesPdf'])->name('admin.report.export-pos-sales-pdf');
});

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

Route::group(['middleware' => ['guest:admin']], function () {
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
});

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
    Route::get('/home', [\App\Http\Controllers\Admin\AdminController::class, 'dashboard'])->name('admin.dashboard');
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
    \App\Support\TrashRoutes::register('category', \App\Http\Controllers\Admin\CategoryController::class, 'category');
    Route::resource('category', \App\Http\Controllers\Admin\CategoryController::class, ['as' => 'admin']);
    Route::post('/category/status', [\App\Http\Controllers\Admin\CategoryController::class, 'getStatus'])->name('admin.category.status');

    //SubCategory module
    \App\Support\TrashRoutes::register('sub-category', \App\Http\Controllers\Admin\SubCategoryController::class, 'sub-category');
    Route::resource('sub-category', \App\Http\Controllers\Admin\SubCategoryController::class, ['as' => 'admin']);
    Route::post('/sub-category/status', [\App\Http\Controllers\Admin\SubCategoryController::class, 'getStatus'])->name('admin.sub_category.status');

    //Product Attribute module
    \App\Support\TrashRoutes::register('product-attribute', \App\Http\Controllers\Admin\ProductAttributeController::class, 'product-attribute');
    Route::resource('product-attribute', \App\Http\Controllers\Admin\ProductAttributeController::class, ['as' => 'admin']);
    Route::post('/product-attribute/status', [\App\Http\Controllers\Admin\ProductAttributeController::class, 'getStatus'])->name('admin.product_attribute.status');

    // Variations
    \App\Support\TrashRoutes::register('variations', \App\Http\Controllers\Admin\VariationController::class, 'variations');
    Route::resource('variations', \App\Http\Controllers\Admin\VariationController::class, ['as' => 'admin']);

    //Product module
    Route::get('/product/out-of-stock', [\App\Http\Controllers\Admin\ProductController::class, 'outOfStock'])->name('admin.product.out-of-stock');
    \App\Support\TrashRoutes::register('product', \App\Http\Controllers\Admin\ProductController::class, 'product');
    Route::get('/product/{id}/duplicate', [\App\Http\Controllers\Admin\ProductController::class, 'duplicate'])->name('admin.product.duplicate');
    Route::post('/product/{id}/duplicate', [\App\Http\Controllers\Admin\ProductController::class, 'duplicateStore'])->name('admin.product.duplicate.store');
    Route::resource('product', \App\Http\Controllers\Admin\ProductController::class, ['as' => 'admin']);
    Route::post('/product/status', [\App\Http\Controllers\Admin\ProductController::class, 'getStatus'])->name('admin.product.status');
    Route::post('/product/featured-status', [\App\Http\Controllers\Admin\ProductController::class, 'getFeaturedStatus'])->name('admin.product.featured-status');
    Route::post('/product/visibility', [\App\Http\Controllers\Admin\ProductController::class, 'updateVisibility'])->name('admin.product.visibility');
    Route::post('/product/manufacturer-status', [\App\Http\Controllers\Admin\ProductController::class, 'getManufacturerStatus'])->name('admin.product.manufacturer-status');
    Route::post('/product/update-price-stock', [\App\Http\Controllers\Admin\ProductController::class, 'updatePriceStock'])->name('admin.product.update-price-stock');
    Route::get('/get-subcategory/{category_id}', [\App\Http\Controllers\Admin\ProductController::class, 'getSubCategory'])->name('admin.product.get_subcategory');

    //Unit module
    \App\Support\TrashRoutes::register('unit', \App\Http\Controllers\Admin\UnitController::class, 'unit');
    Route::resource('unit', \App\Http\Controllers\Admin\UnitController::class, ['as' => 'admin']);
    Route::post('/unit/status', [\App\Http\Controllers\Admin\UnitController::class, 'getStatus'])->name('admin.unit.status');

    //Customer module
    \App\Support\TrashRoutes::register('customer', \App\Http\Controllers\Admin\CustomerController::class, 'customer');
    Route::resource('customer', \App\Http\Controllers\Admin\CustomerController::class, ['as' => 'admin']);
    
    // Worker module
    \App\Support\TrashRoutes::register('worker', \App\Http\Controllers\WorkerController::class, 'worker');
    Route::resource('worker', \App\Http\Controllers\WorkerController::class, ['as' => 'admin']);

    //Dealer module
    \App\Support\TrashRoutes::register('dealer', \App\Http\Controllers\Admin\DealerController::class, 'dealer');
    Route::get('dealer/{id}/login-as', [\App\Http\Controllers\Admin\DealerImpersonationController::class, 'loginAs'])->name('admin.dealer.loginAs');
    Route::resource('dealer', \App\Http\Controllers\Admin\DealerController::class, ['as' => 'admin']);

    //Vendor module
    \App\Support\TrashRoutes::register('vendor', \App\Http\Controllers\Admin\VendorController::class, 'vendor');
    Route::resource('vendor', \App\Http\Controllers\Admin\VendorController::class, ['as' => 'admin']);

    //Fabric module
    \App\Support\TrashRoutes::register('fabric', \App\Http\Controllers\Admin\FabricController::class, 'fabric');
    Route::resource('fabric', \App\Http\Controllers\Admin\FabricController::class, ['as' => 'admin']);

    //Fabric Price module
    \App\Support\TrashRoutes::register('fabric-price', \App\Http\Controllers\Admin\FabricPriceController::class, 'fabric-price');
    Route::resource('fabric-price', \App\Http\Controllers\Admin\FabricPriceController::class, ['as' => 'admin']);

    //Raw Material Product module
    \App\Support\TrashRoutes::register('raw-material-product', \App\Http\Controllers\Admin\RawMaterialProductController::class, 'raw-material-product');
    Route::resource('raw-material-product', \App\Http\Controllers\Admin\RawMaterialProductController::class, ['as' => 'admin']);
    Route::post('/raw-material-product/status', [\App\Http\Controllers\Admin\RawMaterialProductController::class, 'getStatus'])->name('admin.raw_material_product.status');
    \App\Support\TrashRoutes::register('product-recipe', \App\Http\Controllers\Admin\ProductRecipeController::class, 'product-recipe');
    Route::resource('product-recipe', \App\Http\Controllers\Admin\ProductRecipeController::class, ['as' => 'admin']);
    Route::get('/product-price-calculate', [\App\Http\Controllers\Admin\ProductPriceController::class,'calculate'])->name('admin.product-price-calculator');

    // Cost Head module
    \App\Support\TrashRoutes::register('cost-head', \App\Http\Controllers\Admin\CostHeadController::class, 'cost-head');
    Route::resource('cost-head', \App\Http\Controllers\Admin\CostHeadController::class, ['as' => 'admin']);
    Route::post('/cost-head/status', [\App\Http\Controllers\Admin\CostHeadController::class, 'getStatus'])->name('admin.cost-head.status');

    // Cost Setting module
    Route::get('/cost-setting/check-title', [\App\Http\Controllers\Admin\CostSettingController::class, 'checkTitle'])->name('admin.cost-setting.check-title');
    \App\Support\TrashRoutes::register('cost-setting', \App\Http\Controllers\Admin\CostSettingController::class, 'cost-setting');
    Route::get('/cost-setting/{id}/details', [\App\Http\Controllers\Admin\CostSettingController::class, 'getDetails'])->name('admin.cost-setting.details');
    Route::resource('cost-setting', \App\Http\Controllers\Admin\CostSettingController::class, ['as' => 'admin']);
    Route::post('/cost-setting/status', [\App\Http\Controllers\Admin\CostSettingController::class, 'getStatus'])->name('admin.cost-setting.status');

    // Material Type module
    \App\Support\TrashRoutes::register('material-type', \App\Http\Controllers\Admin\MaterialTypeController::class, 'material-type');
    Route::resource('material-type', \App\Http\Controllers\Admin\MaterialTypeController::class, ['as' => 'admin']);
    Route::post('/material-type/status', [\App\Http\Controllers\Admin\MaterialTypeController::class, 'getStatus'])->name('admin.material_type.status');
    // Legacy Custom Purchase module removed (use inventory-purchase for product purchases)
    // Route::resource('purchase', \App\Http\Controllers\Admin\PurchaseController::class, ['as' => 'admin']);

    //Custom Order module
    Route::get('/custom-order/export-pdf/{id}', [\App\Http\Controllers\Admin\CustomOrderController::class, 'exportPdf'])->name('admin.custom-order.export-pdf');
    Route::get('/custom-order/export-excel', [\App\Http\Controllers\Admin\CustomOrderController::class, 'exportExcel'])->name('admin.custom-order.export-excel');
    Route::get('/custom-order/export-list-pdf', [\App\Http\Controllers\Admin\CustomOrderController::class, 'exportListPdf'])->name('admin.custom-order.export-list-pdf');
    Route::post('/custom-order/assign-vendor', [\App\Http\Controllers\Admin\CustomOrderController::class, 'assignVendor'])->name('admin.custom-order.assign-vendor');
    Route::post('/custom-order/update-status', [\App\Http\Controllers\Admin\CustomOrderController::class, 'updateStatus'])->name('admin.custom-order.update-status');
    Route::get('/custom-order-fabric-prices', [\App\Http\Controllers\Admin\CustomOrderController::class, 'getFabricPrices'])->name('admin.custom-order.fabric-prices');
    Route::get('/custom-order/due-list', [\App\Http\Controllers\Admin\CustomOrderController::class, 'dueList'])->name('admin.custom-order.due-list');
    \App\Support\TrashRoutes::register('custom-order', \App\Http\Controllers\Admin\CustomOrderController::class, 'custom-order');
    Route::resource('custom-order', \App\Http\Controllers\Admin\CustomOrderController::class, ['as' => 'admin']);

    Route::get('/web-order/notifications', [\App\Http\Controllers\Admin\WebOrderController::class, 'notifications'])->name('admin.web-order.notifications');
    Route::get('/web-order/export-excel', [\App\Http\Controllers\Admin\WebOrderController::class, 'exportExcel'])->name('admin.web-order.export-excel');
    Route::get('/web-order/export-list-pdf', [\App\Http\Controllers\Admin\WebOrderController::class, 'exportListPdf'])->name('admin.web-order.export-list-pdf');
    Route::get('/web-order/export-pdf/{id}', [\App\Http\Controllers\Admin\WebOrderController::class, 'exportPdf'])->name('admin.web-order.export-pdf');
    Route::post('/web-order/update-status', [\App\Http\Controllers\Admin\WebOrderController::class, 'updateStatus'])->name('admin.web-order.update-status');
    Route::resource('web-order', \App\Http\Controllers\Admin\WebOrderController::class, ['as' => 'admin', 'except' => ['create', 'store', 'destroy']]);

    // Wishlist module
    \App\Support\TrashRoutes::register('wishlist', \App\Http\Controllers\Admin\WishlistController::class, 'wishlist');
    Route::resource('wishlist', \App\Http\Controllers\Admin\WishlistController::class, ['as' => 'admin', 'only' => ['index', 'destroy']]);

    // Order Requests module
    Route::get('order-requests', [\App\Http\Controllers\OrderRequestController::class, 'index'])->name('admin.order-requests.index');
    Route::get('order-requests/{id}', [\App\Http\Controllers\OrderRequestController::class, 'show'])->name('admin.order-requests.show');
    Route::post('order-requests/{id}/confirm', [\App\Http\Controllers\OrderRequestController::class, 'confirm'])->name('admin.order-requests.confirm');
    Route::post('order-requests/{id}/draft', [\App\Http\Controllers\OrderRequestController::class, 'createDraft'])->name('admin.order-requests.draft');

    // Draft Orders module
    \App\Support\TrashRoutes::register('draft-orders', \App\Http\Controllers\Admin\DraftOrderController::class, 'draft-orders');
    Route::get('/draft-orders/{id}/export-pdf', [\App\Http\Controllers\Admin\DraftOrderController::class, 'exportPdf'])->name('admin.draft-orders.export-pdf');
    Route::get('/draft-orders/{id}/print', [\App\Http\Controllers\Admin\DraftOrderController::class, 'print'])->name('admin.draft-orders.print');
    Route::post('/draft-orders/{id}/convert', [\App\Http\Controllers\Admin\DraftOrderController::class, 'convertToOrder'])->name('admin.draft-orders.convert');
    Route::delete('/draft-orders/{id}/items/{itemId}', [\App\Http\Controllers\Admin\DraftOrderController::class, 'deleteItem'])->name('admin.draft-orders.delete-item');
    Route::resource('draft-orders', \App\Http\Controllers\Admin\DraftOrderController::class, ['as' => 'admin']);

    // Dealer Order module
    Route::get('/dealer-order/export-list-excel', [\App\Http\Controllers\Admin\DealerOrderController::class, 'exportExcel'])->name('admin.dealer-order.export-list-excel');
    Route::get('/dealer-order/export-list-pdf', [\App\Http\Controllers\Admin\DealerOrderController::class, 'exportListPdf'])->name('admin.dealer-order.export-list-pdf');
    Route::get('/dealer-order/export-pdf/{id}', [\App\Http\Controllers\Admin\DealerOrderController::class, 'exportPdf'])->name('admin.dealer-order.export-pdf');
    Route::post('/dealer-order/status', [\App\Http\Controllers\Admin\DealerOrderController::class, 'updateStatus'])->name('admin.dealer-order.status');
    Route::get('/dealer-order/due-list', [\App\Http\Controllers\Admin\DealerOrderController::class, 'dueList'])->name('admin.dealer-order.due-list');
    Route::post('/dealer-order/item/update', [\App\Http\Controllers\Admin\DealerOrderController::class, 'updateItem'])->name('admin.dealer-order.item.update');
    \App\Support\TrashRoutes::register('dealer-order', \App\Http\Controllers\Admin\DealerOrderController::class, 'dealer-order');
    Route::resource('dealer-order', \App\Http\Controllers\Admin\DealerOrderController::class, ['as' => 'admin']);

    //POS Order module
    Route::get('/pos-order/export-list-excel', [\App\Http\Controllers\Admin\PosOrderController::class, 'exportListExcel'])->name('admin.pos-order.export-list-excel');
    Route::get('/pos-order/export-list-pdf', [\App\Http\Controllers\Admin\PosOrderController::class, 'exportListPdf'])->name('admin.pos-order.export-list-pdf');
    Route::get('/pos-order/analysis', [\App\Http\Controllers\Admin\PosOrderController::class, 'analysis'])->name('admin.pos-order.analysis');
    Route::get('/pos-order/export-pdf/{id}', [\App\Http\Controllers\Admin\PosOrderController::class, 'exportPdf'])->name('admin.pos-order.export-pdf');
    Route::post('/pos-order/cancel/{id}', [\App\Http\Controllers\Admin\PosOrderController::class, 'cancel'])->name('admin.pos-order.cancel');
    Route::get('/pos-order/due-list', [\App\Http\Controllers\Admin\PosOrderController::class, 'dueList'])->name('admin.pos-order.due-list');
    \App\Support\TrashRoutes::register('pos-order', \App\Http\Controllers\Admin\PosOrderController::class, 'pos-order');
    Route::resource('pos-order', \App\Http\Controllers\Admin\PosOrderController::class, ['as' => 'admin']);

    // Order Activity History API
    Route::get('/orders/{type}/{id}/history', [\App\Http\Controllers\Admin\OrderHistoryController::class, 'getHistory'])->name('admin.orders.history');

    // Collections module
    Route::get('/collections', [\App\Http\Controllers\Admin\CollectionController::class, 'index'])->name('admin.collections.index');
    Route::get('/collections/create', [\App\Http\Controllers\Admin\CollectionController::class, 'create'])->name('admin.collections.create');
    Route::post('/collections/store', [\App\Http\Controllers\Admin\CollectionController::class, 'store'])->name('admin.collections.store');
    Route::post('/collections/{id}/accept', [\App\Http\Controllers\Admin\CollectionController::class, 'accept'])->name('admin.collections.accept');
    Route::post('/collections/{id}/reject', [\App\Http\Controllers\Admin\CollectionController::class, 'reject'])->name('admin.collections.reject');
    Route::get('/collections/customer-history/{id}', [\App\Http\Controllers\Admin\CollectionController::class, 'customerHistory'])->name('admin.collections.customer-history');
    Route::get('/collections/dealer-history/{id}', [\App\Http\Controllers\Admin\CollectionController::class, 'dealerHistory'])->name('admin.collections.dealer-history');

    // Legacy Aliases & Redirects for Due Collection
    Route::get('/due-collection', fn() => redirect()->route('admin.collections.index'))->name('admin.due-collection.index');
    Route::post('/due-collection/store', [\App\Http\Controllers\Admin\CollectionController::class, 'store'])->name('admin.due-collection.store');
    Route::get('/due-collection/customer-history/{id}', [\App\Http\Controllers\Admin\CollectionController::class, 'customerHistory'])->name('admin.due-collection.customer-history');

    // Dealer Wallets module
    Route::get('/dealer-wallets', [\App\Http\Controllers\Admin\DealerWalletController::class, 'index'])->name('admin.dealer-wallets.index');
    Route::get('/dealer-wallets/{id}', [\App\Http\Controllers\Admin\DealerWalletController::class, 'show'])->name('admin.dealer-wallets.show');
    Route::post('/dealer-wallets/{id}/deposit', [\App\Http\Controllers\Admin\DealerWalletController::class, 'deposit'])->name('admin.dealer-wallets.deposit');
    //Inventory Purchase module
    Route::get('/inventory-purchase/vendor-history-pdf', [\App\Http\Controllers\Admin\InventoryPurchaseController::class, 'vendorHistoryPdf'])->name('admin.inventory-purchase.vendor-history-pdf');
    Route::get('/inventory-purchase/vendor-history', [\App\Http\Controllers\Admin\InventoryPurchaseController::class, 'vendorHistory'])->name('admin.inventory-purchase.vendor-history');
    Route::get('/inventory-purchase/receive/{id}', [\App\Http\Controllers\Admin\InventoryPurchaseController::class, 'receive'])->name('admin.inventory-purchase.receive');
    Route::post('/inventory-purchase/receive/{id}', [\App\Http\Controllers\Admin\InventoryPurchaseController::class, 'receiveStore'])->name('admin.inventory-purchase.receive.store');
    Route::post('/inventory-purchase/{id}/payment', [\App\Http\Controllers\Admin\InventoryPurchaseController::class, 'addPayment'])->name('admin.inventory-purchase.payment.store');
    Route::get('/inventory-purchase/{id}/payment-history', [\App\Http\Controllers\Admin\InventoryPurchaseController::class, 'paymentHistory'])->name('admin.inventory-purchase.payment-history');
    Route::resource('inventory-purchase', \App\Http\Controllers\Admin\InventoryPurchaseController::class, ['as' => 'admin']);

    // Manufacture module
    Route::get('/manufacture/pending', [\App\Http\Controllers\Admin\ManufactureListController::class, 'pending'])->name('admin.manufacture.pending');
    Route::get('/manufacture/confirm', [\App\Http\Controllers\Admin\ManufactureListController::class, 'confirm'])->name('admin.manufacture.confirm');
    Route::get('/manufacture/complete', [\App\Http\Controllers\Admin\ManufactureListController::class, 'complete'])->name('admin.manufacture.complete');
    Route::get('/manufacture/body-part', [\App\Http\Controllers\Admin\ManufactureListController::class, 'bodyPart'])->name('admin.manufacture.body-part');
    Route::get('/manufacture/finishing-part', [\App\Http\Controllers\Admin\ManufactureListController::class, 'finishingPart'])->name('admin.manufacture.finishing-part');
    Route::get('/manufacture/export', [\App\Http\Controllers\Admin\ManufactureController::class, 'export'])->name('admin.manufacture.export');
    Route::get('/manufacture/receive', [\App\Http\Controllers\Admin\ManufactureController::class, 'receiveUI'])->name('admin.manufacture.receive');
    Route::post('/manufacture/receive/process', [\App\Http\Controllers\Admin\ManufactureController::class, 'receiveProcess'])->name('admin.manufacture.receive.process');
    Route::get('/manufacture/get-prices', [\App\Http\Controllers\Admin\ManufactureController::class, 'getPrices'])->name('admin.manufacture.get-prices');
    Route::post('/manufacture/change-status', [\App\Http\Controllers\Admin\ManufactureController::class, 'changeStatus'])->name('admin.manufacture.change-status');
    Route::get('/manufacture/{id}/print', [\App\Http\Controllers\Admin\ManufactureController::class, 'print'])->name('admin.manufacture.print');
    \App\Support\TrashRoutes::register('manufacture', \App\Http\Controllers\Admin\ManufactureController::class, 'manufacture');
    Route::resource('manufacture', \App\Http\Controllers\Admin\ManufactureController::class, ['as' => 'admin']);

    //Stock Adjustment module
    \App\Support\TrashRoutes::register('stock-adjustment', \App\Http\Controllers\Admin\StockAdjustmentController::class, 'stock-adjustment');
    Route::post('/stock-adjustment/{id}/receive', [\App\Http\Controllers\Admin\StockAdjustmentController::class, 'receive'])->name('admin.stock-adjustment.receive');
    Route::resource('stock-adjustment', \App\Http\Controllers\Admin\StockAdjustmentController::class, ['as' => 'admin']);

    // Report module
    Route::get('/report/custom-order-sales', [\App\Http\Controllers\Admin\ReportController::class, 'customOrderReport'])->name('admin.report.custom-order-report');
    Route::get('/report/pos-order-sales', [\App\Http\Controllers\Admin\ReportController::class, 'posOrderReport'])->name('admin.report.pos-order-report');
    Route::get('/report/custom-profit-loss', [\App\Http\Controllers\Admin\ReportController::class, 'customProfitLossReport'])->name('admin.report.custom-profit-loss');
    Route::get('/report/pos-profit-loss', [\App\Http\Controllers\Admin\ReportController::class, 'posProfitLossReport'])->name('admin.report.pos-profit-loss');
    Route::get('/report/product-stock', [\App\Http\Controllers\Admin\ReportController::class, 'productStockReport'])->name('admin.report.product-stock');
    Route::get('/report/manufacture-report', [\App\Http\Controllers\Admin\ReportController::class, 'manufactureReport'])->name('admin.report.manufacture-report');
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

    // Banner module
    \App\Support\TrashRoutes::register('banner', \App\Http\Controllers\Admin\BannerController::class, 'banner');
    Route::resource('banner', \App\Http\Controllers\Admin\BannerController::class, ['as' => 'admin']);
    Route::post('/banner/status', [\App\Http\Controllers\Admin\BannerController::class, 'getStatus'])->name('admin.banner.status');

    // Coupon module
    \App\Support\TrashRoutes::register('coupon', \App\Http\Controllers\Admin\CouponController::class, 'coupon');
    Route::resource('coupon', \App\Http\Controllers\Admin\CouponController::class, ['as' => 'admin']);

    // Web Settings module
    Route::get('/web-setting', [\App\Http\Controllers\Admin\WebSettingController::class, 'index'])->name('admin.web-setting.index');
    Route::post('/web-setting/update', [\App\Http\Controllers\Admin\WebSettingController::class, 'update'])->name('admin.web-setting.update');

    // Product Reviews module
    \App\Support\TrashRoutes::register('product-reviews', \App\Http\Controllers\Admin\ProductReviewController::class, 'product_reviews');
    Route::get('/product-reviews', [\App\Http\Controllers\Admin\ProductReviewController::class, 'index'])->name('admin.product_reviews.index');
    Route::post('/product-reviews/status', [\App\Http\Controllers\Admin\ProductReviewController::class, 'updateStatus'])->name('admin.product_reviews.status');
    Route::post('/product-reviews/reply/{id}', [\App\Http\Controllers\Admin\ProductReviewController::class, 'reply'])->name('admin.product_reviews.reply');
    Route::delete('/product-reviews/delete/{id}', [\App\Http\Controllers\Admin\ProductReviewController::class, 'destroy'])->name('admin.product_reviews.destroy');

    // Contact Messages
    \App\Support\TrashRoutes::register('contact-messages', \App\Http\Controllers\Admin\ContactMessageController::class, 'contact-messages');
    Route::get('contact-messages', [\App\Http\Controllers\Admin\ContactMessageController::class, 'index'])->name('admin.contact-messages.index');
    Route::delete('contact-messages/{id}', [\App\Http\Controllers\Admin\ContactMessageController::class, 'destroy'])->name('admin.contact-messages.destroy');

    // Subscribers
    \App\Support\TrashRoutes::register('subscribers', \App\Http\Controllers\Admin\SubscriberController::class, 'subscribers');
    Route::get('subscribers', [\App\Http\Controllers\Admin\SubscriberController::class, 'index'])->name('admin.subscribers.index');
    Route::delete('subscribers/{id}', [\App\Http\Controllers\Admin\SubscriberController::class, 'destroy'])->name('admin.subscribers.destroy');

    // Worker Invoice List (Renamed from worker-payment)
    \App\Support\TrashRoutes::register('worker-invoices', \App\Http\Controllers\Admin\WorkerPaymentController::class, 'worker-invoices');
    Route::get('/worker-invoices', [\App\Http\Controllers\Admin\WorkerPaymentController::class, 'index'])->name('admin.worker-invoices.index');
    Route::delete('/worker-invoices/{id}', [\App\Http\Controllers\Admin\WorkerPaymentController::class, 'destroy'])->name('admin.worker-invoices.destroy');

    // Worker Balance Sheet & Worker Payment List
    \App\Support\TrashRoutes::register('worker-payouts', \App\Http\Controllers\Admin\WorkerBalanceController::class, 'worker-payouts');
    Route::get('/worker-balance-sheet', [\App\Http\Controllers\Admin\WorkerBalanceController::class, 'balanceSheet'])->name('admin.worker-balance-sheet.index');
    Route::get('/worker-payments', [\App\Http\Controllers\Admin\WorkerBalanceController::class, 'paymentList'])->name('admin.worker-payouts.index');
    Route::post('/worker-payouts', [\App\Http\Controllers\Admin\WorkerBalanceController::class, 'storePayout'])->name('admin.worker-payouts.store');
    Route::get('/worker-payments/{id}/download', [\App\Http\Controllers\Admin\WorkerBalanceController::class, 'downloadTokenPdf'])->name('admin.worker-payouts.download');
    Route::get('/worker-payments/{id}', [\App\Http\Controllers\Admin\WorkerBalanceController::class, 'showPayout'])->name('admin.worker-payouts.show');
    Route::delete('/worker-payments/{id}', [\App\Http\Controllers\Admin\WorkerBalanceController::class, 'destroyPayout'])->name('admin.worker-payouts.destroy');

    // Backwards-compatible aliases
    Route::get('/worker-payment', fn() => redirect()->route('admin.worker-invoices.index'))->name('admin.worker-payment.index');
    Route::delete('/worker-payment/{id}', [\App\Http\Controllers\Admin\WorkerPaymentController::class, 'destroy'])->name('admin.worker-payment.destroy');

    // User Full Activities & Real-time Inactivity Status (following SAL /company/user-activities)
    Route::get('/user-activities', [\App\Http\Controllers\Admin\UserActivityController::class, 'index'])->name('admin.user-activities.index');
    Route::get('/user-activities/{role}/{id}/daily-log', [\App\Http\Controllers\Admin\UserActivityController::class, 'dailyLog'])->name('admin.user-activities.daily-log');

    // Central Return Module (Multi-channel: Dealer, POS, Web; Dual refund: Cash & Wallet; Approval Lifecycle)
    Route::get('/returns', [\App\Http\Controllers\Admin\ReturnController::class, 'index'])->name('admin.returns.index');
    Route::get('/returns/create', [\App\Http\Controllers\Admin\ReturnController::class, 'create'])->name('admin.returns.create');
    Route::post('/returns', [\App\Http\Controllers\Admin\ReturnController::class, 'store'])->name('admin.returns.store');
    Route::get('/returns/ajax/invoices', [\App\Http\Controllers\Admin\ReturnController::class, 'getInvoices'])->name('admin.returns.ajax.invoices');
    Route::get('/returns/ajax/invoice-products', [\App\Http\Controllers\Admin\ReturnController::class, 'getInvoiceProducts'])->name('admin.returns.ajax.invoice-products');
    Route::get('/returns/{id}', [\App\Http\Controllers\Admin\ReturnController::class, 'show'])->name('admin.returns.show');
    Route::post('/returns/{id}/approve', [\App\Http\Controllers\Admin\ReturnController::class, 'approve'])->name('admin.returns.approve');
    Route::post('/returns/{id}/reject', [\App\Http\Controllers\Admin\ReturnController::class, 'reject'])->name('admin.returns.reject');
});

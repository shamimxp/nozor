<?php

/**
 * Soft-delete / trash dependency map.
 *
 * blockers  = active relations that prevent normal soft-delete (and permanent force-delete)
 * owned     = child relations soft-deleted/restored/force-deleted with the parent (same deletion_batch_id)
 */
return [

    'guard' => 'admin',

    'modules' => [

        'category' => [
            'model' => \App\Models\Category::class,
            'label' => 'Category',
            'display' => 'name',
            'blockers' => [
                ['relation' => 'products', 'label' => 'Products'],
                ['relation' => 'subCategories', 'label' => 'Sub Categories'],
            ],
            'owned' => [],
        ],

        'sub_category' => [
            'model' => \App\Models\SubCategory::class,
            'label' => 'Sub Category',
            'display' => 'name',
            'blockers' => [
                ['relation' => 'products', 'label' => 'Products'],
            ],
            'owned' => [],
        ],

        'unit' => [
            'model' => \App\Models\Unit::class,
            'label' => 'Unit',
            'display' => 'name',
            'blockers' => [
                ['relation' => 'products', 'label' => 'Products'],
                ['relation' => 'rawMaterialProducts', 'label' => 'Raw Materials'],
            ],
            'owned' => [],
        ],

        'product_attribute' => [
            'model' => \App\Models\ProductAttribute::class,
            'label' => 'Product Attribute',
            'display' => 'name',
            'blockers' => [
                ['relation' => 'values', 'label' => 'Product Attribute Values'],
            ],
            'owned' => [],
        ],

        'variation' => [
            'model' => \App\Models\Variation::class,
            'label' => 'Variation',
            'display' => 'name',
            'route' => 'variations',
            'blockers' => [
                ['relation' => 'productVariations', 'label' => 'Product Variations'],
            ],
            'owned' => [
                ['relation' => 'variationValues'],
            ],
        ],

        'material_type' => [
            'model' => \App\Models\MaterialType::class,
            'label' => 'Material Type',
            'display' => 'name',
            'blockers' => [
                ['relation' => 'rawMaterialProducts', 'label' => 'Raw Materials'],
            ],
            'owned' => [],
        ],

        'cost_head' => [
            'model' => \App\Models\CostHead::class,
            'label' => 'Cost Head',
            'display' => 'name',
            'blockers' => [
                ['relation' => 'costSettingItems', 'label' => 'Cost Setting Items'],
            ],
            'owned' => [],
        ],

        'banner' => [
            'model' => \App\Models\Banner::class,
            'label' => 'Banner',
            'display' => 'title',
            'blockers' => [],
            'owned' => [],
        ],

        'coupon' => [
            'model' => \App\Models\Coupon::class,
            'label' => 'Coupon',
            'display' => 'code',
            'blockers' => [],
            'owned' => [],
        ],

        'contact_message' => [
            'model' => \App\Models\ContactMessage::class,
            'label' => 'Contact Message',
            'display' => 'name',
            'route' => 'contact-messages',
            'blockers' => [],
            'owned' => [],
        ],

        'subscriber' => [
            'model' => \App\Models\Subscriber::class,
            'label' => 'Subscriber',
            'display' => 'email',
            'route' => 'subscribers',
            'blockers' => [],
            'owned' => [],
        ],

        'product' => [
            'model' => \App\Models\Product::class,
            'label' => 'Product',
            'display' => 'name',
            'blockers' => [
                ['relation' => 'posOrderItems', 'label' => 'POS Order Items'],
                ['relation' => 'webOrderItems', 'label' => 'Web Order Items'],
                ['relation' => 'dealerOrderItems', 'label' => 'Dealer Order Items'],
                ['relation' => 'draftOrderItems', 'label' => 'Draft Order Items'],
                ['relation' => 'manufactureOrderItems', 'label' => 'Manufacture Order Items'],
                ['relation' => 'inventoryPurchaseItems', 'label' => 'Inventory Purchase Items'],
                ['relation' => 'returnItems', 'label' => 'Return Items'],
                ['relation' => 'stockAdjustments', 'label' => 'Stock Adjustments'],
            ],
            'owned' => [
                ['relation' => 'gallery'],
                ['relation' => 'attributes'],
                ['relation' => 'variations'],
                ['relation' => 'recipe'],
            ],
        ],

        'raw_material_product' => [
            'model' => \App\Models\RawMaterialProduct::class,
            'label' => 'Raw Material',
            'display' => 'name',
            'blockers' => [
                ['relation' => 'recipeItems', 'label' => 'Recipe Items'],
            ],
            'owned' => [],
        ],

        'product_recipe' => [
            'model' => \App\Models\ProductRecipe::class,
            'label' => 'Product Recipe',
            'display' => 'id',
            'blockers' => [],
            'owned' => [
                ['relation' => 'items'],
            ],
        ],

        'cost_setting' => [
            'model' => \App\Models\CostSetting::class,
            'label' => 'Cost Setting',
            'display' => 'cost_title',
            'blockers' => [
                ['relation' => 'recipes', 'label' => 'Product Recipes'],
                ['relation' => 'manufactureOrderItems', 'label' => 'Manufacture Items'],
            ],
            'owned' => [
                ['relation' => 'items'],
            ],
        ],

        'fabric' => [
            'model' => \App\Models\Fabric::class,
            'label' => 'Fabric',
            'display' => 'name',
            'blockers' => [
                ['relation' => 'prices', 'label' => 'Fabric Prices'],
            ],
            'owned' => [],
        ],

        'fabric_price' => [
            'model' => \App\Models\FabricPrice::class,
            'label' => 'Fabric Price',
            'display' => 'id',
            'blockers' => [
                ['relation' => 'customOrderItems', 'label' => 'Custom Order Items'],
                ['relation' => 'purchaseItems', 'label' => 'Purchase Items'],
            ],
            'owned' => [],
        ],

        'customer' => [
            'model' => \App\Models\Customer::class,
            'label' => 'Customer',
            'display' => 'name',
            'blockers' => [
                ['relation' => 'customOrders', 'label' => 'Custom Orders'],
                ['relation' => 'posOrders', 'label' => 'POS Orders'],
                ['relation' => 'payments', 'label' => 'Customer Payments'],
            ],
            'owned' => [
                ['relation' => 'addresses'],
            ],
        ],

        'dealer' => [
            'model' => \App\Models\Dealer::class,
            'label' => 'Dealer',
            'display' => 'name',
            'blockers' => [
                ['relation' => 'orders', 'label' => 'Dealer Orders'],
                ['relation' => 'walletTransactions', 'label' => 'Wallet Transactions'],
                ['relation' => 'draftOrders', 'label' => 'Draft Orders'],
                ['relation' => 'manufactures', 'label' => 'Manufactures'],
                ['relation' => 'orderRequests', 'label' => 'Order Requests'],
            ],
            'owned' => [],
        ],

        'vendor' => [
            'model' => \App\Models\Vendor::class,
            'label' => 'Vendor',
            'display' => 'name',
            'blockers' => [
                ['relation' => 'purchases', 'label' => 'Purchases'],
                ['relation' => 'inventoryPurchases', 'label' => 'Inventory Purchases'],
                ['relation' => 'payments', 'label' => 'Vendor Payments'],
                ['relation' => 'customOrders', 'label' => 'Custom Orders'],
            ],
            'owned' => [],
        ],

        'worker' => [
            'model' => \App\Models\Worker::class,
            'label' => 'Worker',
            'display' => 'name',
            'blockers' => [
                ['relation' => 'manufactures', 'label' => 'Manufactures'],
                ['relation' => 'payments', 'label' => 'Worker Payments'],
                ['relation' => 'payouts', 'label' => 'Worker Payouts'],
            ],
            'owned' => [],
        ],

        'custom_order' => [
            'model' => \App\Models\CustomOrder::class,
            'label' => 'Custom Order',
            'display' => 'order_number',
            'blockers' => [
                ['relation' => 'purchases', 'label' => 'Purchases'],
            ],
            'owned' => [
                ['relation' => 'items'],
                ['relation' => 'images'],
            ],
        ],

        'pos_order' => [
            'model' => \App\Models\PosOrder::class,
            'label' => 'POS Order',
            'display' => 'order_number',
            'blockers' => [],
            'owned' => [
                ['relation' => 'items'],
            ],
        ],

        'dealer_order' => [
            'model' => \App\Models\DealerOrder::class,
            'label' => 'Dealer Order',
            'display' => 'order_number',
            'blockers' => [
                ['relation' => 'payments', 'label' => 'Order Payments'],
                ['relation' => 'draftOrder', 'label' => 'Draft Orders'],
            ],
            'owned' => [
                ['relation' => 'items'],
            ],
        ],

        'draft_order' => [
            'model' => \App\Models\DraftOrder::class,
            'label' => 'Draft Order',
            'display' => 'draft_number',
            'route' => 'draft-orders',
            'blockers' => [],
            'owned' => [
                ['relation' => 'items'],
            ],
        ],

        'manufacture' => [
            'model' => \App\Models\Manufacture::class,
            'label' => 'Manufacture',
            'display' => 'id',
            'blockers' => [
                ['relation' => 'workerPayments', 'label' => 'Worker Payments'],
            ],
            'owned' => [
                ['relation' => 'items'],
            ],
        ],

        'purchase' => [
            'model' => \App\Models\Purchase::class,
            'label' => 'Purchase',
            'display' => 'purchase_number',
            'blockers' => [
                ['relation' => 'payments', 'label' => 'Vendor Payments'],
            ],
            'owned' => [
                ['relation' => 'items'],
            ],
        ],

        'inventory_purchase' => [
            'model' => \App\Models\InventoryPurchase::class,
            'label' => 'Inventory Purchase',
            'display' => 'purchase_number',
            'blockers' => [
                ['relation' => 'payments', 'label' => 'Payments'],
                ['relation' => 'stocks', 'label' => 'Stocks'],
            ],
            'owned' => [
                ['relation' => 'items'],
            ],
        ],

        'stock_adjustment' => [
            'model' => \App\Models\StockAdjustment::class,
            'label' => 'Stock Adjustment',
            'display' => 'id',
            'blockers' => [],
            'owned' => [],
        ],

        'worker_payment' => [
            'model' => \App\Models\WorkerPayment::class,
            'label' => 'Worker Invoice',
            'display' => 'invoice_no',
            'route' => 'worker-invoices',
            'blockers' => [
                ['relation' => 'payoutItems', 'label' => 'Payout Items'],
            ],
            'owned' => [],
        ],

        'worker_payout' => [
            'model' => \App\Models\WorkerPayout::class,
            'label' => 'Worker Payout',
            'display' => 'payment_number',
            'route' => 'worker-payouts',
            'blockers' => [],
            'owned' => [
                ['relation' => 'items'],
            ],
        ],

        'wishlist' => [
            'model' => \App\Models\Wishlist::class,
            'label' => 'Wishlist',
            'display' => 'id',
            'blockers' => [],
            'owned' => [],
        ],

        'product_review' => [
            'model' => \App\Models\ProductReview::class,
            'label' => 'Product Review',
            'display' => 'id',
            'route' => 'product_reviews',
            'blockers' => [],
            'owned' => [],
        ],

        'admin' => [
            'model' => \App\Models\Admin\Admin::class,
            'label' => 'Admin User',
            'display' => 'name',
            'blockers' => [],
            'owned' => [],
        ],
    ],
];

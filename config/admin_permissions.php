<?php

/**
 * Admin access-control map (guard: admin).
 *
 * Standard actions per module:
 *   list, create, edit, show, delete, force-delete, deleted-list, restore
 *
 * Controllers map resource methods via AuthorizesAdminModule.
 */
return [

    'guard' => 'admin',

    'super_admin_role' => 'Super Admin',

    'actions' => [
        'list',
        'create',
        'edit',
        'show',
        'delete',
        'force-delete',
        'deleted-list',
        'restore',
    ],

    /*
    |--------------------------------------------------------------------------
    | Modules
    |--------------------------------------------------------------------------
    | key          = permission prefix (category.list)
    | label        = module_name stored on permissions table / role UI grouping
    | actions      = subset of actions (defaults to all)
    */
    'modules' => [

        'dashboard' => [
            'label' => 'Dashboard',
            'actions' => ['list'],
        ],

        // Access Control
        'user' => [
            'label' => 'Admin Users',
        ],
        'role' => [
            'label' => 'Roles',
            'actions' => ['list', 'create', 'edit', 'show', 'delete'],
        ],

        // Catalog
        'category' => [
            'label' => 'Categories',
        ],
        'sub_category' => [
            'label' => 'Sub Categories',
        ],
        'product' => [
            'label' => 'Products',
        ],
        'product_attribute' => [
            'label' => 'Product Attributes',
        ],
        'variation' => [
            'label' => 'Variations',
        ],
        'unit' => [
            'label' => 'Units',
        ],

        // Manufacture settings
        'cost_head' => [
            'label' => 'Cost Heads',
        ],
        'cost_setting' => [
            'label' => 'Cost Settings',
        ],
        'material_type' => [
            'label' => 'Material Types',
        ],
        'raw_material_product' => [
            'label' => 'Raw Materials',
        ],
        'product_recipe' => [
            'label' => 'Product Recipes',
        ],
        'stock_adjustment' => [
            'label' => 'Stock Adjustments',
        ],
        'manufacture' => [
            'label' => 'Manufacture Orders',
        ],
        'inventory_purchase' => [
            'label' => 'Inventory Purchases',
        ],

        // Parties
        'customer' => [
            'label' => 'Customers',
        ],
        'worker' => [
            'label' => 'Workers',
        ],
        'dealer' => [
            'label' => 'Dealers',
        ],
        'vendor' => [
            'label' => 'Vendors',
        ],
        'user_activity' => [
            'label' => 'User Activities',
            'actions' => ['list', 'show'],
        ],

        // Orders
        'web_order' => [
            'label' => 'Web Orders',
            'actions' => ['list', 'edit', 'show', 'delete'],
        ],
        'wishlist' => [
            'label' => 'Wishlists',
            'actions' => ['list', 'delete', 'force-delete', 'deleted-list', 'restore'],
        ],
        'pos_order' => [
            'label' => 'POS Orders',
        ],
        'order_request' => [
            'label' => 'Order Requests',
            'actions' => ['list', 'show', 'edit', 'create'],
        ],
        'draft_order' => [
            'label' => 'Draft Orders',
        ],
        'dealer_order' => [
            'label' => 'Dealer Orders',
        ],
        'return_order' => [
            'label' => 'Return Orders',
            'actions' => ['list', 'create', 'edit', 'show', 'delete'],
        ],
        'custom_order' => [
            'label' => 'Custom Orders',
        ],

        // Finance
        'collection' => [
            'label' => 'Collections',
            'actions' => ['list', 'create', 'edit', 'show'],
        ],
        'dealer_wallet' => [
            'label' => 'Dealer Wallets',
            'actions' => ['list', 'create', 'edit', 'show'],
        ],
        'worker_balance' => [
            'label' => 'Worker Balance',
            'actions' => ['list', 'show', 'create', 'edit'],
        ],
        'worker_payment' => [
            'label' => 'Worker Invoices',
        ],
        'worker_payout' => [
            'label' => 'Worker Payouts',
            'actions' => ['list', 'create', 'edit', 'show', 'delete'],
        ],
        'report' => [
            'label' => 'Reports',
            'actions' => ['list', 'show'],
        ],

        // Web / CMS
        'web_setting' => [
            'label' => 'Web Settings',
            'actions' => ['list', 'edit'],
        ],
        'banner' => [
            'label' => 'Banners',
        ],
        'coupon' => [
            'label' => 'Coupons',
        ],
        'product_review' => [
            'label' => 'Product Reviews',
        ],
        'contact_message' => [
            'label' => 'Contact Messages',
        ],
        'subscriber' => [
            'label' => 'Subscribers',
        ],
    ],
];


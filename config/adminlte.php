<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Title
    |--------------------------------------------------------------------------
    |
    | The default title of your admin panel, this goes into the title tag
    | of your page. You can override it per page with the title section.
    | You can optionally also specify a title prefix and/or postfix.
    |
    */

    'title' => 'CULTIVISION',

    'title_prefix' => '',

    'title_postfix' => '',

    /*
    |--------------------------------------------------------------------------
    | Logo
    |--------------------------------------------------------------------------
    |
    | This logo is displayed at the upper left corner of your admin panel.
    | You can use basic HTML here if you want. The logo has also a mini
    | variant, used for the mini side bar. Make it 3 letters or so
    |
    */

    'logo' => '<b>CULTI</b>VISION',

    'logo_mini' => '<i class="fas fa-cannabis" style="padding-top: 15px;color: #01a65a;"></i>',

    /*
    |--------------------------------------------------------------------------
    | Skin Color
    |--------------------------------------------------------------------------
    |
    | Choose a skin color for your admin panel. The available skin colors:
    | blue, black, purple, yellow, red, and green. Each skin also has a
    | light variant: blue-light, purple-light, purple-light, etc.
    |
    */

    'skin' => 'blue-light',

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    |
    | Choose a layout for your admin panel. The available layout options:
    | null, 'boxed', 'fixed', 'top-nav'. null is the default, top-nav
    | removes the sidebar and places your menu in the top navbar
    |
    */

    'layout' => null,

    /*
    |--------------------------------------------------------------------------
    | Collapse Sidebar
    |--------------------------------------------------------------------------
    |
    | Here we choose and option to be able to start with a collapsed side
    | bar. To adjust your sidebar layout simply set this  either true
    | this is compatible with layouts except top-nav layout option
    |
    */

    'collapse_sidebar' => false,

    /*
    |--------------------------------------------------------------------------
    | URLs
    |--------------------------------------------------------------------------
    |
    | Register here your dashboard, logout, login and register URLs. The
    | logout URL automatically sends a POST request in Laravel 5.3 or higher.
    | You can set the request to a GET or POST with logout_method.
    | Set register_url to null if you don't want a register link.
    |
    */

    'dashboard_url' => 'home',

    'logout_url' => 'logout',

    'logout_method' => null,

    'login_url' => 'login',

    'register_url' => 'register',

    /*
    |--------------------------------------------------------------------------
    | Menu Items
    |--------------------------------------------------------------------------
    |
    | Specify your menu items to display in the left sidebar. Each menu item
    | should have a text and a URL. You can also specify an icon from Font
    | Awesome. A string instead of an array represents a header in sidebar
    | layout. The 'can' is a filter on Laravel's built in Gate functionality.
    */

    'menu' => [
        [
            'text' => 'search',
            'search' => true,
        ],
        [
            'text' => 'Walnut Order',
            'icon' => 'fas fa-tasks',
            'can' => [  'order_new','order_fulfillment_list',
                        'order_pending_list','order_fulfilled_list',
                        'order_sign_list','order_delivered','order_pverification',
                        'order_finacial','order_archived',
                        'order_report','order_promo'],
            'submenu' => [
                [
                    'text' => 'WB New Po',
                    'url'  => 'order/form',
                    'icon' => 'glyphicon glyphicon-plus',
                    'can' => 'order_new',
                ],
                [
                    'text'        => 'WB Pending PO',
                    'url'         => 'order/pending_list',
                    'icon'        => 'glyphicon glyphicon-list',
                    'can' => 'order_pending_list',
                ],
                [
                    'text'        => 'Walnut Fulfilment',
                    'url'         => 'order/fulfillment_list',
                    'icon'        => 'glyphicon glyphicon-list',
                    'can' => 'order_fulfillment_list',
                ],
                [
                    'text'        => 'Walnut to Deliver',
                    'url'         => 'order_fulfilled/home',
                    'icon'        => 'glyphicon glyphicon-list',
                    'can' => 'order_fulfilled_list',
                ],
                [
                    'text'        => 'Scheduled Deliveries',
                    'url'         => 'order_fulfilled/scheduled',
                    'icon'        => 'fas fa-shipping-fast',
                    'can' => 'order_fulfilled_list',
                ],
                [
                    'text'        => 'Signature & Delivery',
                    'url'         => 'signature/home',
                    'icon'        => 'fas fa-signature',
                    'can' => 'order_sign_list',
                ],
                [
                    'text'        => 'Walnut Delivered',
                    'url'         => 'order_fulfilled/delivered',
                    'icon'        => 'glyphicon glyphicon-list',
                    'can' => 'order_delivered',
                ],
                [
                    'text'        => 'Walnut CreditNote',
                    'url'         => 'credit_notes',
                    'icon'        => 'glyphicon glyphicon-list',
                    'can' => 'order_delivered',
                ],
                [
                    'text'        => 'Payment Verfication',
                    'url'         => 'order/p_v',
                    'icon'        => 'glyphicon glyphicon-list',
                    'can' => 'order_pverification',
                ],
                [
                    'text'        => 'AR Calendar',
                    'url'         => 'signature/pverification/home',
                    'icon'        => 'fas fa-calendar-alt',
                    'can' => 'order_arcalender',
                ],
                [
                    'text'        => 'Walnut Financial',
                    'url'         => 'admin/financial_export',
                    'icon'        => 'fa fa-file-csv',
                    'can'         => 'order_finacial'
                ],
                [
                    'text'        => 'Walnut Archived',
                    'url'         => 'order_fulfilled/archived',
                    'icon'        => 'fas fa-archive',
                    'can' => 'order_archived',
                ],
                [
                    'text'        => 'Invoice Reports',
                    'url'         => 'order_fulfilled/i_report',
                    'icon'        => 'fas fa-calculator',
                    'can' => 'order_report',
                ],
                
                [
                    'text'        => 'FA Export Logs',
                    'url'         => 'order/fa_export_log',
                    'icon'        => 'fas fa-calculator',
                    'can' => 'order_finacial',
                ],
                [
                    'text'        => 'Report Page',
                    'url'         => 'order/report',
                    'icon'        => 'fas fa-calculator',
                    'can' => 'order_report',
                ],
                [
                    'text' => 'Promo Builder',
                    'url'  => 'promo',
                    'icon' => 'glyphicon glyphicon-list',
                    'can' => 'order_promo',
                ],
            ]
        ],
        [
            'text' => 'Harvest Process',
            'icon' => 'fas fa-tractor',
            'can' => ['new_harvest','harvest_list','harvest_dynamics',
                      'dry_weight_list','curing_stage','inventory_on_hold',],
            'submenu' => [
                [
                    'text' => '1. New Harvest',
                    'url'  => 'harvest/create',
                    'icon' => 'glyphicon glyphicon-scissors',
                    'can' => 'new_harvest',
                ],
                [
                    'text' => '2. Harvest List',
                    'url'  => 'harvest/list',
                    'icon' => 'glyphicon glyphicon-list',
                    'can' => 'harvest_list',
                ],

                [
                    'text'        => '3. Harvest Dynamics',
                    'url'         => 'harvestdynamics',
                    'icon'        => 'glyphicon glyphicon-folder-open',
                    'can' => 'harvest_dynamics',
                ],
                [
                    'text'        => '4. Dry Weight List',
                    'url'         => 'harvest/list_dry',
                    'icon'        => 'glyphicon glyphicon-scale',
                    'can' => 'dry_weight_list',
                ],

                [
                    'text'        => '5. Curing Stage',
                    'url'         => 'harvest/curning',
                    'icon'        => 'glyphicon glyphicon-calendar',
                    'can' => 'curing_stage',
                ],

                [
                    'text'        => '6. Inventory on Hold',
                    'url'         => 'holdingInventory',
                    'icon'        => 'glyphicon glyphicon-eye-close',
                    'can' => 'inventory_on_hold',
                ],


            ]
        ],


        [
            'text' => 'Reporting',
            'icon' => 'glyphicon glyphicon-list',
            'can'  => ['harvest_report_archived','harvest_report_waste','harvest_report_curing',
                       'harvest_report_overflow','harvest_inspection'],
            'submenu' => [
                [
                    'text'        => 'Archived Wet Harvest',
                    'url'         => 'harvest/list_archived',
                    'icon'        => 'fas fa-archive',
                    'can' => 'harvest_report_archived',
                ],
                [
                    'text'        => 'Waste Archive',
                    'url'         => 'waste',
                    'icon'        => 'fas fa-trash',
                    'can' => 'harvest_report_waste',
                ],
                [
                    'text'        => 'Packaged completed',
                    'url'         => 'harvest/process_history',
                    'icon'        => 'fas fa-box',
                    'can' => 'harvest_report_curing',
                ],
                [
                    'text'        => 'Harvest Overview',
                    'url'         => 'harvest/history',
                    'icon'        => 'fas fa-tasks',
                    'can' => 'harvest_report_overflow',
                ],
                [
                    'text'        => 'Harvest Inspection',
                    'url'         => 'harvest/dashboard',
                    'icon'        => 'far fa-eye',
                    'can' => 'harvest_inspection',
                ],


            ]
        ],

       /*
        [
            'text' => 'Statistical',
            'icon' => 'fas fa-calculator',
            'submenu' => [
                                [
                    'text' => 'Plant Room Builder',
                    'url'  => 'harvest/room_builder',
                    'icon' => 'fab fa-buromobelexperte',
                    'can'  => 'Finished Goods'
                ],

                [
                    'text'        => 'Tracking Results',
                    'url'         => 'allocationresults',
                    'icon'        => 'fas fa-braille',
                ],
                [
                    'text' => 'Allocate Harvest',
                    'url'  => 'allocationbuilder',
                    'icon' => 'glyphicon glyphicon-plus',
                ],

            ]
        ],
    */

        [
            'text' => 'User Management',
            'icon' => 'fas fa-users-cog',
            'can'  => ['user_m_user','user_m_roles','user_m_permissions'],
            'submenu' => [
                [
                    'text' => 'User',
                    'url'  => 'users',
                    'icon' => 'fas fa-user',
                    'cam'  => 'user_m_user'
                ],
                [
                    'text'        => 'Roles and Permissions',
                    'url'         => 'roles',
                    'icon'        => 'fas fa-user-lock',
                    'cam'  => 'user_m_roles'
                ],
                [
                    'text'        => 'Permissions',
                    'url'         => 'permissions',
                    'icon'        => 'fas fa-user-lock',
                    'cam'  => 'user_m_permissions'
                ],
            ]
        ],

        [
            'text' => 'Culti - Clock In / Clock Out',
            'url'  => 'clocking',
            'icon' => 'fas fa-user',
            'can'  => 'clock_in_out'
        ],
        [
            'text'        => 'Culti - Time Keeping',
            'url'         => 'clocking_report',
            'icon'        => 'fas fa-clock',
            'can'         => 'time_keeping'
        ],
        [
            'text' => 'Distro - Lunch In / Lunch Out',
            'url'  => 'clocking_distro',
            'icon' => 'fas fa-user',
            'can'  => 'distro_cio'
        ],
        [
            'text'        => 'Distro - Time Keeping',
            'url'         => 'clocking_lunch_report',
            'icon'        => 'fas fa-clock',
            'can'         => 'time_keeping'
        ],
        [
            'text'        => 'Metrc Tag Search',
            'url'         => 'metrc_search',
            'icon'        => 'fas fa-clock',
            'can'         => 'inventory_fg'
        ],


        [
                'text' => 'Inventory on Hold',
                'url'  => 'invrestock',
                'icon' => 'fas fa-boxes',
                'can'  => 'inventory_fg'
        ],
        [
            'text' => 'Inv. 1 - Bulk/Work Order',
            'url'  => 'vaultinventory',
            'icon' => 'fas fa-door-closed',
            'can'  => 'inventory_vault'
        ],

        [
                'text' => 'Inv. 2 - Finished Goods',
                'url'  => 'fginventory',
                'icon' => 'fas fa-boxes',
                'can'  => 'inventory_fg'
        ],
        [
            'text' => 'Inventory Manage',
            'icon' => 'fas fa-sitemap',
            'can'  => ['inventory_combine','inventory_split'],
            'submenu' => [
                [
                    'text' => 'Combine',
                    'url'  => 'inventory/combine',
                    'icon' => 'fas fa-equals',
                    'can' => 'inventory_combine',
                ],
                [
                    'text' => 'Split',
                    'url'  => 'inventory/split',
                    'icon' => 'fas fa-cut',
                    'can' => 'inventory_split',
                ],
                [
                    'text' => 'Bulk Import',
                    'url'  => 'inventory/import',
                    'icon' => 'fas fa-save',
                    'can' => 'inventory_import',
                ],
                [
                    'text' => 'Bulk Awaiting Approval',
                    'url'  => 'inventory/archive_imported',
                    'icon' => 'fas fa-save',
                    'can' => 'inventory_import',
                ],
                [
                    'text' => 'Restock History',
                    'url'  => 'inventory/restock_history',
                    'icon' => 'glyphicon glyphicon-list',
                    'can' => 'inventory_import',
                ],
            ]
        ],
        [
            'text' => 'COA Upload',
            'url'  => 'coalibrary',
            'icon' => 'fas fa-upload',
            'can'  => 'coa_upload'
        ],
        [
            'text' => 'Transfer Inventory',
            'url'  => 'harvest/transfer',
            'icon' => 'fas fa-exchange-alt',
            'can'  => 'transfer_inventory'
        ],
        [
            'text' => 'Product Type','can'  => 'Administration',
            'url'  => 'producttypes',
            'icon' => 'fas fa-border-style',
            'can'  => 'product_type'
        ],
        [
            'text' => 'Delivery Method','can'  => 'Administration',
            'url'  => 'delivery_method',
            'icon' => 'fas fa-border-style',
            'can'  => 'product_type'
        ],
        [
            'text' => 'Strain-Type Controller',
            'url'  => 'upccontroller',
            'icon' => 'fas fa-barcode',
            'can'  => 'upc_controller'
        ],
        [
            'text' => 'Customer Relations',
            'icon' => 'fas fa-sitemap',
            'can'  => ['c_relations_clients','c_relations_person','c_relations_price_matrix',],
            'submenu' => [
                [
                    'text' => 'Clients',
                    'url'  => 'customers',
                    'icon' => 'fas fa-users',
                    'can' => 'c_relations_clients',
                ],
   /*                             [
                    'text' => 'Clients',
                    'url'  => 'customers2',
                    'icon' => 'fas fa-users',
                    'can' => 'c_relations_clients',
                ],*/
             
                [
                    'text' => 'Person',
                    'url'  => 'contactperson',
                    'icon' => 'fas fa-user-tie',
                    'can'  => 'c_relations_person',
                ],
                [
                    'text' => 'Price Matrix',
                    'url'  => 'pricematrix',
                    'icon' => 'fas fa-chart-bar',
                    'can'  => 'c_relations_price_matrix',
                ],
            ]
        ],
        [
            'text' => 'Location',
            'icon' => 'fas fa-industry',
            'can'  => ['location_area','location_cart','location_shelf'],
            'submenu' => [
                [
                    'text' => 'Area',
                    'url'  => 'locationarea',
                    'icon' => 'fas fa-pallet',
                    'can'  => 'location_area'
                ],
                [
                    'text' => 'Cart',
                    'url'  => 'locationcart',
                    'icon' => 'fas fa-dolly-flatbed',
                    'can'  => 'location_cart'
                ],
                [
                    'text' => 'Shelf',
                    'url'  => 'locationshelf',
                    'icon' => 'fas fa-layer-group',
                    'can'  => 'location_shelf'
                ],

            ]
        ],

        [
            'text' => 'Processing Control',
            'icon' => 'fas fa-cogs',
            'can'  => ['ps_ctl_vendor','ps_ctl_cultivator','ps_ctl_distributor','ps_ctl_osp_facility'],
            'submenu' => [
                [
                    'text' => 'Vendor',
                    'url'  => 'vendors',
                    'icon' => 'fas fa-warehouse',
                    'can'  => 'ps_ctl_vendor'
                ],
                [
                    'text' => 'Cultivator',
                    'url'  => 'cultivator',
                    'icon' => 'fas fa-tractor',
                    'can'  => 'ps_ctl_cultivator'
                ],
                [
                    'text' => 'Distributor',
                    'url'  => 'distributor',
                    'icon' => 'fas fa-paper-plane',
                    'can'  => 'ps_ctl_distributor'
                ],

                [
                    'text' => 'Osp facility',
                    'url'  => 'ospfacility',
                    'icon' => 'fas fa-vials',
                    'can'  => 'ps_ctl_osp_facility'
                ],
            ]
        ],
        [
            'text' => 'NDA Management',
            'icon' => 'fas fa-file-contract',
            'url' => 'nda_management/home',
            'can' => 'Administration'
        ],
        ['header' => 'Admin','can'  => 'Administration'],
            [
                'text' => 'Administration',
                'icon' => 'fas fa-desktop',
                'can'  => ['admin_inv_status','admin_h_admin','admin_h_modifier','admin_h_m_modifier','admin_h_builder',
                           'admin_time_keeping','admin_strain','admin_license','admin_status','admin_transfer_his',
                           'admin_terms','admin_units','admin_inv_category',
                           'admin_ospmatrix','admin_person_type','admin_bug_tracker'],
                'submenu' => [
                    
                [
                    'text' => 'Charts',
                    'url'  => 'sales_report',
                    'icon' => 'fas fa-chart-pie',
                    'can' => 'order_promo',
                ],
                    [
                    'text'        => 'NDA',
                    'url'         => 'nda_index',
                    'icon'        => 'fas fa-file',
                    'can'         => 'admin_inv_status'
                ],
                [
                    'text'        => 'Change Status INV2',
                    'url'         => 'fgmodifystatus',
                    'icon'        => 'glyphicon glyphicon-list',
                    'can'         => 'admin_inv_status'
                ],
                [
                    'text'        => 'Change Status INV1',
                    'url'         => 'vaultmodifystatus',
                    'icon'        => 'glyphicon glyphicon-list',
                    'can'         => 'admin_h_admin'
                ],
                [
                    'text'        => 'Harvest Admin',
                    'url'         => 'harvest/list_admin?perm=admin',
                    'icon'        => 'glyphicon glyphicon-list',
                    'can'         => 'admin_h_modifier'
                ],
                [
                    'text'        => 'Harvest modifier',
                    'url'         => 'harvestdata',
                    'icon'        => 'glyphicon glyphicon-list',
                    'can'         => 'admin_h_m_modifier'
                ],
                [
                    'text'        => 'Harvest Metrc modifier',
                    'url'         => 'harvestitem',
                    'icon'        => 'glyphicon glyphicon-list',
                    'can'         => 'Administration'
                ],
                                [
                    'text'        => 'Harvest Builder',
                    'url'         => 'harvestover',
                    'icon'        => 'glyphicon glyphicon-list',
                    'can'         => 'admin_h_builder'
                ],

                                [
                    'text'        => 'Time Keeping',
                    'url'         => 'clocking_report',
                    'icon'        => 'fas fa-clock',
                    'can'         => 'admin_time_keeping'
                ],
                [
                    'text' => 'Strain Name',
                    'url'  => 'strainname',
                    'icon' => 'far fa-id-badge',
                    'can'  => 'admin_strain'
                ],
                [
                    'text' => 'Licensetype',
                    'url'  => 'licensetype',
                    'icon' => 'far fa-id-badge',
                    'can'  => 'admin_license'
                ],
                [
                    'text' => 'Credit Reason',
                    'url'  => 'credit_reasons',
                    'icon' => 'far fa-id-badge',
                    'can'  => 'admin_license'
                ],
                [
                    'text' => 'Status',
                    'url'  => 'status',
                    'icon' => 'fas fa-chart-bar',
                    'can'  => 'admin_status'
                ],
                                [
                    'text'        => 'Transfer History',
                    'url'         => 'harvest/transfer_history',
                    'icon'        => 'glyphicon glyphicon-list',
                    'can'  => 'admin_transfer_his'
                ],
                [
                    'text' => 'Terms',
                    'url'  => 'terms',
                    'icon' => 'fas fa-file-contract',
                    'can'  => 'admin_terms'
                ],
                [
                    'text' => 'Units',
                    'url'  => 'units',
                    'icon' => 'fas fa-balance-scale-right',
                    'can'  => 'admin_units'
                ],
                 [
                    'text' => 'Inventorytype',
                    'url'  => 'inventorytype',
                    'icon' => 'fas fa-boxes',
                    'can'  => 'admin_inv_category'
                ],
                [
                    'text' => 'Inventorycategory',
                    'url'  => 'inventorycategory',
                    'icon' => 'fas fa-layer-group',
                    'can'  => 'admin_inv_category'
                ],
                [
                    'text' => 'Ospmatrix',
                    'url'  => 'ospmatrix',
                    'icon' => 'fas fa-leaf',
                    'can'  => 'admin_ospmatrix'
                ],
                [
                    'text' => 'Person Type',
                    'url'  => 'contacttype',
                    'icon' => 'far fa-handshake',
                    'can'  => 'admin_person_type'
                ],

                                                              [
                    'text' => 'Bug Tracker',
                    'url'  => 'bugtracker',
                    'icon' => 'fas fa-bug',
                    'can'  => 'admin_bug_tracker'
                ],
            ],

        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Menu Filters
    |--------------------------------------------------------------------------
    |
    | Choose what filters you want to include for rendering the menu.
    | You can add your own filters to this array after you've created them.
    | You can comment out the GateFilter if you don't want to use Laravel's
    | built in Gate functionality
    |
    */

    'filters' => [
        JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SearchFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SubmenuFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
        //App\MenuFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\LangFilter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugins Initialization
    |--------------------------------------------------------------------------
    |
    | Configure which JavaScript plugins should be included. At this moment,
    | DataTables, Select2, Chartjs and SweetAlert are added out-of-the-box,
    | including the Javascript and CSS files from a CDN via script and link tag.
    | Plugin Name, active status and files array (even empty) are required.
    | Files, when added, need to have type (js or css), asset (true or false) and location (string).
    | When asset is set to true, the location will be output using asset() function.
    |
    */

    'plugins' => [
        [
            'name' => 'Datatables',
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/v/bs/dt-1.10.18/datatables.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/v/bs/dt-1.10.18/datatables.min.css',
                ],
            ],
        ],
        [
            'name' => 'Select2',
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.css',
                ],
            ],
        ],
        [
            'name' => 'Chartjs',
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js',
                ],
            ],
        ],
        [
            'name' => 'Sweetalert2',
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//unpkg.com/sweetalert/dist/sweetalert.min.js',
                ],
            ],
        ],
        [
            'name' => 'Pace',
            'active' => true,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/blue/pace-theme-center-radar.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js',
                ],
            ],
        ],
    ],
];



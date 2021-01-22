<?php
use GroceryCrud\Core\GroceryCrud;
return [
    'customers' => [
        'table' => 'customers',
        'subject' => 'Customers',
    ],

    'items' => [
        'table' => 'items',
        'subject' => 'Items',
    ],

    'clients' => [
        'table' => 'clients',
        'title' => 'Clients',
        'subject' => 'Clients',
    ],

     'fgInventory' => [
         'table' => 'fginventory',
         'title' => 'Finished Goods',
         'subject' => 'Finished Goods',
         'columns' => 'fgasset_id','stockimage', 'strainname', 'upc_fk', 'batch_fk', 'qtyonhand','weight','um' ,
         'field_upload' =>[
            [
                'fieldName' => 'stockimage',
                'private_dir' => 'system/assets/uploads/files/inv',
                'public_dir' => '../../system/assets/uploads/files/inv',
            ]
                 ],
        'batchcreation' => 'stockimage',         
     ],



    'vendor' => [
        'table' => 'vendors',
        'title' => 'OPS',
        'subject' => 'Vendor',
    ],

    'producttypes' => [
        'table' => 'productcategory',
        'title' => 'Product',
        'subject' => 'Product',
        'columns' => ['producttype','producttype_desc'],
        'fields' => ['producttype','producttype_desc'],
        'displayAs'=>[
            'producttype_name' => 'Name',
            'producttype_desc' => 'Description'
        ]
    ],
    'upcController' => [
        'table' => 'upcinventory',
        'title' => 'OPS',
        'subject' => 'Upccontroller',
    ],
    'opsfacility' => [
        'table' => 'ospfacility',
        'title' => 'OSP',
        'subject' => 'OSP Facility',
        'field_upload' =>[
            [
                'fieldName' => 'licenseupload',
                'private_dir' => 'assets/uploads/files/license',
                'public_dir' => '../../assets/uploads/files/license',
            ]
        ],
        'relation' => [
            [
                'field' => 'state',
                'related_table' => 'states',
                'r_field' => 'abbr'
            ]
        ],
        'unset_columns' => [
            'datelastmodified'
        ],
        'unset_fields' => [
            'datelastmodified'
        ]
    ],
    'cultivator' => [
        'table' => 'cultivator',
        'title' => 'Cultivator',
        'subject' => 'Cultivator',
        'field_upload' =>[
            [
                'fieldName' => 'licenseupload',
                'private_dir' => 'assets/uploads/files/license',
                'public_dir' => '../../assets/uploads/files/license',
            ]
        ],
        'relation' => [
            [
                'field' => 'state',
                'related_table' => 'states',
                'r_field' => 'abbr'
            ]
        ],
        'unset_columns' => [
            'datelastmodified'
        ],
        'unset_fields' => [
            'datelastmodified'
        ]
    ],
    'distributor' => [
        'table' => 'distributor',
        'title' => 'Distributor',
        'subject' => 'Distributor',
        'relation' => [
            [
                'field' => 'state',
                'related_table' => 'states',
                'r_field' => 'abbr'
            ]
        ],
        'unset_columns' => [
            'datelastmodified'
        ],
        'unset_fields' => [
            'datelastmodified'
        ]
    ],
    'batch' => [
        'table' => 'batch',
        'title' => 'Batch',
        'subject' => 'Batch Creation',
        'field_upload' =>[
            [
                'fieldName' => 'image',
                'private_dir' => 'assets/uploads/files/inv',
                'public_dir' => '../../assets/uploads/files/inv',
            ],
            [
                'fieldName' => 'coafile',
                'private_dir' => 'assets/uploads/files/coa',
                'public_dir' => '../../assets/uploads/files/coa',
            ],
        ],
        'relation' => [
            [
                'field' => 'um_sample',
                'related_table' => 'units',
                'r_field' => '{name} - {abbriviation}'
            ],
            [
                'field' => 'um_batch',
                'related_table' => 'units',
                'r_field' => '{name} - {abbriviation}'
            ],
            [
                'field' => 'strainname_fk',
                'related_table' => 'strainname',
                'r_field' => 'strain'
            ],
            [
                'field' => 'distributor_fk',
                'related_table' => 'distributor',
                'r_field' => 'companyname'
            ],
            [
                'field' => 'cultivator_fk',
                'related_table' => 'cultivator',
                'r_field' => 'companyname'
            ],
            [
                'field' => 'ospfacility_fk',
                'related_table' => 'ospfacility',
                'r_field' => 'companyname'
            ],
            [
                'field' => 'matrix_fk',
                'related_table' => 'testingmatrix',
                'r_field' => 'matrix'
            ],
        ],
        'unset_columns' => [
            'datelastmodified'
        ],
        'unset_fields' => [
            'datelastmodified'
        ]
    ],
    'inventorytype' => [
        'table' => 'inventorytype',
        'title' => 'OPS',
        'subject' => 'Inventory Type',
    ],
    'contactperson' => [
        'table' => 'contactperson',
        'title' => 'Person',
        'subject' => 'Contact Person',
        'fieldType' => [
            [
                'type' => 'email',
                'mode' => GroceryCrud::FIELD_TYPE_EMAIL
            ]
        ],
        'relation' => [
            [
                'field' => 'state',
                'related_table' => 'states',
                'r_field' => 'abbr'
            ],
            [
                'field' => 'contacttype',
                'related_table' => 'contacttype',
                'r_field' => 'type'
            ]
        ],
        'unset_columns' => [
            'datelastmodified',
        ],
        'unset_fields' => [
            'datelastmodified',
        ]
    ],
    'contacttype' => [
        'table' => 'contacttype',
        'title' => 'OPS',
        'subject' => 'Person Type',
    ],
    'inventorycategory' => [
        'table' => 'inventorycategory',
        'title' => 'OPS',
        'subject' => 'Inventory Category',
    ],
    'licensetype' => [
        'table' => 'licensetype',
        'title' => 'License Type',
        'subject' => 'License Type',
    ],
    'status' => [
        'table' => 'status',
        'title' => 'Status',
        'subject' => 'Status',
    ],
    'terms' => [
        'table' => 'terms',
        'title' => 'Terms',
        'subject' => 'Terms',
    ],
    'units' => [
        'table' => 'units',
        'title' => 'Units',
        'subject' => 'Units',
    ],
    'testingmatrix' => [
        'table' => 'testingmatrix',
        'title' => 'Ospmatrix',
        'subject' => 'Ospmatrix',
    ]
];
<?php

return array(
    'sales' => array(
        'url' => '/total-sales',
        'response' => array(
            'totalSales' => 'int',
        )
    ),
    'categories' => array(
        'url' => '/categories',
        'response' => array(
            'reportingCategories' => array(
                'id' => 'int',
                'name' => 'string'
            )
        )
    ),
);
<?php

return array(
    'add-user' => array(
        'url' => '/user',
        'body' => array(
            'firstName' => 'Joe',
            'lastName' => 'Doe'
        ),
        'response' => array(
            'userId' => 'int',
            'firstName' => 'string',
            'lastName' => 'string',
        )
    ),
);
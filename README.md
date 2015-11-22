## Test your API !

This app is for testing your API and the expected response, return types and status codes

### Example Usage

In gets.php fill in the endpoint url and the expected response types

    'my-endpoint' => array(
        'url' => '/my-endpoint',
        'response' => array(
            'people' => array(
                'firstName' => 'string',
                'lastName' => 'string',
                'isProgrammer' => 'boolean'
                'otherDetails' => array(
                    'age' => 'int',
                    'ratePerHour' => 'float'
                )                
            )
        )
    )

Example JSON response

    {
        "people":[
            {        
                "firstName":"Harry", 
                "lastName":"Test", 
                "isProgrammer":true,
                "otherDetails":[
                    {
                        "age":24,
                        "ratePerHour": 27.50
                    }
                ]
            },
            {        
                "firstName":"Mike", 
                "lastName":"Test", 
                "isProgrammer":true,
                "otherDetails":[
                    {
                        "age":27,
                        "ratePerHour": 29.00
                    }
                ]
            },            
        ]
    }

### Instructions

* Rename test/config/unit-tests.sample.xml to test/config/unit-tests.xml and fill in
* Rename config/app.sample.php to config/app.php and fill in 
* Rename config/get.sample.php to config/gets.php and fill in with your endpoints
* $ php composer.phar install
* From the root directory execute the following
$ vendor/bin/phpunit --configuration "test/config/unit-tests.xml" 

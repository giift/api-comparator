<?php
require '../vendor/autoload.php';

$config = array(
    'connect'=>array(
        'old'=>array(
            'token'=>'Bearer XbOIHLIge6TkDnDa8xogLtWxXMQpcpPS8YvC9O5K',
            'base_uri'=>'http://localhost/1.5.6.0/public/'
        ),
        'new'=>array(
            'token'=>'Bearer XbOIHLIge6TkDnDa8xogLtWxXMQpcpPS8YvC9O5K',
            'base_uri' => 'http://localhost/giift/public/'
        )
    ),
    'methods'=>array(
        array(
            'endpoint'=>'/report/data/data',
            'method'=>'POST',
            'params'=>array(
                'report_data'=>array(
                    'report_select'=>'agepie',
                    'from'=>'2016-05-24',
                    'to'=>'2016-06-24',
                    'timezone'=>'UTC'
                )
            ),
            'content_type'=>array('application/json')
        ),
        array(
            'endpoint'=>'/report/data/data',
            'method'=>'POST',
            'params'=>array(
                'report_data'=>array(
                    'report_select'=>'countrypie',
                    'from'=>'2016-05-24',
                    'to'=>'2016-06-24',
                    'timezone'=>'UTC',
                )
            ),
            'content_type'=>array('application/json')
        ),
        array(
            'endpoint'=>'/report/data/data',
            'method'=>'POST',
            'params'=>array(
                'report_data'=>array(
                    'report_select'=>'offerreport',
                    'from'=>'2016-05-24',
                    'to'=>'2016-06-24',
                    'timezone'=>'UTC',
                    'retailer'=>'0'
                )
            ),
            'content_type'=>array('application/json')
        ),
        array(
            'endpoint'=>'/report/data/data',
            'method'=>'POST',
            'params'=>array(
                'report_data'=>array(
                    'report_select'=>'dmos'
                )
            ),
            'content_type'=>array('application/json')
        ),
        array(
            'endpoint'=>'/report/data/data',
            'method'=>'POST',
            'params'=>array(
                'report_data'=>array(
                    'report_select'=>'memberloyalty',
                    'retailer'=>'0'
                )
            ),
            'content_type'=>array('application/json')
        )
    ),
    'display_all_results'=>true
);

// file_put_contents('config.json', json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

$compare = new \Giift\Compare\Compare($config);

$compare->run(true);

$compare->to_file('result.xml', 'xml');
$compare->to_file('result.csv', 'csv');

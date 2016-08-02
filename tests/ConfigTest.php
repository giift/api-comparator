<?php
require '../src/Config.php';
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    public function testConstruct()
    {
        $config_object = new \Giift\Compare\Config();
        // Check type of object returned
        $this->assertInstanceOf('\Giift\Compare\Config', $config_object);
    }

    public function testGet()
    {
        $config_object = new \Giift\Compare\Config();

        // Config should be the template
        $actual = $config_object->get_config();
        $expected = array(
            'connect'=>array(
                'old'=>array(
                    'token'=>'',
                    'base_uri'=>''
                ),
                'new'=>array(
                    'token'=>'',
                    'base_uri'=>''
                )
            ),
            'methods'=>array(
                array(
                    'endpoint'=>null,
                    'method'=>'',
                    'params'=>array(
                        'key'=>'value'
                    ),
                    'content_types'=>array(
                        'types'
                    )
                )
            ),
            'display_all_results'=>''
        );

        $this->assertEquals($expected, $actual);
        // $this->assertArrayHasKey('connect', $actual);
        // $this->assertArrayHasKey('old', $actual['connect']);
    }

    public function testSetConnect()
    {
        $config_object = new \Giift\Compare\Config();

        // Set config
        $config_object->set_connect(
            'Bearer aSD4FDSMCskd43fsLKdfa2',
            'http://www.tshirt.com/api/',
            null,
            'http://www.tshirt.com/api/1.0.development/'
        );

        // Get the config
        $actual = $config_object->get_config();
        $expected = array(
            'connect' => array(
                'old'=>array(
                    'token'=>'Bearer aSD4FDSMCskd43fsLKdfa2',
                    'base_uri'=>'http://www.tshirt.com/api/'
                ),
                'new'=>array(
                    'token'=>'Bearer aSD4FDSMCskd43fsLKdfa2',
                    'base_uri'=>'http://www.tshirt.com/api/1.0.development/'
                )
            ),
            'methods'=>array(
                array(
                    'endpoint'=>null,
                    'method'=>'',
                    'params'=>array(
                        'key'=>'value'
                    ),
                    'content_types'=>array(
                        'types'
                    )
                )
            ),
            'display_all_results'=>''
        );

        // Check if equal
        $this->assertEquals($expected, $actual);
    }

    public function testSetDisplay()
    {
        $config_object = new \Giift\Compare\Config();

        // Set display option
        $config_object->set_display_opt(true);

        $actual = $config_object->get_config();
        $expected = array(
            'connect'=>array(
                'old'=>array(
                    'token'=>'',
                    'base_uri'=>''
                ),
                'new'=>array(
                    'token'=>'',
                    'base_uri'=>''
                )
            ),
            'methods'=>array(
                array(
                    'endpoint'=>null,
                    'method'=>'',
                    'params'=>array(
                        'key'=>'value'
                    ),
                    'content_types'=>array(
                        'types'
                    )
                )
            ),
            'display_all_results'=>true
        );
    }

    public function testSetMethods()
    {
        $config_object = new \Giift\Compare\Config();

        // Add methods
        $methods = array(
            array(
                "endpoint"=> "/orders?userId=1964401a-a8b3-40c1-b86e-d8b9f75b5842&size=10&page=0",
                "method"=> "GET",
                "content_types"=> array(
                    "application/json"
                )
            ),
            array(
                'endpoint'=>'report/data/data',
                'method'=>'POST',
                'params'=>array(
                    'report_data'=>array(
                        'report_select'=>'agepie',
                        'from'=>'2016-05-01',
                        'to'=>'2016-06-01',
                        'timezone'=>'UTC'
                    )
                ),
                'content_types'=>array('application/json')
            )
        );

        $config_object->set_methods($methods);

        $actual = $config_object->get_config();
        $expected = array(
            'connect'=>array(
                'old'=>array(
                    'token'=>'',
                    'base_uri'=>''
                ),
                'new'=>array(
                    'token'=>'',
                    'base_uri'=>''
                )
            ),
            'methods'=>array(
                array(
                    "endpoint"=> "/orders?userId=1964401a-a8b3-40c1-b86e-d8b9f75b5842&size=10&page=0",
                    "method"=> "GET",
                    "content_types"=> array(
                        "application/json"
                    )
                ),
                array(
                    'endpoint'=>'report/data/data',
                    'method'=>'POST',
                    'params'=>array(
                        'report_data'=>array(
                            'report_select'=>'agepie',
                            'from'=>'2016-05-01',
                            'to'=>'2016-06-01',
                            'timezone'=>'UTC'
                        )
                    ),
                    'content_types'=>array('application/json')
                )
            ),
            'display_all_results'=>''
        );

        $this->assertEquals($expected, $actual);
    }

    public function testAddMethod()
    {
        $config_object = new \Giift\Compare\Config();

        $params = array(
            'report_data'=>array(
                'report_select'=>'countrypie',
                'from'=>'2016-05-01',
                'to'=>'2016-06-01',
                'timezone'=>'UTC'
            )
        );

        $config_object->add_method('report/data/data', 'POST', array('application/json'), $params);

        $actual = $config_object->get_config();
        $expected = array(
            'connect'=>array(
                'old'=>array(
                    'token'=>'',
                    'base_uri'=>''
                ),
                'new'=>array(
                    'token'=>'',
                    'base_uri'=>''
                )
            ),
            'methods'=>array(
                array(
                    'endpoint'=>'report/data/data',
                    'method'=>'POST',
                    'params'=>array(
                        'report_data'=>array(
                            'report_select'=>'countrypie',
                            'from'=>'2016-05-01',
                            'to'=>'2016-06-01',
                            'timezone'=>'UTC'
                        )
                    ),
                    'content_types'=>array('application/json')
                )
            ),
            'display_all_results'=>''
        );

        $this->assertEquals($expected, $actual);
    }
}

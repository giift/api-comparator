<?php
require __DIR__.'/../vendor/autoload.php';
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    protected $config;

    public function setUp()
    {
        $this->config = new \Giift\Compare\Config();
    }

    /**
     * Constructor should return \Giift\Compare\Config object
     */
    public function testConstruct()
    {
        // Check type of object returned
        $this->assertInstanceOf(\Giift\Compare\Config::class, $this->config);

        // return $config_object;
    }

    /**
     * Test get_config()
     */
    public function testGet()
    {
        // Config should be the template
        $actual = $this->config->get_config();
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
    }

    /**
     * Test set_connect()
     */
    public function testSetConnect()
    {
        // Set config
        $this->config->set_connect(
            'Bearer aSD4FDSMCskd43fsLKdfa2',
            'http://www.tshirt.com/api/',
            null,
            'http://www.tshirt.com/api/1.0.development/'
        );

        // Get the config
        $actual = $this->config->get_config();
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

    /**
     * Test set_display_opt()
     */
    public function testSetDisplay()
    {
        // Set display option
        $this->config->set_display_opt(true);

        // Check config
        $actual = $this->config->get_config();
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

        $this->assertEquals($expected, $actual);
    }

    /**
     * Test set_methods()
     * @group test
     */
    public function testSetMethods()
    {
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
                'endpoint'=>'/returns',
                'method'=>'POST',
                'params'=>array(
                    'size'=>'S',
                    'account'=>'myaccount123'
                ),
                'content_types'=>array('application/json')
            )
        );

        $this->config->set_methods($methods);

        // Check config
        $actual = $this->config->get_config();
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
                    'endpoint'=>'/returns',
                    'method'=>'POST',
                    'params'=>array(
                        'size'=>'S',
                        'account'=>'myaccount123'
                    ),
                    'content_types'=>array('application/json')
                )
            ),
            'display_all_results'=>''
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * add_method() should add on to existing methods
     */
    public function testAddMethod()
    {
        // Set methods
        $methods = array(
            array(
                "endpoint"=> "/orders?userId=1964401a-a8b3-40c1-b86e-d8b9f75b5842&size=10&page=0",
                "method"=> "GET",
                "content_types"=> array(
                    "application/json"
                )
            ),
            array(
                'endpoint'=>'/returns',
                'method'=>'POST',
                'params'=>array(
                    'size'=>'S',
                    'account'=>'myaccount123'
                ),
                'content_types'=>array('application/json')
            )
        );

        $this->config->set_methods($methods);

        // Add a method
        $this->config->add_method('/status', 'GET', array('application/json'));

        // Check config
        $actual = $this->config->get_config();
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
                    'endpoint'=>'/returns',
                    'method'=>'POST',
                    'params'=>array(
                        'size'=>'S',
                        'account'=>'myaccount123'
                    ),
                    'content_types'=>array('application/json')
                ),
                array(
                    'endpoint'=>'/status',
                    'method'=>'GET',
                    'content_types'=>array('application/json')
                )
            ),
            'display_all_results'=>''
        );

        $this->assertEquals($expected, $actual);
    }

    /**
     * Test validate() method
     */
    public function testValidate()
    {
        // Set 'connect'
        $this->config->set_connect(
            'Bearer aSD4FDSMCskd43fsLKdfa2',
            'http://www.tshirt.com/api/',
            null,
            'http://www.tshirt.com/api/1.0.development/'
        );

        // Set 'methods'
        $methods = array(
            array(
                "endpoint"=> "/orders?userId=1964401a-a8b3-40c1-b86e-d8b9f75b5842&size=10&page=0",
                "method"=> "GET",
                "content_types"=> array(
                    "application/json"
                )
            ),
            array(
                'endpoint'=>'/returns',
                'method'=>'POST',
                'params'=>array(
                    'size'=>'S',
                    'account'=>'myaccount123'
                ),
                'content_types'=>array('application/json')
            )
        );

        $this->config->set_methods($methods);

        // Set display_opt
        $this->config->set_display_opt(true);

        // Validate config
        $this->assertTrue($this->config->validate());
    }

    /**
     * Test create_from_file() method
     */
    public function testFileConfig()
    {
        $config = \Giift\Compare\Config::create_from_file(__DIR__.'/../examples/config.json');
        $config_object = new \Giift\Compare\Config($config);

        // Validate the config
        $this->assertTrue($config_object->validate());
    }
}

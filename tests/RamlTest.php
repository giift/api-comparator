<?php
require __DIR__.'/../vendor/autoload.php';

class RamlTest extends PHPUnit_Framework_TestCase
{
    protected $raml;

    public function setUp()
    {
        $this->raml = new \Giift\Compare\Parser\Raml(__DIR__.'/../examples/api.raml');
    }

    /**
     * Constructor should return \Giift\Compare\Parser\Raml object
     */
    public function testConstruct()
    {
        $this->assertInstanceOf(\Giift\Compare\Parser\Raml::class, $this->raml);
    }

    /**
     * Parsing RAML file should return \Raml\ApiDefinition object
     */
    public function testGetParsed()
    {
        $parsed = $this->raml->get_parsed();
        $this->assertInstanceOf(\Raml\ApiDefinition::class, $parsed);
    }

    /**
     * Sample config should be returned
     */
    public function testGetConfig()
    {
        $config = $this->raml->get_config();
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

        $this->assertEquals($expected, $config);
    }

    /**
     * Test create_config()
     */
    public function testCreateConfig()
    {
        $this->assertTrue($this->raml->create_config());

        $config = $this->raml->get_config();

        $config['connect'] = array(
            'old'=>array(
                'token'=>'Bearer SKsfdGSdsfa8sdJDEMNLKd734',
                'base_uri'=>'http://www.tshirt.com/api/'
            ),
            'new'=>array(
                'token'=>'Bearer SKsfdGSdsfa8sdJDEMNLKd734',
                'base_uri'=>'http://www.tshirt.com/api/1.0.development/'
            )
        );
        $config['display_all_results'] = true;

        $this->assertJsonStringEqualsJsonFile(__DIR__.'/../examples/config.json', json_encode($config));
    }

    /**
     * Test get_missing_fields()
     */
    public function testMissingFields()
    {
        $this->assertEmpty($this->raml->get_missing_fields());

        $bad_raml = new \Giift\Compare\Parser\Raml(__DIR__.'/../examples/bad_api.raml');

        $this->assertFalse($bad_raml->create_config());
        $this->assertNotEmpty($bad_raml->get_missing_fields());
    }

    /**
     * Test get_missing_field()
     * @group test
     */
    public function testMissingField()
    {
        $this->assertEmpty($this->raml->get_missing_field('/orders'));

        $bad_raml = new \Giift\Compare\Parser\Raml(__DIR__.'/../examples/bad_api.raml');

        $this->assertFalse($bad_raml->create_config());

        $this->assertNotEmpty($bad_raml->get_missing_field('/orders'));
    }
}

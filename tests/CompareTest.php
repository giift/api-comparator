<?php
require '../vendor/autoload.php';
use PHPUnit\Framework\TestCase;

class CompareTest extends TestCase
{
    protected $compare;

    public function setUp()
    {
        $config = array(
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
            'methods' => array(
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
            'display_all_results'=>true
        );

        $this->compare = new \Giift\Compare\Compare($config);
    }

    /**
     * Constructor should return an object of \Giift\Compare\Compare
     * @return \Giift\Compare\Compare
     */
    public function testConstruct()
    {
        $this->assertInstanceOf(\Giift\Compare\Compare::class, $this->compare);
    }

    /**
     * Test get_connect() method
     */
    public function testGetConnect()
    {
        $connect = $this->compare->get_connect();
        $expected = array(
            'old'=>array(
                'token'=>'Bearer aSD4FDSMCskd43fsLKdfa2',
                'base_uri'=>'http://www.tshirt.com/api/'
            ),
            'new'=>array(
                'token'=>'Bearer aSD4FDSMCskd43fsLKdfa2',
                'base_uri'=>'http://www.tshirt.com/api/1.0.development/'
            )
        );

        $this->assertEquals($expected, $connect);
    }

    /**
     * Test set_connect() method
     */
    public function testSetConnect()
    {
        $this->compare->set_connect('Bearer ytDMkhSsrtD38aJENcsf873', 'http://www.tshirt.com/api/v0', null, 'http://www.tshirt.com/api/v1');
        $connect = $this->compare->get_connect();

        $expected = array(
            'old'=>array(
                'token'=>'Bearer ytDMkhSsrtD38aJENcsf873',
                'base_uri'=>'http://www.tshirt.com/api/v0'
            ),
            'new'=>array(
                'token'=>'Bearer ytDMkhSsrtD38aJENcsf873',
                'base_uri'=>'http://www.tshirt.com/api/v1'
            )
        );

        $this->assertEquals($expected, $connect);
    }

    /**
     * Test get_methods() method
     */
    public function testGetMethods()
    {
        $methods = $this->compare->get_methods();
        $expected = array(
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

        $this->assertEquals($expected, $methods);
    }

    /**
     * add_method should add on to existing methods
     */
    public function testAddMethod()
    {
        $this->compare->add_method('/status', 'GET', array('application/json'));
        $methods = $this->compare->get_methods();

        $expected = array(
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
                'content_types'=>array(
                    'application/json'
                )
            ),
            array(
                'endpoint'=>'/status',
                'method'=>'GET',
                'content_types'=>array(
                    'application/json'
                )
            )
        );

        $this->assertEquals($expected, $methods);
    }

    /**
     * Test get and set methods for display_opt
     */
    public function testDisplay()
    {
        $this->assertTrue($this->compare->get_display_opt());

        $this->compare->set_display_opt(false);
        $this->assertFalse($this->compare->get_display_opt());
    }

    /**
     * Test methods to manipluate index
     */
    public function testIndex()
    {
        // Set index
        $this->compare->set_index(2);
        // Get index
        $index = $this->compare->get_index();
        $this->assertEquals(2, $index);
        // Reset index
        $this->compare->reset_index();
        // Get index
        $index = $this->compare->get_index();
        $this->assertEquals(0, $index);
    }

    /**
     * Test raw, xml, and json comparisons
     */
    public function testCompare()
    {
        $old = new \Giift\Simplecurl\Curl;
        $new = new \Giift\Simplecurl\Curl;

        // Test md5
        $old->_raw = file_get_contents(__DIR__.'/../examples/config.json');
        $new->_raw = $old->_raw;

        $this->assertTrue($this->compare->compare($old, $new, '/test/md5/'));

        $new->_raw = null;

        // Test xml for same responses
        $old->_infos['content_type'] = 'application/xml';
        $new->_infos['content_type'] = 'application/xml';

        $old->_response = file_get_contents(__DIR__.'/../examples/result.xml');
        $new->_response = $old->_response;

        $this->assertTrue($this->compare->compare($old, $new, '/test/xml/same/'));

        // Test xml for different responses
        $new->_response = file_get_contents(__DIR__.'/../examples/result_failure.xml');

        $this->assertFalse($this->compare->compare($old, $new, '/test/xml/diff/'));
        $this->compare->reset_differences();

        // Test json for same responses
        $old->_infos['content_type'] = 'application/json';
        $new->_infos['content_type'] = 'application/json';

        $old->_response = json_decode($old->_raw, true);
        $new->_response = $old->_response;

        $this->assertTrue($this->compare->compare($old, $new, '/test/json/same/'));

        // Test json for different responses
        $new->_response = array(
            'old'=>array(
                'token'=>'Bearer ytDMkhSsrtD38aJENcsf873',
                'base_uri'=>'http://www.tshirt.com/api/v0'
            ),
            'new'=>array(
                'token'=>'Bearer ytDMkhSsrtD38aJENcsf873',
                'base_uri'=>'http://www.tshirt.com/api/v1'
            )
        );

        $this->assertFalse($this->compare->compare($old, $new, '/test/json/diff/'));
    }

    /**
     * Test get and reset methods for differences
     */
    public function testDifferences()
    {
        $differences = array();

        $old = new \Giift\Simplecurl\Curl;
        $new = new \Giift\Simplecurl\Curl;

        $old->_raw = file_get_contents(__DIR__.'/../examples/config.json');
        $new->_raw = null;

        $old->_infos['content_type'] = 'application/json';
        $new->_infos['content_type'] = 'application/json';

        $old->_response = json_decode($old->_raw, true);
        $new->_response = array(
            'old'=>array(
                'token'=>'Bearer ytDMkhSsrtD38aJENcsf873',
                'base_uri'=>'http://www.tshirt.com/api/v0'
            ),
            'new'=>array(
                'token'=>'Bearer ytDMkhSsrtD38aJENcsf873',
                'base_uri'=>'http://www.tshirt.com/api/v1'
            )
        );

        $this->assertFalse($this->compare->compare($old, $new, '/test/diffs/'));

        $differences = $this->compare->get_differences();
        $this->assertNotEmpty($differences);

        // Each difference should have 'old' and 'new' keys
        foreach($differences as $difference)
        {
            $this->assertArrayHasKey('old', $difference);
            $this->assertArrayHasKey('new', $difference);
        }

        $this->compare->reset_differences();

        $this->assertEmpty($this->compare->get_differences());
    }

    /**
     * Test get_results() method
     */
    public function testGetResults()
    {
        $old = new \Giift\Simplecurl\Curl;
        $new = new \Giift\Simplecurl\Curl;

        $old->_raw = file_get_contents(__DIR__.'/../examples/config.json');
        $new->_raw = null;

        $old->_infos['content_type'] = 'application/json';
        $new->_infos['content_type'] = 'application/json';

        $old->_response = json_decode($old->_raw, true);
        $new->_response = array(
            'old'=>array(
                'token'=>'Bearer ytDMkhSsrtD38aJENcsf873',
                'base_uri'=>'http://www.tshirt.com/api/v0'
            ),
            'new'=>array(
                'token'=>'Bearer ytDMkhSsrtD38aJENcsf873',
                'base_uri'=>'http://www.tshirt.com/api/v1'
            )
        );

        $this->assertFalse($this->compare->compare($old, $new, '/test/results/'));

        $results = $this->compare->get_results();

        $this->assertNotEmpty($results);

        foreach ($results as $result)
        {
            $this->assertArrayHasKey('name', $result);
            $this->assertArrayHasKey('differences', $result);
        }
    }

    /**
     * Validate junit against schema
     * @group junit
     */
    public function testToJunit()
    {
        $old = new \Giift\Simplecurl\Curl;
        $new = new \Giift\Simplecurl\Curl;

        $old->_raw = file_get_contents(__DIR__.'/../examples/config.json');
        $new->_raw = null;

        $old->_infos['content_type'] = 'application/json';
        $new->_infos['content_type'] = 'application/json';

        $old->_response = json_decode($old->_raw, true);
        $new->_response = array(
            'connect'=>array(
                'old'=>array(
                    'token'=>'Bearer ytDMkhSsrtD38aJENcsf873',
                    'base_uri'=>'http://www.tshirt.com/v0'
                ),
                'new'=>array(
                    'token'=>'Bearer ytDMkhSsrtD38aJENcsf873',
                    'base_uri'=>'http://www.tshirt.com'
                )
            )
        );

        $this->assertFalse($this->compare->compare($old, $new, '/test/junit/'));

        $xml = new DOMDocument;
        $this->compare->to_file('temp/test.xml', 'xml');
        $this->assertFileExists('temp/test.xml');
        $xml->load('temp/test.xml');
        $this->assertTrue($xml->schemaValidate(__DIR__.'/../schema/Junit.xsd'));
    }

    /**
     * Test to_csv()
     * @group csv
     */
    public function testToCsv()
    {
        $old = new \Giift\Simplecurl\Curl;
        $new = new \Giift\Simplecurl\Curl;

        $old->_raw = file_get_contents(__DIR__.'/../examples/config.json');
        $new->_raw = null;

        $old->_infos['content_type'] = 'application/json';
        $new->_infos['content_type'] = 'application/json';

        $old->_response = json_decode($old->_raw, true);
        $new->_response = array(
            'connect'=>array(
                'old'=>array(
                    'token'=>'Bearer ytDMktD38aJENcsf873',
                    'base_uri'=>'http://www.tshirt.com'
                ),
                'new'=>array(
                    'token'=>'Bearer ytDMkhSsrtD38aJENcsf873',
                    'base_uri'=>'http://www.tshirt.com/api/v1'
                )
            )
        );

        $this->assertFalse($this->compare->compare($old, $new, '/test/csv/'));

        // Check if all columns are there
        $this->compare->to_file('temp/test.csv', 'csv');
        $csv = file('temp/test.csv');

        foreach ($csv as $line)
        {
            $this->assertCount(5, str_getcsv($line, ","));
        }
    }

    /**
     * Formatted results should be a string
     */
    public function testFormat()
    {
        $old = new \Giift\Simplecurl\Curl;
        $new = new \Giift\Simplecurl\Curl;

        $old->_raw = file_get_contents(__DIR__.'/../examples/config.json');
        $new->_raw = null;

        $old->_infos['content_type'] = 'application/json';
        $new->_infos['content_type'] = 'application/json';

        $old->_response = json_decode($old->_raw, true);
        $new->_response = array(
            'old'=>array(
                'token'=>'Bearer ytDMkhSsrtD38aJENcsf873',
                'base_uri'=>'http://www.tshirt.com/api/v0'
            ),
            'new'=>array(
                'token'=>'Bearer ytDMkhSsrtD38aJENcsf873',
                'base_uri'=>'http://www.tshirt.com/api/v1'
            )
        );

        $this->assertFalse($this->compare->compare($old, $new, '/test/format/'));

        $outputs = array(
            $this->compare->format(),
            $this->compare->format('csv'),
            $this->compare->format('xml')
        );

        foreach ($outputs as $output)
        {
            $this->assertNotNull($output);
            $this->assertInternalType('string', $output);
        }
    }

    /**
     * Formatted results should be same as data in file
     */
    public function testToFile()
    {
        $old = new \Giift\Simplecurl\Curl;
        $new = new \Giift\Simplecurl\Curl;

        $old->_raw = file_get_contents(__DIR__.'/../examples/config.json');
        $new->_raw = null;

        $old->_infos['content_type'] = 'application/json';
        $new->_infos['content_type'] = 'application/json';

        $old->_response = json_decode($old->_raw, true);
        $new->_response = array(
            'old'=>array(
                'token'=>'Bearer ytDMkhSsrtD38aJENcsf873',
                'base_uri'=>'http://www.tshirt.com/api/v0'
            ),
            'new'=>array(
                'token'=>'Bearer ytDMkhSsrtD38aJENcsf873',
                'base_uri'=>'http://www.tshirt.com/api/v1'
            )
        );

        $this->assertFalse($this->compare->compare($old, $new, '/test/file/'));

        // // Test json format
        $json = $this->compare->format();
        $this->compare->to_file('temp/results.json', 'json');
        $this->assertFileExists('temp/results.json');
        $this->assertJsonStringEqualsJsonFile('temp/results.json', $json);

        // // Test csv format
        $csv = $this->compare->format('csv');
        $this->compare->to_file('temp/results.csv', 'csv');
        $this->assertFileExists('temp/results.csv');
        $this->assertStringEqualsFile('temp/results.csv', $csv);

        // Test xml/junit format
        $xml = $this->compare->format('xml');
        $this->compare->to_file('temp/results.xml', 'xml');
        $this->assertFileExists('temp/results.xml');
        $file = file_get_contents('temp/results.xml');
        $this->assertEquals($xml, $file);
    }
}

<?php
require '../vendor/autoload.php';

use PHPUnit\Framework\TestCase;

class RamlTest extends TestCase
{
	public function testConstruct()
	{
		$raml_object = new \Giift\Compare\Parser\Raml('/Users/KohChinWee/dev/api-anisha/report.raml');

		$this->assertInstanceOf('\Giift\Compare\Parser\Raml', $raml_object);
	}
}

<?php

namespace Fluxoft\Migrant;

use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase {
	protected $configMock;

	protected function setup(): void {
		$this->configMock = $this->getMockBuilder('\Fluxoft\Migrant\Config')
			->disableOriginalConstructor()
			->getMock();
	}

	protected function teardown(): void {}

	public function testFooNotEqualBar() {
		$this->assertNotEquals('foo', 'bar');
	}
}
 
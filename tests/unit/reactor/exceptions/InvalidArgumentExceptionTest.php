<?php

/**
 * @copyright Frederic G. Østby
 * @license   http://www.makoframework.com/license
 */

namespace mako\tests\unit\reactor\exceptions;

use mako\reactor\exceptions\InvalidArgumentException;
use mako\tests\TestCase;

/**
 * @group unit
 */
class InvalidArgumentExceptionTest extends TestCase
{
	/**
	 *
	 */
	public function testException(): void
	{
		$exception = new InvalidArgumentException('foo', 'bar');

		$this->assertEquals('foo', $exception->getMessage());

		$this->assertEquals('bar', $exception->getName());
	}
}

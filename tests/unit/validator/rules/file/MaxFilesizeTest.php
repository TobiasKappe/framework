<?php

/**
 * @copyright Frederic G. Østby
 * @license   http://www.makoframework.com/license
 */

namespace mako\tests\unit\validator\rules\file;

use mako\tests\TestCase;
use mako\validator\rules\file\MaxFilesize;
use SplFileInfo;

/**
 * @group unit
 */
class MaxFilesizeTest extends TestCase
{
	/**
	 *
	 */
	public function testValidatesWhenEmpty(): void
	{
		$rule = new MaxFilesize;

		$this->assertFalse($rule->validateWhenEmpty());
	}

	/**
	 *
	 */
	public function testConvertToBytes(): void
	{
		$rule = new class extends MaxFilesize
		{
			public function convert($size)
			{
				return $this->convertToBytes($size);
			}
		};

		$this->assertSame(1024, $rule->convert(1024));
		$this->assertSame(1024, $rule->convert('1KiB'));
		$this->assertSame(1024 ** 2, $rule->convert('1MiB'));
		$this->assertSame(1024 ** 3, $rule->convert('1GiB'));
		$this->assertSame(1024 ** 4, $rule->convert('1TiB'));
		$this->assertSame(1024 ** 5, $rule->convert('1PiB'));
		$this->assertSame(1024 ** 6, $rule->convert('1EiB'));
		$this->assertSame(1024 ** 7, $rule->convert('1ZiB'));
		$this->assertSame(1024 ** 8, $rule->convert('1YiB'));
	}

	/**
	 * @expectedException RuntimeException
	 * @expectedExceptionMessage Invalid unit type [ Foo ].
	 */
	public function testConvertToBytesWithInvalidUnit(): void
	{
		$rule = new class extends MaxFilesize
		{
			public function convert($size)
			{
				return $this->convertToBytes($size);
			}
		};

		$this->assertSame(1024 ** 8, $rule->convert('1Foo'));
	}

	/**
	 *
	 */
	public function testWithValidValue(): void
	{
		$rule = new MaxFilesize;

		$rule->setParameters(['1MiB']);

		$this->assertTrue($rule->validate(new SplFileInfo(__DIR__ . '/fixtures/png.png'), []));
	}

	/**
	 *
	 */
	public function testWithInvalidValue(): void
	{
		$rule = new MaxFilesize;

		$rule->setParameters(['0.5KiB']);

		$this->assertFalse($rule->validate(new SplFileInfo(__DIR__ . '/fixtures/png.png'), []));

		$this->assertSame('The foobar must be less than 0.5KiB in size.', $rule->getErrorMessage('foobar'));
	}
}

<?php

/**
 * @copyright Frederic G. Østby
 * @license   http://www.makoframework.com/license
 */

namespace mako\tests\unit\cli\output\helpers;

use mako\cli\output\helpers\UnorderedList;
use mako\tests\TestCase;
use Mockery;

/**
 * @group unit
 */
class UnorderedListTest extends TestCase
{
	/**
	 *
	 */
	public function testBasicList(): void
	{
		$output = Mockery::mock('mako\cli\output\Output');

		$list = new UnorderedList($output);

		$expected  = '';
		$expected .= '* one' . PHP_EOL;
		$expected .= '* two' . PHP_EOL;
		$expected .= '* three' . PHP_EOL;

		$this->assertSame($expected, $list->render(['one', 'two', 'three']));
	}

	/**
	 *
	 */
	public function testNestedLists(): void
	{
		$output = Mockery::mock('mako\cli\output\Output');

		$list = new UnorderedList($output);

		$expected  = '';
		$expected .= '* one' . PHP_EOL;
		$expected .= '* two' . PHP_EOL;
		$expected .= '* three' . PHP_EOL;
		$expected .= '  * one' . PHP_EOL;
		$expected .= '  * two' . PHP_EOL;
		$expected .= '  * three' . PHP_EOL;
		$expected .= '* four' . PHP_EOL;

		$this->assertSame($expected, $list->render(['one', 'two', 'three', ['one', 'two', 'three'], 'four']));
	}

	/**
	 *
	 */
	public function testCustomMarker(): void
	{
		$output = Mockery::mock('mako\cli\output\Output');

		$list = new UnorderedList($output);

		$expected  = '';
		$expected .= '# one' . PHP_EOL;
		$expected .= '# two' . PHP_EOL;
		$expected .= '# three' . PHP_EOL;

		$this->assertSame($expected, $list->render(['one', 'two', 'three'], '#'));
	}

	/**
	 *
	 */
	public function testDraw(): void
	{
		$output = Mockery::mock('mako\cli\output\Output');

		$list = new UnorderedList($output);

		$expected  = '';
		$expected .= '* one' . PHP_EOL;
		$expected .= '* two' . PHP_EOL;
		$expected .= '* three' . PHP_EOL;

		$output->shouldReceive('write')->once()->with($expected, 1);

		$list->draw(['one', 'two', 'three']);
	}

	/**
	 *
	 */
	public function testDrawWithCustomMarker(): void
	{
		$output = Mockery::mock('mako\cli\output\Output');

		$list = new UnorderedList($output);

		$expected  = '';
		$expected .= '# one' . PHP_EOL;
		$expected .= '# two' . PHP_EOL;
		$expected .= '# three' . PHP_EOL;

		$output->shouldReceive('write')->once()->with($expected, 1);

		$list->draw(['one', 'two', 'three'], '#');
	}
}

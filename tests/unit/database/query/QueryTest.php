<?php

/**
 * @copyright Frederic G. Østby
 * @license   http://www.makoframework.com/license
 */

namespace mako\tests\unit\database\query;

use mako\database\query\Query;
use mako\tests\TestCase;
use Mockery;

/**
 * @group unit
 */
class QueryTest extends TestCase
{
	/**
	 *
	 */
	public function getQuery()
	{
		$connection = Mockery::mock('mako\database\connections\Connection');

		$connection->shouldReceive('getQueryBuilderHelper')->andReturn(Mockery::mock('\mako\database\query\helpers\HelperInterface'));

		$connection->shouldReceive('getQueryCompiler')->andReturn(Mockery::mock('\mako\database\query\compilers\Compiler'));

		return new Query($connection);
	}

	/**
	 *
	 */
	public function testFrom(): void
	{
		$query = $this->getQuery();

		$query->from('foobar');

		$this->assertSame('foobar', $query->getTable());
	}

	/**
	 *
	 */
	public function testInto(): void
	{
		$query = $this->getQuery();

		$query->into('foobar');

		$this->assertSame('foobar', $query->getTable());
	}
}

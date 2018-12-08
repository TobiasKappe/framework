<?php

/**
 * @copyright Frederic G. Østby
 * @license   http://www.makoframework.com/license
 */

namespace mako\tests\unit\database\query\compilers;

use mako\database\query\Query;
use mako\tests\TestCase;
use Mockery;

/**
 * @group unit
 */
class SQLServerCompilerTest extends TestCase
{
	/**
	 *
	 */
	protected function getConnection()
	{
		$connection = Mockery::mock('\mako\database\connections\Connection');

		$connection->shouldReceive('getQueryBuilderHelper')->andReturn(Mockery::mock('\mako\database\query\helpers\HelperInterface'));

		$connection->shouldReceive('getQueryCompiler')->andReturnUsing(function($query)
		{
			return new \mako\database\query\compilers\SQLServer($query);
		});

		return $connection;
	}

	/**
	 *
	 */
	protected function getBuilder($table = 'foobar')
	{
		return (new Query($this->getConnection()))->table($table);
	}

	/**
	 *
	 */
	public function testBasicSelect(): void
	{
		$query = $this->getBuilder();

		$query = $query->getCompiler()->select();

		$this->assertEquals('SELECT * FROM [foobar]', $query['sql']);
		$this->assertEquals([], $query['params']);
	}

	/**
	 *
	 */
	public function testSelectWithLimit(): void
	{
		$query = $this->getBuilder();

		$query->limit(10);

		$query = $query->getCompiler()->select();

		$this->assertEquals('SELECT * FROM [foobar] ORDER BY (SELECT 0) OFFSET 0 ROWS FETCH NEXT 10 ROWS ONLY', $query['sql']);
		$this->assertEquals([], $query['params']);
	}

	/**
	 *
	 */
	public function testSelectWithLimitAndOrder(): void
	{
		$query = $this->getBuilder();

		$query->orderBy('foo', 'DESC');

		$query->limit(10);

		$query = $query->getCompiler()->select();

		$this->assertEquals('SELECT * FROM [foobar] ORDER BY [foo] DESC OFFSET 0 ROWS FETCH NEXT 10 ROWS ONLY', $query['sql']);
		$this->assertEquals([], $query['params']);
	}

	/**
	 *
	 */
	public function testSelectWithOffset(): void
	{
		$query = $this->getBuilder();

		$query->offset(10);

		$query = $query->getCompiler()->select();

		$this->assertEquals('SELECT * FROM [foobar] ORDER BY (SELECT 0) OFFSET 10 ROWS', $query['sql']);
		$this->assertEquals([], $query['params']);
	}

	/**
	 *
	 */
	public function testSelectWithOffsetAndOrder(): void
	{
		$query = $this->getBuilder();

		$query->orderBy('foo', 'DESC');

		$query->offset(10);

		$query = $query->getCompiler()->select();

		$this->assertEquals('SELECT * FROM [foobar] ORDER BY [foo] DESC OFFSET 10 ROWS', $query['sql']);
		$this->assertEquals([], $query['params']);
	}

	/**
	 *
	 */
	public function testSelectWithLimitAndOffset(): void
	{
		$query = $this->getBuilder();

		$query->limit(10);
		$query->offset(10);

		$query = $query->getCompiler()->select();

		$this->assertEquals('SELECT * FROM [foobar] ORDER BY (SELECT 0) OFFSET 10 ROWS FETCH NEXT 10 ROWS ONLY', $query['sql']);
		$this->assertEquals([], $query['params']);
	}

	/**
	 *
	 */
	public function testSelectWithLimitOffsetAndOrder(): void
	{
		$query = $this->getBuilder();

		$query->orderBy('foo', 'DESC');

		$query->limit(10);
		$query->offset(10);

		$query = $query->getCompiler()->select();

		$this->assertEquals('SELECT * FROM [foobar] ORDER BY [foo] DESC OFFSET 10 ROWS FETCH NEXT 10 ROWS ONLY', $query['sql']);
		$this->assertEquals([], $query['params']);
	}

	/**
	 *
	 */
	public function testSelectWithJSONColumn(): void
	{
		$query = $this->getBuilder();

		$query->select(['json->foo->0->bar']);

		$query = $query->getCompiler()->select();

		$this->assertEquals('SELECT JSON_VALUE([json], \'lax $."foo"[0]."bar"\') FROM [foobar]', $query['sql']);
		$this->assertEquals([], $query['params']);

		//

		$query = $this->getBuilder();

		$query->select(['json->foo->0->\'bar']);

		$query = $query->getCompiler()->select();

		$this->assertEquals('SELECT JSON_VALUE([json], \'lax $."foo"[0]."\'\'bar"\') FROM [foobar]', $query['sql']);
		$this->assertEquals([], $query['params']);

		//

		$query = $this->getBuilder();

		$query->select(['json->foo->0->"bar']);

		$query = $query->getCompiler()->select();

		$this->assertEquals('SELECT JSON_VALUE([json], \'lax $."foo"[0]."\\\"bar"\') FROM [foobar]', $query['sql']);
		$this->assertEquals([], $query['params']);

		//

		$query = $this->getBuilder();

		$query->select(['json->foo->0->bar as jsonvalue']);

		$query = $query->getCompiler()->select();

		$this->assertEquals('SELECT JSON_VALUE([json], \'lax $."foo"[0]."bar"\') AS [jsonvalue] FROM [foobar]', $query['sql']);
		$this->assertEquals([], $query['params']);

		//

		$query = $this->getBuilder();

		$query->select(['foobar.json->foo->0->bar as jsonvalue']);

		$query = $query->getCompiler()->select();

		$this->assertEquals('SELECT JSON_VALUE([foobar].[json], \'lax $."foo"[0]."bar"\') AS [jsonvalue] FROM [foobar]', $query['sql']);
		$this->assertEquals([], $query['params']);
	}

	/**
	 *
	 */
	public function testUpdateWithJSONColumn(): void
	{
		$query = $this->getBuilder();

		$query = $query->getCompiler()->update(['data->foo->bar->0' => 1]);

		$this->assertEquals('UPDATE [foobar] SET [data] = JSON_MODIFY([data], \'lax $."foo"."bar"[0]\', JSON_QUERY(\'?\'))', $query['sql']);
		$this->assertEquals([1], $query['params']);
	}

	/**
	 *
	 */
	public function testSelectWithExclusiveLock(): void
	{
		$query = $this->getBuilder();

		$query->lock();

		$query = $query->getCompiler()->select();

		$this->assertEquals('SELECT * FROM [foobar] WITH (UPDLOCK, ROWLOCK)', $query['sql']);
		$this->assertEquals([], $query['params']);
	}

	/**
	 *
	 */
	public function testSelectWithSharedLock(): void
	{
		$query = $this->getBuilder();

		$query->lock(false);

		$query = $query->getCompiler()->select();

		$this->assertEquals('SELECT * FROM [foobar] WITH (HOLDLOCK, ROWLOCK)', $query['sql']);
		$this->assertEquals([], $query['params']);
	}

	/**
	 *
	 */
	public function testSelectWithSharedLockMethod(): void
	{
		$query = $this->getBuilder();

		$query->sharedLock();

		$query = $query->getCompiler()->select();

		$this->assertEquals('SELECT * FROM [foobar] WITH (HOLDLOCK, ROWLOCK)', $query['sql']);
		$this->assertEquals([], $query['params']);
	}

	/**
	 *
	 */
	public function testSelectWithCustomLock(): void
	{
		$query = $this->getBuilder();

		$query->lock('CUSTOM LOCK');

		$query = $query->getCompiler()->select();

		$this->assertEquals('SELECT * FROM [foobar] CUSTOM LOCK', $query['sql']);
		$this->assertEquals([], $query['params']);
	}
}

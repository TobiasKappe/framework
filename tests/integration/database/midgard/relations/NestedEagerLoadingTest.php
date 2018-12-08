<?php

/**
 * @copyright Frederic G. Østby
 * @license   http://www.makoframework.com/license
 */

namespace mako\tests\integration\database\midgard\relations;

use mako\tests\integration\ORMTestCase;
use mako\tests\integration\TestORM;

// --------------------------------------------------------------------------
// START CLASSES
// --------------------------------------------------------------------------

class NestedEagerLoadingUser extends TestORM
{
	protected $tableName = 'users';

	public function articles()
	{
		return $this->hasMany('mako\tests\integration\database\midgard\relations\NestedEagerLoadingArticle', 'user_id');
	}
}

class NestedEagerLoadingArticle extends TestORM
{
	protected $tableName = 'articles';

	public function comments()
	{
		return $this->hasMany('mako\tests\integration\database\midgard\relations\NestedEagerLoadingComment', 'article_id');
	}
}

class NestedEagerLoadingComment extends TestORM
{
	protected $tableName = 'article_comments';
}

// --------------------------------------------------------------------------
// END CLASSES
// --------------------------------------------------------------------------

/**
 * @group integration
 * @group integration:database
 * @requires extension PDO
 * @requires extension pdo_sqlite
 */
class NestedEagerLoadingTest extends ORMTestCase
{
	/**
	 *
	 */
	public function testNestedEagerLoading(): void
	{
		$users = NestedEagerLoadingUser::including(['articles', 'articles.comments'])->ascending('id')->all();

		foreach($users as $user)
		{
			$this->assertInstanceOf('mako\database\midgard\ResultSet', $user->articles);

			foreach($user->articles as $article)
			{
				$this->assertInstanceOf('mako\tests\integration\database\midgard\relations\NestedEagerLoadingArticle', $article);

				$this->assertEquals($article->user_id, $user->id);

				$this->assertInstanceOf('mako\database\midgard\ResultSet', $article->comments);

				foreach($article->comments as $comment)
				{
					$this->assertInstanceOf('mako\tests\integration\database\midgard\relations\NestedEagerLoadingComment', $comment);

					$this->assertEquals($comment->article_id, $article->id);
				}
			}
		}

		$this->assertEquals(3, count($this->connectionManager->connection('sqlite')->getLog()));

		$this->assertEquals('SELECT * FROM "users" ORDER BY "id" ASC', $this->connectionManager->connection('sqlite')->getLog()[0]['query']);

		$this->assertEquals('SELECT * FROM "articles" WHERE "articles"."user_id" IN (\'1\', \'2\', \'3\')', $this->connectionManager->connection('sqlite')->getLog()[1]['query']);

		$this->assertEquals('SELECT * FROM "article_comments" WHERE "article_comments"."article_id" IN (\'1\', \'2\', \'3\')', $this->connectionManager->connection('sqlite')->getLog()[2]['query']);
	}

	/**
	 *
	 */
	public function testNestedEagerLoadingWithConstraints(): void
	{
		$users = NestedEagerLoadingUser::including(['articles', 'articles.comments' => function($query): void
		{
			$query->where('comment', '=', 'does not exist');
		}, ])->ascending('id')->all();

		foreach($users as $user)
		{
			$this->assertInstanceOf('mako\database\midgard\ResultSet', $user->articles);

			foreach($user->articles as $article)
			{
				$this->assertInstanceOf('mako\tests\integration\database\midgard\relations\NestedEagerLoadingArticle', $article);

				$this->assertEquals($article->user_id, $user->id);

				$this->assertInstanceOf('mako\database\midgard\ResultSet', $article->comments);

				$this->assertEquals(0, count($article->comments));
			}
		}

		$this->assertEquals(3, count($this->connectionManager->connection('sqlite')->getLog()));

		$this->assertEquals('SELECT * FROM "users" ORDER BY "id" ASC', $this->connectionManager->connection('sqlite')->getLog()[0]['query']);

		$this->assertEquals('SELECT * FROM "articles" WHERE "articles"."user_id" IN (\'1\', \'2\', \'3\')', $this->connectionManager->connection('sqlite')->getLog()[1]['query']);

		$this->assertEquals('SELECT * FROM "article_comments" WHERE "comment" = \'does not exist\' AND "article_comments"."article_id" IN (\'1\', \'2\', \'3\')', $this->connectionManager->connection('sqlite')->getLog()[2]['query']);
	}
}

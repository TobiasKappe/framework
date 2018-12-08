<?php

/**
 * @copyright Frederic G. Østby
 * @license   http://www.makoframework.com/license
 */

namespace mako\application\services;

use mako\redis\ConnectionManager;

/**
 * Redis service.
 *
 * @author Frederic G. Østby
 */
class RedisService extends Service
{
	/**
	 * {@inheritdoc}
	 */
	public function register(): void
	{
		$this->container->registerSingleton([ConnectionManager::class, 'redis'], function()
		{
			$config = $this->config->get('redis');

			return new ConnectionManager($config['default'], $config['configurations']);
		});
	}
}

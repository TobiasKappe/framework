<?php

/**
 * @copyright Frederic G. Østby
 * @license   http://www.makoframework.com/license
 */

namespace mako\http\exceptions;

use Throwable;

/**
 * Not found exception.
 *
 * @author Frederic G. Østby
 */
class NotFoundException extends HttpException
{
	/**
	 * Constructor.
	 *
	 * @param string|null     $message  Exception message
	 * @param \Throwable|null $previous Previous exception
	 */
	public function __construct(?string $message = null, ?Throwable $previous = null)
	{
		parent::__construct(404, $message, $previous);
	}
}

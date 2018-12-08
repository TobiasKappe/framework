<?php

/**
 * @copyright Frederic G. Østby
 * @license   http://www.makoframework.com/license
 */

namespace mako\reactor\exceptions;

use Throwable;

/**
 * Argument exception.
 *
 * @author Frederic G. Østby
 */
class ArgumentException extends ReactorException
{
	/**
	 * Argument name.
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * Constructor.
	 *
	 * @param string          $message  The Exception message to throw
	 * @param string          $name     Argument name
	 * @param int             $code     The Exception code
	 * @param \Throwable|null $previous The previous exception used for the exception chaining
	 */
	public function __construct(string $message, string $name, int $code = 0, ?Throwable $previous = null)
	{
		parent::__construct($message, $code, $previous);

		$this->name = $name;
	}

	/**
	 * Returns the name of the missing argument.
	 *
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}
}

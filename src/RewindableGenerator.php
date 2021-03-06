<?php

declare( strict_types = 1 );

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class RewindableGenerator implements Iterator {

	/**
	 * @var callable
	 */
	private $generatorFunction;

	/**
	 * @var Generator
	 */
	private $generator;

	/**
	 * @var callable|null
	 */
	private $onRewind;

	/**
	 * @param callable $generatorConstructionFunction A callable that should return a Generator
	 * @param callable $onRewind callable that gets invoked with 0 arguments after the iterator was rewinded
	 *
	 * @throws InvalidArgumentException
	 */
	public function __construct( callable $generatorConstructionFunction, callable $onRewind = null ) {
		$this->generatorFunction = $generatorConstructionFunction;
		$this->onRewind = $onRewind;
		$this->generateGenerator();
	}

	private function generateGenerator() {
		$this->generator = call_user_func( $this->generatorFunction );

		if ( !( $this->generator instanceof Generator ) ) {
			throw new InvalidArgumentException( 'The callable needs to return a Generator' );
		}
	}

	/**
	 * Return the current element
	 * @see Iterator::current
	 * @link http://php.net/manual/en/iterator.current.php
	 * @return mixed
	 */
	public function current() {
		return $this->generator->current();
	}

	/**
	 * Move forward to next element
	 * @see Iterator::next
	 * @link http://php.net/manual/en/iterator.next.php
	 */
	public function next() {
		$this->generator->next();
	}

	/**
	 * Return the key of the current element
	 * @see Iterator::key
	 * @link http://php.net/manual/en/iterator.key.php
	 * @return mixed scalar on success, or null on failure.
	 */
	public function key() {
		return $this->generator->key();
	}

	/**
	 * Checks if current position is valid
	 * @see Iterator::rewind
	 * @link http://php.net/manual/en/iterator.valid.php
	 * @return boolean
	 */
	public function valid() {
		return $this->generator->valid();
	}

	/**
	 * Rewind the Iterator to the first element
	 * @see Iterator::rewind
	 * @link http://php.net/manual/en/iterator.rewind.php
	 */
	public function rewind() {
		$this->generateGenerator();

		if ( is_callable( $this->onRewind ) ) {
			call_user_func( $this->onRewind );
		}

		if ( defined( 'HHVM_VERSION' ) ) {
			$this->generator->next();
		}
	}

	/**
	 * Sets a callable that gets invoked with 0 arguments after the iterator was rewinded.
	 * If a callable has been set already, an exception will be thrown.
	 *
	 * @since 1.1.0
	 *
	 * @param callable $onRewind
	 * @throws InvalidArgumentException
	 */
	public function onRewind( callable $onRewind ) {
		if ( $this->onRewind !== null ) {
			throw new InvalidArgumentException( 'Can only bind a onRewind handler once' );
		}

		$this->onRewind = $onRewind;
	}

}


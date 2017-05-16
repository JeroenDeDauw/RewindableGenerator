<?php

declare( strict_types = 1 );

use PHPUnit\Framework\TestCase;

/**
 * @covers RewindableGenerator
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class RewindableGeneratorTest extends TestCase {

	public function testAdaptsEmptyGenerator() {
		// Not using simply (yield) as a several static code analysis tools break on it
		$iterator = new RewindableGenerator( function() {
			foreach ( [] as $element ) {
				yield $element;
			}
		} );

		$this->assertSame( [], iterator_to_array( $iterator ) );
	}

	public function testAdaptsNotEmptyGenerator() {
		$iterator = new RewindableGenerator( function() {
			yield 'foo';
			yield 'bar';
			yield 'baz';
		} );

		$this->assertSame(
			[ 'foo', 'bar', 'baz' ],
			iterator_to_array( $iterator )
		);
	}

	public function testCanRewind() {
		$iterator = new RewindableGenerator( function() {
			yield 'foo';
			yield 'bar';
			yield 'baz';
		} );

		$iterator->next();

		if ( defined( 'HHVM_VERSION' ) ) {
			// The fuck HHVM?
			$iterator->next();
		}

		$this->assertSame( 'bar', $iterator->current(), 'next behaves as expected' );
		$iterator->rewind();
		$this->assertSame( 'foo', $iterator->current(), 'rewind behaves as expected' );
	}

	public function testIterateTwice() {
		$iterator = new RewindableGenerator( function() {
			yield 'foo';
			yield 'bar';
			yield 'baz';
		} );

		$this->assertSame(
			[ 'foo', 'bar', 'baz' ],
			iterator_to_array( $iterator )
		);

		$this->assertSame(
			[ 'foo', 'bar', 'baz' ],
			iterator_to_array( $iterator )
		);
	}

	public function testGivenNonGeneratorFunction_constructorThrowsException() {
		$this->expectException( 'InvalidArgumentException' );
		new RewindableGenerator( function() {} );
	}

	public function testWhenCallingItTwice_onRewindThrowsException() {
		$iterator = new RewindableGenerator( function() {
			yield 'foo';
			yield 'bar';
			yield 'baz';
		} );


		$iterator->onRewind( function() {} );
		$this->expectException( 'InvalidArgumentException' );
		$iterator->onRewind( function() {} );
	}

	public function testWhenOnRewindSetInConstructor_onRewindThrowsException() {
		$iterator = new RewindableGenerator(
			function() {
				yield 'foo';
				yield 'bar';
				yield 'baz';
			},
			function() {}
		);

		$this->expectException( 'InvalidArgumentException' );
		$iterator->onRewind( function() {} );
	}

	public function testWhenCallingRewind_onRewindCallbackGetsCalled() {
		$events = [];

		$iterator = new RewindableGenerator(
			function() {
				yield 'foo';
				yield 'bar';
				yield 'baz';
			},
			function() use ( &$events ) {
				$events[] = 'callback';
			}
		);

		$events[] = 'start';
		$iterator->rewind();
		$events[] = 'done';

		$this->assertSame(
			[ 'start', 'callback', 'done' ],
			$events
		);
	}

	public function testIteratingMultipleTimes_onRewindCallbackGetsCalled() {
		$events = [];

		$iterator = new RewindableGenerator(
			function() {
				yield 'foo';
				yield 'bar';
				yield 'baz';
			},
			function() use ( &$events ) {
				$events[] = 'callback';
			}
		);

		$events[] = 'start';
		iterator_to_array( $iterator );
		$events[] = 'one done';
		iterator_to_array( $iterator );
		$events[] = 'two done';

		$this->assertSame(
			[ 'start', 'callback', 'one done', 'callback', 'two done' ],
			$events
		);
	}

}


<?php

/**
 * @covers RewindableGenerator
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class RewindableGeneratorTest extends \PHPUnit_Framework_TestCase {

	public function testAdaptsEmptyGenerator() {
		$this->assertCount(
			0,
			// Not using simply (yield) as a several static code analysis tools break on it
			new RewindableGenerator( function() {
				foreach ( [] as $element ) {
					yield $element;
				}
			} )
		);
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
		$this->assertSame( 'bar', $iterator->current() );
		$iterator->rewind();
		$this->assertSame( 'foo', $iterator->current() );
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
		$this->setExpectedException( 'InvalidArgumentException' );
		new RewindableGenerator( function() {} );
	}

	public function testWhenCallingItTwice_onRewindThrowsException() {
		$iterator = new RewindableGenerator( function() {
			yield 'foo';
			yield 'bar';
			yield 'baz';
		} );


		$iterator->onRewind( function() {} );
		$this->setExpectedException( 'InvalidArgumentException' );
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

		$this->setExpectedException( 'InvalidArgumentException' );
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


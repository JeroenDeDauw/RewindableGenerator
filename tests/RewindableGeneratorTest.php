<?php

/**
 * @covers RewindableGenerator
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class RewindableGeneratorTest extends \PHPUnit_Framework_TestCase {

	public function testAdaptsEmptyGenerator() {
		$this->assertCount( 0, new RewindableGenerator( (yield) ) );
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

	public function testGivenNonGeneratorFunction_constructorThrowsException() {
		$this->setExpectedException( 'InvalidArgumentException' );
		new RewindableGenerator( function() {} );
	}

}


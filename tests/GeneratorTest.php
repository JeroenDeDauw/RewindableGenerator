<?php

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class GeneratorTest extends \PHPUnit_Framework_TestCase {

	private function helloGenerator() {
		yield "hello";
		yield "world";
	}

	public function testCannotIterateTwice() {
		$generator = $this->helloGenerator();

		iterator_to_array( $generator );

		$this->setExpectedException( 'Exception' );
		iterator_to_array( $generator ); // boom!
	}

	public function testCannotUseAndRewind() {
		if ( defined( 'HHVM_VERSION' ) ) {
			$this->markTestSkipped( 'Yay HHVM!' );
		}

		$generator = $this->helloGenerator();

		$generator->next();

		$this->setExpectedException( 'Exception' );
		$generator->rewind(); // boom!
	}

}


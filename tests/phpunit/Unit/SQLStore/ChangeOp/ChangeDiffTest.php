<?php

namespace SMW\Tests\SQLStore\ChangeOp;

use SMW\SQLStore\ChangeOp\ChangeDiff;
use SMW\DIWikiPage;

/**
 * @covers \SMW\SQLStore\ChangeOp\ChangeDiff
 * @group semantic-mediawiki
 *
 * @license GNU GPL v2+
 * @since 3.0
 *
 * @author mwjames
 */
class ChangeDiffTest extends \PHPUnit_Framework_TestCase {

	public function testCanConstruct() {

		$this->assertInstanceOf(
			ChangeDiff::class,
			new ChangeDiff( DIWikiPage::newFromText( 'Foo' ), [], [] )
		);
	}

	public function testGetSubject() {

		$subject = DIWikiPage::newFromText( 'Foo' );
		$instance = new ChangeDiff(
			$subject,
			[],
			[]
		);

		$this->assertEquals(
			$subject,
			$instance->getSubject()
		);
	}

	public function testGetPropertyList() {

		$instance = new ChangeDiff(
			DIWikiPage::newFromText( 'Foo' ),
			[],
			[ 'Foo' => 42 ]
		);

		$this->assertEquals(
			[ 'Foo' => 42 ],
			$instance->getPropertyList()
		);

		$this->assertEquals(
			[ 42 => 'Foo' ],
			$instance->getPropertyList( true )
		);
	}

	public function testSave() {

		$cache = $this->getMockBuilder( '\Onoi\Cache\Cache' )
			->disableOriginalConstructor()
			->getMock();

		$tableChangeOp = $this->getMockBuilder( '\SMW\SQLStore\ChangeOp\TableChangeOp' )
			->disableOriginalConstructor()
			->getMock();

		$instance = new ChangeDiff(
			DIWikiPage::newFromText( 'Foo' ),
			[ $tableChangeOp ],
			[ 'Foo' => 42 ]
		);

		$cache->expects( $this->once() )
			->method( 'save' )
			->with(
				$this->stringContains( ChangeDiff::CACHE_NAMESPACE ),
				$this->equalTo( $instance->serialize() ) );

		$instance->save( $cache );
	}

	public function testFetch() {

		$subject = DIWikiPage::newFromText( 'Foo' );

		$cache = $this->getMockBuilder( '\Onoi\Cache\Cache' )
			->disableOriginalConstructor()
			->getMock();

		$tableChangeOp = $this->getMockBuilder( '\SMW\SQLStore\ChangeOp\TableChangeOp' )
			->disableOriginalConstructor()
			->getMock();

		$instance = new ChangeDiff(
			DIWikiPage::newFromText( 'Foo' ),
			[ $tableChangeOp ],
			[ 'Foo' => 42 ]
		);

		$cache->expects( $this->once() )
			->method( 'fetch' )
			->will( $this->returnValue( $instance->serialize() ) );

		$this->assertEquals(
			$instance,
			ChangeDiff::fetch( $cache, $subject )
		);
	}

}

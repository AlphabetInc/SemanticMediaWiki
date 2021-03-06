<?php

namespace SMW\Tests\Updater;

use SMW\Updater\DeferredTransactionalUpdate;
use SMW\Tests\TestEnvironment;

/**
 * @covers \SMW\Updater\DeferredTransactionalUpdate
 * @group semantic-mediawiki
 *
 * @license GNU GPL v2+
 * @since 3.0
 *
 * @author mwjames
 */
class DeferredTransactionalUpdateTest extends \PHPUnit_Framework_TestCase {

	private $testEnvironment;
	private $spyLogger;
	private $connection;

	protected function setUp() {
		parent::setUp();
		$this->testEnvironment = new TestEnvironment();
		$this->spyLogger = $this->testEnvironment->getUtilityFactory()->newSpyLogger();

		$this->connection = $this->getMockBuilder( '\SMW\MediaWiki\Database' )
			->disableOriginalConstructor()
			->getMock();
	}

	protected function tearDown() {
		$this->testEnvironment->clearPendingDeferredUpdates();
		$this->testEnvironment->tearDown();
		parent::tearDown();
	}

	public function testCanConstruct() {

		$callback = function() {
			return null;
		};

		$this->assertInstanceOf(
			DeferredTransactionalUpdate::class,
			new DeferredTransactionalUpdate( $callback, $this->connection )
		);
	}

	public function testUpdate() {

		$test = $this->getMockBuilder( '\stdClass' )
			->disableOriginalConstructor()
			->setMethods( array( 'doTest' ) )
			->getMock();

		$test->expects( $this->once() )
			->method( 'doTest' );

		$callback = function() use ( $test ) {
			$test->doTest();
		};

		$instance = new DeferredTransactionalUpdate(
			$callback,
			$this->connection
		);

		$instance->setLogger( $this->spyLogger );
		$instance->pushUpdate();

		$this->testEnvironment->executePendingDeferredUpdates();
	}

	public function testUpdateOnEmptyCallback() {

		$instance = new DeferredTransactionalUpdate(
			null,
			$this->connection
		);

		$instance->setLogger( $this->spyLogger );
		$instance->pushUpdate();

		$this->testEnvironment->executePendingDeferredUpdates();

		$this->assertContains(
			'Empty callback',
			$this->spyLogger->getMessagesAsString()
		);
	}

	public function testUpdateOnLateCallback() {

		$instance = new DeferredTransactionalUpdate(
			null,
			$this->connection
		);

		$test = $this->getMockBuilder( '\stdClass' )
			->disableOriginalConstructor()
			->setMethods( array( 'doTest' ) )
			->getMock();

		$test->expects( $this->once() )
			->method( 'doTest' );

		$callback = function() use ( $test ) {
			$test->doTest();
		};

		$instance->setCallback( $callback );

		$instance->setLogger( $this->spyLogger );
		$instance->pushUpdate();

		$this->testEnvironment->executePendingDeferredUpdates();

		$this->assertContains(
			'[DeferrableUpdate] Added',
			$this->spyLogger->getMessagesAsString()
		);
	}

	public function testWaitableUpdate() {

		$test = $this->getMockBuilder( '\stdClass' )
			->disableOriginalConstructor()
			->setMethods( array( 'doTest' ) )
			->getMock();

		$test->expects( $this->once() )
			->method( 'doTest' );

		$callback = function() use ( $test ) {
			$test->doTest();
		};

		$instance = new DeferredTransactionalUpdate(
			$callback,
			$this->connection
		);

		$instance->setLogger( $this->spyLogger );

		$instance->markAsPending( true );
		$instance->pushUpdate();

		$instance->releasePendingUpdates();

		$this->testEnvironment->executePendingDeferredUpdates();
	}

	public function testUpdateWithDisabledDeferredUpdate() {

		$test = $this->getMockBuilder( '\stdClass' )
			->disableOriginalConstructor()
			->setMethods( array( 'doTest' ) )
			->getMock();

		$test->expects( $this->once() )
			->method( 'doTest' );

		$callback = function() use ( $test ) {
			$test->doTest();
		};

		$instance = new DeferredTransactionalUpdate(
			$callback,
			$this->connection
		);

		$instance->setLogger( $this->spyLogger );

		$instance->enabledDeferredUpdate( false );
		$instance->pushUpdate();
	}

	public function testOrigin() {

		$callback = function() {
		};

		$instance = new DeferredTransactionalUpdate(
			$callback,
			$this->connection
		);

		$instance->setLogger( $this->spyLogger );

		$instance->setOrigin( 'Foo' );

		$this->assertContains(
			'Foo',
			$instance->getOrigin()
		);
	}

	public function testFilterDuplicateQueueEntryByFingerprint() {

		$test = $this->getMockBuilder( '\stdClass' )
			->disableOriginalConstructor()
			->setMethods( array( 'doTest' ) )
			->getMock();

		$test->expects( $this->once() )
			->method( 'doTest' );

		$callback = function() use ( $test ) {
			$test->doTest();
		};

		$instance = new DeferredTransactionalUpdate(
			$callback,
			$this->connection
		);

		$instance->setLogger( $this->spyLogger );

		$instance->setFingerprint( __METHOD__ );
		$instance->markAsPending( true );
		$instance->pushUpdate();

		$instance = new DeferredTransactionalUpdate(
			$callback,
			$this->connection
		);

		$instance->setLogger( $this->spyLogger );

		$instance->setFingerprint( __METHOD__ );
		$instance->markAsPending( true );
		$instance->pushUpdate();

		$this->testEnvironment->executePendingDeferredUpdates();
	}

	public function testUpdateOnTransactionIdle() {

		$callback = function( $callback ) {
			return call_user_func( $callback );
		};

		$connection = $this->getMockBuilder( '\SMW\MediaWiki\Database' )
			->disableOriginalConstructor()
			->getMock();

		$connection->expects( $this->once() )
			->method( 'onTransactionIdle' )
			->will( $this->returnCallback( $callback ) );

		$this->testEnvironment->clearPendingDeferredUpdates();

		$test = $this->getMockBuilder( '\stdClass' )
			->disableOriginalConstructor()
			->setMethods( array( 'doTest' ) )
			->getMock();

		$test->expects( $this->once() )
			->method( 'doTest' );

		$callback = function() use ( $test ) {
			$test->doTest();
		};

		$instance = new DeferredTransactionalUpdate(
			$callback,
			$connection
		);

		$instance->setLogger( $this->spyLogger );

		$instance->waitOnTransactionIdle();
		$instance->pushUpdate();

		$this->testEnvironment->executePendingDeferredUpdates();
	}

	public function testCommitWithTransactionTicketOnDeferrableUpdate() {

		$connection = $this->getMockBuilder( '\SMW\MediaWiki\Database' )
			->disableOriginalConstructor()
			->getMock();

		$connection->expects( $this->once() )
			->method( 'getEmptyTransactionTicket' );

		$this->testEnvironment->clearPendingDeferredUpdates();

		$test = $this->getMockBuilder( '\stdClass' )
			->disableOriginalConstructor()
			->setMethods( array( 'doTest' ) )
			->getMock();

		$test->expects( $this->once() )
			->method( 'doTest' );

		$callback = function() use ( $test ) {
			$test->doTest();
		};

		$instance = new DeferredTransactionalUpdate(
			$callback,
			$connection
		);

		$instance->setLogger( $this->spyLogger );

		$instance->isDeferrableUpdate( true );
		$instance->commitWithTransactionTicket();
		$instance->pushUpdate();

		$this->testEnvironment->executePendingDeferredUpdates();
	}

	public function testCommitWithTransactionTicketOnNonDeferrableUpdate() {

		$connection = $this->getMockBuilder( '\SMW\MediaWiki\Database' )
			->disableOriginalConstructor()
			->getMock();

		$connection->expects( $this->never() )
			->method( 'getEmptyTransactionTicket' );

		$this->testEnvironment->clearPendingDeferredUpdates();

		$test = $this->getMockBuilder( '\stdClass' )
			->disableOriginalConstructor()
			->setMethods( array( 'doTest' ) )
			->getMock();

		$test->expects( $this->once() )
			->method( 'doTest' );

		$callback = function() use ( $test ) {
			$test->doTest();
		};

		$instance = new DeferredTransactionalUpdate(
			$callback,
			$connection
		);

		$instance->setLogger( $this->spyLogger );

		$instance->isDeferrableUpdate( false );
		$instance->commitWithTransactionTicket();
		$instance->pushUpdate();

		$this->testEnvironment->executePendingDeferredUpdates();
	}

}

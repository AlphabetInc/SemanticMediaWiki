<?php

namespace SMW\Tests\Connection;

use SMW\Connection\ConnectionManager;

/**
 * @covers \SMW\Connection\ConnectionManager
 * @group semantic-mediawiki
 *
 * @license GNU GPL v2+
 * @since 2.1
 *
 * @author mwjames
 */
class ConnectionManagerTest extends \PHPUnit_Framework_TestCase {

	public function testCanConstruct() {

		$this->assertInstanceOf(
			ConnectionManager::class,
			new ConnectionManager()
		);
	}

	public function testDefaultRegisteredConnectionProvided() {

		$instance = new ConnectionManager();
		$instance->releaseConnections();

		$connection = $instance->getConnection( 'mw.db' );

		$this->assertSame(
			$connection,
			$instance->getConnection( 'mw.db' )
		);

		$this->assertInstanceOf(
			'\SMW\MediaWiki\Database',
			$connection
		);

		$instance->releaseConnections();

		$this->assertNotSame(
			$connection,
			$instance->getConnection( 'mw.db' )
		);
	}

	public function testUnregisteredConnectionTypeThrowsException() {

		$instance = new ConnectionManager();

		$this->setExpectedException( 'RuntimeException' );
		$instance->getConnection( 'mw.master' );
	}

	public function testRegisterConnectionProvider() {

		$connectionProvider = $this->getMockBuilder( '\SMW\Connection\ConnectionProvider' )
			->disableOriginalConstructor()
			->getMock();

		$connectionProvider->expects( $this->once() )
			->method( 'getConnection' );

		$instance = new ConnectionManager();
		$instance->registerConnectionProvider( 'foo', $connectionProvider );

		$instance->getConnection( 'FOO' );
	}

}

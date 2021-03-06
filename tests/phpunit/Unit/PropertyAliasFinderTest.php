<?php

namespace SMW\Tests;

use SMW\PropertyAliasFinder;

/**
 * @covers \SMW\PropertyAliasFinder
 * @group semantic-mediawiki
 *
 * @license GNU GPL v2+
 * @since 2.1
 *
 * @author mwjames
 */
class PropertyAliasFinderTest extends \PHPUnit_Framework_TestCase {

	private $cache;
	private $store;

	protected function setUp() {
		parent::setUp();

		$this->cache = $this->getMockBuilder( '\Onoi\Cache\Cache' )
			->disableOriginalConstructor()
			->getMock();

		$this->store = $this->getMockBuilder( '\SMW\Store' )
			->disableOriginalConstructor()
			->getMockForAbstractClass();
	}

	public function testCanConstruct() {

		$languageIndependentPropertyLabels = array();

		$this->assertInstanceOf(
			PropertyAliasFinder::class,
			new PropertyAliasFinder( $this->cache )
		);
	}

	public function testFindPropertyAliasById() {

		$propertyAliases = array( 'Bar' => '_Foo' );

		$instance = new PropertyAliasFinder(
			$this->cache,
			$propertyAliases
		);

		$this->assertEquals(
			$propertyAliases,
			$instance->getKnownPropertyAliases()
		);

		$this->assertEquals(
			'Bar',
			$instance->findPropertyAliasById( '_Foo' )
		);
	}

	public function testFindPropertyIdByAlias() {

		$canonicalPropertyAliases = array( 'Bar' => '_Foo' );

		$instance = new PropertyAliasFinder(
			$this->cache,
			array(),
			$canonicalPropertyAliases
		);

		$this->assertEquals(
			'_Foo',
			$instance->findPropertyIdByAlias( 'Bar' )
		);
	}

	public function testRegisterAliasByFixedLabel() {

		$instance = new PropertyAliasFinder(
			$this->cache
		);

		$instance->registerAliasByFixedLabel( '_Foo', 'Bar' );

		$this->assertEquals(
			'_Foo',
			$instance->findPropertyIdByAlias( 'Bar' )
		);
	}

	public function testGetKnownPropertyAliasesByLanguageCodeCached() {

		$this->cache->expects( $this->once() )
			->method( 'fetch' )
			->will( $this->returnValue( [ '⧼smw-bar⧽' => '_Foo' ] ) );

		$instance = new PropertyAliasFinder(
			$this->cache
		);

		$instance->registerAliasByMsgKey( '_Foo', 'smw-bar' );

		$this->assertEquals(
			[ '⧼smw-bar⧽' => '_Foo' ],
			$instance->getKnownPropertyAliasesByLanguageCode( 'en' )
		);
	}

	public function testGetKnownPropertyAliasesByLanguageCode() {

		$this->cache->expects( $this->once() )
			->method( 'fetch' )
			->will( $this->returnValue( false ) );

		$instance = new PropertyAliasFinder(
			$this->cache
		);

		$instance->registerAliasByMsgKey( '_Foo', 'smw-bar' );

		$msgKey = version_compare( $GLOBALS['wgVersion'], '1.28', '<' ) ? '<smw-bar>' : '⧼smw-bar⧽' ;

		$this->assertEquals(
			[ $msgKey => '_Foo' ],
			$instance->getKnownPropertyAliasesByLanguageCode( 'en' )
		);
	}

}

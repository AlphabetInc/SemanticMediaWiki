<?php

namespace SMW\Maintenance;

use Onoi\MessageReporter\MessageReporterFactory;
use SMW\ApplicationFactory;
use SMW\MediaWiki\ManualEntryLogger;
use SMW\SQLStore\PropertyStatisticsStore;
use SMW\Maintenance\DuplicateEntitiesDisposer;
use SMW\SQLStore\SQLStore;
use SMW\Store;

/**
 * @license GNU GPL v2+
 * @since 2.2
 *
 * @author mwjames
 */
class MaintenanceFactory {

	/**
	 * @since 2.2
	 *
	 * @return MaintenanceHelper
	 */
	public function newMaintenanceHelper() {
		return new MaintenanceHelper();
	}

	/**
	 * @since 2.2
	 *
	 * @param Store $store
	 * @param Callable|null $reporterCallback
	 *
	 * @return DataRebuilder
	 */
	public function newDataRebuilder( Store $store, $reporterCallback = null ) {

		$messageReporter = MessageReporterFactory::getInstance()->newObservableMessageReporter();
		$messageReporter->registerReporterCallback( $reporterCallback );

		$dataRebuilder = new DataRebuilder(
			$store,
			ApplicationFactory::getInstance()->newTitleCreator()
		);

		$dataRebuilder->setMessageReporter( $messageReporter );

		return $dataRebuilder;
	}

	/**
	 * @since 2.2
	 *
	 * @param Store $store
	 * @param Callable|null $reporterCallback
	 *
	 * @return ConceptCacheRebuilder
	 */
	public function newConceptCacheRebuilder( Store $store, $reporterCallback = null ) {

		$messageReporter = MessageReporterFactory::getInstance()->newObservableMessageReporter();
		$messageReporter->registerReporterCallback( $reporterCallback );

		$conceptCacheRebuilder = new ConceptCacheRebuilder(
			$store,
			ApplicationFactory::getInstance()->getSettings()
		);

		$conceptCacheRebuilder->setMessageReporter( $messageReporter );

		return $conceptCacheRebuilder;
	}

	/**
	 * @since 2.2
	 *
	 * @param Store $store
	 * @param Callable|null $reporterCallback
	 *
	 * @return PropertyStatisticsRebuilder
	 */
	public function newPropertyStatisticsRebuilder( Store $store, $reporterCallback = null ) {

		$messageReporter = MessageReporterFactory::getInstance()->newObservableMessageReporter();
		$messageReporter->registerReporterCallback( $reporterCallback );

		$propertyStatisticsStore = new PropertyStatisticsStore(
			$store->getConnection( 'mw.db' )
		);

		$propertyStatisticsRebuilder = new PropertyStatisticsRebuilder(
			$store,
			$propertyStatisticsStore
		);

		$propertyStatisticsRebuilder->setMessageReporter( $messageReporter );

		return $propertyStatisticsRebuilder;
	}

	/**
	 * @since 2.4
	 *
	 * @return RebuildPropertyStatistics
	 */
	public function newRebuildPropertyStatistics() {
		return new RebuildPropertyStatistics();
	}

	/**
	 * @since 3.0
	 *
	 * @return DuplicateEntitiesDisposer
	 */
	public function newDuplicateEntitiesDisposer( Store $store, $reporterCallback = null  ) {

		$messageReporter = MessageReporterFactory::getInstance()->newObservableMessageReporter();
		$messageReporter->registerReporterCallback( $reporterCallback );

		$duplicateEntitiesDisposer = new DuplicateEntitiesDisposer(
			$store
		);

		$duplicateEntitiesDisposer->setMessageReporter(
			$messageReporter
		);

		return $duplicateEntitiesDisposer;
	}

	/**
	 * @since 2.4
	 *
	 * @param string $performer
	 *
	 * @return MaintenanceLogger
	 */
	public function newMaintenanceLogger( $performer ) {

		$maintenanceLogger = new MaintenanceLogger( $performer, new ManualEntryLogger() );
		$maintenanceLogger->setMaxNameChars( $GLOBALS['wgMaxNameChars'] );

		return $maintenanceLogger;
	}

}

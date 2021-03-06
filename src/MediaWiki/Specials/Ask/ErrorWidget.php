<?php

namespace SMW\MediaWiki\Specials\Ask;

use SMW\ProcessingErrorMsgHandler;
use SMW\Message;
use SMWQuery as Query;
use Html;

/**
 * @license GNU GPL v2+
 * @since   2.5
 *
 * @author mwjames
 */
class ErrorWidget {

	/**
	 * @since 2.5
	 *
	 * @return string
	 */
	public static function noResult() {
		return Html::element(
			'div',
			[
				'id'    => 'no-result',
				'class' => 'smw-callout smw-callout-info'
			],
			Message::get( 'smw_result_noresults', Message::TEXT, Message::USER_LANGUAGE )
		);
	}

	/**
	 * @since 3.0
	 *
	 * @return string
	 */
	public static function noScript() {
		return Html::rawElement(
			'div',
			array(
				'id'    => 'ask-status',
				'class' => 'smw-ask-status plainlinks'
			),
			Html::rawElement(
				'noscript',
				array(),
				Html::rawElement(
					'div',
					array(
						'class' => 'smw-callout smw-callout-error',
					),
					Message::get( 'smw-noscript', Message::PARSE, Message::USER_LANGUAGE )
				)
			)
		);
	}

	/**
	 * @since 3.0
	 *
	 * @return string
	 */
	public static function sessionFailure() {
		return Html::rawElement(
			'div',
			[
				'class' => 'smw-callout smw-callout-error'
			],
			Message::get( 'sessionfailure', Message::TEXT, Message::USER_LANGUAGE )
		);
	}

	/**
	 * @since 2.5
	 *
	 * @param Query|null $query
	 *
	 * @return string
	 */
	public static function queryError( Query $query = null ) {

		if ( $query === null || !is_array( $query->getErrors() ) || $query->getErrors() === array() ) {
			return '';
		}

		$errors = array();

		foreach ( ProcessingErrorMsgHandler::normalizeAndDecodeMessages( $query->getErrors() ) as $value ) {

			if ( $value === '' ) {
				continue;
			}

			if ( is_array( $value ) ) {
				$value = implode( " ", $value );
			}

			$errors[] = $value;
		}

		if ( count( $errors ) > 1 ) {
			$error = '<ul><li>' . implode( '</li><li>', $errors ) . '</li></ul>';
		} else {
			$error =  implode( ' ', $errors );
		}

		return Html::rawElement(
			'div',
			[
				'id'    => 'result-error',
				'class' => 'smw-callout smw-callout-error'
			],
			$error
		);
	}

}

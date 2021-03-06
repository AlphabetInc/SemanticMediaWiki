<?php

namespace SMW;

use SMWDataItem as DataItem;
use SMW\DataValues\ImportValue;
use SMW\DataValues\PropertyChainValue;
use SMW\DataValues\TelephoneUriValue;
use SMW\DataValues\TemperatureValue;
use SMW\DataValues\MonolingualTextValue;
use SMW\DataValues\ReferenceValue;
use SMW\DataValues\ExternalIdentifierValue;
use SMW\DataValues\LanguageCodeValue;
use SMW\DataValues\AllowsValue;
use SMW\DataValues\AllowsListValue;
use SMW\DataValues\AllowsPatternValue;
use SMW\DataValues\UniquenessConstraintValue;
use SMW\DataValues\ExternalFormatterUriValue;
use SMW\DataValues\ErrorMsgTextValue;
use SMW\DataValues\BooleanValue;
use SMWPropertyValue as PropertyValue;
use SMWStringValue as StringValue;
use SMWQuantityValue as QuantityValue;
use SMWNumberValue as NumberValue;
use SMWTimeValue as TimeValue;

/**
 * @license GNU GPL v2+
 * @since 3.0
 *
 * @author mwjames
 */
class DefaultList {

	/**
	 * @note All IDs must start with an underscore, two underscores indicate a
	 * truly internal (non user-interacted type). All others should also get a
	 * translation in the language files, or they won't be available for users.
	 *
	 * @since 2.5
	 *
	 * @return array
	 */
	public static function getTypeList() {
		return [

			// ID => Class, DI type, isSubDataType

			// Special import vocabulary type
			ImportValue::TYPE_ID => [ ImportValue::class, DataItem::TYPE_BLOB, false ],
			// Property chain
			PropertyChainValue::TYPE_ID => [ PropertyChainValue::class, DataItem::TYPE_BLOB, false ],
			// Property type (possibly predefined, not always based on a page)
			PropertyValue::TYPE_ID => [ PropertyValue::class, DataItem::TYPE_PROPERTY, false ],
			 // Text type
			StringValue::TYPE_ID => [ StringValue::class, DataItem::TYPE_BLOB, false ],
			 // Code type
			StringValue::TYPE_COD_ID => [ StringValue::class, DataItem::TYPE_BLOB, false ],
			 // Legacy string ID `_str`
			StringValue::TYPE_LEGACY_ID => [ StringValue::class, DataItem::TYPE_BLOB, false ],
			 // Email type
			'_ema' => [ 'SMWURIValue', DataItem::TYPE_URI, false ],
			 // URL/URI type
			'_uri' => [ 'SMWURIValue', DataItem::TYPE_URI, false ],
			 // Annotation URI type
			'_anu' => [ 'SMWURIValue', DataItem::TYPE_URI, false ],
			 // Phone number (URI) type
			'_tel' => [ TelephoneUriValue::class, DataItem::TYPE_URI, false ],
			 // Page type
			'_wpg' => [ 'SMWWikiPageValue', DataItem::TYPE_WIKIPAGE, false ],
			 // Property page type TODO: make available to user space
			'_wpp' => [ 'SMWWikiPageValue', DataItem::TYPE_WIKIPAGE, false ],
			 // Category page type TODO: make available to user space
			'_wpc' => [ 'SMWWikiPageValue', DataItem::TYPE_WIKIPAGE, false ],
			 // Form page type for Semantic Forms
			'_wpf' => [ 'SMWWikiPageValue', DataItem::TYPE_WIKIPAGE, false ],
			 // Number type
			NumberValue::TYPE_ID => [ NumberValue::class, DataItem::TYPE_NUMBER, false ],
			 // Temperature type
			TemperatureValue::TYPE_ID => [ TemperatureValue::class, DataItem::TYPE_NUMBER, false ],
			 // Time type
			TimeValue::TYPE_ID => [ TimeValue::class, DataItem::TYPE_TIME, false ],
			 // Boolean type
			'_boo' => [ BooleanValue::class, DataItem::TYPE_BOOLEAN, false ],
			 // Value list type (replacing former nary properties)
			'_rec' => [ 'SMWRecordValue', DataItem::TYPE_WIKIPAGE, true ],
			MonolingualTextValue::TYPE_ID => [ MonolingualTextValue::class, DataItem::TYPE_WIKIPAGE, true ],
			ReferenceValue::TYPE_ID => [ ReferenceValue::class, DataItem::TYPE_WIKIPAGE, true ],
			 // Geographical coordinates
			'_geo' => [ null, DataItem::TYPE_GEO, false ],
			 // Geographical polygon
			'_gpo' => [ null, DataItem::TYPE_BLOB, false ],
			// External identifier
			ExternalIdentifierValue::TYPE_ID => [ ExternalIdentifierValue::class, DataItem::TYPE_BLOB, false ],
			 // Type for numbers with units of measurement
			QuantityValue::TYPE_ID => [ QuantityValue::class, DataItem::TYPE_NUMBER, false ],
			// Special types are not avaialble directly for users (and have no local language name):
			// Special type page type
			'__typ' => [ 'SMWTypesValue', DataItem::TYPE_URI, false ],
			// Special type list for decalring _rec properties
			'__pls' => [ 'SMWPropertyListValue', DataItem::TYPE_BLOB, false ],
			// Special concept page type
			'__con' => [ 'SMWConceptValue', DataItem::TYPE_CONCEPT, false ],
			// Special string type
			'__sps' => [ 'SMWStringValue', DataItem::TYPE_BLOB, false ],
			// Special uri type
			'__spu' => [ 'SMWURIValue', DataItem::TYPE_URI, false ],
			// Special subobject type
			'__sob' => [ 'SMWWikiPageValue', DataItem::TYPE_WIKIPAGE, true ],
			// Special subproperty type
			'__sup' => [ 'SMWWikiPageValue', DataItem::TYPE_WIKIPAGE, false ],
			// Special subcategory type
			'__suc' => [ 'SMWWikiPageValue', DataItem::TYPE_WIKIPAGE, false ],
			// Special Form page type for Semantic Forms
			'__spf' => [ 'SMWWikiPageValue', DataItem::TYPE_WIKIPAGE, false ],
			// Special instance of type
			'__sin' => [ 'SMWWikiPageValue', DataItem::TYPE_WIKIPAGE, false ],
			// Special redirect type
			'__red' => [ 'SMWWikiPageValue', DataItem::TYPE_WIKIPAGE, false ],
			// Special error type
			'__err' => [ 'SMWErrorValue', DataItem::TYPE_ERROR, false ],
			// Special error type
			'__errt' => [ ErrorMsgTextValue::class, DataItem::TYPE_BLOB, false ],
			// Sort key of a page
			'__key' => [ 'SMWStringValue', DataItem::TYPE_BLOB, false ],
			LanguageCodeValue::TYPE_ID => [ LanguageCodeValue::class, DataItem::TYPE_BLOB, false ],
			AllowsValue::TYPE_ID => [ AllowsValue::class, DataItem::TYPE_BLOB, false ],
			AllowsListValue::TYPE_ID => [ AllowsListValue::class, DataItem::TYPE_BLOB, false ],
			AllowsPatternValue::TYPE_ID => [ AllowsPatternValue::class, DataItem::TYPE_BLOB, false ],
			'__pvuc' => [ UniquenessConstraintValue::class, DataItem::TYPE_BOOLEAN, false ],
			'__pefu' => [ ExternalFormatterUriValue::class, DataItem::TYPE_URI, false ]
		];
	}

	/**
	 * @note All ids must start with underscores. The translation for each ID,
	 * if any, is defined in the language files. Properties without translation
	 * cannot be entered by or displayed to users, whatever their "show" value
	 * below.
	 *
	 * @since 3.0
	 *
	 * @param boolean $useCategoryHierarchy
	 *
	 * @return  array
	 */
	public static function getPropertyList( $useCategoryHierarchy = true ) {
		return [

			// ID => [ valueType, isVisible, isAnnotable, isDeclarative ]

			'_TYPE' => [ '__typ', true, true, true ], // "has type"
			'_URI'  => [ '__spu', true, true, true ], // "equivalent URI"
			'_INST' => [ '__sin', false, true, false ], // instance of a category
			'_UNIT' => [ '__sps', true, true, true ], // "displays unit"
			'_IMPO' => [ '__imp', true, true, true ], // "imported from"
			'_CONV' => [ '__sps', true, true, true ], // "corresponds to"
			'_SERV' => [ '__sps', true, true, true ], // "provides service"
			'_PVAL' => [ '__pval', true, true, true ], // "allows value"
			'_REDI' => [ '__red', true, true, false ], // redirects to some page
			'_SUBP' => [ '__sup', true, true, true ], // "subproperty of"
			'_SUBC' => [ '__suc', !$useCategoryHierarchy, true, true ], // "subcategory of"
			'_CONC' => [ '__con', false, true, false ], // associated concept
			'_MDAT' => [ '_dat', false, false, false ], // "modification date"
			'_CDAT' => [ '_dat', false, false, false ], // "creation date"
			'_NEWP' => [ '_boo', false, false, false ], // "is a new page"
			'_EDIP' => [ '_boo', true, true, false ], // "is edit protected"
			'_LEDT' => [ '_wpg', false, false, false ], // "last editor is"
			'_ERRC' => [ '__sob', false, false, false ], // "has error"
			'_ERRT' => [ '__errt', false, false, false ], // "has error text"
			'_ERRP' => [ '_wpp', false, false, false ], // "has improper value for"
			'_LIST' => [ '__pls', true, true, true ], // "has fields"
			'_SKEY' => [ '__key', false, true, false ], // sort key of a page

			// FIXME SF related properties to be removed with 3.0
			'_SF_DF' => [ '__spf', true, true, false ], // Semantic Form's default form property
			'_SF_AF' => [ '__spf', true, true, false ],  // Semantic Form's alternate form property

			'_SOBJ' => [ '__sob', true, false, false ], // "has subobject"
			'_ASK'  => [ '__sob', false, false, false ], // "has query"
			'_ASKST' => [ '_cod', true, false, false ], // "Query string"
			'_ASKFO' => [ '_txt', true, false, false ], // "Query format"
			'_ASKSI' => [ '_num', true, false, false ], // "Query size"
			'_ASKDE' => [ '_num', true, false, false ], // "Query depth"
			'_ASKDU' => [ '_num', true, false, false ], // "Query duration"
			'_ASKSC' => [ '_txt', true, false, false ], // "Query source"
			'_ASKPA' => [ '_cod', true, false, false ], // "Query parameters"
			'_ASKCO' => [ '_num', true, false, false ], // "Query scode"
			'_MEDIA' => [ '_txt', true, false, false ], // "has media type"
			'_MIME' => [ '_txt', true, false, false ], // "has mime type"
			'_PREC' => [ '_num', true, true, true ], // "Display precision of"
			'_LCODE' => [ '__lcode', true, true, false ], // "Language code"
			'_TEXT' => [ '_txt', true, true, false ], // "Text"
			'_PDESC' => [ '_mlt_rec', true, true, true ], // "Property description"
			'_PVAP' => [ '__pvap', true, true, true ], // "Allows pattern"
			'_PVALI' => [ '__pvali', true, true, true ], // "Allows value list"
			'_DTITLE' => [ '_txt', false, true, false ], // "Display title of"
			'_PVUC' => [ '__pvuc', true, true, true ], // Uniqueness constraint
			'_PEID' => [ '_eid', true, true, false ], // External identifier
			'_PEFU' => [ '__pefu', true, true, true ], // External formatter uri
			'_PPLB' => [ '_mlt_rec', true, true, true ], // Preferred property label
			'_CHGPRO' => [ '_cod', true, false, true ], // "Change propagation"
			'_PPGR' => [ '_boo', true, true, true ], // "Property group"
		];
	}

}

<?php
/***************************************************************
* Copyright notice
*
* (c) 2009-2011 Niels Pardon (mail@niels-pardon.de)
* All rights reserved
*
* This script is part of the TYPO3 project. The TYPO3 project is
* free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* The GNU General Public License can be found at
* http://www.gnu.org/copyleft/gpl.html.
*
* This script is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * Class 'tx_oelib_Translator' for the 'oelib' extension.
 *
 * This class returns localized labels in the given languages.
 *
 * @package TYPO3
 * @subpackage tx_oelib
 *
 * @author Niels Pardon <mail@niels-pardon.de>
 */
class tx_oelib_Translator {
	/**
	 * @var string the key of the language to load the translations for
	 */
	private $languageKey;

	/**
	 * @var string the key of the alternative language to load the translations
	 *             for
	 */
	private $alternativeLanguageKey;

	/**
	 * @var array holds the localized labels in a nested associative array:
	 *            'languageKey' => array('labelkey' => 'label')
	 */
	private $localizedLabels;

	/**
	 * The constructor.
	 *
	 * @param string the key of the language to load the translations for, may
	 *               be empty
	 * @param string the key of the alternative language to load the translations
	 *               for, may be empty
	 * @param array the localized labels in a nested associative array:
	 *				'languageKey' => array('labelkey' => 'label'),
	 *              may be empty
	 */
	public function __construct($languageKey, $alternativeLanguageKey, array $localizedLabels) {
		$this->languageKey = $languageKey;
		$this->alternativeLanguageKey = $alternativeLanguageKey;
		$this->localizedLabels = $localizedLabels;
	}

	/**
	 * Frees as much memory that has been used by this object as possible.
	 */
	public function __destruct() {
		unset($this->localizedLabels);
	}

	/**
	 * Returns the localized label for the key $key.
	 *
	 * @param string the key of the label to get the localization for, must not
	 *               be empty
	 * @param boolean whether the localized label should be processes with
	 *                htmlspecialchars prior to returning it
	 *
	 * @return string the localized label, might be empty
	 */
	public function translate($key, $useHtmlSpecialChars = FALSE) {
		if ($key == '') {
			throw new Exception('The parameter $key must not be empty.');
		}

		if (isset($this->localizedLabels[$this->languageKey][$key])) {
			$translation = $this->localizedLabels[$this->languageKey][$key];
		} elseif (
			($this->alternativeLanguageKey != '')
			&& isset($this->localizedLabels[$this->alternativeLanguageKey][$key])
		) {
			$translation = $this->localizedLabels[$this->alternativeLanguageKey][$key];
		} elseif (isset($this->localizedLabels['default'][$key])) {
			$translation = $this->localizedLabels['default'][$key];
		} else {
			$translation = $key;
		}

		return ($useHtmlSpecialChars)
			? htmlspecialchars($translation)
			: $translation;
	}

	/**
	 * Returns the language key in $this->languageKey.
	 *
	 * Note: This method is meant for testing purposes.
	 *
	 * @return string the language key in $this->languageKey, may be empty
	 */
	public function getLanguageKey() {
		return $this->languageKey;
	}

	/**
	 * Returns the alternative language key in $this->alternativeLanguageKey.
	 *
	 * Note: This method is meant for testing purposes.
	 *
	 * @return string the alternative language key in
	 *                $this->alternativeLanguageKey, may be empty
	 */
	public function getAlternativeLanguageKey() {
		return $this->alternativeLanguageKey;
	}
}

if (defined('TYPO3_MODE') && $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/oelib/class.tx_oelib_Translator.php']) {
	include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/oelib/class.tx_oelib_Translator.php']);
}
?>
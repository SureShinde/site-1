<?php
// 2020-03-02 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
// The patch for `FishPig_WordPress` has been added by someone at 2020-01-28.
/*
 * This file extends the default Magento translation function so that it works with WordPress too
 * This doesn't cause any change in behaviour in Magento.
 * The only change is that when called in WordPress, the return value is always a string.
 * In Magento the return value is a \Magento\Framework\Phrase object
 */
declare(strict_types=1);
if (!function_exists('__')) {
	function __() {
		$argc = func_get_args();
		$text = array_shift($argc);
		if (!empty($argc) && is_array($argc[0])) {
			$argc = $argc[0];
		}
		// 2020-03-02 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
		// This patch has been added by someone at 2019-02-27.
		if (isset($GLOBALS['phrase_as_string'])) {
			return (string)new \Magento\Framework\Phrase($text, $argc);
		}
		return new \Magento\Framework\Phrase($text, $argc);
	}
	if (class_exists('\FishPig\WordPress\Model\Integration\CoreTest')) {
		FishPig\WordPress\Model\Integration\CoreTest::setMagentoTranslationPatchIsApplied(true);
	}
}
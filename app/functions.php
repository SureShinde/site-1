<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Create value-object \Magento\Framework\Phrase
 *
 * @return \Magento\Framework\Phrase
 */
function __()
{
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

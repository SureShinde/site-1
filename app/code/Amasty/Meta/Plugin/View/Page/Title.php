<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Meta
 */

namespace Amasty\Meta\Plugin\View\Page;

class Title
{


    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;
    /**
     * @var \Amasty\Meta\Helper\Data
     */
    private $data;

    public function __construct(
        \Amasty\Meta\Helper\Data $data,
        \Magento\Framework\App\Config\ScopeConfigInterface $configInterface
    ) {
        $this->_scopeConfig = $configInterface;
        $this->data = $data;
    }
    public function afterGet(
        \Magento\Framework\View\Page\Title $config,
        $title
    ) {
        $prefix = $this->_scopeConfig->getValue(
            'design/head/title_prefix',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $suffix = $this->_scopeConfig->getValue(
            'design/head/title_suffix',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $replacedMetaKeywords = $this->data->getReplaceData('meta_title'); // $replaced['meta_title'];
        if ($replacedMetaKeywords) {
            $title = $prefix.$replacedMetaKeywords.$suffix;
        }
        return $title;

    }
}
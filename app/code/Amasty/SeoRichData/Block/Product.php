<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_SeoRichData
 */

namespace Amasty\SeoRichData\Block;

use Magento\Framework\DataObject;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Store\Model\ScopeInterface;
use Amasty\SeoRichData\Model\Source\Product\Description as DescriptionSource;

class Product extends AbstractBlock
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;
    /**
     * @var \Magento\Framework\View\Page\Config
     */
    protected $pageConfig;

    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Page\Config $pageConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->coreRegistry = $coreRegistry;
        $this->pageConfig = $pageConfig;
    }

    protected function prepareData()
    {
        if (!$this->_scopeConfig->isSetFlag(
            'amseorichdata/product/enabled', ScopeInterface::SCOPE_STORE
        )) {
            return [];
        }

        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->coreRegistry->registry('current_product');

        if (!$product)
            return [];

        $data = [];

        $descriptionMode = $this->_scopeConfig->getValue(
            'amseorichdata/product/description', ScopeInterface::SCOPE_STORE
        );

        switch ($descriptionMode) {
            case DescriptionSource::SHORT_DESCRIPTION:
                $data['description'] = $product->getShortDescription();
                break;
            case DescriptionSource::FULL_DESCRIPTION:
                $data['description'] = $product->getDescription();
                break;
            case DescriptionSource::META_DESCRIPTION:
                $data['description'] = $this->pageConfig->getDescription();
                break;
        }

        $images = $product->getMediaGalleryImages();
        if ($images instanceof \Magento\Framework\Data\Collection) {
            /** @var DataObject $image */
            foreach ($images as $image) {
                if ($product->getImage() == $image->getData('file')) {
                    $data['image'] = $image->getData('url');
                    break;
                }
            }
        }

        return $data;
    }

    protected function _toHtml()
    {
        $data = $this->prepareData();

        $result = '';
        foreach ($data as $name => $value) {
            $value = $this->escapeHtml($this->stripTags($value));
            $result .= "\n<meta itemprop=\"$name\" content=\"$value\">";
        }

        return $result;
    }
}

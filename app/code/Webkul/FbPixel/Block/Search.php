<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_FbPixel
 * @author    Webkul
 * @copyright Copyright (c) 2010-2018 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\FbPixel\Block;

use Magento\CatalogSearch\Helper\Data;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Search extends Template
{
    /**
     * Catalog search data
     *
     * @var Data
     */
    protected $catalogSearchData;

    /**
     * @param Context $context
     * @param Data $catalogSearchData
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $catalogSearchData,
        array $data = []
    ) {
        $this->catalogSearchData = $catalogSearchData;
        parent::__construct($context, $data);
    }

    public function getSearchQuery() {
        return $this->catalogSearchData->getEscapedQueryText();
    }
}
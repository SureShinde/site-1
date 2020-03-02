<?php
/**
 * Webkul FbPixel SalesQuoteAddItem Observer.
 * @category  Webkul
 * @package   Webkul_FbPixel
 * @author    Webkul
 * @copyright Copyright (c) 2010-2018 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\FbPixel\Observer;

use Magento\Framework\Event\ObserverInterface;

class SalesQuoteAddItem implements ObserverInterface
{
    /**
     * @var \Webkul\Auction\Helper\Data
     */
    protected $_dataHelper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    protected $productRepository;

    /**
     * @param \Webkul\FbPixel\Helper\Data $dataHelper
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magentp\Catalog\Api\ProductRepositoryInterface $productRepository
     */
    public function __construct(
        \Webkul\FbPixel\Helper\Data $dataHelper,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
    ) {
        $this->_dataHelper = $dataHelper;
        $this->_checkoutSession = $checkoutSession;
        $this->productRepository = $productRepository;
    }

    /**
     * Sales quote add item event handler.
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $item = $observer->getEvent()->getData('quote_item');
        $product = $this->productRepository->getById($item->getProductId());
        
        $addTocartData = [
                          'value' => $item->getProduct()->getPrice(),
                          'currency' => $this->_dataHelper->getCurrentCurrencyCode(),
                          'content_name' => $item->getName(),
                          'content_type' => 'product',
                          'content_ids' => $product->getSku()
                          ];
        $this->_checkoutSession->setPixelAddToCartData($addTocartData);
    }
}

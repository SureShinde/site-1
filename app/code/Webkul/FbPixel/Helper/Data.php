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
namespace Webkul\FbPixel\Helper;

use Magento\TestFramework\ErrorLog\Logger;

/**
 * Webkul FbPixel Helper Data.
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_order;

    protected $_checkoutSession;

    protected $_customerSession;

    protected $_storeManager;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Sales\Api\Data\OrderInterface $order,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig          = $context->getScopeConfig();
        $this->_customerSession     = $customerSession;
        $this->_order               = $order;
        $this->_checkoutSession     = $checkoutSession;
        $this->_storeManager        = $storeManager;
        parent::__construct($context);
    }

    public function getStatus() {
        return $this->scopeConfig->getValue(
            'wk_fbpixel/general_settings/enable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * getPixelId function
     *
     * @return id
     */
    public function getPixelId() {
        if ($this->getStatus()) {
            return $this->scopeConfig->getValue(
                'wk_fbpixel/general_settings/pixelid',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        } else {
            return 0;
        }
    }

    public function getConfigValue($field) {
        $string = 'wk_fbpixel/events/'.$field;
        return $this->scopeConfig->getValue(
            $string,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getCustomer() {
        return $this->_customerSession->getCustomer();
    }

    public function arryToContentIdString($a) {
        return implode(',', array_map(function ($i) { return '"'.$i.'"'; }, $a));
    }

    public function getOrderDetails() {
        $orderid = $this->_checkoutSession->getLastOrderId();
        $order = $this->_order->load($orderid);
        $total = $order->getBaseGrandTotal();
        $items = $order->getAllVisibleItems();
        $currency = $order->getBaseCurrencyCode();
        $sku = [];
        foreach ($items as $item) {
            $sku[] = $item->getSku();
        }
        $product_sku = $this->arryToContentIdString($sku);
        return [
                    'product_sku'=> $product_sku,
                    'total' => $total,
                    'currency' => $currency
                ];
    }

    public function isCustomerRegister() {
        $customer = $this->_customerSession->getWkFbpixelCustomer();
        if (is_array($this->_customerSession->getWkFbpixelCustomer())) {
            $this->_customerSession->setWkFbpixelCustomer(NULL);
        }
        return $customer;
    }

    public function getStore() {
        return $this->_storeManager->getStore();
    }

    public function addTocartData() {
      return $this->_checkoutSession->getPixelAddToCartData();
    }

    public function unSetAddToCartData() {
      return $this->_checkoutSession->unsPixelAddToCartData();
    }

    // return currency currency code
    public function getCurrentCurrencyCode()
    {
        return $this->_storeManager->getStore()->getCurrentCurrencyCode();
    }

}

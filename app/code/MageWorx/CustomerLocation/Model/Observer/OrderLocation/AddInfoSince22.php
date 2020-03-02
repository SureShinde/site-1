<?php
/**
 * Copyright © 2017 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\CustomerLocation\Model\Observer\OrderLocation;

/**
 * Customer Location observer
 */
class AddInfoSince22 implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \MageWorx\CustomerLocation\Helper\Data
     */
    protected $helperData;

    /**
     * @var \MageWorx\CustomerLocation\Helper\Html
     */
    protected $helperHtml;

    /**
     * @var \MageWorx\GeoIP\Model\Geoip
     */
    protected $modelGeoip;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    /**
     * AddInfoSince22 constructor.
     * @param \MageWorx\CustomerLocation\Helper\Data $helperData
     * @param \MageWorx\CustomerLocation\Helper\Html $helperHtml
     * @param \MageWorx\GeoIP\Model\Geoip $modelGeoip
     * @param \Magento\Framework\UrlInterface $url
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
     */
    public function __construct(
        \MageWorx\CustomerLocation\Helper\Data $helperData,
        \MageWorx\CustomerLocation\Helper\Html $helperHtml,
        \MageWorx\GeoIP\Model\Geoip $modelGeoip,
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata
    ) {

        $this->helperData = $helperData;
        $this->helperHtml = $helperHtml;
        $this->modelGeoip = $modelGeoip;
        $this->url = $url;
        $this->productMetadata = $productMetadata;
    }

    /**
     * Adds GeoIP location html to order view
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return \MageWorx\CustomerLocation\Model\Observer\OrderLocation
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->helperData->isEnabledForOrders() && version_compare($this->productMetadata->getVersion(), '2.2.0', '>=')) {
            $_order = null;
            $block = $observer->getEvent()->getBlock();
            $currentUrl = $this->url->getCurrentUrl();

            if ($block instanceof \Magento\Sales\Block\Adminhtml\Order\View\Info && strpos($currentUrl, '/sales/order/view/') !== false) {
                $_order = $block->getOrder();
            } elseif ($block instanceof \Magento\Shipping\Block\Adminhtml\Create\Form && strpos($currentUrl, 'admin/order_shipment/new/') !== false) {
                $_order = $block->getOrder();
            } elseif ($block instanceof \Magento\Sales\Block\Adminhtml\Order\Invoice\Create\Form && strpos($currentUrl, 'sales/order_invoice/new') !== false) {
                $_order = $block->getOrder();
            }

            if ($_order !== null) {

                $ip = $_order->getRemoteIp();

                if (!$ip) {
                    return $this;
                }

                $ipInfoString = $block->escapeHtml($_order->getRemoteIp());
                $xforwardedfor = $_order->getXForwardedFor();

                if ($xforwardedfor) {
                    $ipInfoString .= ' (' . $block->escapeHtml($_order->getXForwardedFor()) . ')';
                }

                $transport = $observer->getTransport();
                $html = $transport->getHtml();

                $geoIpObj = $this->modelGeoip->getLocation($ip);

                if ($geoIpObj->getCode()) {
                    $data = [
                        'geo_ip' => $geoIpObj,
                        'ip' => $ip,
                        'xforwarderfor' => $xforwardedfor
                    ];
                    $obj = new \Magento\Framework\DataObject($data);
                    $locationHtml = $this->helperHtml->getGeoIpHtml($obj);

                    $html = str_replace($ipInfoString, $locationHtml, $html);
                    $transport->setHtml($html);
                }
            }
        }
        return $this;
    }
}

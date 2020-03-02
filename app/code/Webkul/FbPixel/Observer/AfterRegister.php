<?php

namespace Webkul\FbPixel\Observer;

use Magento\Framework\Event\ObserverInterface;

class AfterRegister implements ObserverInterface
{
    protected $_customerSession;

    public function __construct(
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->_customerSession = $customerSession;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $customer = $observer->getEvent()->getCustomer();
        $data = ['email'=> $customer->getEmail()];
        $this->_customerSession->setWkFbpixelCustomer($data);
    }
}
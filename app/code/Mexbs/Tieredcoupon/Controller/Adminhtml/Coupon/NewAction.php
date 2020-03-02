<?php
namespace Mexbs\Tieredcoupon\Controller\Adminhtml\Coupon;

class NewAction extends \Magento\Backend\App\Action
{
    /**
     * New Tiered coupon action
     *
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}

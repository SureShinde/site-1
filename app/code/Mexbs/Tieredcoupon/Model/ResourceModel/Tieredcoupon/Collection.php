<?php
namespace Mexbs\Tieredcoupon\Model\ResourceModel\Tieredcoupon;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Constructor
     * Configures collection
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Mexbs\Tieredcoupon\Model\Tieredcoupon', 'Mexbs\Tieredcoupon\Model\ResourceModel\Tieredcoupon');
    }

    public function joinSubCoupons(){
        $select = $this->getSelect();
        $select->joinInner(
            'mexbs_tieredcoupon_coupon'
        );
    }
}

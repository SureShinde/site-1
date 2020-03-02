<?php
namespace Mexbs\Tieredcoupon\Model\Plugin;

class AppendCodes
{
    /**
     * @var \Mexbs\Tieredcoupon\Model\Tieredcoupon
     */
    protected $_tieredCoupon;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_date;

    /**
     * @param \Mexbs\Tieredcoupon\Model\Tieredcoupon $ruleResource
     */
    public function __construct(
        \Mexbs\Tieredcoupon\Model\Tieredcoupon $tieredCoupon,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date
    )
    {
        $this->_tieredCoupon = $tieredCoupon;
        $this->_date = $date;
    }

    /**
     * if the current coupon code is a tier, replace the where with its sub coupons
     */
    public function aroundSetValidationFilter(
        \Magento\SalesRule\Model\ResourceModel\Rule\Collection $subject,
        \Closure $proceed,
        $websiteId,
        $customerGroupId,
        $couponCode = '',
        $now = null,
        \Magento\Quote\Model\Quote\Address $address = null
        )
    {
        $tieredcoupon = $this->_tieredCoupon->load($couponCode, 'code');

        $result = $proceed(
            $websiteId,
            $customerGroupId,
            $couponCode,
            $now,
            $address
        );

        if($tieredcoupon->getId()){
            $subCouponCodes = $tieredcoupon->getSubCouponCodes();


            $select = $subject->getSelect();
            $select->reset('where');

            $connection = $subject->getConnection();
                $noCouponWhereCondition = $connection->quoteInto(
                    'main_table.coupon_type = ? ',
                    \Magento\SalesRule\Model\Rule::COUPON_TYPE_NO_COUPON
                );

            $orWhereConditions = [
                $connection->quoteInto(
                    '(main_table.coupon_type = ? AND rule_coupons.type = 0)',
                    \Magento\SalesRule\Model\Rule::COUPON_TYPE_AUTO
                ),
                $connection->quoteInto(
                    '(main_table.coupon_type = ? AND main_table.use_auto_generation = 1 AND rule_coupons.type = 1)',
                    \Magento\SalesRule\Model\Rule::COUPON_TYPE_SPECIFIC
                ),
                $connection->quoteInto(
                    '(main_table.coupon_type = ? AND main_table.use_auto_generation = 0 AND rule_coupons.type = 0)',
                    \Magento\SalesRule\Model\Rule::COUPON_TYPE_SPECIFIC
                ),
            ];

            $andWhereConditions = [
                $connection->quoteInto(
                    'rule_coupons.code in (?)',
                    array_values($subCouponCodes)
                ),
                $connection->quoteInto(
                    '(rule_coupons.expiration_date IS NULL OR rule_coupons.expiration_date >= ?)',
                    $this->_date->date()->format('Y-m-d')
                ),
            ];

            $orWhereCondition = implode(' OR ', $orWhereConditions);
            $andWhereCondition = implode(' AND ', $andWhereConditions);

            $select->where(
                $noCouponWhereCondition . ' OR ((' . $orWhereCondition . ') AND ' . $andWhereCondition . ')'
            );
        }

        return $result;
    }
}

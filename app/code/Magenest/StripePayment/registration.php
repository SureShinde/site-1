<?php
/**
 * Created by Magenest JSC.
 * Author: Jacob
 * Date: 10/01/2019
 * Time: 9:41
 */
\Magento\Framework\Component\ComponentRegistrar::register(
    \Magento\Framework\Component\ComponentRegistrar::MODULE,
    'Magenest_StripePayment',
    __DIR__
);
if (!class_exists('Stripe\Stripe')) {
    @include_once('stripe-php/init.php');
}
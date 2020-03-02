<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Store\Url\Plugin;

use \Magento\Store\Model\Store;
use \Magento\Store\Model\ScopeInterface as StoreScopeInterface;

/**
 * Plugin for \Magento\Framework\Url\RouteParamsResolver
 */
class RouteParamsResolver
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Url\QueryParamsResolverInterface
     */
    protected $queryParamsResolver;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Url\QueryParamsResolverInterface $queryParamsResolver
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Url\QueryParamsResolverInterface $queryParamsResolver
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->queryParamsResolver = $queryParamsResolver;
    }

    /**
     * Process scope query parameters.
     *
     * @param \Magento\Framework\Url\RouteParamsResolver $subject
     * @param array $data
     * @param bool $unsetOldParams
     * @return array
     */
    public function beforeSetRouteParams(
        \Magento\Framework\Url\RouteParamsResolver $subject,
        array $data,
        $unsetOldParams = true
    ) {
        if (isset($data['_scope'])) {
            $subject->setScope($data['_scope']);
            unset($data['_scope']);
        }
        if (isset($data['_scope_to_url']) && (bool)$data['_scope_to_url'] === true) {
            $storeCode = $subject->getScope() ?: $this->storeManager->getStore()->getCode();
			/**
			 * 2018-08-17 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
			 * «For dev site when I go to dev2.inkifi.com I get re-directedto : https://dev2.inkifi.com/uk/
			 * Even though in admin it says the base URL is only dev2.inkifi.com
			 * I need the URL for the UK store to be only dev2.inkifi.com, did you change this?
			 * For the USA the URL is correct now - dev2.inkifi.com/us/»
			 * https://www.upwork.com/messages/rooms/room_22a80485ffe6bb541dae55cc66b85198/story_453f4ab09ce06a14327346d0161b5717
			 */
            $useStoreInUrl = 'uk' !== $storeCode && $this->scopeConfig->getValue(
                Store::XML_PATH_STORE_IN_URL,
                StoreScopeInterface::SCOPE_STORE,
                $storeCode
            );
            if (!$useStoreInUrl && !$this->storeManager->hasSingleStore()) {
                $this->queryParamsResolver->setQueryParam('___store', $storeCode);
            }
        }
        unset($data['_scope_to_url']);

        return [$data, $unsetOldParams];
    }
}

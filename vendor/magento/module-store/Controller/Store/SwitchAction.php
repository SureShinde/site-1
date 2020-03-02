<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Store\Controller\Store;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context as ActionContext;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Api\StoreCookieManagerInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreIsInactiveException;
use Magento\Store\Model\StoreResolver;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Switch current store view.
 */
class SwitchAction extends Action
{
    /**
     * @var StoreCookieManagerInterface
     */
    protected $storeCookieManager;

    /**
     * @var HttpContext
     */
    protected $httpContext;

    /**
     * @var StoreRepositoryInterface
     */
    protected $storeRepository;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Initialize dependencies.
     *
     * @param ActionContext $context
     * @param StoreCookieManagerInterface $storeCookieManager
     * @param HttpContext $httpContext
     * @param StoreRepositoryInterface $storeRepository
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ActionContext $context,
        StoreCookieManagerInterface $storeCookieManager,
        HttpContext $httpContext,
        StoreRepositoryInterface $storeRepository,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->storeCookieManager = $storeCookieManager;
        $this->httpContext = $httpContext;
        $this->storeRepository = $storeRepository;
        $this->storeManager = $storeManager;
    }

    /**
     * @return void
     */
    public function execute()
    {
        $currentActiveStore = $this->storeManager->getStore();
        $storeCode = $this->_request->getParam(
            StoreResolver::PARAM_NAME,
            $this->storeCookieManager->getStoreCodeFromCookie()
        );

        try {
            $store = $this->storeRepository->getActiveStoreByCode($storeCode);
        } catch (StoreIsInactiveException $e) {
            $error = __('Requested store is inactive');
        } catch (NoSuchEntityException $e) {
            $error = __('Requested store is not found');
        }

        if (isset($error)) {
            $this->messageManager->addError($error);
            $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl());
            return;
        }

        $defaultStoreView = $this->storeManager->getDefaultStoreView();
        if ($defaultStoreView->getId() == $store->getId()) {
            $this->storeCookieManager->deleteStoreCookie($store);
        } else {
            $this->httpContext->setValue(Store::ENTITY, $store->getCode(), $defaultStoreView->getCode());
            $this->storeCookieManager->setStoreCookie($store);
        }

		// 2019-03-02 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
		// «Remove the primary store's code from URLs»
		// https://www.upwork.com/ab/f/contracts/21683566
        if (true || $store->isUseStoreInUrl()) {
            // Change store code in redirect url
            if (strpos($this->_redirect->getRedirectUrl(), $currentActiveStore->getBaseUrl()) !== false) {
                $this->getResponse()->setRedirect(
                    str_replace(
                        $currentActiveStore->getBaseUrl(),
                        $store->getBaseUrl(),
                        $this->_redirect->getRedirectUrl()
                    )
                );
            } else {
                $this->getResponse()->setRedirect($store->getBaseUrl());
            }
        } else {
			// 2018-09-17 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
			// «Implement the store/currency switcher as in publicdesire.com»
			// https://github.com/inkifi/store/issues/1
        	$this->getResponse()->setRedirect($store->getBaseUrl());
            //$this->getResponse()->setRedirect($this->_redirect->getRedirectUrl());
        }
    }
}

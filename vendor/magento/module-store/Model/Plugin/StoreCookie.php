<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Store\Model\Plugin;

use Magento\Store\Api\StoreCookieManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\Model\StoreIsInactiveException;
use Magento\Framework\Exception\NoSuchEntityException;
use \InvalidArgumentException;
use Magento\Store\Api\StoreResolverInterface;
use Magento\Framework\App\ObjectManager;

/**
 * Class StoreCookie
 */
class StoreCookie
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var StoreCookieManagerInterface
     */
    protected $storeCookieManager;

    /**
     * @var StoreRepositoryInterface
     */
    protected $storeRepository;

    /**
     * @var StoreResolverInterface
     */
    private $storeResolver;

    /**
     * @param StoreManagerInterface $storeManager
     * @param StoreCookieManagerInterface $storeCookieManager
     * @param StoreRepositoryInterface $storeRepository
     * @param StoreResolverInterface $storeResolver
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        StoreCookieManagerInterface $storeCookieManager,
        StoreRepositoryInterface $storeRepository,
        StoreResolverInterface $storeResolver = null
    ) {
        $this->storeManager = $storeManager;
        $this->storeCookieManager = $storeCookieManager;
        $this->storeRepository = $storeRepository;
        $this->storeResolver = $storeResolver ?: ObjectManager::getInstance()->get(StoreResolverInterface::class);
    }

    /**
     * Delete cookie "store" if the store (a value in the cookie) does not exist or is inactive
     *
     * @param \Magento\Framework\App\FrontController $subject
     * @param \Magento\Framework\App\RequestInterface $request
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeDispatch(
        \Magento\Framework\App\FrontController $subject,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $storeCodeFromCookie = $this->storeCookieManager->getStoreCodeFromCookie();
		// 2019-01-18 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
		// "Fix a Mediaclip module issue in Magento 2: Undefined variable: product in quoteSaveBefore.php"
		// https://www.upwork.com/ab/f/contracts/21420844
		// https://www.upwork.com/messages/rooms/room_7788403d0ee0b953a47b704fee282e85/story_f4239e8699fd133ebb800f89b73d310a
		$uri = @$_SERVER['REQUEST_URI'];
		$needSendCookie = false;
		if ('/us' === $uri || 0 === strpos(@$_SERVER['REQUEST_URI'], '/us/')) {
			$needSendCookie = true;
			$storeCodeFromCookie = 'us';
			$_COOKIE['store'] = 'us';
		}
        if ($storeCodeFromCookie) {
            try {
                $this->storeRepository->getActiveStoreByCode($storeCodeFromCookie);
            } catch (StoreIsInactiveException $e) {
                $this->storeCookieManager->deleteStoreCookie($this->storeManager->getDefaultStoreView());
            } catch (NoSuchEntityException $e) {
                $this->storeCookieManager->deleteStoreCookie($this->storeManager->getDefaultStoreView());
            } catch (InvalidArgumentException $e) {
                $this->storeCookieManager->deleteStoreCookie($this->storeManager->getDefaultStoreView());
            }
        }
        if ($this->storeCookieManager->getStoreCodeFromCookie() === null
            || $request->getParam(StoreResolverInterface::PARAM_NAME) !== null
			// 2019-01-18 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
			// "Fix a Mediaclip module issue in Magento 2: Undefined variable: product in quoteSaveBefore.php"
			// https://www.upwork.com/ab/f/contracts/21420844
			// https://www.upwork.com/messages/rooms/room_7788403d0ee0b953a47b704fee282e85/story_f4239e8699fd133ebb800f89b73d310a
			|| $needSendCookie
        ) {
            $storeId = $this->storeResolver->getCurrentStoreId();
            $store = $this->storeRepository->getActiveStoreById($storeId);
            $this->storeCookieManager->setStoreCookie($store);
        }
    }
}

<?php
/******************************************************
 * @package Magento 2 Membership
 * @author http://www.magefox.com
 * @copyright (C) 2018 - Magefox.Com
 * @license MIT
 *******************************************************/

namespace Magefox\Membership\Model\Product\Type;

class Membership extends \Magento\Catalog\Model\Product\Type\AbstractType
{
    const TYPE_CODE = 'membership';

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magefox\Membership\Helper\Config
     */
    protected $configHelper;

    /**
     * Membership constructor.
     *
     * @param \Magento\Catalog\Model\Product\Option $catalogProductOption
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Catalog\Model\Product\Type $catalogProductType
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\MediaStorage\Helper\File\Storage\Database $fileStorageDb
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magefox\Membership\Helper\Config $configHelper
     */
    public function __construct(
        \Magento\Catalog\Model\Product\Option $catalogProductOption,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Catalog\Model\Product\Type $catalogProductType,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\MediaStorage\Helper\File\Storage\Database $fileStorageDb,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Registry $coreRegistry,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magefox\Membership\Helper\Config $configHelper
    ) {
        $this->customerSession = $customerSession;
        $this->checkoutSession = $checkoutSession;
        $this->configHelper = $configHelper;

        parent::__construct(
            $catalogProductOption,
            $eavConfig,
            $catalogProductType,
            $eventManager,
            $fileStorageDb,
            $filesystem,
            $coreRegistry,
            $logger,
            $productRepository
        );
    }

    /**
     * Check is virtual product
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return bool
     */
    // @codingStandardsIgnoreLine
    public function isVirtual($product)
    {
        return true;
    }

    /**
     * Check that product of this type has weight
     *
     * @return bool
     */
    public function hasWeight()
    {
        return false;
    }

    /**
     * Delete data specific for Membership product type
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return void
     */
    // @codingStandardsIgnoreLine
    public function deleteTypeSpecificData(\Magento\Catalog\Model\Product $product) {}

    /**
     * Ensure the customer is logged in when attempting to purchase a Membership.
     *
     * @param \Magento\Framework\DataObject $buyRequest
     * @param \Magento\Catalog\Model\Product $product
     * @param string $processMode
     * @return array|string
     */
    // @codingStandardsIgnoreLine
    protected function _prepareProduct(\Magento\Framework\DataObject $buyRequest, $product, $processMode)
    {
        // Don't allow the customer to purchase if functionality is disabled.
        if (!$this->configHelper->isEnabled()) {
            return __("Membership is currently disabled.");
        }

        // Only logged in users can add to cart.
        if ($this->_isStrictProcessMode($processMode) && !$this->customerSession->isLoggedIn()) {
            return __("You need to be logged in to purchase a membership.");
        }

        $quote = $this->checkoutSession->getQuote();
        $items = $quote->getAllItems();
        foreach ($items as $item) {
            /**
             * @var \Magento\Quote\Model\Quote\Item $item
             */
            if ($item->getProductType() == self::TYPE_CODE) {
                return __("Membership product existed in your cart, please remove and try again.");
            }
        }

        return parent::_prepareProduct($buyRequest, $product, $processMode);
    }
}

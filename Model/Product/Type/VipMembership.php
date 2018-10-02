<?php
/******************************************************
 * @package Magento 2 Membership
 * @author http://www.magefox.com
 * @copyright (C) 2018 - Magefox.Com
 * @license MIT
 *******************************************************/
namespace Magefox\Membership\Model\Product\Type;

use Magento\Catalog\Api\ProductRepositoryInterface,
    \Magento\Catalog\Model\Product\Option,
    \Magento\Eav\Model\Config,
    \Magento\Catalog\Model\Product\Type,
    \Magento\Framework\Event\ManagerInterface,
    \Magento\MediaStorage\Helper\File\Storage\Database,
    \Magento\Framework\Filesystem,
    \Magento\Framework\Registry,
    \Psr\Log\LoggerInterface,
    \Magento\Customer\Model\Session,
    \Magefox\Membership\Helper\Config as ConfigHelper;

class Membership extends \Magento\Catalog\Model\Product\Type\AbstractType
{
    const TYPE_CODE = 'vip';

    /** @var  \Magento\Customer\Model\Session */
    protected $customerSession;

    /** @var \Magefox\Membership\Helper\Config */
    protected $configHelper;

    public function __construct(
        Option $catalogProductOption,
        Config $eavConfig,
        Type $catalogProductType,
        ManagerInterface $eventManager,
        Database $fileStorageDb,
        Filesystem $filesystem,
        Registry $coreRegistry,
        LoggerInterface $logger,
        ProductRepositoryInterface $productRepository,
        Session $customerSession,
        ConfigHelper $configHelper)
    {
        $this->configHelper = $configHelper;
        $this->customerSession = $customerSession;
        parent::__construct($catalogProductOption, $eavConfig, $catalogProductType, $eventManager, $fileStorageDb, $filesystem, $coreRegistry, $logger, $productRepository);
    }

    /**
     * Check is virtual product
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return bool
     */
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
    public function deleteTypeSpecificData(\Magento\Catalog\Model\Product $product)
    {
    }

    /**
     * Ensure the customer is logged in when attempting to purchase a VIP Membership.
     *
     * @param \Magento\Framework\DataObject $buyRequest
     * @param \Magento\Catalog\Model\Product $product
     * @param string $processMode
     * @return array|string
     */
    protected function _prepareProduct(\Magento\Framework\DataObject $buyRequest, $product, $processMode)
    {
        // Don't allow the customer to purchase if functionality is disabled.
        if (!$this->configHelper->isEnabled()) {
            return __("VIP Membership is currently disabled.");
        }

        // Only logged in users can add to cart.
        if ($this->_isStrictProcessMode($processMode) && !$this->customerSession->isLoggedIn()) {
            return __("You need to be logged in to purchase a membership");
        }

        return parent::_prepareProduct($buyRequest, $product, $processMode);
    }
}

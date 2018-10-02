<?php
/******************************************************
 * @package Magento 2 Membership
 * @author http://www.magefox.com
 * @copyright (C) 2018 - Magefox.Com
 * @license MIT
 *******************************************************/
namespace Magefox\Membership\Plugin;

class ProductGet
{
    protected $productExtensionFactory;
    protected $productFactory;

    public function __construct(
        \Magento\Catalog\Api\Data\ProductExtensionFactory $productExtensionFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory
    )
    {
        $this->productFactory = $productFactory;
        $this->productExtensionFactory = $productExtensionFactory;
    }

    public function aroundGetById(
        \Magento\Catalog\Api\ProductRepositoryInterface $subject,
        \Closure $proceed,
        $customerId
    ) {
        /** @var \Magento\Customer\Api\Data\CustomerInterface $resultCustomer */
        $product = $proceed($customerId);

        $product = $this->addVipDataToProduct($product);

        return $product;
    }

    /**
     * Add vip data to customer
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @return \Magento\Catalog\Api\Data\ProductInterface
     */
    protected function addVipDataToProduct(\Magento\Catalog\Api\Data\ProductInterface $product)
    {
        if ($product->getExtensionAttributes() && $product->getExtensionAttributes()->getVipLength()) {
            return $product;
        }

        if (!$product->getExtensionAttributes()) {
            $productExtension = $this->productExtensionFactory->create();
            $product->setExtensionAttributes($productExtension);
        }

        $productModel = $this->productFactory->create()->load($product->getId());
        $product->getExtensionAttributes()
            ->setVipLength($productModel->getData('vip_length'))
            ->setVipLengthUnit($productModel->getData('vip_length_unit'));

        return $product;
    }
}

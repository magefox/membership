<?php
/******************************************************
 * @package Magento 2 Membership
 * @author http://www.magefox.com
 * @copyright (C) 2018 - Magefox.Com
 * @license MIT
 *******************************************************/
namespace Magefox\Membership\Helper;

use \Magefox\Membership\Model\Product\Type\Membership,
    \Magento\Sales\Api\Data\OrderInterface,
    \Magento\Catalog\Api\ProductRepositoryInterface;

class Order extends \Magento\Framework\App\Helper\AbstractHelper
{
    /** @var Config */
    protected $_configHelper;

    /** @var \Magento\Catalog\Api\ProductRepositoryInterface */
    protected $_productRepository;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        Config $configHelper,
        ProductRepositoryInterface $productRepository)
    {
        parent::__construct($context);
        $this->_configHelper = $configHelper;
        $this->_productRepository = $productRepository;
    }

    public function canOrderBecomeVip(OrderInterface $order)
    {
        return $order->getStatus() === $this->_configHelper->getOrderStatus();
    }

    public function hasVipProductBeenPurchased(OrderInterface $order)
    {
        return !empty($this->_getVipOrderItems($order));
    }


    public function getPurchasedMembershipLength(OrderInterface $order)
    {
        /** @var \Magento\Sales\Api\Data\OrderItemInterface $item */
        $order_items = $this->_getVipOrderItems($order);
        $item = array_pop($order_items);
        /** @var \Magento\Catalog\Api\Data\ProductInterface $product */
        $product = $this->_productRepository->getById($item->getProductId());

        return new \DateInterval(sprintf(
            'P%d%s',
            $product->getExtensionAttributes()->getVipLength() * $item->getQtyOrdered(),
            $product->getExtensionAttributes()->getVipLengthUnit()
        ));
    }

    protected function _getVipOrderItems(OrderInterface $order)
    {
        return array_filter($order->getItems(),
            function ($v) {
                /** @var $v \Magento\Sales\Api\Data\OrderItemInterface */
                return $v->getProductType() == Membership::TYPE_CODE;
            });
    }
}

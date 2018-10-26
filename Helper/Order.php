<?php
/******************************************************
 * @package Magento 2 Membership
 * @author http://www.magefox.com
 * @copyright (C) 2018 - Magefox.Com
 * @license MIT
 *******************************************************/

namespace Magefox\Membership\Helper;

class Order extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var Config
     */
    protected $_configHelper;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $_productRepository;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        Config $configHelper
    )
    {
        $this->_productRepository = $productRepository;
        $this->_configHelper = $configHelper;

        parent::__construct($context);
    }

    /**
     * Get membership item in order
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return array
     */
    protected function _getMembershipItem(
        \Magento\Sales\Api\Data\OrderInterface $order
    ) {
        return array_filter(
            $order->getItems(),
            function ($item) {
                /**
                 * @var $item \Magento\Sales\Api\Data\OrderItemInterface
                 */
                return $item->getProductType() == \Magefox\Membership\Model\Product\Type\Membership::TYPE_CODE;
            }
        );
    }

    /**
     * Has membership item on order
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return bool
     */
    public function hasMembershipItemPurchased(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        return !empty($this->_getMembershipItem($order));
    }

    /**
     * Check order can invoke membership
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return bool
     */
    public function canInvokeMembership(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        return $order->getStatus() === $this->_configHelper->getOrderStatus();
    }

    /**
     * Get purchased membership length
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return \DateInterval
     * @throws \Exception
     */
    public function getPurchasedMembershipLength(
        \Magento\Sales\Api\Data\OrderInterface $order
    ) {
        /**
         * @var \Magento\Sales\Api\Data\OrderItemInterface $item
         */
        $items = $this->_getMembershipItem($order);
        $item = array_pop($items);

        /**
         * @var \Magento\Catalog\Api\Data\ProductInterface $product
         */
        try {
            $product = $this->_productRepository
                ->getById($item->getProductId());
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return new \DateInterval('P0D');
        }

        return new \DateInterval(
            sprintf(
                'P%d%s',
                $product->getData('membership_length') * $item->getQtyOrdered(),
                $product->getData('membership_length_unit')
            )
        );
    }

    /**
     * Get purchased membership group id
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return int
     */
    public function getPurchasedMembershipGroupId(
        \Magento\Sales\Api\Data\OrderInterface $order
    ) {
        /**
         * @var \Magento\Sales\Api\Data\OrderItemInterface $item
         */
        $items = $this->_getMembershipItem($order);
        $item = array_pop($items);

        /**
         * @var \Magento\Catalog\Api\Data\ProductInterface $product
         */
        try {
            $product = $this->_productRepository
                ->getById($item->getProductId());
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return $this->_configHelper->getRevokeGroup();
        }

        return $product->getData('membership_group_to_assign') ?? $this->_configHelper->getRevokeGroup();
    }
}

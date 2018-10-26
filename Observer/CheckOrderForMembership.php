<?php
/******************************************************
 * @package Magento 2 Membership
 * @author http://www.magefox.com
 * @copyright (C) 2018 - Magefox.Com
 * @license MIT
 *******************************************************/

namespace Magefox\Membership\Observer;

class CheckOrderForMembership implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $_orderRepository;

    /**
     * @var \Magefox\Membership\Helper\Config
     */
    protected $_configHelper;

    /**
     * @var \Magefox\Membership\Helper\Order
     */
    protected $_orderHelper;

    /**
     * @var \Magefox\Membership\Model\CustomerManagement
     */
    protected $_customerManagement;

    public function __construct(
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magefox\Membership\Helper\Config $configHelper,
        \Magefox\Membership\Helper\Order $orderHelper,
        \Magefox\Membership\Model\CustomerManagement $customerManagement
    ) {
        $this->_orderRepository = $orderRepository;
        $this->_customerFactory = $customerFactory;
        $this->_configHelper = $configHelper;
        $this->_orderHelper = $orderHelper;
        $this->_customerManagement = $customerManagement;
    }

    /**
     * Execute when trigger 'checkout_onepage_controller_success_action' event
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @throws \Exception
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /**
         * @var \Magento\Sales\Api\Data\OrderInterface $order
         */
        $order = $observer->getEvent()->getOrder();
        $customer = $this->_customerFactory
            ->create()
            ->load($order->getCustomerId());

        if ($this->_orderHelper->hasMembershipItemPurchased($order)) {
            if ($this->_orderHelper->canInvokeMembership($order)) {
                $this->_customerManagement->invokeMembership($customer, $order);
            } elseif ($order->getStatus() === 'closed') {
                $this->_customerManagement->revokeMembership($customer);
            }
        }
    }
}

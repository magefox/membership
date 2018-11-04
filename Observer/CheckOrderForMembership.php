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
    protected $customerFactory;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var \Magefox\Membership\Helper\Config
     */
    protected $configHelper;

    /**
     * @var \Magefox\Membership\Helper\Order
     */
    protected $orderHelper;

    /**
     * @var \Magefox\Membership\Model\CustomerManagement
     */
    protected $customerManagement;

    public function __construct(
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magefox\Membership\Helper\Config $configHelper,
        \Magefox\Membership\Helper\Order $orderHelper,
        \Magefox\Membership\Model\CustomerManagement $customerManagement
    ) {
        $this->orderRepository = $orderRepository;
        $this->customerFactory = $customerFactory;
        $this->configHelper = $configHelper;
        $this->orderHelper = $orderHelper;
        $this->customerManagement = $customerManagement;
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
        $customer = $this->customerFactory
            ->create()
            ->load($order->getCustomerId());

        if ($this->orderHelper->hasMembershipItemPurchased($order)) {
            if ($this->orderHelper->canInvokeMembership($order)) {
                $this->customerManagement->invokeMembership($customer, $order);
            } elseif ($order->getStatus() === 'closed') {
                $this->customerManagement->revokeMembership($customer);
            }
        }
    }
}

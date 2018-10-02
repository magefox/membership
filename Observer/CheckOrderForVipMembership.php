<?php
/******************************************************
 * @package Magento 2 Membership
 * @author http://www.magefox.com
 * @copyright (C) 2018 - Magefox.Com
 * @license MIT
 *******************************************************/
namespace Magefox\Membership\Observer;

use \Magento\Framework\Event\ObserverInterface,
    \Magento\Sales\Model\ResourceModel\Order\CollectionFactory,
    \Magefox\Membership\Helper\Config,
    \Magefox\Membership\Helper\Order,
    \Magefox\Membership\Model\VipCustomerManagement,
    \Magento\Customer\Model\Session;

class CheckOrderForMembership implements ObserverInterface
{
    /** @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory */
    protected $_ordersCollectionFactory;

    /** @var \Magefox\Membership\Helper\Config */
    protected $_configHelper;

    /** @var \Magefox\Membership\Helper\Order */
    protected $_orderHelper;

    /** @var \Magefox\Membership\Model\VipCustomerManagement */
    protected $_vipCustomerManager;

    /** @var \Magento\Customer\Model\Session */
    protected $_customerSession;

    public function __construct(
        CollectionFactory $ordersCollectionFactory,
        Config $configHelper,
        Order $orderHelper,
        VipCustomerManagement $vipCustomerManagement,
        Session $customerSession)
    {
        $this->_ordersCollectionFactory = $ordersCollectionFactory;
        $this->_configHelper = $configHelper;
        $this->_orderHelper = $orderHelper;
        $this->_vipCustomerManager = $vipCustomerManagement;
        $this->_customerSession = $customerSession;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        // Don't proceed if VIP Membership functionality is disabled.

        if (!$this->_configHelper->isEnabled()) {
            return;
        }
        
        $orderIds = $observer->getData('order_ids');

        /** @var /Magento/Sales/Model/ResourceModel/Order/Collection $orderCollection */
        $orderCollection = $this->_ordersCollectionFactory->create();
        $orderCollection->addFieldToFilter('entity_id', ['in' => $orderIds]);
        
        /** @var /Magento/Sales/Model/Order $order */
        foreach ($orderCollection as $order) {
            if (!$this->_orderHelper->canOrderBecomeVip($order)) {
                continue;
            }

            if (!$this->_orderHelper->hasVipProductBeenPurchased($order)) {
                continue;
            }

            $this->_vipCustomerManager->becomeVipMember($this->_customerSession->getCustomerData(), $order);
        }
    }
}

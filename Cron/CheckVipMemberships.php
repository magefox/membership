<?php
/******************************************************
 * @package Magento 2 Membership
 * @author http://www.magefox.com
 * @copyright (C) 2018 - Magefox.Com
 * @license MIT
 *******************************************************/
namespace Magefox\Membership\Cron;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Customer;

class CheckMemberships
{
    /** @var \Magefox\Membership\Helper\Config */
    protected $_configHelper;

    /** @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory */
    protected $_customerCollectionFactory;

    /** @var \Magento\Customer\Api\GroupManagementInterface */
    protected $_groupManagement;

    /** @var \Magento\Framework\Event\Manager */
    protected $_eventManager;

    /** @var \Magefox\Membership\Model\VipCustomerManagement */
    protected $_vipCustomerManagement;

    public function __construct(
        \Magefox\Membership\Helper\Config $configHelper,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory,
        \Magento\Customer\Api\GroupManagementInterface $groupManagement,
        \Magento\Framework\Event\Manager $eventManager,
        \Magefox\Membership\Model\VipCustomerManagement $vipCustomerManagement
    )
    {
        $this->_configHelper = $configHelper;
        $this->_customerCollectionFactory = $customerCollectionFactory;
        $this->_groupManagement = $groupManagement;
        $this->_eventManager = $eventManager;
        $this->_vipCustomerManagement = $vipCustomerManagement;
    }

    /**
     *
     * @return void
     */
    public function execute()
    {
        // Check that the module functionality is enabled.
        if (!$this->_configHelper->isEnabled()) {
            return;
        }

        /** @var \Magento\Customer\Model\ResourceModel\Customer\Collection $customerCollection */
        $customerCollection = $this->_customerCollectionFactory->create();
        $customerCollection->addFieldToFilter(CustomerInterface::GROUP_ID, ['eq' => $this->_configHelper->getVipCustomerGroup()])
            ->addFieldToFilter('vip_expiry_date', ['lteq' => (new \DateTime('now'))]);

        /** @var Customer $customer */
        foreach ($customerCollection as $customer) {
            $this->_vipCustomerManagement->revokeMembership($customer->getDataModel());
        }

        // Could be used for dispatching emails to inform the customer of their expired membership.
        $this->_eventManager->dispatch('Magefox_Membership_expired_customers', ['customers' => $customerCollection]);
    }
}

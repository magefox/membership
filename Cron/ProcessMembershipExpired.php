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

class ProcessMembershipExpired
{
    /**
     * @var \Magento\Framework\Event\Manager
     */
    protected $_eventManager;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_dateTime;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
     */
    protected $_customerCollectionFactory;

    /**
     * @var \Magefox\Membership\Helper\Config
     */
    protected $_configHelper;

    /**
     * @var \Magefox\Membership\Api\CustomerManagementInterface
     */
    protected $_customerManagement;

    /**
     * ProcessMembershipExpired constructor.
     *
     * @param \Magento\Framework\Event\Manager $eventManager
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory
     * @param \Magefox\Membership\Helper\Config $configHelper
     * @param \Magefox\Membership\Api\CustomerManagementInterface $customerManagement
     */
    public function __construct(
        \Magento\Framework\Event\Manager $eventManager,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory,
        \Magefox\Membership\Helper\Config $configHelper,
        \Magefox\Membership\Api\CustomerManagementInterface $customerManagement
    )
    {
        $this->_eventManager = $eventManager;
        $this->_dateTime = $dateTime;
        $this->_customerCollectionFactory = $customerCollectionFactory;
        $this->_configHelper = $configHelper;
        $this->_customerManagement = $customerManagement;
    }

    /**
     * Execute process membership expired
     *
     * @throws \Exception
     */
    public function execute()
    {
        // Check that the module functionality is enabled.
        if (!$this->_configHelper->isEnabled()) {
            return;
        }

        /** @var \Magento\Customer\Model\ResourceModel\Customer\Collection $customerCollection */
        $customers = $this->_customerCollectionFactory->create();
        $customers
            ->addFieldToFilter('group_id', [
                'neq' => $this->_configHelper->getRevokeGroup()
            ])
            ->addAttributeToFilter('membership_expiry', [
                'lteq' => $this->_dateTime->gmtDate(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT)
            ]);

        /** @var Customer $customer */
        foreach ($customers as $customer) {
            $this->_customerManagement->revokeMembership($customer);
        }

        // Could be used for dispatching emails to inform the customer of their expired membership.
        $this->_eventManager->dispatch(
            'membership_expired_customers',
            [
                'customers' => $customers
            ]
        );
    }
}

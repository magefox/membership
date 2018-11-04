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
    protected $eventManager;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $dateTime;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
     */
    protected $customerCollectionFactory;

    /**
     * @var \Magefox\Membership\Helper\Config
     */
    protected $configHelper;

    /**
     * @var \Magefox\Membership\Api\CustomerManagementInterface
     */
    protected $customerManagement;

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
    ) {
        $this->eventManager = $eventManager;
        $this->dateTime = $dateTime;
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->configHelper = $configHelper;
        $this->customerManagement = $customerManagement;
    }

    /**
     * Execute process membership expired
     *
     * @throws \Exception
     */
    public function execute()
    {
        // Check that the module functionality is enabled.
        if (!$this->configHelper->isEnabled()) {
            return;
        }

        /** @var \Magento\Customer\Model\ResourceModel\Customer\Collection $customerCollection */
        $customers = $this->customerCollectionFactory->create();
        $customers
            ->addFieldToFilter('group_id', [
                'neq' => $this->configHelper->getRevokeGroup()
            ])
            ->addAttributeToFilter('membership_expiry', [
                'lteq' => $this->dateTime->gmtDate(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT)
            ]);

        /** @var Customer $customer */
        foreach ($customers as $customer) {
            $this->customerManagement->revokeMembership($customer);
        }

        // Could be used for dispatching emails to inform the customer of their expired membership.
        $this->eventManager->dispatch(
            'membership_expired_customers',
            [
                'customers' => $customers
            ]
        );
    }
}

<?php
/******************************************************
 * @package Magento 2 Membership
 * @author http://www.magefox.com
 * @copyright (C) 2018 - Magefox.Com
 * @license MIT
 *******************************************************/

namespace Magefox\Membership\Model;

class CustomerManagement implements \Magefox\Membership\Api\CustomerManagementInterface
{
    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaInterface
     */
    protected $searchCriteria;

    /**
     * @var \Magento\Framework\Api\Search\FilterGroup
     */
    protected $filterGroup;

    /**
     * @var \Magento\Framework\Api\Filter
     */
    protected $filter;

    /**
     * @var \Magento\Customer\Api\GroupManagementInterface
     */
    protected $groupManagement;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $dateTime;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * @var \Magefox\Membership\Helper\Config
     */
    protected $configHelper;

    /**
     * @var \Magefox\Membership\Helper\Order
     */
    protected $orderHelper;

    /**
     * CustomerManagement constructor.
     *
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @param \Magento\Framework\Api\Search\FilterGroup $filterGroup
     * @param \Magento\Framework\Api\Filter $filter
     * @param \Magento\Customer\Api\GroupManagementInterface $groupManagement
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param \Magefox\Membership\Helper\Config $configHelper
     * @param \Magefox\Membership\Helper\Order $orderHelper
     */
    public function __construct(
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria,
        \Magento\Framework\Api\Search\FilterGroup $filterGroup,
        \Magento\Framework\Api\Filter $filter,
        \Magento\Customer\Api\GroupManagementInterface $groupManagement,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magefox\Membership\Helper\Config $configHelper,
        \Magefox\Membership\Helper\Order $orderHelper
    ) {
        $this->customerRepository = $customerRepository;
        $this->searchCriteria = $searchCriteria;
        $this->filterGroup = $filterGroup;
        $this->filter = $filter;
        $this->groupManagement = $groupManagement;
        $this->dateTime = $dateTime;
        $this->eventManager = $eventManager;
        $this->configHelper = $configHelper;
        $this->orderHelper = $orderHelper;
    }

    /**
     * Calculate expiry date
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return \DateTime
     * @throws \Exception
     */
    public function calculateExpiryDate(
        \Magento\Sales\Api\Data\OrderInterface $order
    ) {
        $now = new \DateTime(
            $this->dateTime->gmtDate(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT),
            new \DateTimeZone('UTC')
        );

        return $now->add($this->orderHelper->getPurchasedMembershipLength($order));
    }

    /**
     * Retrieve the Membership Group ID defined in product
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return int
     */
    public function getGroupId(
        \Magento\Sales\Api\Data\OrderInterface $order
    ) {
        return $this->orderHelper->getPurchasedMembershipGroupId($order);
    }

    /**
     * Make a given customer a Membership from a given order.
     *
     * @param \Magento\Customer\Model\Customer $customer
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return \Magento\Customer\Model\Customer
     * @throws \Exception
     */
    public function invokeMembership(
        \Magento\Customer\Model\Customer $customer,
        \Magento\Sales\Api\Data\OrderInterface $order
    ) {
        if (!$this->orderHelper->canInvokeMembership($order)) {
            return $customer;
        }

        $this->eventManager->dispatch(
            'membership_before_invoke',
            [
                'customer'  => $customer,
                'order'     => $order
            ]
        );

        $expiry = $this->calculateExpiryDate($order)
            ->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);

        $customerData = $customer->getDataModel();
        $customerData->setCustomAttribute('membership_expiry', $expiry)
            ->setCustomAttribute('membership_order_id', $order->getIncrementId())
            ->setGroupId($this->getGroupId($order));

        $customer = $customer->updateData($customerData)
            ->save();

        $this->eventManager->dispatch(
            'membership_after_invoke',
            [
                'customer'  => $customer,
                'order'     => $order
            ]
        );

        return $customer;
    }

    /**
     * Remove a given customers Membership.
     *
     * @param  \Magento\Customer\Model\Customer $customer
     * @return \Magento\Customer\Model\Customer
     * @throws \Exception
     */
    public function revokeMembership(
        \Magento\Customer\Model\Customer $customer
    ) {
        $this->eventManager->dispatch(
            'membership_before_revoke',
            [
                'customer' => $customer
            ]
        );

        $expiry = $this->dateTime->gmtDate(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);

        $customerData = $customer->getDataModel();
        $customerData->setCustomAttribute('membership_expiry', $expiry)
            ->setGroupId($this->configHelper->getRevokeGroup());

        $customer = $customer->updateData($customerData)
            ->save();

        $this->eventManager->dispatch(
            'membership_after_revoke',
            [
                'customer' => $customer
            ]
        );

        return $customer;
    }

    /**
     * Check customer is membership.
     *
     * @param  \Magento\Customer\Model\Customer $customer
     * @return bool
     */
    public function isMembership(\Magento\Customer\Model\Customer $customer)
    {
        return $this->getDaysLeft($customer) > 0;
    }

    /**
     * Get expire time
     *
     * @param \Magento\Customer\Model\Customer $customer
     * @return string
     */
    public function getExpiry(\Magento\Customer\Model\Customer $customer)
    {
        return $customer->getData('membership_expiry');
    }

    /**
     * Get days to membership expire.
     *
     * @param \Magento\Customer\Model\Customer $customer
     * @return int
     */
    public function getDaysLeft(\Magento\Customer\Model\Customer $customer)
    {
        $expiry = new \DateTime($this->getExpiry($customer), new \DateTimeZone('UTC'));
        $today = new \DateTime(
            $this->dateTime->gmtDate(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT),
            new \DateTimeZone('UTC')
        );

        return $today->getTimestamp() < $expiry->getTimestamp() ? $today->diff($expiry)->days : 0;
    }
}

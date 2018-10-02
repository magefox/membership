<?php
/******************************************************
 * @package Magento 2 Membership
 * @author http://www.magefox.com
 * @copyright (C) 2018 - Magefox.Com
 * @license MIT
 *******************************************************/
namespace Magefox\Membership\Model;

use \Magefox\Membership\Api\VipCustomerManagementInterface,
    \Magento\Customer\Api\Data\CustomerInterface,
    \Magento\Sales\Api\Data\OrderInterface,
    \Magento\Customer\Api\CustomerRepositoryInterface,
    \Magento\Framework\Api\SearchCriteriaInterface,
    \Magento\Framework\Api\Search\FilterGroup,
    \Magento\Framework\Api\Filter,
    \Magefox\Membership\Helper\Config,
    \Magefox\Membership\Helper\Order,
    \Magento\Customer\Api\GroupManagementInterface;

class VipCustomerManagement implements VipCustomerManagementInterface
{
    /** @var \Magento\Customer\Api\CustomerRepositoryInterface */
    protected $_customerRepository;

    /** @var \Magento\Framework\Api\SearchCriteriaInterface */
    protected $_searchCriteria;

    /** @var \Magento\Framework\Api\Search\FilterGroup */
    protected $_filterGroup;

    /** @var \Magento\Framework\Api\Filter */
    protected $_filter;

    /** @var \Magefox\Membership\Helper\Config */
    protected $_configHelper;

    /** @var \Magefox\Membership\Helper\Order */
    protected $_orderHelper;

    /** @var \Magento\Customer\Api\GroupManagementInterface */
    protected $_groupManagement;

    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        SearchCriteriaInterface $searchCriteria,
        FilterGroup $filterGroup,
        Filter $filter,
        Config $configHelper,
        Order $orderHelper,
        GroupManagementInterface $groupManagement)
    {
        $this->_customerRepository = $customerRepository;
        $this->_searchCriteria = $searchCriteria;
        $this->_filterGroup = $filterGroup;
        $this->_filter = $filter;
        $this->_configHelper = $configHelper;
        $this->_orderHelper = $orderHelper;
        $this->_groupManagement = $groupManagement;
    }

    /**
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return \Magento\Customer\Api\Data\CustomerInterface
     */
    public function becomeVipMember(CustomerInterface $customer, OrderInterface $order)
    {
        if (!$this->_orderHelper->canOrderBecomeVip($order)) {
            return $customer;
        }

        $customer->getExtensionAttributes()->setVipExpiry($this->calculateExpiryDate($customer, $order)->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT))
            ->setVipOrderId($order->getIncrementId());
        $customer->setGroupId($this->getGroupId());

        $this->_customerRepository->save($customer);

        return $customer;
    }

    /**
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return \Magento\Customer\Api\Data\CustomerInterface
     */
    public function revokeMembership(CustomerInterface $customer)
    {
        $expiry = (new \DateTime('now'))->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);
        $customer->getExtensionAttributes()
            ->setVipExpiry($expiry);
        $customer->setGroupId($this->_groupManagement->getDefaultGroup()->getId());
        $this->_customerRepository->save($customer);

        return $customer;
    }

    /**
     * @return \Magento\Customer\Api\Data\CustomerSearchResultsInterface
     */
    public function getAllVipCustomers()
    {
        $searchCriteria = $this->_searchCriteria;
        $filterGroup = $this->_filterGroup->setFilters([
            $this->_filter->setField('group_id')->setConditionType('eq')->setValue($this->getGroupId())
        ]);
        $searchCriteria->setFilterGroups([$filterGroup]);
        return $this->_customerRepository->getList($searchCriteria);
    }

    /**
     * @return integer
     */
    public function getGroupId()
    {
        return $this->_configHelper->getVipCustomerGroup();
    }

    /**
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return \DateTime
     */
    public function calculateExpiryDate(CustomerInterface $customer, OrderInterface $order)
    {
        $expiry = $customer->getExtensionAttributes()->getVipExpiry();
        if (!$expiry) {
            $expiry = new \DateTime('now');
        }
        return $expiry->add($this->_orderHelper->getPurchasedMembershipLength($order));
    }

    public function isVip(CustomerInterface $customer)
    {
        return $customer->getGroupId() == $this->_configHelper->getVipCustomerGroup();
    }

    public function getDaysLeft(CustomerInterface $customer)
    {
        $expiry = new \DateTime($customer->getExtensionAttributes()->getVipExpiry());
        $today = new \DateTime('now');

        return $today->diff($expiry)->days;
    }
}

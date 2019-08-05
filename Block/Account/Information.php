<?php
/******************************************************
 * @package Magento 2 Membership
 * @author http://www.magefox.com
 * @copyright (C) 2018 - Magefox.Com
 * @license MIT
 *******************************************************/

namespace Magefox\Membership\Block\Account;

use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Model\Session as CustomerSession;
use Magefox\Membership\Block\AbstractAccount;
use Magefox\Membership\Api\CustomerManagementInterface;
use Magento\Customer\Model\GroupFactory;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class Information extends AbstractAccount
{
    /**
     * @var GroupFactory
     */
    protected $customerGroupFactory;

    /**
     * @var TimezoneInterface
     */
    protected $localeDate;

    /**
     * Information constructor.
     *
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param CustomerManagementInterface $customerManagement
     * @param GroupFactory $customerGroupFactory
     * @param TimezoneInterface $localeDate
     * @param array $data
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        CustomerManagementInterface $customerManagement,
        GroupFactory $customerGroupFactory,
        TimezoneInterface $localeDate,
        array $data = []
    ) {
        $this->customerGroupFactory = $customerGroupFactory;
        $this->localeDate = $localeDate;

        parent::__construct($context, $customerSession, $customerManagement, $data);
    }

    /**
     * Get expire time
     *
     * @return string
     */
    public function getExpiry()
    {
        $expiry = $this->customerManagement->getExpiry($this->getCustomer());
        return $this->localeDate->date($expiry)->format('M d, Y H:i:s');
    }

    /**
     * Get days to membership expire.
     *
     * @return int
     */
    public function getDaysLeft()
    {
        return $this->customerManagement->getDaysLeft($this->getCustomer());
    }

    /**
     * Get customer group
     *
     * @return mixed
     */
    public function getCustomerGroup()
    {
        $group = $this->customerGroupFactory->create();
        $group->load($this->getCustomer()->getGroupId());

        return $group->getCode();
    }

    /**
     * Check customer is membership
     *
     * @return bool
     */
    public function isMembership()
    {
        return $this->customerManagement->isMembership($this->getCustomer());
    }
}

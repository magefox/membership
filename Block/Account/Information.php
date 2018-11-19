<?php
/******************************************************
 * @package Magento 2 Membership
 * @author http://www.magefox.com
 * @copyright (C) 2018 - Magefox.Com
 * @license MIT
 *******************************************************/

namespace Magefox\Membership\Block\Account;

use Magefox\Membership\Block\AbstractAccount;

class Information extends AbstractAccount
{
    /**
     * @var \Magento\Customer\Model\GroupFactory
     */
    protected $customerGroupFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $localeDate;

    /**
     * Information constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magefox\Membership\Api\CustomerManagementInterface $customerManagement
     * @param \Magento\Customer\Model\GroupFactory $customerGroupFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magefox\Membership\Api\CustomerManagementInterface $customerManagement,
        \Magento\Customer\Model\GroupFactory $customerGroupFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
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
     * Produce and return block's html output
     *
     * This method should not be overridden. You can override _toHtml() method in descendants if needed.
     *
     * @return string
     */
    public function toHtml()
    {
        if (!$this->customerManagement->isMembership($this->getCustomer())) {
            return '';
        }

        return parent::toHtml();
    }
}

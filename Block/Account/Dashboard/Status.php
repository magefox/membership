<?php
/******************************************************
 * @package Magento 2 Membership
 * @author http://www.magefox.com
 * @copyright (C) 2018 - Magefox.Com
 * @license MIT
 *******************************************************/

namespace Magefox\Membership\Block\Account\Dashboard;

class Status  extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magefox\Membership\Api\CustomerManagementInterface
     */
    protected $_customerManagement;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;

    /**
     * Avatar constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magefox\Membership\Api\CustomerManagementInterface $customerManagement
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magefox\Membership\Api\CustomerManagementInterface $customerManagement,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        array $data = []
    ) {
        $this->_customerSession = $customerSession;
        $this->_customerManagement = $customerManagement;
        $this->_localeDate = $localeDate;

        parent::__construct($context, $data);
    }

    /**
     * Get logged in customer
     *
     * @return \Magento\Customer\Model\Customer
     */
    public function getCustomer()
    {
        return $this->_customerSession->getCustomer();
    }

    /**
     * Get expire time
     *
     * @return string
     */
    public function getExpiry()
    {
        $expiry = $this->_customerManagement->getExpiry($this->getCustomer());
        return $this->_localeDate->date($expiry)->format('M d, Y H:i:s');
    }

    /**
     * Get days to membership expire.
     *
     * @return int
     */
    public function getDaysLeft()
    {
        return $this->_customerManagement->getDaysLeft($this->getCustomer());
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
        if (!$this->_customerManagement->isMembership($this->getCustomer())) {
            return '';
        }

        return parent::toHtml();
    }
}

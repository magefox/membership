<?php
/******************************************************
 * @package Magento 2 Membership
 * @author http://www.magefox.com
 * @copyright (C) 2018 - Magefox.Com
 * @license MIT
 *******************************************************/

namespace Magefox\Membership\Block;

class AbstractAccount extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magefox\Membership\Api\CustomerManagementInterface
     */
    protected $customerManagement;

    /**
     * Avatar constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magefox\Membership\Api\CustomerManagementInterface $customerManagement
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magefox\Membership\Api\CustomerManagementInterface $customerManagement,
        array $data = []
    ) {
        $this->customerSession = $customerSession;
        $this->customerManagement = $customerManagement;

        parent::__construct($context, $data);
    }

    /**
     * Get logged in customer
     *
     * @return \Magento\Customer\Model\Customer
     */
    public function getCustomer()
    {
        return $this->customerSession->getCustomer();
    }
}

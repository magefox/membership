<?php
/******************************************************
 * @package Magento 2 Membership
 * @author http://www.magefox.com
 * @copyright (C) 2018 - Magefox.Com
 * @license MIT
 *******************************************************/
namespace Magefox\Membership\Api;

use Magento\Customer\Api\Data\CustomerInterface;

interface CustomerManagementInterface {

    /**
     * Make a given customer a VIP from a given order.
     * 
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return \Magento\Customer\Api\Data\CustomerInterface
     */
    public function becomeVipMember(\Magento\Customer\Api\Data\CustomerInterface $customer, \Magento\Sales\Api\Data\OrderInterface $order);

    /**
     * Remove a given customers VIP Membership.
     * 
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return \Magento\Customer\Api\Data\CustomerInterface
     */
    public function revokeMembership(\Magento\Customer\Api\Data\CustomerInterface $customer);

    /**
     * @return \Magento\Customer\Api\Data\CustomerSearchResultsInterface
     */
    public function getAllVipCustomers();

    /**
     * Retrieve the VIP Group ID defined in config
     * @return integer
     */
    public function getGroupId();

    /**
     * @param CustomerInterface $customer
     * @return boolean
     */
    public function isVip(CustomerInterface $customer);

    /**
     * @param CustomerInterface $customer
     * @return integer
     */
    public function getDaysLeft(CustomerInterface $customer);

}

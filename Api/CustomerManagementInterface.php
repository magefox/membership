<?php
/******************************************************
 * @package Magento 2 Membership
 * @author http://www.magefox.com
 * @copyright (C) 2020 - Magefox.Com
 * @license MIT
 *******************************************************/

namespace Magefox\Membership\Api;

interface CustomerManagementInterface
{

    /**
     * Make a given customer a Membership from a given order.
     *
     * @param \Magento\Customer\Model\Customer $customer
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return \Magento\Customer\Model\Customer
     */
    public function invokeMembership(
        \Magento\Customer\Model\Customer $customer,
        \Magento\Sales\Api\Data\OrderInterface $order
    );

    /**
     * Remove a given customers Membership.
     *
     * @param \Magento\Customer\Model\Customer $customer
     * @return \Magento\Customer\Model\Customer
     */
    public function revokeMembership(\Magento\Customer\Model\Customer $customer);

    /**
     * Retrieve the Membership Group ID defined in product
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return integer
     */
    public function getGroupId(\Magento\Sales\Api\Data\OrderInterface $order);

    /**
     * Check customer is membership.
     *
     * @param \Magento\Customer\Model\Customer $customer
     * @return boolean
     */
    public function isMembership(\Magento\Customer\Model\Customer $customer);

    /**
     * Get expire time
     *
     * @param \Magento\Customer\Model\Customer $customer
     * @return string
     */
    public function getExpiry(\Magento\Customer\Model\Customer $customer);

    /**
     * Get days to membership expire.
     *
     * @param \Magento\Customer\Model\Customer $customer
     * @return integer
     */
    public function getDaysLeft(\Magento\Customer\Model\Customer $customer);
}

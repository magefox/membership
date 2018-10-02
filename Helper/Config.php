<?php
/******************************************************
 * @package Magento 2 Membership
 * @author http://www.magefox.com
 * @copyright (C) 2018 - Magefox.Com
 * @license MIT
 *******************************************************/
namespace Magefox\Membership\Helper;

use \Magento\Store\Model\ScopeInterface;

class Config extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_IS_ENABLED = 'vip_membership/general/enabled';
    const XML_PATH_VIP_CUSTOMER_GROUP = 'vip_membership/general/vip_group';
    const XML_PATH_ORDER_STATUS = 'vip_membership/general/vip_order_status';

    /**
     * Retrieve configuration setting if the module is enabled or not.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return (bool) $this->scopeConfig->getValue(self::XML_PATH_IS_ENABLED, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Retrieve the Customer Group defined for VIP members usage.
     * @return integer
     */
    public function getVipCustomerGroup()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_VIP_CUSTOMER_GROUP, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string
     */
    public function getOrderStatus()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_ORDER_STATUS, ScopeInterface::SCOPE_STORE);
    }
}

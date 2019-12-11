<?php
/******************************************************
 * @package   Magento 2 Membership
 * @author    http://www.magefox.com
 * @copyright (C) 2020 - Magefox.Com
 * @license   MIT
 *******************************************************/

namespace Magefox\Membership\Observer\Adminhtml;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magefox\Membership\Helper\Config;
use Magento\Customer\Model\Data\Customer;
use Magento\Framework\App\Request\Http;

class CheckMembershipExpiry implements ObserverInterface
{
    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var Config
     */
    protected $configHelper;

    /**
     * CheckMembershipExpiry constructor.
     *
     * @param DateTime $dateTime
     * @param Config $configHelper
     */
    public function __construct(
        DateTime $dateTime,
        Config $configHelper
    ) {
        $this->dateTime = $dateTime;
        $this->configHelper = $configHelper;
    }

    /**
     * Execute when trigger 'adminhtml_customer_prepare_save' event
     *
     * @param Observer $observer
     * @throws \Exception
     */
    public function execute(Observer $observer)
    {
        // Check that the module functionality is enabled.
        if (!$this->configHelper->isEnabled()) {
            return;
        }

        $now = $this->dateTime->gmtTimestamp();
        /**
         * @var $customer Customer
         * @var $request Http
         */
        $customer = $observer->getEvent()->getData('customer');
        $request = $observer->getEvent()->getData('request');
        $data = $request->getParam('customer');
        if (isset($data['membership_expiry'])) {
            $expiry = strtotime($data['membership_expiry'] . ' 00:00:00');
            if ($expiry <= $now) {
                $customer->setGroupId($this->configHelper->getRevokeGroup());
            }
        }
    }
}

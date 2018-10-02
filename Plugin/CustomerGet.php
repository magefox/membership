<?php
/******************************************************
 * @package Magento 2 Membership
 * @author http://www.magefox.com
 * @copyright (C) 2018 - Magefox.Com
 * @license MIT
 *******************************************************/
namespace Magefox\Membership\Plugin;

class CustomerGet
{
    protected $customerExtensionFactory;
    protected $customerFactory;

    public function __construct(
        \Magento\Customer\Api\Data\CustomerExtensionFactory $customerExtensionFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory
    )
    {
        $this->customerFactory = $customerFactory;
        $this->customerExtensionFactory = $customerExtensionFactory;
    }

    public function aroundGetById(
        \Magento\Customer\Api\CustomerRepositoryInterface $subject,
        \Closure $proceed,
        $customerId
    )
    {
        /** @var \Magento\Customer\Api\Data\CustomerInterface $resultCustomer */
        $resultCustomer = $proceed($customerId);

        $resultCustomer = $this->addVipDataToCustomer($resultCustomer);

        return $resultCustomer;
    }

    /**
     * Add vip data to customer
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return \Magento\Customer\Api\Data\CustomerInterface
     */
    protected function addVipDataToCustomer(\Magento\Customer\Api\Data\CustomerInterface $customer)
    {
        if ($customer->getExtensionAttributes() && $customer->getExtensionAttributes()->getVipExpiry()) {
            return $customer;
        }

        $customerModel = $this->customerFactory->create()->load($customer->getId());
        /** @var \Magento\Customer\Api\Data\CustomerExtension $orderExtension */
        if (!$customer->getExtensionAttributes()) {
            $customerExtension = $this->customerExtensionFactory->create();
            $customer->setExtensionAttributes($customerExtension);
        }

        $expiry = $customerModel->getData('vip_expiry');
        if (!$expiry) {
            $expiry = (new \DateTime('now'))->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);
        }

        $customer->getExtensionAttributes()
            ->setVipExpiry($expiry)
            ->setVipOrderId($customerModel->getData('vip_order_id'));

        return $customer;
    }
}

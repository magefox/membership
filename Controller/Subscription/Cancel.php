<?php
namespace Magefox\Membership\Controller\Subscription;

use Magefox\Membership\Controller\AbstractAccount;
use Magefox\Membership\Model\CustomerManagement;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;

class Cancel extends AbstractAccount
{
    /**
     * @var CustomerManagement
     */
    protected $customerManagement;

    public function __construct(
        Context $context,
        Session $customerSession,
        CustomerManagement $customerManagement
    ) {
        $this->customerManagement = $customerManagement;

        parent::__construct($context, $customerSession);
    }

    /**
     * Execute cancel subscription
     *
     * @return \Magento\Backend\Model\View\Result\Redirect|ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /**
         * @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect
         */
        $resultRedirect = $this->resultRedirectFactory->create();

        try {
            $this->customerManagement->revokeMembership($this->getCustomer());
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __('Have an error when try to cancel your subscription, please try again.')
            );
            return $resultRedirect->setPath('membership');
        }

        $this->messageManager->addSuccessMessage(__('Cancel your subscription successfully.'));
        return $resultRedirect->setPath('membership');
    }
}

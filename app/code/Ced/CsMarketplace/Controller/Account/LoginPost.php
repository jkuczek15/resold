<?php

/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsMarketplace\Controller\Account;

use Ced\CsMarketplace\Model\Account\Redirect as AccountRedirect;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Customer\Api\AccountManagementInterface;
use Ced\CsMarketplace\Model\Url as CustomerUrl;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Data\Form\FormKey\Validator;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class LoginPost extends \Magento\Customer\Controller\AbstractAccount
{
    /**
* 
     *
 * @var AccountManagementInterface 
*/
    protected $vendorAccountManagement;

    /**
* 
     *
 * @var Validator 
*/
    protected $formKeyValidator;

    /**
     * @var VendorAcRedirect
     */
    protected $vendorAcRedirect;

    /**
     * @var VendorUrl
     */
    protected $vendorUrl;

    /**
     * @var VendorSession
     */
    protected $vendorSession;

    /**
     * @param Context                    $context
     * @param Session                    $customerSession
     * @param AccountManagementInterface $vendorAccountManagement
     * @param CustomerUrl                $vendorHelperData
     * @param Validator                  $formKeyValidator
     * @param AccountRedirect            $vendorAcRedirect
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        AccountManagementInterface $vendorAccountManagement,
        CustomerUrl $vendorHelperData,
        Validator $formKeyValidator,
        AccountRedirect $vendorAcRedirect
    ) {
        $this->vendorSession = $customerSession;
        $this->vendorAccountManagement = $vendorAccountManagement;
        $this->vendorUrl = $vendorHelperData;
        $this->formKeyValidator = $formKeyValidator;
        $this->vendorAcRedirect = $vendorAcRedirect;
        parent::__construct($context);
    }

    /**
     * Login post action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        if ($this->vendorSession->isLoggedIn()) {
            /* || !$this->formKeyValidator->validate($this->getRequest())*/
            /**
             *
             *
             * @var \Magento\Framework\Controller\Result\Redirect $resultRedirect
             */
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('sell');
            return $resultRedirect;
        }

        if ($this->getRequest()->isPost()) {
        
            $login = $this->getRequest()->getPost('login');
            if (!empty($login['username']) && !empty($login['password'])) {
                try {

                    $customer = $this->vendorAccountManagement->authenticate($login['username'], $login['password']);
                    $this->vendorSession->setCustomerDataAsLoggedIn($customer);
                    $this->vendorSession->regenerateId();
                } catch (\Magento\Framework\Exception\EmailNotConfirmedException $e) {
                    $value = $this->vendorUrl->getEmailConfirmationUrl($login['username']);

                    $this->messageManager->addComplexErrorMessage('addCustomSuccessMessage', [
                            'html' => 'This account is not confirmed. <a href="%1">Click here</a> to resend confirmation email.',
                            'params' => $value
                        ]
                    );
                    $this->vendorSession->setUsername($login['username']);
                }                
                catch (\Magento\Framework\Exception\InvalidEmailOrPasswordException $e) {
                    $message = __('Invalid login or password.');
                    $this->messageManager->addErrorMessage($message);
                    $this->vendorSession->setUsername($login['username']);
                }
                 catch (AuthenticationException $e) {
                    $message = __('Invalid login or password.');
                    $this->messageManager->addErrorMessage($message);
                    $this->vendorSession->setUsername($login['username']);
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage(__('Something went wrong.'));
                }
            } else {
                $this->messageManager->addErrorMessage(__('A login and a password are required.'));
            }
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('sell');
        return $resultRedirect;
    }
}

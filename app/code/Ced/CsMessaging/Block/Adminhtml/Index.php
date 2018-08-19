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
 * @category  Ced
 * @package   Ced_CsMessaging
 * @author    CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\CsMessaging\Block\Adminhtml;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
/**
 * HTML anchor element block
 *
 * @method string getLabel()
 * @method string getPath()
 * @method string getTitle()
 */



class Index extends \Magento\Backend\Block\Template
{

    public $_vendorFactory;

    public $_scopeConfig;

    public $_messagingFactory;

    public $formKey;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Ced\CsMarketplace\Model\VendorFactory $vendorFactory,
        \Ced\CsMessaging\Model\MessagingFactory $messagingFactory,
        array $data = []
    ) {

        $this->_messagingFactory = $messagingFactory->create();
        parent::__construct($context, $data);
        $this->_vendorFactory = $vendorFactory->create();
        $this->_storeManager = $context->getStoreManager();


    }
    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }

    public function getMediaurl(){
    	return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }
    public function isAllowToSend(){
    	return  $this->_scopeConfig->getValue('ced_csmarketplace/csmessaging/admin_send_mail',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);

    }

    public function OwnerEmail()
    {
       $reciever_email = $this->_scopeConfig->getValue('trans_email/ident_general/email', \Magento\Store\Model\ScopeInterface::SCOPE_STORE); //fetch receiver email address
       return $reciever_email;
    }

}

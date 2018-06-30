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

namespace Ced\CsMarketplace\Controller\Vpayments;

use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Ced\CsMarketplace\Helper\Payment;
use Magento\Framework\App\Response\Http\FileFactory;

class ExportVpaymentCsv extends \Magento\Framework\App\Action\Action
{

    protected $_fileFactory;
    
    protected $resultPageFactory;

    public $_payment;
    
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        FileFactory $fileFactory,
        Payment $payment
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_fileFactory = $fileFactory;
        $this->_payment = $payment;
    }

    public function execute()
    {

        
        $filename = 'vendor_vpayments.csv';
        $content =   $this->_payment->getVendorCommision();
        
        return $this->_fileFactory->create($filename, $content, DirectoryList::VAR_DIR);
        
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Export Payments CSV'));
        return $resultPage;
    }
    
   
}

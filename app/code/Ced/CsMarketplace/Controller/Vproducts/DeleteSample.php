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

namespace Ced\CsMarketplace\Controller\Vproducts;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\UrlFactory;

class DeleteSample extends \Ced\CsMarketplace\Controller\Vproducts
{

    public $resultJsonFactory;

    public function __construct(
        Context $context,
        Session $customerSession,
        PageFactory $resultPageFactory,
        UrlFactory $urlFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context, $customerSession, $resultPageFactory, $urlFactory, $moduleManager);
        $this->resultJsonFactory = $resultJsonFactory;
    }

    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        if(!$this->_getSession()->getVendorId()) {
            return false;
        }
        $result=0;
        $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')
            ->setCurrentStore(\Magento\Store\Model\Store::DEFAULT_STORE_ID);
        $data= $this->getRequest()->getParams();
        try{
            $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')
                ->setCurrentStore(\Magento\Store\Model\Store::DEFAULT_STORE_ID);
            
            if(isset($data['sampleid'])) {
                $sample=$this->_objectManager->create('Magento\Downloadable\Model\Sample')
                    ->load($data['sampleid']);
                if($sample&&$sample->getId()) {
                    $sample->delete();
                    $result=1;
                }
            }
        }
        catch (\Exception $e ) {
            $result=0;
        }
        $resultJson->setData($result);
        return $resultJson;
    }
}

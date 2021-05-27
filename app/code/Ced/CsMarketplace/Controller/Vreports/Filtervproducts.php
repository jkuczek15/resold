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

namespace Ced\CsMarketplace\Controller\Vreports;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\UrlFactory;
use Ced\CsMarketplace\Model\Session as MarketplaceSession;

class Filtervproducts extends \Ced\CsMarketplace\Controller\Vendor
{
    public $resultJsonFactory;
    public $marketplaceSession;

    public function __construct(
        Context $context,
        Session $customerSession,
        PageFactory $resultPageFactory,
        UrlFactory $urlFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        MarketplaceSession  $MarketplaceSession
    ) {
        parent::__construct($context, $customerSession, $resultPageFactory, $urlFactory, $moduleManager);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->marketplaceSession = $MarketplaceSession;
    }

    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        if(!$this->_getSession()->getVendorId()) { 
            return false;
        } 

        $params1 = $this->getRequest()->getParams();
        $params1=current($params1);
        $params = [];
        parse_str($params1, $params);
        

        if(!isset($params['p']) && !isset($params['limit']) &&  is_array($params) ) {
            $this->marketplaceSession->setData('vproducts_reports_filter', $params);
        }

        $navigationBlock = $this->_view->getLayout()->createBlock('Ced\CsMarketplace\Block\Vendor\Navigation');
        if ($navigationBlock) {
            $navigationBlock->setActive('csmarketplace\vreports\vorders');
        }
        $result = $this->resultPageFactory->create(true)->getLayout()
            ->createBlock('Ced\CsMarketplace\Block\Vreports\Vproducts\ListOrders')
            ->setName('csmarketplace_report_vproducts3')
            ->setTemplate('Ced_CsMarketplace::vreports/vproducts/list.phtml')->toHtml();

        $resultJson->setData($result);
        return $resultJson;

    }
}

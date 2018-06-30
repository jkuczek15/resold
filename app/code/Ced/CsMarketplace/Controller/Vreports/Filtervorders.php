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
 * @author        CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsMarketplace\Controller\Vreports;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\UrlFactory;
use Ced\CsMarketplace\Model\Session as MarketplaceSession;

class Filtervorders extends \Ced\CsMarketplace\Controller\Vendor
{

    public $resultJsonFactory;
    public $marketplaceSession;

    /**
     * Filtervorders constructor.
     * @param Context $context
     * @param Session $customerSession
     * @param PageFactory $resultPageFactory
     * @param UrlFactory $urlFactory
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param MarketplaceSession $MarketplaceSession
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        PageFactory $resultPageFactory,
        UrlFactory $urlFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        MarketplaceSession $MarketplaceSession
    ) {
        parent::__construct($context, $customerSession, $resultPageFactory, $urlFactory, $moduleManager);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->marketplaceSession = $MarketplaceSession;
    }

    /**
     * @return bool|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();

        $params1 = $this->getRequest()->getParams();
        $params1 = current($params1);

        $params = [];
        parse_str($params1, $params);

        if (!isset($params['p']) && !isset($params['limit']) && is_array($params)) {
            $this->marketplaceSession->setData('vorders_reports_filter', $params);
        }
        $result = $this->resultPageFactory->create(true)->getLayout()
            ->createBlock('Ced\CsMarketplace\Block\Vreports\Vorders\ListOrders')
            ->setName('csmarketplace_report_orders3')
            ->setTemplate('Ced_CsMarketplace::vreports/vorders/list.phtml')->toHtml();

        return $resultJson->setData($result);
    }
}

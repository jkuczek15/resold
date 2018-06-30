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

namespace Ced\CsMarketplace\Controller\Vendor;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\UrlFactory;

class Chart extends \Ced\CsMarketplace\Controller\Vendor
{

    public $resultJsonFactory;

    public function __construct(
        Context $context,
        Session $customerSession,
        PageFactory $resultPageFactory,
        UrlFactory $urlFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    )
    {
        parent::__construct($context, $customerSession, $resultPageFactory, $urlFactory, $moduleManager);
        $this->resultJsonFactory = $resultJsonFactory;
    }


    /**
     * Default vendor dashboard page
     *
     * @return \Magento\Framework\View\Result\Page
     */


    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        if (!$this->_getSession()->getVendorId()) {
            return false;
        }
        $json = [];

        $json['order'] = [];
        $json['xaxis'] = [];

        $json['order']['label'] = __('Orders');
        $json['order']['data'] = [];

        $range = $this->getRequest()->getParam('range', 'day');
        $customerId = $this->_getSession()->getVendorId();

        $vendor = $this->_objectManager->create('Ced\CsMarketplace\Model\Vendor')->load($customerId);

        $reportHelper = $this->_objectManager->get('Ced\CsMarketplace\Helper\Report');
        if ($vendor && $vendor->getId()) {
            $order = $reportHelper->getChartData($vendor, 'order', $range);

            foreach ($order as $key => $value) {
                $json['order']['data'][] = array($key, $value['total']);
            }
            switch ($range) {
                default:
                case 'day':

                    for ($i = 0; $i < 24; $i++) {
                        $json['xaxis'][] = [$i, $i];
                    }
                    break;
                case 'week':
                    $date_start = strtotime('-' . date('w') . ' days');

                    for ($i = 0; $i < 7; $i++) {
                        $date = date('Y-m-d', $date_start + ($i * 86400));

                        $json['xaxis'][] = [date('w', strtotime($date)), date('D', strtotime($date))];
                    }
                    break;
                case 'month':

                    for ($i = 1; $i <= date('t'); $i++) {
                        $date = date('Y') . '-' . date('m') . '-' . $i;

                        $json['xaxis'][] = [date('j', strtotime($date)), date('d', strtotime($date))];
                    }
                    break;
                case 'year':

                    for ($i = 1; $i <= 12; $i++) {
                        $json['xaxis'][] = [$i, date('M', mktime(0, 0, 0, $i))];
                    }
                    break;
            }
         }

        $resultJson->setData($json);
        return $resultJson;
     }
}

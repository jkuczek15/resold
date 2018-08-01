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
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsMarketplace\Controller\Index;

use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Context     $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
    	if (!$this->_objectManager->create('\Magento\Framework\App\Config\ScopeConfigInterface')->getValue('ced_csmarketplace/general/activation')) {
    		return $this->_redirect('customer/account');
    	}
        $data = $this->getRequest()->getParams();

        if (isset($data['product_list_mode'])) {
            $this->_coreRegistry->register('product_list_mode', $data['product_list_mode']);
        } else {
            if (isset($data['char']) || isset($data['country_id']) || isset($data['estimate_postcode'])) {
                $this->_coreRegistry->register('vendor_name', $data['char']);
                $this->_coreRegistry->register('country', $data['country_id']);
                $this->_coreRegistry->register('zip_code', $data['estimate_postcode']);

            }
            if (isset($data['product_list_dir'])) {
                 $this->_coreRegistry->register('name_filter', $data['product_list_dir']);
            }
        }
        $resultPage = $this->resultPageFactory->create();
        return $resultPage;
    }
}

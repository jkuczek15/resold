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
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsMarketplace\Controller\Vproducts;

class DeleteLink extends \Ced\CsMarketplace\Controller\Vproducts
{
    /**
     *
     *
     * @var \Magento\Framework\View\Result\Page
     */
    protected $resultPageFactory;
    public $resultJsonFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(\Magento\Framework\App\Action\Context $context,
                                \Magento\Customer\Model\Session $customerSession,
                                \Magento\Framework\View\Result\PageFactory $resultPageFactory,
                                \Magento\Framework\UrlFactory $urlFactory,
                                \Magento\Framework\Module\Manager $moduleManager,
                                \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    )
    {

        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context, $customerSession, $resultPageFactory, $urlFactory, $moduleManager);
    }

    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        if (!$this->_getSession()->getVendorId()) {
            return false;
        }
        $result = 0;
        $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')
            ->setCurrentStore(\Magento\Store\Model\Store::DEFAULT_STORE_ID);
        $data = $this->getRequest()->getParams();
        try {
            $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')
                ->setCurrentStore(\Magento\Store\Model\Store::DEFAULT_STORE_ID);
            if (isset($data['linkid'])) {
                $link = $this->_objectManager->get('Magento\Downloadable\Model\Link')->load($data['linkid']);
                if ($link && $link->getId()) {
                    $link->delete();
                    $result = 1;
                }
            }
        } catch (\Exception $e) {
            $result = 0;
        }
        $resultJson->setData($result);
        return $resultJson;

    }
}

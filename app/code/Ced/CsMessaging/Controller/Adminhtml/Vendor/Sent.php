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
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license   http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\CsMessaging\Controller\Adminhtml\Vendor;

use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;

class Sent extends Action
{
    /**
     * @param Context $context
     * @param Builder $productBuilder
     */
    public function __construct(Context $context,PageFactory $resultPageFactory)
    {
        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();

    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();

        $resultPage->setActiveMenu('Ced_CsMessaging::sent');

        $resultPage->getConfig()->getTitle()->prepend(__('Compose Message'));

        return $resultPage;


    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ced_CsMessaging::sentbox');
    }
}
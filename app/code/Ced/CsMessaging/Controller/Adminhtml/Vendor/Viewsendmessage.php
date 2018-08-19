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
namespace Ced\CsMessaging\Controller\Adminhtml\Vendor;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Ced\CsMessaging\Model\MessagingFactory;

class Viewsendmessage extends \Magento\Backend\App\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Chatview constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param MessagingFactory $messagingFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        MessagingFactory $messagingFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_messagingFactory = $messagingFactory;

    }

    /**
     * Index action
     *
     * @return void
     */
    public function execute()
    {
        /**
         * @var \Magento\Backend\Model\View\Result\Page $resultPage
         */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Ced_CsMessaging::grid');
        $resultPage->addBreadcrumb(__('Read Message'), __('Read Message'));
        $resultPage->addBreadcrumb(__('Read Message'), __('Read Message'));
        $resultPage->getConfig()->getTitle()->prepend(__('Read Message'));

        return $resultPage;
    }
}
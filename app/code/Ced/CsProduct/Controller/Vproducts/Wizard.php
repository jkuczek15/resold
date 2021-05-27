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
 * @package     Ced_CsProduct
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\CsProduct\Controller\Vproducts;

use Magento\Framework\Controller\ResultFactory;
use Magento\Catalog\Controller\Adminhtml\Product\Builder;
use Magento\Backend\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Framework\UrlFactory;
/**
 * Class Wizard
 */
class Wizard extends \Ced\CsProduct\Controller\Vproducts
{
    /**
     * @var Builder
     */
    protected $productBuilder;

    /**
     * Wizard constructor.
     * @param Context $context
     * @param Session $customerSession
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param UrlFactory $urlFactory
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param Builder $productBuilder
     */
    public function __construct(
    		Context $context, 
    		Session $customerSession,
    		\Magento\Framework\View\Result\PageFactory $resultPageFactory,
    		UrlFactory $urlFactory,
    		\Magento\Framework\Module\Manager $moduleManager,
    		Builder $productBuilder
)
    {
        parent::__construct($context,$customerSession,$resultPageFactory,$urlFactory,$moduleManager);
        $this->productBuilder = $productBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $this->productBuilder->build($this->getRequest());
        /** @var \Magento\Framework\View\Result\Layout $resultLayout */
        $resultLayout = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultLayout->getLayout()->getUpdate()->removeHandle('default');

        return $resultLayout;
    }
}

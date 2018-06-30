<?php
namespace Ced\CsMarketplace\Controller\Adminhtml\Vproducts;

class Vproductgrid extends \Ced\CsMarketplace\Controller\Adminhtml\Vendor
{
	/**
	 * @var PageFactory
	 */
	protected $resultPageFactory;
	
	/**
	 * @param Context $context
	 * @param PageFactory $resultPageFactory
	 */
	public function __construct(
			\Magento\Backend\App\Action\Context $context,
			\Magento\Framework\View\Result\PageFactory $resultPageFactory
			//\Magento\Framework\App\Response\RedirectInterface $redirect
			
	) {
		$this->resultPageFactory = $resultPageFactory;
		//$this->redirect = $redirect;
		parent::__construct($context);
		
	}
	
	public function execute()
	{
			$redirectUrl = $this->_redirect->getRedirectUrl();
			$actions = explode('vproducts', $redirectUrl);
			$action = explode('/', $actions[1]);
			$actionName = $action[1];
			if($actionName == 'pending'){
				$this->_objectManager->get('Magento\Framework\Registry')->register('usePendingProductFilter', true);
			}
			elseif($actionName == 'approved'){
				$this->_objectManager->get('Magento\Framework\Registry')->register('useApprovedProductFilter', true);
			}
			$resultPage = $this->resultPageFactory->create();
			
			return $resultPage;
		
	}
}

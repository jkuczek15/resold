<?php
namespace Ced\CsMarketplace\Controller\Adminhtml\Vendor;

class Vproductsgrid extends \Ced\CsMarketplace\Controller\Adminhtml\Vendor
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
	) {
		parent::__construct($context);
		$this->resultPageFactory = $resultPageFactory;
	}
	
	public function execute()
	{
			$resultPage = $this->resultPageFactory->create();
			return $resultPage;
		
	}
}
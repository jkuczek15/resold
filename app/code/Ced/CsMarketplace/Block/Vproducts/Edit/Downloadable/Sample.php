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
 
namespace Ced\CsMarketplace\Block\Vproducts\Edit\Downloadable;
use Magento\Framework\UrlFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Result\PageFactory;

class Sample extends \Ced\CsMarketplace\Block\Vendor\AbstractBlock
{
	/**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    public $_objectManager;
	
	protected $urlModel;

	protected $session;
    /**
     * @param Context $context
     * @param Session $customerSession
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
		Session $customerSession,
		\Magento\Framework\ObjectManagerInterface $objectManager,		
		UrlFactory $urlFactory
    ){
		
		$this->session = $customerSession;
		$this->urlModel = $urlFactory;
		$this->_objectManager = $objectManager;
		parent::__construct($context,$customerSession,$objectManager,$urlFactory);
	}
	
	public function getDownloadableProductSamples($_product){
		return $this->_objectManager->get('Magento\Downloadable\Model\Product\Type')->getSamples($_product);
	}
	
	public function getDownloadableHasSamples($_product){
		return $this->_objectManager->get('Magento\Downloadable\Model\Product\Type')->hasSamples($_product);
	}
	
}

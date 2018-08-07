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
 * @package   Ced_CsStripePayment
 * @author    CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\CsStripePayment\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;

class Savepayment implements ObserverInterface
{
    
    protected $request;
    protected $catalogSession;
    protected $_objectManager;
    
    public function __construct(RequestInterface $request,\Magento\Framework\ObjectManagerInterface $ob,\Magento\Framework\Stdlib\DateTime\DateTime $date,\Magento\Catalog\Model\Session $catalogSession)
    {
    	
    	$this->catalogSession = $catalogSession;
        $this->request = $request;
        $this->_objectManager = $ob;
        $this->date = $date;
    }
    /**
     * Save Faq from product edit page
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
    	$order = $observer->getEvent()->getOrder();
    	 
    	$this->_objectManager->create('Ced\CsStripePayment\Model\SetVendorOrder')->setVendorOrder($order,$observer->getOrders());
    	
    	return $this;
    }
    
		
	
}
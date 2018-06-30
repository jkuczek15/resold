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
  * @package   Ced_CsProduct
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
namespace Ced\CsProduct\Observer; 
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;

Class ChangeListLink implements ObserverInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    protected $resultPageFactory;
    protected $_urlManager;
    public function __construct(
        RequestInterface $request,
        \Magento\Framework\UrlInterface $urlManager,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->_objectManager = $objectManager;
        $this->request = $request;
        $this->_urlManager = $urlManager;
    }
    /**
   *redirect on advance product link
     *
   *@param $observer
   */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {   
        if($this->_objectManager->get('Ced\CsProduct\Helper\Data')->isActive()) {
            $controller = $observer->getControllerAction();
            $url = $this->_urlManager->getUrl('csproduct/vproducts/index');
            $this->request->setModuleName('csproduct');
            $controller->getResponse()->setRedirect($url);
        }
    }
}

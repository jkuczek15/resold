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
  * @package   Ced_CsOrder
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license   http://cedcommerce.com/license-agreement.txt
  */

namespace Ced\CsOrder\Observer; 

use Magento\Framework\Event\ObserverInterface;

Class ChangeListLink implements ObserverInterface
{
    protected $helper;
    protected $_urlManager;

    public function __construct(
        \Magento\Framework\UrlInterface $urlManager,
        \Ced\CsOrder\Helper\Data $helper
    ) {
        $this->_urlManager = $urlManager;
        $this->helper = $helper;
    }

    /**
     *redirect on advance order link
     *
     *@param $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {     
        if($this->helper->isActive()) {
            $controller = $observer->getControllerAction();
            $url = $this->_urlManager->getUrl('csorder/vorders/index');
            $controller->getResponse()->setRedirect($url);
        }
    }

}

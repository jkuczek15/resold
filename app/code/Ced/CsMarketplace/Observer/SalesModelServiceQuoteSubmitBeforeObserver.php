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
 * @license     http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\CsMarketplace\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
 
class SalesModelServiceQuoteSubmitBeforeObserver implements ObserverInterface
{
    private $quoteItems = [];
    private $quote = null;
    private $order = null;

    /**
     * Add order information into GA block to render on checkout success pages
     *
     * @param EventObserver $observer
     * @return void
     */
    public function execute(EventObserver $observer)
    {   
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 
	    if ($objectManager->get('Magento\Framework\App\State')->getAreaCode() !== 'adminhtml' ) {
            $this->quote = $observer->getQuote();
            $this->order = $observer->getOrder();
	        foreach ($this->order->getItems() as $orderItem) {
	            if ($quoteItem = $this->getQuoteItemById($orderItem->getQuoteItemId(), $this->quote)) {
	                if ($additionalOptionsQuote = $quoteItem->getOptionByCode('additional_options')) {
	                    if ($additionalOptionsOrder = $orderItem->getProductOptionByCode('additional_options')) {
	                        $additionalOptions = array_merge($additionalOptionsQuote, $additionalOptionsOrder);
	                    } else {
	                        $additionalOptions = $additionalOptionsQuote;
	                    }
	                    if (count($additionalOptions) > 0) {
	                        $options = $orderItem->getProductOptions();
	                        $options['additional_options'] = class_exists(
	                            "\\Magento\\Framework\\Serialize\\Serializer\\Json")? $objectManager->create('\Magento\Framework\Serialize\Serializer\Json')->unserialize($additionalOptions->getValue()) : unserialize($additionalOptions->getValue());
	                        $orderItem->setProductOptions($options);
	                    }
	               }
	           }
	       }
	   }
    }

    private function getQuoteItemById($id, $quote)
    {
        if (empty($this->quoteItems)) {
            /* @var  \Magento\Quote\Model\Quote\Item $item */
            foreach ($quote->getAllItems() as $item) {
                //filter out config/bundle etc product
                $this->quoteItems[$item->getId()] = $item;
            }
        }
        if (array_key_exists($id, $this->quoteItems)) {
            return $this->quoteItems[$id];
        }
        return null;
    }
}


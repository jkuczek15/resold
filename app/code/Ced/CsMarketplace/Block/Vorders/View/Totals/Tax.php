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
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsMarketplace\Block\Vorders\View\Totals;

class Tax extends \Magento\Tax\Block\Sales\Order\Tax
{

	/**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    public $_objectManager;
	
	public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
		\Magento\Framework\Registry $registry,
		\Magento\Framework\ObjectManagerInterface $objectManager,
		\Magento\Tax\Model\Config $taxConfig,
        array $data = []
    ) {
		parent::__construct($context,$taxConfig, $data);
		$this->_objectManager = $objectManager;	
	}

    /**
     * Get full information about taxes applied to order
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getFullTaxInfo()
    {
        /** @var $source Mage_Sales_Model_Order */
        $source = $this->getOrder();
        $info = array();
        if ($source instanceof \Magento\Sales\Model\Order) {

            $rates = $this->_objectManager->create('Magento\Sales\Model\Order\Tax')->getCollection()->loadByOrder($source)->toArray();
            $info  = $this->_objectManager->create('Magento\Tax\Model\Calculation')->reproduceProcess($rates['items']);

            /**
             * Set right tax amount from invoice
             * (In $info tax invalid when invoice is partial)
             */
            $blockInvoice = $this->getLayout()->getBlock('tax');
            /** @var $invoice Magento\Sales\Model\Order\Invoice */
            $invoice = $blockInvoice->getSource();
            $items = $invoice->getItemsCollection();
            $i = 0;
            /** @var $item Magento\Sales\Model\Order\Invoice\Item */
            foreach ($items as $item) {
                $info[$i]['hidden']           = $item->getHiddenTaxAmount();
                $info[$i]['amount']           = $item->getTaxAmount();
                $info[$i]['base_amount']      = $item->getBaseTaxAmount();
                $info[$i]['base_real_amount'] = $item->getBaseTaxAmount();
                $i++;
            }
        }

        return $info;
    }

    /**
     * Display tax amount
     *
     * @return string
     */
    public function displayAmount($amount, $baseAmount)
    {
        return $this->displayPrices(
            $this->getSource(), $baseAmount, $amount, false, '<br />'
        );
    }
    
    /**
     * Get "double" prices html (block with base and place currency)
     *
     * @param   float $basePrice
     * @param   float $price
     * @param   bool $strong
     * @param   string $separator
     * @return  string
     */
    public function displayPrices($dataObject, $basePrice, $price, $strong = false, $separator = '<br/>')
    {
    	$order = false;
    	if ($dataObject instanceof \Magento\Sales\Model\Order) {
    		$order = $dataObject;
    	} else {
    		$order = $dataObject->getOrder();
    	}
        $res = '';
    	if ($order && $order->isCurrencyDifferent()) {
    		$res.= $order->formatBasePrice($basePrice);
    		$res.= $separator;
    		$res.= '['.$order->formatPrice($price).']';
    	} elseif ($order) {
    		$res = $order->formatPrice($price);
    		if ($strong) {
    			$res = $res;
    		}
    	} else {
    		$res = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore(null)->formatPrice($price);
    		if ($strong) {
    			$res = $res;
    		}
    	}
    	return $res;
    }

    /**
     * Get store object for process configuration settings
     *
     * @return Magento\Store\Model\StoreManagerInterface
     */
    public function getStore()
    {
        return $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore(null);
    }
}

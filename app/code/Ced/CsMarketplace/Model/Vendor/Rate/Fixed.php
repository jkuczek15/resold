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

namespace Ced\CsMarketplace\Model\Vendor\Rate;

class Fixed extends \Ced\CsMarketplace\Model\Vendor\Rate\Abstractrate
{

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectInterface
    ) {
        $this->_objectManager=$objectInterface;
    }


    /**
     * Get the commission based on group
     * @param int $grand_total
     * @param int $base_grand_total
     * @param int $base_to_global_rate
     * @param array $commissionSetting
     * @param int $qty
     * @return array
     */
   public function calculateCommission($grand_total = 0, $base_grand_total = 0, $base_to_global_rate = 1, $commissionSetting = array(), $qty = 0) 
    {
        $result = [];
        
        $order = $this->getOrder();
        $fee = 0;
        
        $result['base_fee'] = min($base_grand_total, $commissionSetting['rate']) * $qty;
        $result['fee'] = min(
            $grand_total, $this->_objectManager->get('Magento\Directory\Helper\Data')
                ->currencyConvert($commissionSetting['rate'], $order->getBaseCurrencyCode(), $order->getGlobalCurrencyCode())
        ) * $qty;
        
        
        $itemCommission = isset($commissionSetting['item_commission']) ? $commissionSetting['item_commission'] : [];
        if(count($itemCommission) > 0) {
            unset($commissionSetting['item_commission']);
            $item_commission = array();
            foreach($itemCommission as $itemId=>$base_price) {
                $qty = (int)$this->_objectManager->get('Magento\Quote\Model\Quote\Item')->load($itemId)->getQty();
                $price = $this->_objectManager->get('Magento\Directory\Helper\Data')->currencyConvert($base_price, $order->getBaseCurrencyCode(), $order->getGlobalCurrencyCode());
                $item_commission[$itemId] = $this->calculateCommission($price, $base_price, $base_to_global_rate, $commissionSetting, $qty);
            }
            $result['item_commission'] = json_encode($item_commission);
            foreach ($item_commission as $commission) {
                $fee += $commission['fee'];
            }
            $result['base_fee'] = $fee;
            $result['fee'] = $fee;
        }
        
        
        return $result;
    }
}

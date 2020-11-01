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
  * @license      http://cedcommerce.com/license-agreement.txt
  */

namespace Magento\Sales\Block\Adminhtml\Order\Totals;

/**
 * Totals item block
 */
class Item extends \Magento\Sales\Block\Adminhtml\Order\Totals
{
    /**
     * Determine display parameters before rendering HTML
     *
     * @return $this
     */
    protected function _beforeToHtml()
    {
        parent::_beforeToHtml();

        $this->setCanDisplayTotalPaid($this->getParentBlock()->getCanDisplayTotalPaid());
        $this->setCanDisplayTotalRefunded($this->getParentBlock()->getCanDisplayTotalRefunded());
        $this->setCanDisplayTotalDue($this->getParentBlock()->getCanDisplayTotalDue());

        return $this;
    }

    /**
     * Initialize totals object
     *
     * @return $this
     */
    public function initTotals()
    {
        $total = new \Magento\Framework\DataObject(
            [
                'code' => $this->getNameInLayout(),
                'block_name' => $this->getNameInLayout(),
                'area' => $this->getDisplayArea(),
                'strong' => $this->getStrong(),
            ]
        );
        if ($this->getBeforeCondition()) {
            $this->getParentBlock()->addTotalBefore($total, $this->getBeforeCondition());
        } else {
            $this->getParentBlock()->addTotal($total, $this->getAfterCondition());
        }
        return $this;
    }

    /**
     * Price HTML getter
     *
     * @param  float $baseAmount
     * @param  float $amount
     * @return string
     */
    public function displayPrices($baseAmount, $amount)
    {
        return $this->_adminHelper->displayPrices($this->getOrder(), $baseAmount, $amount);
    }

    /**
     * Price attribute HTML getter
     *
     * @param  string $code
     * @param  bool   $strong
     * @param  string $separator
     * @return string
     */
    public function displayPriceAttribute($code, $strong = false, $separator = '<br/>')
    {
        return $this->_adminHelper->displayPriceAttribute($this->getSource(), $code, $strong, $separator);
    }

    /**
     * Source order getter
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }
}

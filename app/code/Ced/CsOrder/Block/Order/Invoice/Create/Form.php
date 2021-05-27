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
namespace Ced\CsOrder\Block\Order\Invoice\Create;

/**
 * Adminhtml invoice create form
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Form extends \Magento\Sales\Block\Adminhtml\Order\Invoice\Create\Form
{
    /**
     * Get save url
     *
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('csorder/*/save', ['order_id' => $this->getRequest()->getParam('order_id')]);
    }

}

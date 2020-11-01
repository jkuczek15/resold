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
namespace Ced\CsOrder\Block\Order\Creditmemo;

/**
 * Adminhtml creditmemo create
 */
class Create extends \Magento\Sales\Block\Adminhtml\Order\Creditmemo\Create
{
    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry           $registry
     * @param array                                 $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {

        parent::__construct($context, $registry, $data);
        $this->buttonList->remove('reset');

    }

    /**
     * Get back url
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl(
            'csorder/vorders/view',
            ['order_id' => $this->getCreditmemo() ? $this->getRequest()->getParam('order_id') : null]
        );
    }
}

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

namespace Ced\CsMarketplace\Block\Adminhtml;

class Vpayments extends \Magento\Backend\Block\Widget\Container
{
	 /**
     * @var string
     */
    protected $_template = 'vpayments/vpayments.phtml';
 
    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }
 

    /**
     * Prepare button and grid
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
    $this->addButton('assign', array(
            'label'     => __('Credit Amount'),
            'onclick'   => 'setLocation(\'' . $this->_getCreateUrl() .'\')',
            'class'     => 'primary',
        ));
        $this->addButton('go', array(
            'label'     => __('Debit Amount'),
            'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/new',array('type'=>\Ced\CsMarketplace\Model\Vpayment::TRANSACTION_TYPE_DEBIT)) .'\')',
            'class'     => 'primary',
        ));

 
        $this->setChild(
            'grid',
            $this->getLayout()->createBlock('Ced\CsMarketplace\Block\Adminhtml\Vpayments\Grid', 'ced.csmarketplace.vendor.payments.grid')
        );
        return parent::_prepareLayout();
    }
 
    /**
     *
     *
     * @return array
     */
    protected function _getAddButtonOptions()
    {
 
        $splitButtonOptions[] = [
            'label' => __('Add New'),
            'onclick' => "setLocation('" . $this->_getCreateUrl() . "')"
        ];
 
        return $splitButtonOptions;
    }
 
    /**
     *
     *
     * @param string $type
     * @return string
     */
    protected function _getCreateUrl()
    {

        return $this->getUrl(
            '*/*/new'
        );
    }
 
    /**
     * Render grid
     *
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getChildHtml('grid');
    }
}

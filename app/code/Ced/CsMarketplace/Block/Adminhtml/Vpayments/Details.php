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

namespace Ced\CsMarketplace\Block\Adminhtml\Vpayments;
use Magento\Backend\Block\Widget\Context;

class Details extends \Magento\Backend\Block\Widget\Form\Container
{

  protected $_objectManager;
  
  public function __construct(Context $context,
    \Magento\Framework\ObjectManagerInterface $objectManager,
    array $data = [])
    {
    $this->_objectManager = $objectManager;  
    $this->_controller = '';
    

    parent::__construct($context, $data);
    $this->_headerText = __('Transaction Details');
    $this->removeButton('reset')
        ->removeButton('delete')
        ->removeButton('save');

    }

  

    /**
     * Initialize form
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->setChild('form', $this->getLayout()->createBlock('Ced\CsMarketplace\Block\Adminhtml\Vpayments\Details\Form'));
        return $this;
    }
}
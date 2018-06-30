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
 
namespace Ced\CsMarketplace\Model\System\Config\Source ;
 
class Paymentmethods  extends AbstractBlock
{
    
    protected $_context;
    
    protected $_objectManager;
    
    public function __construct(
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory $attrOptionFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($attrOptionCollectionFactory, $attrOptionFactory, $objectManager);
        $this->_context = $context;
        $this->_objectManager = $objectManager;
    }
    

    const XML_PATH_CED_CSMARKETPLACE_VENDOR_PAYMENT_METHODS = 'ced_csmarketplace/vendor/payment_methods';
    /**
     * Retrieve Option values array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $payment_methods = $this->_context->getScopeConfig()->getValue(self::XML_PATH_CED_CSMARKETPLACE_VENDOR_PAYMENT_METHODS);
        $payment_methods = array_keys((array)$payment_methods);
        $options = array();
        foreach($payment_methods as $payment_method) {
            $payment_method = strtolower(trim($payment_method));
            $options[] = array('value'=>$payment_method, 'label'=>__(ucfirst($payment_method)));
        }
        return $options;
    }

}

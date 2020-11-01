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
 * @author 		   CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */
 
namespace Ced\CsMarketplace\Model\System\Config\Source;
 
class Status extends AbstractBlock
{


    public function __construct(
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory $attrOptionFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        parent::__construct($attrOptionCollectionFactory, $attrOptionFactory, $objectManager);
    }

    /**
     * Retrieve Option values array
     *
     * @return array
     */
    public function toOptionArray($defaultValues = false)
    {
        $options = array();
        $new = \Ced\CsMarketplace\Model\Vendor::VENDOR_NEW_STATUS;
        $appove = \Ced\CsMarketplace\Model\Vendor::VENDOR_APPROVED_STATUS;
        $disaprove = \Ced\CsMarketplace\Model\Vendor::VENDOR_DISAPPROVED_STATUS;
       
        if($defaultValues) {
            $options[$new] = __('New'); 
        }
        $options[$appove] = __('Approved');
        $options[$disaprove] = __('Disapproved');        
        return $options;
    }

}

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

namespace Ced\CsMessaging\Block\Adminhtml\Vendor\Entity;

class Grid extends \Ced\CsMarketplace\Block\Adminhtml\Vendor\Entity\Grid
{

    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setTemplate('Magento_Catalog::product/grid/massaction_extended.phtml');
        $this->getMassactionBlock()->setFormFieldName('vendor_id');
 
        $this->getMassactionBlock()->addItem(
            'delete',
            [
                'label' => __('Delete Vendor(s)'),
                'url' => $this->getUrl('*/*/massDelete'),
                'confirm' => __('Are you sure?')
            ]
        );
 
        $statuses = $this->_status->toOptionArray();
		
		$this->getMassactionBlock()->addItem('status',
				[
				'label'=> __('Change Vendor(s) Status'),
				'url'  => $this->getUrl('*/*/massStatus/', ['_current'=>true]),
				'additional' => [
						'visibility' => [
								'name' => 'status',
								'type' => 'select',
								'class' => 'required-entry',
								'label' => ('Status'),
								'default'=>'-1',
								'values' =>$statuses,
						]
				]
				]
		);
		
		$this->getMassactionBlock()->addItem('shop_disable',
			[
  			'label'=>__('Change Vendor Shops'),
  			'url'  => $this->getUrl('*/*/massDisable/', ['_current'=>true]),
  			'additional' => [
  					'visibility' => [
  							'name' => 'shop_disable',
  							'type' => 'select',
  							'class' => 'required-entry',
  							'label' => __('Vendor Shop Status'),
  							'default'=>'-1',
  							'values' =>[
										['value' => \Ced\CsMarketplace\Model\Vshop::ENABLED, 'label'=>__('Enabled')],
										['value' => \Ced\CsMarketplace\Model\Vshop::DISABLED, 'label'=>__('Disabled')]
									],
					]
				]
			]
		);

        $this->getMassactionBlock()->addItem('send_message',
                [
                'label'=> __('Send Message'),
                'url'  => $this->getUrl('csmessaging/vendor/massmessage/', ['_current'=>true]),
                ]
        );
				
        return $this;
    }

}

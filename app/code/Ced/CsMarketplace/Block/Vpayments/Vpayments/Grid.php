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
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */


namespace Ced\CsMarketplace\Block\Vpayments\Vpayments;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended {
    
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
    	\Magento\Customer\Model\Session $session,
    	\Ced\CsMarketplace\Helper\Data $csmarketplaceHelper,
    	array $data = []
    ) {
    	
    	$this->_csMarketplaceHelper = $csmarketplaceHelper;
    	$this->session = $session;
        $this->_objectManager = $objectManager;
        parent::__construct($context, $backendHelper, $data);
        $this->setData('area','adminhtml');
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('marketplacetransactionGrid');
        $this->setDefaultSort('post_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
	    $payments = $this->_objectManager->get('Ced\CsMarketplace\Block\Vpayments\ListBlock')->getVpayments();
    	$this->setCollection($payments);
        parent::_prepareCollection();
        return $this;
    }
    
    protected function _prepareColumns()
    {
    	$this->addColumn(
    			'order_date',
    			[
    			'header' => __('Created At #'),
    			'index' => 'created_at',
    			'type'=>'date'
    			]
    	);
        
        $this->addColumn(
                'payment_method', 
                [
                    'header' => __('Payment Mode'), 
                    'index' => 'payment_method',
                    'type' => 'options',
                    'options' => array(__('Offline'), __('Online'))   
                ]
        );
        
        $this->addColumn(
            'transaction_id', 
                [
                    'header' => __('Transaction Id'), 
                    'index' => 'transaction_id'
                ]
        );
        
        $this->addColumn(
            'amount', 
                [
                    'header' => __('Amount'), 
                    'index' => 'amount',
                    'type' => 'price'
                ]
        );
        
        $this->addColumn(
            'fee', 
                [
                    'header' => __('Adjustment Amount'), 
                    'index' => 'fee',
                    'type' => 'price'
                ]
        );
        
        $this->addColumn(
            'net_amount', 
                [
                    'header' => __('Net Amount'), 
                    'index' => 'net_amount',
                    'type' => 'price'
                ]
        );
        
        $this->addColumn(
            'edits',
                [
                    'header' => __('Action'),
                    'caption' => __('Action'),
                    'sortable'=>false,
                    'filter'=>false,
                    'renderer' => 'Ced\CsMarketplace\Block\Vpayments\Vpayments\Renderer\Action',
                ]
        );
        

        return parent::_prepareColumns();
    }
    
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', ['_current' => true]);
    }

    public function getVendorId() {
    	return $this->session->getVendorId();
    }
}

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
 * @package   Ced_CsMessaging
 * @author    CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsMessaging\Block\Adminhtml\Sent;

use Magento\Backend\Block\Widget\Grid\Extended;

class Grid extends Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Ced\CsMessaging\Model\MessagingFactory
     */
    protected $_messagingFactory;

    /**
     * @var
     */
    protected $_status;

    /**
     * @var
     */
    protected $backendHelper;

    /**
     * Grid constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Ced\CsMessaging\Model\MessagingFactory $messagingFactory
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Ced\CsMessaging\Model\MessagingFactory $messagingFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    )
    {

        $this->_messagingFactory = $messagingFactory;
        $this->moduleManager = $moduleManager;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('gridGrid');
        $this->setDefaultSort('chat_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('grid_record');
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->_messagingFactory->create()->getCollection()
            ->addFieldToFilter('role', 'admin');
        $this->setCollection($collection);
        parent::_prepareCollection();
        return $this;
    }

    /**
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {

        $this->addColumn(
            'receiver_email',
            [
                'header' => __('Receiver'),
                'index' => 'receiver_email',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );

        $this->addColumn(
            'subject',
            [
                'header' => __('Subject'),
                'index' => 'subject',
            ]
        );

        $this->addColumn(
            'postread',
            [
                'header' => __('Status'),
                'index' => 'postread',
            ]
        );


//        $this->addColumn(
//            'edit',
//            [
//                'header' => __('Action'),
//                'type' => 'action',
//                'getter' => 'getSenderId',
//                'actions' => [
//                    [
//                        'caption' => __('Reply'),
//                        'url' => [
//                            'base' => '*/*/adminarea'
//                        ],
//                        'field' => 'sender_id'
//                    ]
//                ],
//                'filter' => false,
//                'sortable' => false,
//                'index' => 'stores',
//                'header_css_class' => 'col-action',
//                'column_css_class' => 'col-action'
//            ]
//        );

        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }

        return parent::_prepareColumns();
    }

    /**
     * @return $this
     */


    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', ['_current' => true]);
    }


}

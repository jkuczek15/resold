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
namespace Ced\CsMessaging\Block\Adminhtml\Message\CustomerInbox\Edit\Tab;

use Magento\Backend\Block\Widget\Grid\Extended;

class Main extends Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Cedcoss\Grid\Model\GridFactory
     */
    protected $_messagingFactory;

    /**
     * @var \Cedcoss\Grid\Model\Status
     */
    protected $_status;

    protected $backendHelper;
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data            $backendHelper
     * @param \Magento\Framework\Module\Manager       $moduleManager
     * @param array                                   $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Ced\CsMessaging\Model\MessagingFactory $messagingFactory,
        //\Cedcoss\Grid\Model\Status $status,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {

        $this->_messagingFactory = $messagingFactory;
        // $this->_status = $status;
        $this->moduleManager = $moduleManager;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('customermsggrid');
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
        $email=$this->_scopeConfig->getValue('trans_email/ident_general/email', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $collection = $this->_messagingFactory->create()->getCollection()
            ->addFieldToFilter('role', 'customer')
            ->addFieldToFilter('receiver_email',$email);
        //$collection->getSelect()->group('vendor_id');
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
            'sender_email',
            [
                'header' => __('Sender'),
                'index' => 'sender_email',
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
            'date',
            [
                'type' => 'date',
                'header' => __('Date'),
                'index' => 'date',
            ]
        );
        $this->addColumn(
            'postread',
            [
                'header' => __('Status'),
                'index' => 'postread',
            ]
        );

        $this->addColumn(
            'edit',
            [
                'header' => __('Action'),
                'type' => 'action',
                'getter' => 'getChatId',
                'actions' => [
                    [
                        'caption' => __('Read More'),
                        'url' => [
                            'base' => '*/customer/chatview'
                        ],
                        'field' => 'chat_id'
                    ]
                ],
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'header_css_class' => 'col-action',
                'column_css_class' => 'col-action'
            ]
        );

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
        return $this->getUrl('*/*/customerinboxgrid', array('_secure'=>true, '_current'=>true));
    }

}

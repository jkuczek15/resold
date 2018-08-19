<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_CsMessaging
 * @author      CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsMessaging\Block\Adminhtml\Vendor\MassMessage\Edit;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\ObjectManagerInterface;
use Ced\CsMessaging\Model\MessagingFactory;
/**
 * Admin page left menu
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{

    protected function _construct()
    {
        parent::_construct();
        $this->setId('vmsg_id');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Compose Message'));

    }

    public function __construct(
        Context $context,
        EncoderInterface $jsonEncoder,
        Session $authSession,
        ObjectManagerInterface $objectManager,
        MessagingFactory $messagingFactory,
        array $data = []
    )
    {
        parent::__construct($context, $jsonEncoder, $authSession, $data);
        $this->_objectManager = $objectManager;
        $this->_messagingFactory = $messagingFactory;
    }
    protected function _prepareLayout()
    {
        $this->addTab('General', array(
            'label'     => __('Compose'),
            'title'     => __('Compose'),
            'content'   => $this->getLayout()->createBlock('Ced\CsMessaging\Block\Adminhtml\Vendor\MassMessage\Edit\Tab\Main')->toHtml(),
        ));

        return parent::_prepareLayout();
    }

}
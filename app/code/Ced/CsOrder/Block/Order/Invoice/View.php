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

namespace Ced\CsOrder\Block\Order\Invoice;

/**
 * Adminhtml invoice create
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
class View extends \Magento\Sales\Block\Adminhtml\Order\Invoice\View
{

	/**
	 * Admin session
	 *
	 * @var \Magento\Backend\Model\Auth\Session
	 */
	protected $_session;
	
	/**
	 * Core registry
	 *
	 * @var \Magento\Framework\Registry
	 */
	protected $_coreRegistry = null;
	
	/**
	 * Backend session
	 *
	 * @var \Magento\Backend\Model\Auth\Session
	 */
	protected $_backendSession;
	
	/**
	 * @param \Magento\Backend\Block\Widget\Context $context
	 * @param \Magento\Backend\Model\Auth\Session $backendSession
	 * @param \Magento\Framework\Registry $registry
	 * @param array $data
	 */
	public function __construct(
			\Magento\Backend\Block\Widget\Context $context,
			\Magento\Backend\Model\Auth\Session $backendSession,
			\Magento\Framework\Registry $registry,
			array $data = []
	) {
		$this->_backendSession = $backendSession;
		$this->_coreRegistry = $registry;
		parent::__construct($context, $backendSession,$registry,$data);
		$this->setData('area','adminhtml');
	}
	/**
	 * Constructor
	 *
	 * @return void
	 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
	 * @SuppressWarnings(PHPMD.NPathComplexity)
	 */
	protected function _construct()
	{
		parent::_construct();
		
		$this->_objectId = 'invoice_id';
		$this->_controller = 'invoice';
		$this->_blockGroup = 'Ced_CsOrder';
		$this->_mode = 'view';
		$this->_session = $this->_backendSession;

		$this->buttonList->remove('save');
		$this->buttonList->remove('reset');
		$this->buttonList->remove('delete');
		$this->buttonList->remove('cancel');
		$this->buttonList->remove('send_notification');
		$this->buttonList->remove('capture');
		$this->buttonList->remove('void');
		if (!$this->getInvoice()) {
			return;
		}
		if ($this->getInvoice()->getId()) {
			
			$this->buttonList->add(
					'print',
					[
					'label' => __('Print'),
					'class' => 'print',
					'onclick' => 'setLocation(\'' . $this->getPrintUrl() . '\')'
					]
			);
		}
	}
    /**
     * Get back url
     *
     * @return string
     */
    public function getBackUrl()
    {
        if($this->getRequest()->getParam('order_id')) {
                return $this->getUrl(
                    'csorder/vorders/view',
                    [
                    'order_id' => $this->getInvoice() ? $this->getRequest()->getParam('order_id')  : null,
                    'active_tab' => 'order_invoices'
                    ]
                );
        }else{
            return $this->getUrl(
                'csorder/invoice/index'
            );
        }
    }

    /**
     * Get capture url
     *
     * @return string
     */
    public function getCaptureUrl()
    {
        return $this->getUrl('csorder/*/capture', ['invoice_id' => $this->getInvoice()->getId()]);
    }

    /**
     * Get void url
     *
     * @return string
     */
    public function getVoidUrl()
    {
        return $this->getUrl('csorder/*/void', ['invoice_id' => $this->getInvoice()->getId()]);
    }

    /**
     * Get cancel url
     *
     * @return string
     */
    public function getCancelUrl()
    {
        return $this->getUrl('csorder/*/cancel', ['invoice_id' => $this->getInvoice()->getId()]);
    }

    /**
     * Get email url
     *
     * @return string
     */
    public function getEmailUrl()
    {
        return $this->getUrl(
            'csorder/*/email',
            ['order_id' => $this->getInvoice()->getOrder()->getId(), 'invoice_id' => $this->getInvoice()->getId()]
        );
    }

    /**
     * Get credit memo url
     *
     * @return string
     */
    public function getCreditMemoUrl()
    {
        return $this->getUrl(
            'csorder/order_creditmemo/start',
            ['order_id' => $this->getInvoice()->getOrder()->getId(), 'invoice_id' => $this->getInvoice()->getId()]
        );
    }

    /**
     * Get print url
     *
     * @return string
     */
    public function getPrintUrl()
    {
        return $this->getUrl('csorder/*/print', ['invoice_id' => $this->getInvoice()->getId()]);
    }

    /**
     * @param bool $flag
     * @return $this
     */
    public function updateBackButtonUrl($flag)
    {
        if ($flag) {
            if ($this->getInvoice()->getBackUrl()) {
                return $this->buttonList->update(
                    'back',
                    'onclick',
                    'setLocation(\'' . $this->getInvoice()->getBackUrl() . '\')'
                );
            }
            return $this->buttonList->update('back', 'onclick', 'setLocation(\'' . $this->getUrl('csorder/invoice/') . '\')');
        }
        return $this;
    }



}

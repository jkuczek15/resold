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
namespace Ced\CsOrder\Controller\Shipment; 
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\UrlFactory;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\App\Filesystem\DirectoryList;

class PrintAction extends \Ced\CsMarketplace\Controller\Vendor
{
    
    
 
    protected $_coreRegistry;

    /**
     * PrintAction constructor.
     * @param Context $context
     * @param Session $customerSession
     * @param PageFactory $resultPageFactory
     * @param UrlFactory $urlFactory
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param FileFactory $fileFactory
     * @param ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        PageFactory $resultPageFactory,
        UrlFactory $urlFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        FileFactory $fileFactory,
        ForwardFactory $resultForwardFactory,
        \Magento\Framework\Registry $registry
    ) {
        $this->_fileFactory = $fileFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->_coreRegistry = $registry;
        parent::__construct($context, $customerSession, $resultPageFactory, $urlFactory, $moduleManager);
    }
    
    

    /**
     * Save invoice
     * We can save only new invoice. Existing invoices are not editable
     *
     * @return \Magento\Framework\Controller\ResultInterface
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function execute()
    {
        $vendorId = $this->session->getVendorId();
        $shipmentId = $this->getRequest()->getParam('shipment_id');
        if ($shipmentId) {
            $shipment = $this->_objectManager->create('Magento\Sales\Model\Order\Shipment')->load($shipmentId);

            $vorder = $this->_objectManager->get('Ced\CsMarketplace\Model\Vorders')->setVendorId($vendorId)->getVorderByShipment($shipment);

            $this->_coreRegistry->register('current_vorder', $vorder);

            
            if ($shipment) {
                $pdf = $this->_objectManager->create(
                    'Ced\CsOrder\Model\Order\Pdf\Shipment'
                )->getPdf(
                    [$shipment]
                );
                $date = $this->_objectManager->get('Magento\Framework\Stdlib\DateTime\DateTime')->date('Y-m-d_H-i-s');
                return $this->_fileFactory->create(
                    'packingslip' . $date . '.pdf',
                    $pdf->render(),
                    DirectoryList::VAR_DIR,
                    'application/pdf'
                );
            }
        } else {
            /**
             *
             *
             * @var \Magento\Backend\Model\View\Result\Forward $resultForward
             */
            $resultForward = $this->resultForwardFactory->create();
            return $resultForward->forward('noroute');
        }
      return false;
    }
}
 

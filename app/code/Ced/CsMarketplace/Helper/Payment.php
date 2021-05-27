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
namespace Ced\CsMarketplace\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

class Payment extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Contains current collection
     *
     * @var string
     */
    protected $_list = null;
    protected $_objectManager = null;
    protected $_filesystem;
    protected $_path = 'export';
    protected $_directory;

    /**
     * Payment constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param Acl $acl
     * @param Filesystem $filesystem
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(\Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Ced\CsMarketplace\Helper\Acl $acl,
        Filesystem $filesystem
    ) {
        $list = $objectManager->get('Ced\CsMarketplace\Block\Vpayments\ListBlock');
        $this->setList($list->getVpayments());
        $this->_filesystem = $filesystem;
        $this->_acl = $acl;
        $this->_objectManager = $objectManager;
        $this->_directory = $this->_filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        parent::__construct($context);
    }
 
    /**
     * Sets current collection
     *
     * @param $query
     */
    public function setList($collection)
    {   
        $this->_list = $collection;
    }
 
    /**
     * Returns indexes of the fetched array as headers for CSV
     *
     * @param  array $products
     * @return array
     */
    protected function _getCsvHeaders($payment)
    {
        $_payment = current($payment);
        $headers = array_keys($_payment->getData());
        return $headers;
    }
 

    /**
     * Generates CSV file with product's list according to the collection in the $this->_list
     * @return array|bool
     * @throws Magento\Framework\Exception\FileSystemException
     */
    public function getVendorCommision()
    {
        if (!is_null($this->_list)) {
            $items = $this->_list->getItems();
            if (count($items) > 0) {
                $name = md5(microtime());
                
                $file = $this->_path . '/' . $name . '.csv';
                $this->_directory->create($this->_path);
                $stream = $this->_directory->openFile($file, 'w+');
                $stream->lock();
                $stream->writeCsv($this->_getCsvHeaders($items));
                $payment_status=$this->_objectManager->get('Ced\CsMarketplace\Model\Vpayment')->getStatuses();
                foreach ($items as $payment) {
                    $payment['transaction_type']=($payment->getData('transaction_type') == 0)?__('Credit Type'):__('Debit Type');
                    $payment['payment_method']=$this->_acl->getDefaultPaymentTypeLabel($payment->getData('payment_method'));
                    $index=$payment['status'];
                    if(isset($index))
                    $payment['status']=$payment_status[$index]->getText();
                    $stream->writeCsv($payment->getData());
                }
                return [
                    'type'  => 'filename',
                    'value' => $file,
                    'rm'    => true // can delete file after use
                ];
            }
        }
        return false;
    }
    
    public function _getTransactionsStats($vendor) 
    {
        $this->_vendor = $vendor;
        if ($this->_vendor != null && $this->_vendor && $this->_vendor->getId()) {
            $model = $this->_vendor->getAssociatedOrders();
            $model->getSelect()
                    ->reset(\Zend_Db_Select::COLUMNS)
                    ->columns('payment_state')
                    ->columns('COUNT(*) as count')
                    ->columns('SUM(order_total) as order_total')
                    ->columns('(SUM(order_total) - SUM(shop_commission_fee)) AS net_amount')
                    ->where("order_payment_state='".\Magento\Sales\Model\Order\Invoice::STATE_PAID."'")
                    ->group("payment_state");
            return $model && count($model)?$model:[];
        }
        return false;
    }
}

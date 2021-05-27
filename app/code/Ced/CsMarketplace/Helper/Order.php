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
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsMarketplace\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

class Order extends \Magento\Framework\App\Helper\AbstractHelper
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

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        Filesystem $filesystem
    ) {
        $list = $objectManager->get('Ced\CsMarketplace\Block\Vorders\ListOrders');
        $this->setList($list->getVorders());
        $this->_filesystem = $filesystem;
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
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function getCsvData()
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
                $statusArray = $this->_objectManager->get('Magento\Sales\Model\Order\Invoice')->getStates();
                $paymentarray = $this->_objectManager->get('Ced\CsMarketplace\Model\Vorders')->getStates();
                foreach ($items as $payment) {
                    if (isset($payment['order_payment_state']))
                        $payment['order_payment_state'] = isset($statusArray[$payment['order_payment_state']]) ? $statusArray[$payment['order_payment_state']] : "";
                    if (isset($payment['payment_state']))
                        $payment['payment_state'] = isset($paymentarray[$payment['payment_state']]) ? $paymentarray[$payment['payment_state']] : "";
                    $stream->writeCsv($payment->getData());
                }
                return [
                    'type' => 'filename',
                    'value' => $file,
                    'rm' => true // can delete file after use
                ];
            }
        }
        return false;
    }
}

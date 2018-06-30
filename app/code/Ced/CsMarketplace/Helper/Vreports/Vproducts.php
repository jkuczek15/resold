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
 * @license     http://cedcommerce.com/license-agreement.txt
 */ 
 
namespace Ced\CsMarketplace\Helper\Vreports;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

class Vproducts extends \Magento\Framework\App\Helper\AbstractHelper
{

    protected $_list = null;
    protected $_objectManager = null;
    protected $_filesystem;
    protected $_path = 'export';
    protected $_directory;

    public function __construct(\Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        Filesystem $filesystem
    ) {
        $this->_objectManager = $objectManager;
        $list = $this->_objectManager->create('Ced\CsMarketplace\Block\Vreports\Vproducts\ListOrders');
        $this->setList($list->getVproductsReports());
        $this->_filesystem = $filesystem;
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
             
                foreach ($items as $payment) {
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
    
}

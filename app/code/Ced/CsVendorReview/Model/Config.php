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
 * @package   Ced_CsVendorReview
 * @author    CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license   http://cedcommerce.com/license-agreement.txt
 */


namespace Ced\CsVendorReview\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\ValueFactory;
use Magento\Framework\App\Config\ValueInterface;
use Magento\Framework\DataObject;
use Magento\Framework\DB\Transaction;
use Magento\Store\Model\StoreManagerInterface;

class Config extends DataObject
{

    protected $_storeManager;
    
    protected $_scopeConfig;

    protected $_backendModel;
    
    protected $_transaction;

    protected $_configValueFactory;
    
    protected $_storeId;
    
    protected $_storeCode;


    public function __construct(
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        ValueInterface $backendModel,
        Transaction $transaction,
        ValueFactory $configValueFactory,
        array $data = []
    ) {
        parent::__construct($data);
        $this->_storeManager = $storeManager;
        $this->_scopeConfig = $scopeConfig;
        $this->_backendModel = $backendModel;
        $this->_transaction = $transaction;
        $this->_configValueFactory = $configValueFactory;
        $this->_storeId=(int)$this->_storeManager->getStore()->getId();
        $this->_storeCode=$this->_storeManager->getStore()->getCode();
    }
    
    
    public function getCurrentStoreConfigValue($path)
    {
        return $this->_scopeConfig->getValue($path, 'store', $this->_storeCode);
    }
    
    
    public function setCurrentStoreConfigValue($path, $value)
    {
        $data = [
                    'path' => $path,
                    'scope' =>  'stores',
                    'scope_id' => $this->_storeId,
                    'scope_code' => $this->_storeCode,
                    'value' => $value,
                ];

        $this->_backendModel->addData($data);
        $this->_transaction->addObject($this->_backendModel);
        $this->_transaction->save();
    }
}

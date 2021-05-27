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
 
namespace Ced\CsMarketplace\Model\ResourceModel\Vendor;

/**
 * Customers collection
 *
 * @author                                         Magento Core Team <core@magentocommerce.com>
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Collection extends \Magento\Eav\Model\Entity\Collection\VersionControl\AbstractCollection
{
    /**
     * Name of collection model
     */
    const CUSTOMER_MODEL_NAME = 'Ced\CsMarketplace\Model\Vendor';

    /**
     * @var \Magento\Framework\Object\Copy\Config
     */
    protected $_fieldsetConfig;

    /**
     * @var string
     */
    protected $_modelName;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactory                  $entityFactory
     * @param \Psr\Log\LoggerInterface                                          $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface      $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface                         $eventManager
     * @param \Magento\Eav\Model\Config                                         $eavConfig
     * @param \Magento\Framework\App\Resource                                   $resource
     * @param \Magento\Eav\Model\EntityFactory                                  $eavEntityFactory
     * @param \Magento\Eav\Model\ResourceModel\Helper                           $resourceHelper
     * @param \Magento\Framework\Validator\UniversalFactory                     $universalFactory
     * @param \Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot $entitySnapshot
     * @param \Magento\Framework\Object\Copy\Config                             $fieldsetConfig
     * @param \Zend_Db_Adapter_Abstract                                         $connection
     * @param string                                                            $modelName
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Eav\Model\EntityFactory $eavEntityFactory,
        \Magento\Eav\Model\ResourceModel\Helper $resourceHelper,
        \Magento\Framework\Validator\UniversalFactory $universalFactory,
        \Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot $entitySnapshot,
        \Magento\Framework\DataObject\Copy\Config $fieldsetConfig,
        \Magento\Framework\ObjectManagerInterface $objectInterface,
        $connection = null,
        $modelName = self::CUSTOMER_MODEL_NAME
    ) {
    	$this->_objectmanager = $objectInterface;
        $this->_fieldsetConfig = $fieldsetConfig;
        $this->_modelName = $modelName;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $eavConfig,
            $resource,
            $eavEntityFactory,
            $resourceHelper,
            $universalFactory,
            $entitySnapshot,
            $connection
        );
    }

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init($this->_modelName, 'Ced\CsMarketplace\Model\ResourceModel\Vendor');
    }


    /**
     * Get SQL for get record count
     *
     * @return \Magento\Framework\DB\Select
     */
    public function getSelectCountSql()
    {
        $select = parent::getSelectCountSql();
        $select->resetJoinLeft();

        return $select;
    }

    /**
     * Reset left join
     *
     * @param  int $limit
     * @param  int $offset
     * @return \Magento\Eav\Model\Entity\Collection\AbstractCollection
     */
    protected function _getAllIdsSelect($limit = null, $offset = null)
    {
        $idsSelect = parent::_getAllIdsSelect($limit, $offset);
        $idsSelect->resetJoinLeft();
        return $idsSelect;
    }
    

    /**
     * Retrieve Option values array
     * @param int $vendor_id
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function toOptionArray($vendor_id = 0)
    {
        $options = array();
        $vendors = $this->addAttributeToSelect(array('name','email'));
        if($vendor_id) {
            $vendors->addAttributeToFilter('entity_id', array('eq'=>(int)$vendor_id));
        }
        $options['']=__('-- please select vendor --');
        foreach($vendors as $vendor) {
            $options[$vendor->getId()] = $vendor->getName().' ('.$vendor->getEmail().')';
        }
        return $options;
    }

    /**
     * Retrieve Option values array for payment
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function toOptionpayArray()
    {
    	$vorders = $this->_objectmanager->create('Ced\Csmarketplace\Model\Vorders')
    	            ->getCollection()
    	            ->addFieldToFilter('payment_state',\Ced\CsMarketplace\Model\Vpayment::PAYMENT_STATUS_OPEN)
    	            ->addFieldToFilter('order_payment_state',\Ced\CsMarketplace\Model\Vpayment::PAYMENT_STATUS_PAID)
    	            ->addFieldToFilter('order_payment_state',\Ced\CsMarketplace\Model\Vpayment::PAYMENT_STATUS_REFUND);
    	$vorders->getSelect()->group('vendor_id');
    	$verdersArray = $vorders->getColumnValues('vendor_id');
    	
    	$options = array();
    	$vendors = $this->addAttributeToSelect(array('name','email'));
    	
    	if(!empty($verdersArray)){
    		$vendors->addAttributeToFilter('entity_id', ['in'=>$verdersArray]);
    	}
    	$options['']=__('-- please select vendor --');
    	foreach($vendors as $vendor) {
    		$options[$vendor->getId()] = $vendor->getName().' ('.$vendor->getEmail().')';
    	}
    	return $options;
    }
}

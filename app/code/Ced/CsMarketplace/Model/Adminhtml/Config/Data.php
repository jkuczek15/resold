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
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsMarketplace\Model\Adminhtml\Config;
class Data extends \Magento\Config\Model\Config
{
    /**
     * Save config section
     * Require set: section, website, store and groups
     *
     * @return Mage_Adminhtml_Model_Config_Data
     */
    protected $_objectManager;
    /**
     * Config data for sections
     *
     * @var array
     */
    protected $_configData;

    /**
     * Event dispatcher
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * System configuration structure
     *
     * @var \Magento\Config\Model\Config\Structure
     */
    protected $_configStructure;

    /**
     * Application config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_appConfig;

    /**
     * Global factory
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_objectFactory;

    /**
     * TransactionFactory
     *
     * @var \Magento\Framework\DB\TransactionFactory
     */
    protected $_transactionFactory;

    /**
     * Config data loader
     *
     * @var \Magento\Config\Model\Config\Loader
     */
    protected $_configLoader;

    /**
     * Config data factory
     *
     * @var \Magento\Framework\App\Config\ValueFactory
     */
    protected $_configValueFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    protected $_request;

    /**
     * @param \Magento\Framework\App\Config\ReinitableConfigInterface $config
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Config\Model\Config\Structure $configStructure
     * @param \Magento\Framework\DB\TransactionFactory $transactionFactory
     * @param \Magento\Config\Model\Config\Loader $configLoader
     * @param \Magento\Framework\App\Config\ValueFactory $configValueFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ReinitableConfigInterface $config,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Config\Model\Config\Structure $configStructure,
        \Magento\Framework\DB\TransactionFactory $transactionFactory,
        \Magento\Config\Model\Config\Loader $configLoader,
        \Magento\Framework\App\Config\ValueFactory $configValueFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Request\Http $request,
        array $data = []
    )
    {

        parent::__construct($config, $eventManager, $configStructure, $transactionFactory, $configLoader, $configValueFactory, $storeManager, $data);

        $this->_objectManager = $objectManager;
        $this->_scopeConfig = $scopeConfig;
        $this->_request = $request;
    }

    protected function _processGroup(
        $groupId,
        array $groupData,
        array $groups,
        $sectionPath,
        array &$extraOldGroups,
        array &$oldConfig,
        \Magento\Framework\DB\Transaction $saveTransaction,
        \Magento\Framework\DB\Transaction $deleteTransaction
    )
    {


        $groupPath = $sectionPath . '/' . $groupId;
        $scope = $this->getScope();
        $scopeId = $this->getScopeId();
        $scopeCode = $this->getScopeCode();


        /**
         *
         * Map field names if they were cloned
         */
        /** @var $group \Magento\Config\Model\Config\Structure\Element\Group */
        $group = $this->_configStructure->getElement($groupPath);


        // set value for group field entry by fieldname
        // use extra memory
        $fieldsetData = [];
        if (isset($groupData['fields'])) {
            if ($group->shouldCloneFields()) {
                $cloneModel = $group->getCloneModel();
                $mappedFields = [];

                /** @var $field \Magento\Config\Model\Config\Structure\Element\Field */
                foreach ($group->getChildren() as $field) {
                    foreach ($cloneModel->getPrefixes() as $prefix) {
                        $mappedFields[$prefix['field'] . $field->getId()] = $field->getId();
                    }
                }
            }
            foreach ($groupData['fields'] as $fieldId => $fieldData) {
                $fieldsetData[$fieldId] = is_array(
                    $fieldData
                ) && isset(
                    $fieldData['value']
                ) ? $fieldData['value'] : null;
            }

            foreach ($groupData['fields'] as $fieldId => $fieldData) {
                $originalFieldId = $fieldId;
                if ($group->shouldCloneFields() && isset($mappedFields[$fieldId])) {
                    $originalFieldId = $mappedFields[$fieldId];
                }
                /** @var $field \Magento\Config\Model\Config\Structure\Element\Field */
                $field = $this->_configStructure->getElement($groupPath . '/' . $originalFieldId);

                /** @var \Magento\Framework\App\Config\ValueInterface $backendModel */
                $backendModel = $field->hasBackendModel() ? $field
                    ->getBackendModel() : $this
                    ->_configValueFactory
                    ->create();

                $data = [
                    'field' => $fieldId,
                    'groups' => $groups,
                    'group_id' => $group->getId(),
                    'scope' => $scope,
                    'scope_id' => $scopeId,
                    'scope_code' => $scopeCode,
                    'field_config' => $field->getData(),
                    'fieldset_data' => $fieldsetData
                ];


                $backendModel->addData($data);

                $this->_checkSingleStoreMode($field, $backendModel);

                if (false == isset($fieldData['value'])) {
                    $fieldData['value'] = null;
                }

                $path = $field->getGroupPath() . '/' . $fieldId;
                $vendorId = $this->_objectManager->get('\Magento\Framework\Registry')->registry('data_com');

                /**
                 * Look for custom defined field path
                 */
                if ($field && $field->getConfigPath()) {
                    $configPath = $field->getConfigPath();
                    if (!empty($configPath) && strrpos($configPath, '/') > 0) {
                        // Extend old data with specified section group
                        $configGroupPath = substr($configPath, 0, strrpos($configPath, '/'));
                        if ($this->_objectManager->get('Magento\Framework\Module\Manager')->isEnabled('Ced_CsCommission')) {
                            if ($vendorId) {
                                $configGroupPath = $vendorId . '/' . $configGroupPath;
                            }
                        }
                        if (!isset($extraOldGroups[$configGroupPath])) {
                            $oldConfig = $this->extendConfig($configGroupPath, true, $oldConfig);
                            $extraOldGroups[$configGroupPath] = true;
                        }
                        $path = $configPath;

                    }
                }

                $inherit = !empty($fieldData['inherit']);
                $oldpath = $path;
                $vendorId = $this->_objectManager->get('\Magento\Framework\Registry')->registry('data_com');

                if ($vendorId) {
                    $path = $vendorId . '/' . $path;
                }

                $groupDatas = $this->_request->getPost();
                $gcode = isset($groupDatas['group_code']) && strlen($groupDatas['group_code']) > 0 ? $groupDatas['group_code'] : ($this->_request->getParam('gcode', false) ? $this->_request->getParam('gcode') : '');
                if (strlen($gcode) > 0) {
                    $path = $gcode . '/' . $path;
                }
                $backendModel->setPath($path)->setValue($fieldData['value']);
                if (isset($oldConfig[$path])) {
                    $backendModel->setConfigId($oldConfig[$path]['config_id']);

                    /**
                     * Delete config data if inherit
                     */
                    if (!$inherit) {
                        $saveTransaction->addObject($backendModel);
                    } else {
                        $deleteTransaction->addObject($backendModel);
                    }
                }
                if (isset($oldConfig[$oldpath])) {
                    $backendModel->setConfigId($oldConfig[$oldpath]['config_id']);

                    /**
                     * Delete config data if inherit
                     */
                    if (!$inherit) {
                        $saveTransaction->addObject($backendModel);
                    } else {
                        $deleteTransaction->addObject($backendModel);
                    }
                } elseif (!$inherit) {
                    $backendModel->unsConfigId();
                    $saveTransaction->addObject($backendModel);
                }
                $deleteTransaction->delete();
                $saveTransaction->save();
            }
        }

        if (isset($groupData['groups'])) {
            foreach ($groupData['groups'] as $subGroupId => $subGroupData) {
                $this->_processGroup(
                    $subGroupId,
                    $subGroupData,
                    $groups,
                    $groupPath,
                    $extraOldGroups,
                    $oldConfig,
                    $saveTransaction,
                    $deleteTransaction
                );
            }
        }

    }

    public function load()
    {
        $is_csgroup = $this->_request->getParams('is_csgroup');
        if (!$is_csgroup) return parent::load();
        $this->initScope();
        $this->_configData = $this->_getConfig(false);
        return $this->_configData;
    }

    public function extendConfig($path, $full = true, $oldConfig = [])
    {
        $extended = $this->getConfigByPath($path, $this->getScope(), $this->getScopeId(), $full);
        if (is_array($oldConfig) && !empty($oldConfig)) {
            return $oldConfig + $extended;
        }
        return $extended;
    }

    /**
     * Add data by path section/group/field
     *
     * @param string $path
     * @param mixed $value
     * @return void
     * @throws \UnexpectedValueException
     */
    public function setDataByPath($path, $value)
    {
        $path = trim($path);
        if ($path === '') {
            throw new \UnexpectedValueException('Path must not be empty');
        }
        $pathParts = explode('/', $path);
        $keyDepth = count($pathParts);
        if ($keyDepth !== 3) {
            throw new \UnexpectedValueException(
                "Allowed depth of configuration is 3 (<section>/<group>/<field>). Your configuration depth is "
                . $keyDepth . " for path '$path'"
            );
        }
        $data = [
            'section' => $pathParts[0],
            'groups' => [
                $pathParts[1] => [
                    'fields' => [
                        $pathParts[2] => ['value' => $value],
                    ],
                ],
            ],
        ];
        $this->addData($data);
    }


    /**
     * Get scope name and scopeId
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function initScope()
    {
        $is_csgroup = $this->_request->getParams('is_csgroup');
        if (!$is_csgroup) {
            if ($this->getSection() === null) {
                $this->setSection('');
            }
            if ($this->getWebsite() === null) {
                $this->setWebsite('');
            }
            if ($this->getStore() === null) {
                $this->setStore('');
            }

            if ($this->getStore()) {
                $scope = 'stores';
                $store = $this->_storeManager->getStore($this->getStore());
                $scopeId = (int)$store->getId();
                $scopeCode = $store->getCode();
            } elseif ($this->getWebsite()) {
                $scope = 'websites';
                $website = $this->_storeManager->getWebsite($this->getWebsite());
                $scopeId = (int)$website->getId();
                $scopeCode = $website->getCode();
            } else {
                $scope = 'default';
                $scopeId = 0;
                $scopeCode = '';
            }
            $this->setScope($scope);
            $this->setScopeId($scopeId);
            $this->setScopeCode($scopeCode);
        } else {
            if ($this->getStore()) {
                $scope = 'stores';
                $store = $this->_storeManager->getStore($this->getStore());
                $scopeId = (int)$store->getId();
                $scopeCode = $store->getCode();
            } elseif ($this->getWebsite()) {
                $scope = 'websites';
                $website = $this->_storeManager->getWebsite($this->getWebsite());
                $scopeId = (int)$website->getId();
                $scopeCode = $website->getCode();
            } else {
                $scope = 'default';
                $scopeId = 0;
                $scopeCode = '';
            }
            $this->setScope($scope);
            $this->setScopeId($scopeId);
            $this->setScopeCode($scopeCode);
        }

    }

    /**
     * Return formatted config data for current section
     *
     * @param bool $full Simple config structure or not
     * @return array
     */
    protected function _getConfig($full = true)
    {
        $is_csgroup = $this->_request->getParams('is_csgroup');
        if (!$is_csgroup) return parent::_getConfig($full);
        $groupData = $this->_request->getPost();
        $gcode = isset($groupData['group_code']) && strlen($groupData['group_code']) > 0 ? $groupData['group_code'] : ($this->_request->getParam('gcode', false) ? $this->_request->getParam('gcode') : '');
        if (strlen($gcode) > 0) {
            return $this->getConfigByPath($this->getSection(), $this->getScopeId(), $full);
        } else {
            return parent::_getConfig($full);
        }
    }

    /**
     * Set correct scope if isSingleStoreMode = true
     *
     * @param \Magento\Config\Model\Config\Structure\Element\Field $fieldConfig
     * @param \Magento\Framework\App\Config\ValueInterface $dataObject
     * @return void
     */
    protected function _checkSingleStoreMode(
        \Magento\Config\Model\Config\Structure\Element\Field $fieldConfig,
        $dataObject
    )
    {
        $isSingleStoreMode = $this->_storeManager->isSingleStoreMode();
        if (!$isSingleStoreMode) {
            return;
        }
        if (!$fieldConfig->showInDefault()) {
            $websites = $this->_storeManager->getWebsites();
            $singleStoreWebsite = array_shift($websites);
            $dataObject->setScope('websites');
            $dataObject->setWebsiteCode($singleStoreWebsite->getCode());
            $dataObject->setScopeCode($singleStoreWebsite->getCode());
            $dataObject->setScopeId($singleStoreWebsite->getId());
        }
    }

    /**
     * Get config data value
     *
     * @param string $path
     * @param null|bool &$inherit
     * @param null|array $configData
     * @return \Magento\Framework\Simplexml\Element
     */
    public function getConfigDataValue($path, &$inherit = null, $configData = null)
    {
        $this->load();
        if ($configData === null) {
            $configData = $this->_configData;
        }

        if (isset($configData[$path])) {
            $data = $configData[$path];
            $inherit = false;
        } else {
            $data = $this->_appConfig->getValue($path, $this->getScope(), $this->getScopeCode());
            $inherit = true;
        }

        return $data;
    }

    protected function getConfigByPath($path, $scope, $scopeId, $full = true)
    {
        $is_csgroup = $this->_request->getParams('is_csgroup');

        switch ($is_csgroup) {
            case 1:
                $groupData = $this->_request->getPost();
                $gcode = isset($groupData['group_code']) && strlen($groupData['group_code']) > 0 ? $groupData['group_code'] : ($this->_request->getParam('gcode', false) ? $this->_request->getParam('gcode') : '');
                if (strlen($gcode) > 0) {
                    $path = $gcode . '/' . $path;
                }
                break;
            case 2 :
                $vendorId = $this->_request->getParam('vendor_id', 0);
                $vendor = $this->_objectManager->get('Ced\CsMarketplace\Model\Vendor')->load($vendorId);
                if ($vendor && $vendor->getId()) {
                    $path = $vendor->getId() . '/' . $path;
                }
        }

        $configDataCollection = $this->_configValueFactory->create()->getCollection()
                                ->addScopeFilter($this->getScope(), $this->getScopeId(), $path);

        $config = [];
        foreach ($configDataCollection as $data) {
            if ($full) {
                $config[$data->getPath()] = array(
                    'path' => $data->getPath(),
                    'value' => $data->getValue(),
                    'config_id' => $data->getConfigId()
                );
            } else {
                $config[$data->getPath()] = $data->getValue();
            }
        }
        return $config;
    }


}
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

class Acl extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_defaultAclValues = null;
    protected $_storeId = 0;
    protected $_order = null;
    protected $_vendorId = null;
    public $request;

    const XML_PATH_CED_CSMARKETPLACE_CONFIG = 'global/ced_csmarketplace/vendor/config';

    public static $PAYMENT_MODES = ['Offline', 'Online'];

    protected $_storeManager;
    protected $_scopeConfig;
    protected $_objectManager;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectInterface,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        RequestInterface $request
    )
    {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
        $this->request = $request;
        $this->_scopeConfig = $this->scopeConfig;
        $this->_objectManager = $objectInterface;
    }


    /**
     * Set a specified store ID value
     *
     * @param  int $store
     * @return $this
     */
    public function setStoreId($store)
    {
        $this->_storeId = $store;
        return $this;
    }

    /**
     *  Set Order
     *
     * @param  Ced_CsMarketplace_Model_Vorders
     * @return Ced_CsMarketplace_Helper_Acl
     */
    public function setOrder($order)
    {
        $this->_order = $order;
        return $this;
    }

    /**
     *  Get Order
     *
     * @return Ced_CsMarketplace_Model_Vorders
     */
    public function getOrder($order = null)
    {
        if ($this->_order == null && $order != null) {
            $this->_order = $order;
        }
        return $this->_order;
    }

    /**
     *  Set Order
     *
     * @param  Ced_CsMarketplace_Model_Vorders
     * @return Ced_CsMarketplace_Helper_Acl
     */
    public function setVendorId($vendorId = 0)
    {
        $this->_vendorId = $vendorId;
        return $this;
    }

    /**
     *  Get Order
     *
     * @return Ced_CsMarketplace_Model_Vorders
     */
    public function getVendorId($vendorId = null)
    {
        if ($this->_vendorId == null && $vendorId != null) {
            $this->_vendorId = $vendorId;
        }
        return $this->_vendorId;
    }

    /**
     * Check the system availability
     *
     * @return boolean true|false
     */
    public function isEnabled()
    {
        return $this->_scopeConfig->getValue('ced_csmarketplace/general/activation', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->getStore()->getId());
    }

    /**
     * Get current store
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        $params = $this->request->getParams();
        if ($this->_storeId) {
            $storeId = (int)$this->_storeId;
        } else {
            $storeId = isset($params['store']) ? (int)$params['store'] : null;
        }
        return $this->_storeManager->getStore($storeId);
    }

    /**
     * Get the default group setting
     *
     * @return String
     */
    public function getDefaultGroup()
    {
        return $this->_scopeConfig
            ->getValue(
                'ced_csmarketplace/vendor/group',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $this->getStore()->getId()
            );
    }

    /**
     * Get the default payment type
     *
     * @return String
     */
    public function getDefaultPaymentType()
    {
        return $this->_scopeConfig
        ->getValue(
            'vendor_vpayments/general/online',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->getStore()->getId()
        );
    }

    /**
     * Get the default payment type
     *
     * @return String
     */
    public function getDefaultPaymentTypeLabel($mode = null)
    {
        if ($mode == null || $mode == '') {
            $mode = $this->getDefaultPaymentType();
        }
        return isset(self::$PAYMENT_MODES[$mode]) ? __(self::$PAYMENT_MODES[$mode]) : __('Offline');
    }

    /**
     * Get the default payment type
     *
     * @return String
     */
    public function getDefaultPaymentTypeValue($name = null)
    {
        if ($name == null || $name == '') {
            $name = __('Offline');
        }
        $values = array();
        foreach (self::$PAYMENT_MODES as $mid => $mname) {
            $mname = __($mname);
            if (preg_match('/' . $name . '/i', $mname)) {
                $values[] = $mid;
            }
        }
        return $values;
    }

    /**
     * Get the default commission mode
     *
     * @return String
     */
    public function getDefaultCommissionMode()
    {
        return $this->_scopeConfig->getValue('ced_vpayments/general/commission_mode', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->getStore()->getId());
    }

    /**
     * Get the default commission fee
     *
     * @return String
     */
    public function getDefaultCommissionFee()
    {
        return $this->_scopeConfig->getValue('ced_vpayments/general/commission_fee', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->getStore()->getId());
    }

    /**
     * Default ACL
     * Default ACL can be override by Group ACLs
     *
     * @return array
     */
    public function getDefultAclValues()
    {
        $storeId = $this->getStore()->getId();
        if ($this->_defaultAclValues == null) {
            if ($this->getIsApprovalRequired($storeId)) {
                $this->_defaultAclValues ['status'] = \Ced\CsMarketplace\Model\Vendor::VENDOR_NEW_STATUS;
            } else {
                $this->_defaultAclValues ['status'] = \Ced\CsMarketplace\Model\Vendor::VENDOR_APPROVED_STATUS;
            }
            $this->_defaultAclValues['group'] = $this->getDefaultGroup();
        }
        return $this->_defaultAclValues;
    }

    /**
     * Admin approval required or not (Default ACL)
     *
     * @param  int $storeId
     * @return boolean true|false
     */
    public function getIsApprovalRequired($storeId = 0)
    {
        if (!$storeId) {
            $storeId = $this->getStore()->getId();
        }
        return $this->_scopeConfig->getValue('ced_csmarketplace/general/confirmation', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * Get the commission Setting based on group
     *
     * @param  int $vendor_id
     * @return array
     */
    public function getCommissionSettings($vendor_id = 0)
    {
        $vendor = $this->_objectManager->get('Ced\CsMarketplace\Model\Vendor')->load($vendor_id);
        $groupCode = $this->getDefaultGroup();
        if ($vendor && $vendor->getId()) {
            $groupCode = $vendor->getGroup();
            if ($groupCode) {
                $groups = $this->_objectManager->get('Ced\CsMarketplace\Model\System\Config\Source\Group')->getGroups();
                if (isset($groups[$groupCode]['model'])) {
                    $group = $groups[$groupCode]['model'];
                } else {
                    $group = 'csmarketplace/vendor_group_' . strtolower($groupCode);
                }

                try {
                    $group = $this->_objectManager->get($group);
                } catch (\Exception $e) {
                }

                if (is_object($group) && $settings = $group->getCommissionSettings($vendor)) {
                    return $settings;
                }
            }
        }
        return array('type' => $this->getDefaultCommissionMode(), 'rate' => $this->getDefaultCommissionFee(), 'group' => $groupCode);
    }

    /**
     * Get the commission based on group
     *
     * @param  float $grand_total
     * @param  float $base_grand_total
     * @param  string $currency
     * @param  array $commissionSetting
     * @return array
     */
    public function calculateCommission($grand_total = 0, $base_grand_total = 0, $base_to_global_rate = 1, $commissionSetting = array())
    {
        try {
            $order = $this->getOrder();
            $vendorId = $this->getVendorId();
            /* Set default commission settings */
            if (!isset($commissionSetting['type'])) {
                $commissionSetting['type'] = $this->getDefaultCommissionMode();
            }
            if (!isset($commissionSetting['rate'])) {
                $commissionSetting['rate'] = $this->getDefaultCommissionFee();
            }
            if (!isset($commissionSetting['group'])) {
                $commissionSetting['group'] = $this->getDefaultGroup();
            }

            /* print_r($commissionSetting);die; */
            if ($grand_total > 0) {
                if ($base_grand_total <= 0) {
                    $base_grand_total = $grand_total;
                }
                $rates = $this->_objectManager->get('Ced\CsMarketplace\Model\System\Config\Source\Rate')->getRates();

                if (isset($rates[$commissionSetting['type']]['model'])) {
                    $rate = $this->_objectManager->get($rates[$commissionSetting['type']]['model']);

                } else {
                    $rate = $this->_objectManager->get('csmarketplace/vendor_rate_' . strtolower($commissionSetting['type']));

                }
                $rate->setOrder($order);
                $rate->setVendorId($vendorId);
                /* echo $rate->calculateCommission($grand_total,$base_grand_total,$base_to_global_rate, $commissionSetting); die('dsffd'); */
                if (is_object($rate) && $commission = $rate->setOrder($order)->setVendorId($vendorId)->calculateCommission($grand_total, $base_grand_total, $base_to_global_rate, $commissionSetting)) {
                    return $commission;
                }
            }
        } catch (\Exception $e) {
            $this->_logger->critical($e->getMessage(), null, 'csmarketplace_commission_calculation.log');
        }
        return array('base_fee' => 0.00, 'fee' => 0.00, 'item_commission' => '');
    }

}

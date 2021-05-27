<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ced\FbNative\Model\System\Config\Backend;

/**
 * Backend for serialized array data
 */
class Fbsetting extends \Magento\Config\Model\Config\Backend\Serialized
{
    /**
     * Fbsetting constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * Load saved Data
     *
     * @return \Magento\Framework\App\Config\Value|string
     */
    protected function _afterLoad()
    {
        $value = $this->getValue();
        //$arr   = @unserialize($value);
        $arr   = json_decode($value, true);
        if(!is_array($arr)) return '';

        // some cleanup
        foreach ($arr as $k => $val) {
            if(!is_array($val)) {
                unset($arr[$k]);
                continue;
            }
        }

        $this->setValue($arr);
    }

    /**
     * Prepare Data for Save
     *
     * @return \Magento\Framework\App\Config\Value|void
     */
    public function beforeSave()
    {
        $values = $this->getValue();
        //$value = serialize($values);
        $value = json_encode($values);
        $this->setValue($value);

    }
}

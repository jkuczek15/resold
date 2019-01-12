<?php
/**
 * Created by PhpStorm.
 * User: cedcoss
 * Date: 9/12/18
 * Time: 5:37 PM
 */

namespace Ced\FbNative\Helper;

use Magento\Framework\App\Helper\Context;

class Config extends \Magento\Framework\App\Helper\AbstractHelper
{
    const ATTRIBUTE_MAPPING = 'fbnativeconfiguration/productinfo_map/fbnative_mapping';

    public $scopeConfigManager;

    public function __construct(
        Context $context
    ) {
        $this->scopeConfigManager = $context->getScopeConfig();
        parent::__construct($context);
    }

    public function getAttributeMapping()
    {
        $attributeMapping = $this->scopeConfig->getValue(self::ATTRIBUTE_MAPPING);
        return $attributeMapping;
    }

}

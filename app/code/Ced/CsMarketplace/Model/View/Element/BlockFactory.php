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

namespace Ced\CsMarketplace\Model\View\Element;

/**
 * Layout model
 *
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
 */
class BlockFactory extends \Magento\Framework\View\Element\BlockFactory
{
    const XML_PATH_CED_REWRITES = 'ced/rewrites';

    protected $_context;

    /**
    * Block Factory
    *
    * @param string $type
    * @param string $name
    * @param array $arguments
    * @return \Magento\Framework\View\Element\AbstractBlock
    */
    public function createBlock($blockName, array $arguments = [])
    {
        $vendorThemeList = ['Ced/ced', 'Ced/ced_2k18'];
        $themeCode = $this->objectManager->get('Magento\Framework\View\DesignInterface')->getDesignTheme()->getCode();
        if (in_array($themeCode, $vendorThemeList)) {
            $this->_httpRequest = $this->objectManager->get('Magento\Framework\App\Request\Http');
            $this->_context = $this->objectManager->get('Magento\Framework\App\Helper\Context');

            $module = $this->_httpRequest->getModuleName();
            $controller = $this->_httpRequest->getControllerName();
            $action = $this->_httpRequest->getActionName();
             
            $exceptionblocks = '';
            $exceptionblocks = $this->_context->getScopeConfig()->getValue(self::XML_PATH_CED_REWRITES."/".$module."/".$controller."/".$action);
            if (strlen($exceptionblocks) == 0) {
                $action = "all";
                $exceptionblocks = $this->_context->getScopeConfig()->getValue(self::XML_PATH_CED_REWRITES."/".$module."/".$controller."/".$action);
            }

            $block = parent::createBlock($blockName, $arguments);
            $exceptionblocks = explode(",", $exceptionblocks);
            if (count($exceptionblocks) > 0) {
                foreach ($exceptionblocks as $exceptionblock) {
                    if (strlen($exceptionblock) != 0 && strpos(get_class($block), $exceptionblock) !== false) {
                        $block->setArea('adminhtml');
                    }
                }
            }
            return $block;
        } else {
            return parent::createBlock($blockName, $arguments);
        }
    }
}

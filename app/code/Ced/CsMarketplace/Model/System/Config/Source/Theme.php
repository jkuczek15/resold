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

namespace Ced\CsMarketplace\Model\System\Config\Source;

class Theme extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_CED_CSMARKETPLACE_VENDOR_THEME = 'ced_csmarketplace/vendor/theme';
    
    protected $_scopeConfig;
    
    /**
* 
     *
 * @var \Magento\Framework\App\Helper\Context 
*/
    protected $_context;
    
    /**
     * @param \Magento\Framework\App\Helper\Context      $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_storeManger = $storeManager;
        $this->_context = $context;
    }
    
    /**
     * Retrieve theme data form config.xml
     *
     * @return array
     */
    public  function getThemes() 
    {
        $themes = $this->_context->getScopeConfig()->getValue(self::XML_PATH_CED_CSMARKETPLACE_VENDOR_THEME);
        return json_decode(json_encode($themes), true);
    }
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();
        if(is_array($this->getThemes())){       
            $options = array();
            foreach($this->getThemes() as$themes=> $theme) {
                $options[] = array('value'=>$theme,'label'=>ucfirst(str_replace('_', ' ', $themes)));
            }
        }
        return $options;
    }
}

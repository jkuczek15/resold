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
  * @package     Ced_CsOrder
  * @author      CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */


var config = {
    map: {
        '*': {
            "mage/backend/tabs" : 'mage/backend/tabs',
            "floatingHeader":       "mage/backend/floating-header",
            "Magento_Sales/order/giftoptions_tooltip" : "Ced_CsOrder/js/giftoptions_tooltip"
        }
    },
    deps: [
        "jquery",
        "jquery/ui",
        "jquery/validate",
        "mage/translate"
    ]
};



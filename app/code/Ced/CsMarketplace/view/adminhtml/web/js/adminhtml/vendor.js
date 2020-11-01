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
 require([
    "jquery",
    "jquery/ui",
    "prototype"
], function($){
var ced_csmarketplace = Class.create();
var ced_csmarketplace = ced_csmarketplace;
ced_csmarketplace.prototype = {
    initialize: function(url) {
        this.url  = url;
    },
    collectPendingPayments: function(params) {
        var url = ced_csmarketplace.url + params;
        new Ajax.Request(
            url, {
                method: 'get',
                asynchronous: true,
                onSuccess: function(transport) {
                    var response = transport.responseText.evalJSON();
                    validateTrueEmailMsg = response.message;

                    if (response.success == 0) {
                        //alert('Error: '+validateTrueEmailMsg);
                        if ($('advice-validate-shopurl-ced-shop-url-field')) {
                            $('advice-validate-shopurl-ced-shop-url-field').remove();
                        }
                        if ($('advice-validate-shopurl-shop_url')) {
                            $('advice-validate-shopurl-shop_url').remove();
                        }
                        if(document.getElementById('ced-csmarketplace-availability')) {
                            document.getElementById('ced-csmarketplace-availability').className = 'ced-csmarketplace-availability-failed'; }
                        Validation.get('validate-shopurl').error = validateTrueEmailMsg;
                        ok = false;
                    } else {
                        //alert('Success: '+validateTrueEmailMsg);
                        if(document.getElementById('ced-csmarketplace-availability')) {
                            document.getElementById('ced-csmarketplace-availability').className = 'ced-csmarketplace-availability-passed'; }
                        if ($('advice-validate-shopurl-ced-shop-url-field')) {
                            $('advice-validate-shopurl-ced-shop-url-field').remove();
                        }
                        if ($('advice-validate-shopurl-shop_url')) {
                            $('advice-validate-shopurl-shop_url').remove();
                        }
                        ok = true; /* return true or false */    
                    }
                },
            }
        );
    }
};
});
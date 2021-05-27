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
 * @package     Ced_CsProduct
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
var config = {
"bundles": {
        "js/theme": [
            "globalNavigation",
            "globalSearch",
            "modalPopup",
            "useDefault",
            "loadingPopup",
            "collapsable"
        ]
    },
    map: {
        '*': {
        	
        	"Magento_CatalogInventory/js/components/use-config-settings":"Ced_CsProduct/js/components/use-config-settings",
        	"Magento_ConfigurableProduct/js/components/container-configurable-handler":"Ced_CsProduct/js/components/container-configurable-handler",
        	"Magento_CatalogInventory/js/components/qty-validator-changer":"Ced_CsProduct/js/components/qty-validator-changer",
        	"Magento_ConfigurableProduct/js/components/dynamic-rows-configurable":"Ced_CsProduct/js/components/dynamic-rows-configurable",
        	"Magento_ConfigurableProduct/js/components/associated-product-insert-listing":"Ced_CsProduct/js/components/associated-product-insert-listing",
        	"Magento_CatalogInventory/js/components/use-config-min-sale-qty":"Ced_CsProduct/js/components/use-config-min-sale-qty",
        	"Magento_ConfigurableProduct/js/components/custom-options-warning":"Ced_CsProduct/js/components/custom-options-warning",
        	"Magento_Downloadable/js/components/is-downloadable-handler":"Ced_CsProduct/js/components/is-downloadable-handler",
        	"Magento_Bundle/js/components/bundle-input-type":"Ced_CsProduct/js/components/bundle-input-type",
        	"Magento_Catalog/js/bundle-proxy-button":"Ced_CsProduct/js/bundle-proxy-button",	
        	"Magento_Catalog/js/custom-options-type":"Ced_CsProduct/js/custom-options-type",
        	"Magento_Catalog/component/static-type-input":"Ced_CsProduct/component/static-type-input",
        	"Magento_ConfigurableProduct/js/components/custom-options-price-type":"Ced_CsProduct/js/components/custom-options-price-type",
        	"Magento_Downloadable/js/components/file-uploader":"Ced_CsProduct/js/components/file-uploader",
        	"Magento_Downloadable/js/components/upload-type-handler":"Ced_CsProduct/js/components/upload-type-handler",
        	"Magento_Downloadable/js/components/price-handler":"Ced_CsProduct/js/components/price-handler",
        	"Magento_Bundle/js/components/bundle-checkbox":"Ced_CsProduct/js/components/bundle-checkbox",
        	"Magento_Bundle/js/components/bundle-option-qty":"Ced_CsProduct/js/components/bundle-option-qty",
        	"Magento_ConfigurableProduct/js/components/file-uploader":"Ced_CsProduct/js/components/file-uploader",
        	//"Magento_Ui/js/form/client":"Ced_CsProduct/js/form/client",
        	
        	"Magento_Backend/js/media-uploader":"Ced_CsProduct/js/media-uploader",
        	"Magento_Catalog/js/components/new-category":"Ced_CsProduct/js/components/new-category",
        	"Magento_Catalog/js/components/dynamic-rows-import-custom-options":"Ced_CsProduct/js/components/dynamic-rows-import-custom-options",
        	"Magento_Catalog/js/components/attribute-set-select":"Ced_CsProduct/js/components/attribute-set-select",
        	"Magento_Catalog/js/components/import-handler":"Ced_CsProduct/js/components/import-handler",
        	//"mage/tabs" : "mage/backend/tabs",
          "Magento_Catalog/catalog/type-events" : "Ced_CsProduct/catalog/type-events",
	     "Magento_Catalog/js/product/weight-handler" : "Ced_CsProduct/js/product/weight-handler",
	     "Magento_Catalog/catalog/apply-to-type-switcher" : "Ced_CsProduct/catalog/apply-to-type-switcher",
	     form : "mage/backend/form",
	     calendar : "mage/calendar",
	     productGallery : 'Ced_CsProduct/js/product-gallery',
	     newCategoryDialog:  'Ced_CsProduct/js/new-category-dialog',
	     baseImage : 'Ced_CsProduct/catalog/base-image-uploader',
	     suggest : "mage/backend/suggest",
	     "floatingHeader" : "mage/backend/floating-header",
	     "button" : "mage/backend/button",
	   
	   // "jquery/jquery.tabs" : "jquery/jquery.tabs",
	     //"toolbar_entry" : "Ced_CsProduct/catalog/toolbar_entry",
	     "backend/bootstrap" : "mage/backend/bootstrap",
	     "globals" : "mage/adminhtml/globals",
	     "dropdown-old" : "mage/dropdown_old",
	     "product-attributes" : "Ced_CsProduct/catalog/product-attributes",
	     "select" : "Magento_Ui/js/form/element/select",
	     "integration" : "Ced_CsProduct/catalog/integration",
	     "notification" : "mage/backend/notification",
	     "productAttributes" : "Ced_CsProduct/catalog/product-attributes",
	     
	     "Magento_ConfigurableProduct/js/advanced-pricing-handler" : "Ced_CsProduct/js/ConfigurableProduct/advanced-pricing-handler",
	     "Magento_ConfigurableProduct/js/configurable-type-handler" : "Ced_CsProduct/js/ConfigurableProduct/configurable-type-handler",
	     "Magento_ConfigurableProduct/js/options/price-type-handler" : "Ced_CsProduct/js/ConfigurableProduct/options/price-type-handler",
	     "Magento_ConfigurableProduct/js/variations/product-grid" : "Ced_CsProduct/js/ConfigurableProduct/product/variations/product-grid",
	     "Magento_ConfigurableProduct/js/variations/variations" : "Ced_CsProduct/js/ConfigurableProduct/product/variations/variations",
	     "Magento_ConfigurableProduct/js/variations/steps/attributes_values" : "Ced_CsProduct/js/ConfigurableProduct/product/variations/steps/attributes_values",
	     "Magento_ConfigurableProduct/js/variations/steps/bulk" : "Ced_CsProduct/js/ConfigurableProduct/product/variations/steps/bulk",
	     "Magento_ConfigurableProduct/js/variations/steps/select_attributes" : "Ced_CsProduct/js/ConfigurableProduct/product/variations/steps/select_attributes",
	     "Magento_ConfigurableProduct/js/variations/steps/summary" : "Ced_CsProduct/js/ConfigurableProduct/product/variations/steps/summary",
	     "Magento_Downloadable/template/components/file-uploader.html":"Ced_CsProduct/template/components/file-uploader.html",
	     "Magento_ConfigurableProduct/js/components/modal-configurable":"Ced_CsProduct/js/components/modal-configurable",
	     "Magento_Downloadable/downloadable-type-handler" : "Ced_CsProduct/js/DownloadableProduct/downloadable-type-handler",

	    "mage/adminhtml/browser":"Ced_CsProduct/js/adminhtml/browser",
	     
	     "Magento_Bundle/js/bundle-product" : "Ced_CsProduct/js/BundleProduct/bundle-product",
          "Magento_Bundle/js/bundle-type-handler" : "Ced_CsProduct/js/BundleProduct/bundle-type-handler",

	     "groupedProduct" : "Ced_CsProduct/js/GroupedProduct/grouped-product",

	     "Magento_Theme/js/sortable" : "Ced_CsProduct/js/Theme/sortable",

	     //"js/theme" : "Ced_CsProduct/js/Theme_admin_backend/theme",

	     "Magento_Catalog/js/custom-options" : "Ced_CsProduct/js/custom-options",

	     "mediabrowser" : "jquery/jstree/jquery.jstree",
	     "folderTree" : "Ced_CsProduct/js/folder-tree",

	     "Magento_Variable/variables" : "Ced_CsProduct/js/variables",
	     "treeSuggest" : "mage/backend/tree-suggest",
	     "jstree" : "jquery/jstree/jquery.jstree",
	     "actionLink" : "mage/backend/action-link",
	   
	     newVideoDialog : "Ced_CsProduct/js/video/new-video-dialog",
	     openVideoModal:  "Ced_CsProduct/js/video/video-modal",
	     "Magento_ProductVideo/js/get-video-information" : "Ced_CsProduct/js/video/get-video-information",

	     "global" : "mage/adminhtml/globals"
        }
    }
    
};

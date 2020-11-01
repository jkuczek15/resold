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

use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Sales\Model\Order\Address\Renderer;

class Mail extends \Magento\Framework\App\Helper\AbstractHelper
{

    const XML_PATH_ACCOUNT_EMAIL_IDENTITY = 'ced_csmarketplace/vendor/email_identity';
    const XML_PATH_ACCOUNT_CONFIRMED_EMAIL_TEMPLATE = 'ced_csmarketplace/vendor/account_confirmed_template';
    const XML_PATH_ACCOUNT_REJECTED_EMAIL_TEMPLATE = 'ced_csmarketplace/vendor/account_rejected_template';
    const XML_PATH_ACCOUNT_DELETED_EMAIL_TEMPLATE = 'ced_csmarketplace/vendor/account_deleted_template';

    const XML_PATH_SHOP_ENABLED_EMAIL_TEMPLATE = 'ced_csmarketplace/vendor/shop_enabled_template';
    const XML_PATH_SHOP_DISABLED_EMAIL_TEMPLATE = 'ced_csmarketplace/vendor/shop_disabled_template';

    const XML_PATH_PRODUCT_EMAIL_IDENTITY = 'ced_vproducts/general/email_identity';
    const XML_PATH_PRODUCT_CONFIRMED_EMAIL_TEMPLATE = 'ced_vproducts/general/product_approved_template';
    const XML_PATH_PRODUCT_REJECTED_EMAIL_TEMPLATE = 'ced_vproducts/general/product_rejected_template';
    const XML_PATH_PRODUCT_DELETED_EMAIL_TEMPLATE = 'ced_vproducts/general/product_deleted_template';

    const XML_PATH_ORDER_EMAIL_IDENTITY = 'ced_vorders/general/email_identity';
    const XML_PATH_ORDER_NEW_EMAIL_TEMPLATE = 'ced_vorders/general/order_new_template';
    const XML_PATH_ORDER_CANCEL_EMAIL_TEMPLATE = 'ced_vorders/general/order_cancel_template';
    protected $_objectManager = null;
    protected $paymentHelper;
    protected $addressRenderer;

    public function __construct(
		Renderer $addressRenderer,
		PaymentHelper $paymentHelper,
		\Magento\Framework\App\Helper\Context $context,
		\Magento\Store\Model\App\Emulation $appEmulation,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
    	$this->_appEmulation = $appEmulation;
        $this->_objectManager = $objectManager;
        $this->paymentHelper = $paymentHelper;
        $this->addressRenderer = $addressRenderer;
        parent::__construct($context);
    }
    /**
     * Can send new order notification email
     *
     * @param  int $storeId
     * @return boolean
     */
    public function canSendNewOrderEmail($storeId)
    {
        return $this->_objectManager->get('Ced\CsMarketplace\Helper\Data')
            ->getStoreConfig(
                'ced_vorders/general/order_email_enable',
                $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')
                    ->getStore(null)->getStoreId()
            );
    }

    /**
     * Can send new order notification email
     *
     * @param  int $storeId
     * @return boolean
     */
    public function canSendCancelOrderEmail($storeId)
    {
        return $this->_objectManager->get('Ced\CsMarketplace\Helper\Data')->getStoreConfig('ced_vorders/general/order_cancel_email_enable', $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore(null)->getStoreId());
    }


    /**
     * Send account status change email to vendor
     *
     * @param  string $type
     * @param  string $backUrl
     * @param  string $storeId
     * @throws Mage_Core_Exception
     * @return Mage_Customer_Model_Customer
     */
    public function sendAccountEmail($status, $backUrl = '', $vendor, $storeId = null)
    {
        $types = [
            \Ced\CsMarketplace\Model\Vendor::VENDOR_APPROVED_STATUS   => self::XML_PATH_SHOP_ENABLED_EMAIL_TEMPLATE,
            \Ced\CsMarketplace\Model\Vendor::VENDOR_DISAPPROVED_STATUS => self::XML_PATH_ACCOUNT_REJECTED_EMAIL_TEMPLATE,
            \Ced\CsMarketplace\Model\Vendor::VENDOR_DELETED_STATUS => self::XML_PATH_ACCOUNT_DELETED_EMAIL_TEMPLATE,
        ];
        if (!isset($types[$status])) {
            return false;
        }
        if ($storeId === null) {
            $customer = $this->_objectManager->get('Magento\Customer\Model\Customer')->load($vendor->getCustomerId());
            $storeId = $customer->getStoreId();
        }

        $this->_sendEmailTemplate($types[$status], self::XML_PATH_ACCOUNT_EMAIL_IDENTITY,
            ['vendor' => $vendor, 'back_url' => $backUrl], $storeId
        );
        return $this;
    }

    /**
     * Send shop enable/disable to vendor
     *
     * @param  string $type
     * @param  string $backUrl
     * @param  string $storeId
     * @throws Mage_Core_Exception
     * @return Mage_Customer_Model_Customer
     */
    public function sendShopEmail($status, $backUrl = '', $vendor, $storeId = '0')
    {
        $types = array(
        \Ced\CsMarketplace\Model\Vshop::ENABLED   => self::XML_PATH_SHOP_ENABLED_EMAIL_TEMPLATE,
        \Ced\CsMarketplace\Model\Vshop::DISABLED => self::XML_PATH_SHOP_DISABLED_EMAIL_TEMPLATE,
        );
        if (!isset($types[$status])) {
            return false;
        }

        if (!$storeId) {
            $customer = $this->_objectManager->get('Magento\Customer\Model\Customer')->load($vendor->getCustomerId());
            $storeId=$customer->getStoreId();
        }

        $this->_sendEmailTemplate(
            $types[$status], self::XML_PATH_ACCOUNT_EMAIL_IDENTITY,
            array('vendor' => $vendor, 'back_url' => $backUrl), $storeId
        );
        return $this;
    }

    /**
     * Send order notification email to vendor
     *
     * @param Mage_Sales_Model_Order $order
     */
    public function sendOrderEmail(\Magento\Sales\Model\Order $order,$type, $vendorId, $vorder)
    {
        $types = array(
        \Ced\CsMarketplace\Model\Vorders::ORDER_NEW_STATUS   =>self::XML_PATH_ORDER_NEW_EMAIL_TEMPLATE,
        \Ced\CsMarketplace\Model\Vorders::ORDER_CANCEL_STATUS => self::XML_PATH_ORDER_CANCEL_EMAIL_TEMPLATE,
        );
        if (!isset($types[$type])) {
            return;
        }
        $storeId = $order->getStore()->getId();
        if($type == \Ced\CsMarketplace\Model\Vorders::ORDER_NEW_STATUS) {
            if (!$this->canSendNewOrderEmail($storeId)) {
                return;
            }
        }
        if($type == \Ced\CsMarketplace\Model\Vorders::ORDER_CANCEL_STATUS) {
            if (!$this->canSendCancelOrderEmail($storeId)) {
                return;
            }
        }

        $items = $order->getAllItems();
        $product = $items[0]->getProduct();

        $vendor = $this->_objectManager->get('Ced\CsMarketplace\Model\Vendor')->load($vendorId);
        $vorder = $this->_objectManager->get('Ced\CsMarketplace\Model\Vorders')->loadByField(array('order_id','vendor_id'), array($order->getIncrementId(),$vendorId));
        if($this->_objectManager->get('Magento\Framework\Registry')->registry('current_order')!='') {
            $this->_objectManager->get('Magento\Framework\Registry')->unregister('current_order');
        }

        if($this->_objectManager->get('Magento\Framework\Registry')->registry('current_vorder')!='') {
            $this->_objectManager->get('Magento\Framework\Registry')->unregister('current_vorder');
        }
        $this->_objectManager->get('Magento\Framework\Registry')->register('current_order', $order);
        $this->_objectManager->get('Magento\Framework\Registry')->register('current_vorder', $vorder);

        $customer_name = $order->getCustomerName();
        $customer_email = $order->getCustomerEmail();
        $isGuest = $customer_name == 'Guest';
        if($isGuest){
            $billing_address = $order->getBillingAddress();
            $customer_name = $billing_address->getFirstname() . ' ' . $billing_address->getLastname();
            $customer_email = $billing_address->getEmail();
        }
        $this->_sendEmailTemplate(
            $types[$type], self::XML_PATH_ORDER_EMAIL_IDENTITY,
            array('vendor' => $vendor,
                  'order' => $order,
                  'billing' => $order->getBillingAddress(),
                  'payment_html' => '<strong>Stripe</strong>'.str_replace('Stripe', '', $this->getPaymentHtml($order)),
                  'formattedShippingAddress'=>$this->getFormattedShippingAddress($order),
                  'formattedBillingAddress'=>$this->getFormattedBillingAddress($order),
                  'customer_name' => $customer_name,
                  'customer_email' => $customer_email,
                  'product_name' => $product->getName(),
                  'host' => $_SERVER['HTTP_HOST'],
                  'sender_id' => $order->getCustomerId(),
                  'vendor_id' => $vendor->getCustomerId(),
                  'product_id' => $product->getId(),
                  'encoded_subject' => urlencode($product->getName()),
                  'order_id' => $order->getId(),
                  'isGuest' => $isGuest
                ),
            $storeId,
            $customer_name,
            'orders@resold.us'
        );
    }


    /**
     * Send product status change notification email to vendor
     *
     * @param Mage_Catalog_Model_Product $product,int $status
     */
    public function sendProductNotificationEmail($ids,$status)
    {
        $types = array(
        \Ced\CsMarketplace\Model\Vproducts::APPROVED_STATUS   => self::XML_PATH_PRODUCT_CONFIRMED_EMAIL_TEMPLATE,
        \Ced\CsMarketplace\Model\Vproducts::NOT_APPROVED_STATUS => self::XML_PATH_PRODUCT_REJECTED_EMAIL_TEMPLATE,
        \Ced\CsMarketplace\Model\Vproducts::DELETED_STATUS => self::XML_PATH_PRODUCT_DELETED_EMAIL_TEMPLATE,
        );

        if (!isset($types[$status])) {
            return;
        }

        $vendorIds = array();
        foreach($ids as $productId){
            $vendorId=$this->_objectManager->create('Ced\CsMarketplace\Model\Vproducts')->getVendorIdByProduct($productId);
            $vendorIds[$vendorId][] = $productId;
        }

        foreach($vendorIds as $vendorId=>$productIds){
            $vendor = $this->_objectManager->create('Ced\CsMarketplace\Model\Vendor')->load($vendorId);
            if(!$vendor->getId()) {
                continue;
            }
            $products=array();
            $vproducts=array();
            foreach($productIds as $productId){
                if($status!=\Ced\CsMarketplace\Model\Vproducts::DELETED_STATUS) {
                    $product=$this->_objectManager->create('Magento\Catalog\Model\Product')->load($productId);
                    if($product && $product->getId()) {
                        $products[0][]=$product;
                    }
                }
                $products[1][$productId]=$this->_objectManager->create('Ced\CsMarketplace\Model\Vproducts')->getCollection()->addFieldToFilter('product_id', array('eq'=>$productId))->getFirstItem();
            }
            $customer = $this->_objectManager->create('Magento\Customer\Model\Customer')->load($vendor->getCustomerId());
            $storeId=$customer->getStoreId();
            $this->_sendEmailTemplate(
                $types[$status], self::XML_PATH_PRODUCT_EMAIL_IDENTITY,
                array('vendor' => $vendor,'products' => $products), $storeId
            );
        }
    }

    /**
     * Send corresponding email template
     *
     * @param  string   $emailTemplate  configuration path of email template
     * @param  string   $emailSender    configuration path of email identity
     * @param  array    $templateParams
     * @param  int|null $storeId
     * @return Mage_Customer_Model_Customer
     */
    protected function _sendEmailTemplate($template, $sender, $templateParams = array(), $storeId = null, $customer_name = null, $customer_email = null)
    {

        /*reference file vendor\magento\module-sales\Model\Order\Email\SenderBuilder.php */
        /**
        *
        *
        * @var $mailer Mage_Core_Model_Email_Template_Mailer
        */
        try{
            //$templateContainer=
            $vendor=$templateParams['vendor'];
            $transportBuilder=$this->_objectManager->get('Magento\Framework\Mail\Template\TransportBuilder');
            $transportBuilder->addTo($vendor->getEmail(), $vendor->getName());
            $transportBuilder->setTemplateIdentifier($this->_objectManager->get('Ced\CsMarketplace\Helper\Data')->getStoreConfig($template, $storeId));
            $transportBuilder->setTemplateOptions(
                [
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $storeId
                ]
            );

            $transportBuilder->setTemplateVars($templateParams);
            $transportBuilder->setFrom([
              'name' => $customer_name,
              'email' => $customer_email
            ]);

            $transport = $transportBuilder->getTransport();
            $transport->sendMessage();
        }
        catch(\Exception $e)
        {

        }
        return $this;
    }


    protected function getPaymentHtml($order)
    {
    	 $storeId =$this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore(null)->getId();
    	return $this->paymentHelper->getInfoBlockHtml(
    			$order->getPayment(),
    			$storeId
    	);
    }
    protected function getFormattedShippingAddress($order)
    {
    	return $order->getIsVirtual()
    	? null
    	: $this->addressRenderer->format($order->getShippingAddress(), 'html');
    }

    /**
     * @param Order $order
     * @return string|null
     */
    protected function getFormattedBillingAddress($order)
    {
    	return $this->addressRenderer->format($order->getBillingAddress(), 'html');
    }

    /*set up a Mail functionality For Product Delete From Admin Panel*/
    public function ProductDelete($status,$vendorIds)
    {   $types = array(
        \Ced\CsMarketplace\Model\Vproducts::APPROVED_STATUS   => self::XML_PATH_PRODUCT_CONFIRMED_EMAIL_TEMPLATE,
        \Ced\CsMarketplace\Model\Vproducts::NOT_APPROVED_STATUS => self::XML_PATH_PRODUCT_REJECTED_EMAIL_TEMPLATE,
        \Ced\CsMarketplace\Model\Vproducts::DELETED_STATUS => self::XML_PATH_PRODUCT_DELETED_EMAIL_TEMPLATE,
        );

        foreach($vendorIds as $vendorId=>$productIds)
        {

            $vendor = $this->_objectManager->get('Ced\CsMarketplace\Model\Vendor')->load($vendorId);
            $customer = $this->_objectManager->get('Magento\Customer\Model\Customer')->load($vendor->getCustomerId());
            $storeId=$customer->getStoreId();
            $this->_sendEmailTemplate(
                    $types[$status], self::XML_PATH_PRODUCT_EMAIL_IDENTITY,
                    array('vendor' => $vendor,'products' => $vendorIds[$vendorId]), $storeId
                );
        }
    }
}

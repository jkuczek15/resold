<?php
/**
 * Resold
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 *
 * @category    Resold
 * @package     Resold
 * @author      Resold Core Team <dev@resold.us>
 * @copyright   Copyright Resold (https://resold.us/)
 * @license     https://resold.us/license-agreement
 */
namespace Resold\Api\Controller\Refund;

use Magento\Customer\Model\Session;
use \Magento\Framework\App\Action\Context;
use \Magento\Framework\Controller\Result\JsonFactory;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var Session
     */
    protected $session;

    /**
     * @var resultJsonFactory
     */
    protected $resultJsonFactory;

    /**
     * Recipient email config path
     */
    const XML_PATH_EMAIL_RECIPIENT = 'contact/email/recipient_email';

    /**
     * Sender email config path
     */
    const XML_PATH_EMAIL_SENDER = 'contact/email/sender_email_identity';

    /**
     * Email template config path
     */
    const XML_PATH_EMAIL_TEMPLATE = 'report/email/email_template';

    /**
     * Enabled config path
     */
    const XML_PATH_ENABLED = 'contact/contact/enabled';

    /**
     * @param \Magento\Framework\App\Action\Context $context
     */
     public function __construct(
        Context $context,
        Session $customerSession,
        JsonFactory $resultJsonFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->session = $customerSession;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context);
    }

    /**
     * Return all categories and subcategories
     *
     * @return void
     */
    public function execute()
    {
      if($_SERVER['HTTP_USER_AGENT'] !== 'Stripe/1.0 (+https://stripe.com/docs/webhooks)'){
        return $this->resultJsonFactory->create()->setData(['error' => 'Invalid Request']);
      }

      // Retrieve the request's body and parse it as JSON:
      $input = @file_get_contents('php://input');
      $event_json = json_decode($input, true);

      $ob = $this->_objectManager;
      $store = $ob->get('Magento\Framework\App\Config\ScopeConfigInterface');
      $clientIdMode = $store->getValue('payment/ced_csstripe_method_one/client_id_mode');

      if($clientIdMode=='Development'){
        $clientId = $store->getValue('payment/ced_csstripe_method_one/client_did');
      }else{
        $clientId = $store->getValue('payment/ced_csstripe_method_one/client_pid');
      }

      /** get API mode test/live */
      $mode = $store->getValue('payment/ced_csstripe_method_one/gateway_mode');

      $skey = "api_{$mode}_secret_key";
      $key = $store->getValue('payment/ced_csstripe_method_one/'.$skey);

      // get the charge id from refund request
      $charge_id = $event_json['data']['object']['id'];

      $i = 0;
      do {
        $success = false;
        try {
            \Stripe\Stripe::setApiKey($key);
            $fee_id = \Stripe\ApplicationFee::all(["charge" => $charge_id])->data[0]['id'];
            $fee = \Stripe\ApplicationFee::retrieve($fee_id);
            $fee->refunds->create();
            $success = true;
        } catch (\Stripe\Error\Base $e) {
            // Catch Stripe exceptions
        } catch (Exception $e) {
          // Catch any other non-Stripe exceptions
        }

        if($success){ break; }
        $i++;
      } while($i < 10);

      return $this->resultJsonFactory->create()->setData(['success' => 'Y']);
    }// end function execute
  }

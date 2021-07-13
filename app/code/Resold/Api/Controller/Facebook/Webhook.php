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
namespace Resold\Api\Controller\Facebook;

use Magento\Customer\Model\Session;
use \Magento\Framework\App\Action\Context;
use \Magento\Framework\Controller\Result\JsonFactory;
use GuzzleHttp;

class Webhook extends \Magento\Framework\App\Action\Action
{
    protected $session;

    protected $resultJsonFactory;

    private $hubVerifyToken = null;

    private $accessToken = null;

    private $token = false;

    protected $client = null;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     */
     public function __construct(
        Context $context,
        Session $customerSession,
        JsonFactory $resultJsonFactory
    )
    {
        $this->session = $customerSession;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    /**
     * Facebook webhook for chatbot
     *
     * @return void
     */
    public function execute()
    {
      // set the request parameters for verifying fb
      $mode = isset($_REQUEST['hub_mode']) ? $_REQUEST['hub_mode'] : '';
      $token = isset($_REQUEST['hub_verify_token']) ?  $_REQUEST['hub_verify_token']  : '';
      $challenge = isset($_REQUEST['hub_challenge']) ? $_REQUEST['hub_challenge'] : '';
      $hubVerifyToken = '<your hub verify token>';
      $accessToken = '<your access token>';

      // verify facebook access token for chatbot
      $this->setHubVerifyToken($hubVerifyToken);
      $this->setAccessToken($accessToken);

      if($mode == 'subscribe'){
        $verify_challenge = $this->verifyToken($token, $challenge);
        if($challenge === $verify_challenge){
          echo $challenge;
          exit;
        }// end if challenge
      }// end if mode == 'subscribe'

      // send the message
      $message = [
        'message' => 'This is a test',
        'recipientId' => '100000601804340'
      ];
      $this->sendMessage($message);
      return $this->resultJsonFactory->create()->setData(['Success' => 'OK']);
    }

    public function setHubVerifyToken($value)
    {
      $this->hubVerifyToken = $value;
    }

    public function setAccessToken($value)
    {
      $this->accessToken = $value;
    }

    public function verifyToken($hub_verify_token, $challenge)
    {
      try
      {
        if ($hub_verify_token === $this->hubVerifyToken)
        {
            return $challenge;
        }
        else
        {
          throw new \Exception("Token not verified");
        }
      }
      catch(Exception $ex)
      {
        return $ex->getMessage();
      }
    }

    public function sendMessage($input)
    {
      try
      {
        // set up http client
        $client = new GuzzleHttp\Client(['base_uri' => 'https://graph.facebook.com']);
        $url = "/v2.6/me/messages";
        $messageText = strtolower($input['message']);
        $senderId = $input['recipientId'];
        $msgarray = explode(' ', $messageText);

        $response = null;
        $header = array(
            'content-type' => 'application/json'
        );

        $answer = $messageText;
        if (in_array('hi', $msgarray))
        {
            $answer = "Hello! how may I help you today?";
        }

        $response = ['recipient' => ['id' => $senderId], 'message' => ['text' => $answer], 'access_token' => $this->accessToken];
        $response = $client->post($url, ['query' => $response, 'headers' => $header]);

        return true;
      }
      catch(RequestException $e)
      {
        $response = json_decode($e->getResponse()->getBody(true)->getContents());
        file_put_contents("payytorrrre.json", json_encode($response));
        return $response;
      }
    }
}

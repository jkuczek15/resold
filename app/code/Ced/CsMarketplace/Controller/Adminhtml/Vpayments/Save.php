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

namespace Ced\CsMarketplace\Controller\Adminhtml\Vpayments;

use Magento\Backend\App\Action\Context;

class Save extends \Ced\CsMarketplace\Controller\Adminhtml\Vendor
{
    /**
     * @param Context     $context
     *
     */
    public function __construct(
        Context $context
    ) {
        parent::__construct($context);

    }
    /**
     * Customer edit action
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function execute()
    {
        if ($data = $this->getRequest()->getPost()) {
            $params = $this->getRequest()->getParams();
            $type = isset($params['type']) && in_array($params['type'], array_keys(\Ced\CsMarketplace\Model\Vpayment::getStates()))?$params['type']:\Ced\CsMarketplace\Model\Vpayment::TRANSACTION_TYPE_CREDIT;
        
            $model = $this->_objectManager->create('Ced\CsMarketplace\Model\Vpayment');
            $amount_desc = isset($data['amount_desc'])?$data['amount_desc']:json_encode(array());
            $total_amount = json_decode($amount_desc);
                        
            $baseCurrencyCode = $this->_objectManager->get('Magento\Directory\Helper\Data')->getBaseCurrencyCode();
            $allowedCurrencies = $this->_objectManager->get('Magento\Directory\Model\Currency')->getConfigAllowCurrencies();
            $rates = $this->_objectManager->get('Magento\Directory\Model\Currency')->getCurrencyRates($baseCurrencyCode, array_values($allowedCurrencies));
            $data['base_to_global_rate'] = isset($data['currency'])&& isset($rates[$data['currency']]) ? $rates[$data['currency']] : 1;

            $base_amount = 0;
            if(count($total_amount) > 0) {
                foreach($total_amount as $key=>$value) {
                    $base_amount += $value;
                }
            }
            
            if($base_amount != $data['base_amount']) {
                $this->messageManager->addError('Amount entered should be equal to the sum of all selected order(s)');
                return $this->_redirect('*/*/edit', array('vendor_id' => $this->getRequest()->getParam('vendor_id'),'type'=>$type));

            }
        
            $data['transaction_type'] = $type;
            $data['payment_method'] = isset($data['payment_method'])?$data['payment_method']:0;
            /*Will use it when vendor will pay in different currenncy  */
            
            list($currentBalance,$currentBaseBalance) = $model->getCurrentBalance($data['vendor_id']);


            $base_net_amount = floatval($data['base_amount'])+floatval($data['base_fee']);
            if($type == \Ced\CsMarketplace\Model\Vpayment::TRANSACTION_TYPE_DEBIT) {
                /* Case of Deduct credit */
                if($currentBaseBalance > 0) { $newBaseBalance = $currentBaseBalance - $base_net_amount; 
                }
                else { $newBaseBalance = $base_net_amount; 
                }
                $base_net_amount = -$base_net_amount;
                if(-$base_net_amount <= 0.00) {
                     $this->messageManager->addError("Refund Net Amount can't be less than zero");
                    return $this->_redirect('*/*/edit', array('vendor_id' => $this->getRequest()->getParam('vendor_id'),'type'=>$type));

                }
            } else {
                // Case of Add credit 
                $newBaseBalance = $currentBaseBalance + $base_net_amount;
                if($base_net_amount <= 0.00) {
                     $this->messageManager->addError("Net Amount can't be less than zero");
                     return $this->_redirect('*/*/edit', array('vendor_id' => $this->getRequest()->getParam('vendor_id'),'type'=>$type));

                }
            }
              $data['base_currency']= $baseCurrencyCode;
              $data['base_net_amount'] = $base_net_amount;
              $data['base_balance'] = $newBaseBalance;
            
              $data['amount'] = $base_amount*$data['base_to_global_rate'];
              $data['balance'] = $this->_objectManager->get('Magento\Directory\Helper\Data')->currencyConvert($newBaseBalance, $baseCurrencyCode, $data['currency']);
              $data['fee'] = $this->_objectManager->get('Magento\Directory\Helper\Data')->currencyConvert(floatval($data['base_fee']), $baseCurrencyCode, $data['currency']);
              $data['net_amount'] = $this->_objectManager->get('Magento\Directory\Helper\Data')->currencyConvert($base_net_amount, $baseCurrencyCode, $data['currency']);
            
              $data['tax'] = 0.00;
              $data['payment_detail'] = isset($data['payment_detail'])?$data['payment_detail']:'n/a';
            
              $model->addData($data->toArray());    


              $openStatus = $model->getOpenStatus();
              $model->setStatus($openStatus);
            
            try {
                $model->saveOrders($data);
                $model->save();               
                $this->messageManager->addSuccessMessage(__('Payment is  successfully saved'));
                $this->_session->setFormData(false);
                return $this->_redirect('*/*/');

            } catch (\Exception $e) {
                 $this->messageManager->addError($e->getMessage());
                 $this->_session->setFormData($data);
                 return $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));

            }
        }
         $this->messageManager->addError(__('Unable to find vendor to save'));
        return $this->_redirect('*/*/');
    }
}

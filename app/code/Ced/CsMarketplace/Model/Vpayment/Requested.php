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
 * @license      http://cedcommerce.com/license-agreement.txt
 */
 

namespace Ced\CsMarketplace\Model\Vpayment;
class Requested extends \Ced\CsMarketplace\Model\FlatAbstractModel
{
	const PAYMENT_STATUS_REQUESTED 	  = 1;
	const PAYMENT_STATUS_PROCESSED 	  = 2;
	const PAYMENT_STATUS_CANCELED = 3;
	
	public static $_statuses = null;
	
	protected $_eventPrefix      = 'csmarketplace_vpayments_requested';
	protected $_eventObject      = 'vpayment_requested';
	
	/**
     * Initialize resource model
     */
	protected function _construct()
	{
		$this->_init('Ced\CsMarketplace\Model\ResourceModel\Requested');
		//$this->_init('csmarketplace/vpayment_requested');
	}
	
	/**
     * Retrieve vendor payment status array
     *
     * @return array
     */
    public static function getStatuses()
    {
        if (is_null(self::$_statuses)) {
            self::$_statuses = array(
                self::PAYMENT_STATUS_REQUESTED       => __('Requested'),
                self::PAYMENT_STATUS_PROCESSED       => __('Processed'),
                self::PAYMENT_STATUS_CANCELED   => __('Canceled'),
            );
        }
        return self::$_statuses;
    }
	
}
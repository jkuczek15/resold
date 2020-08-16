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
namespace Resold\Api\Model;

class ProductManagement
{
  /**
   * @param \Magento\Framework\App\Action\Context $context
   */
   public function __construct(
    \Magento\Authorization\Model\UserContextInterface $userContext
  )
  {
      $this->userContext = $userContext;
  }

	/**
	 * {@inheritdoc}
	 */
	public function createProduct($param)
	{
    $mobileCustomerId = $this->userContext->getUserId();
		return 'api POST return the $param ' . $param . ' with customer id: ' . $mobileCustomerId;
	}
}

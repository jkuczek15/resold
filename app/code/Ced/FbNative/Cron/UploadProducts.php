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
 * @package     Ced_Fbnative
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\Fbnative\Cron;

class UploadProducts
{
    /**
     * Logger
     * @var \Psr\Log\LoggerInterface
     */
    public $logger;

    /**
     * OM
     * @var \Magento\Framework\ObjectManagerInterface
     */
    public $objectManager;

    /**
     * Data Helper
     * @var $helper
     */
    public $helper;

    /**
     * UploadProducts constructor.
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Ced\Fbnative\Helper\Data $helper
    ) {

        $this->objectManager = $objectManager;
        $this->logger = $logger;
        $this->helper = $helper;
    }

    /**
     * Execute
     * @return boolean
     */
    public function execute()
    {
            if($this->helper->productCron()) {
                return true;
            } else {
                $this->logger->debug("Fbnative Cron : Product CSV not updated" );
            }

    }
}

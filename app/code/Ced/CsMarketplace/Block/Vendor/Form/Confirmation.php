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

namespace Ced\CsMarketplace\Block\Vendor\Form;

use Ced\CsMarketplace\Model\Url;
use Magento\Framework\View\Element\Template;

/**
 * Customer account navigation sidebar
 */
class Confirmation extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Url
     */
    protected $vendorUrl;

    /**
     * @param Template\Context $context
     * @param Url $vendorUrl
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Url $vendorUrl,
        array $data = []
    ) {
        $this->vendorUrl = $vendorUrl;
        parent::__construct($context, $data);
    }

    /**
     * Get login URL
     *
     * @return string
     */
    public function getLoginUrl()
    {
        return $this->vendorUrl->getLoginUrl();
    }
}

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
 * @category  Ced
 * @package   Ced_CsVendorReview
 * @author    CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license   http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsVendorReview\Block\Adminhtml;

class Review extends \Magento\Backend\Block\Widget\Grid\Container
{
    
    protected function _construct()
    {
        parent::_construct();
        $this->_controller = 'adminhtml_review';
        $this->_blockGroup = 'Ced_CsVendorReview';
        $this->_headerText = __('Manage Vendor Review');
        $this->removeButton('add');
    }
}

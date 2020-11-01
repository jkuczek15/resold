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
  * @package   Ced_CsOrder
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */

namespace Ced\CsOrder\Block\Order\View;

/**
 * Adminhtml sales order view plane
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Form extends \Magento\Backend\Block\Template
{
    /**
     * Template
     *
     * @var string
     */
    protected $_template = 'order/view/form.phtml';

    public function __construct()
    {
        $this->setData('area', 'adminhtml');
    }

}

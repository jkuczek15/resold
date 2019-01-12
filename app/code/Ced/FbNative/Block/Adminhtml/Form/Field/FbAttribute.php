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
 * @package     Ced_Fyndiq
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\FbNative\Block\Adminhtml\Form\Field;

use Magento\Framework\Api\SearchCriteriaBuilder;


/**
 * HTML select element block with customer groups options
 */
class FbAttribute extends \Magento\Framework\View\Element\Html\Select
{
    /**
     * @var
     */
    private $fbmethod;

    private  $searchCriteriaBuilder;

    private  $fbAttr;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        \Ced\FbNative\Model\Source\FbAttribute\FbAttributes $fbAttr,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->fbAttr = $fbAttr;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }


    /**
     * @return array|null
     */
    protected function _getShippingMethod()
    {
        if ($this->fbmethod === null) {
            $fbAttr = $this->fbAttr->toOptionArray();
            $this->fbmethod = $fbAttr;
        }

        return $this->fbmethod;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * @return string
     */
    public function _toHtml()
    {
        if (!$this->getOptions()) {
            foreach ($this->_getShippingMethod() as  $method) {
                $this->addOption($method['value'], addslashes($method['label']));
            }
        }
        return parent::_toHtml();
    }
}

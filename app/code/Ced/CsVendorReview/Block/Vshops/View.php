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

 
namespace Ced\CsVendorReview\Block\Vshops;

use Magento\Framework\Registry;

class View extends \Magento\Framework\View\Element\Template
{
    protected $_vendor;
    
    protected $_storeManager;
    
    public $_objectManager;
    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
    
        parent::__construct($context, $data);
        $this->_coreRegistry = $registry;
        $this->_objectManager=$objectManager;
    }
    
    public function isActivated()
    {
        $scopeConfig = $this->_objectManager->create('Magento\Framework\App\Config\ScopeConfigInterface');
        if ($scopeConfig->getValue('ced_csmarketplace/vendorreview/activation')) {
            return true;
        } else {
            return false;
        }
    }
    public function getVendor()
    {
        if (!$this->_vendor) {
            $this->_vendor=$this->_coreRegistry->registry('current_vendor');
        }
        return $this->_vendor;
    }
    
    public function getVendorRating()
    {
        $review_data = $this->_objectManager->create('Ced\CsVendorReview\Model\Review')->getCollection()
            ->addFieldToFilter('vendor_id', $this->getVendorId())
            ->addFieldToFilter('status', 1);
            
        $rating = $this->_objectManager->create('Ced\CsVendorReview\Model\Rating')->getCollection()
            ->addFieldToSelect('rating_code');
        $count = 0;
        $rating_sum = 0;
            
        $vendor_products = $this->getVendorProducts($this->getVendorId());
        if (count($vendor_products)>0) {
            $rating_sum_prod=$this->getProductRating($vendor_products);
            if ($rating_sum_prod>0) {
                $rating_sum = $rating_sum_prod;
                $count++;
            }
        }
            
        foreach ($review_data as $key => $value) {
            foreach ($rating as $k => $val) {
                if ($value[$val['rating_code']] > 0) {
                    $rating_sum += $value[$val['rating_code']];
                    $count++;
                }
            }
        }
        
        if ($count > 0 && $rating_sum > 0) {
            $width = $rating_sum/$count;
            return ceil($width);
        } else {
            return false;
        }
    }
    public function getRatingDetails()
    {
        $review_data = $this->_objectManager->create('Ced\CsVendorReview\Model\Review')->getCollection()
            ->addFieldToFilter('vendor_id', $this->getVendorId())
            ->addFieldToFilter('status', 1);
            
        $rating = $this->_objectManager->create('Ced\CsVendorReview\Model\Rating')->getCollection()
            ->addFieldToSelect('rating_code')
            ->addFieldToSelect('rating_label')
            ->addFieldToSelect('sort_order')
            ->setOrder('sort_order', 'ASC');
        $rating_details=[];
        $vendor_products = $this->getVendorProducts($this->getVendorId());
        if (count($vendor_products)>0) {
            $rating_sum_prod=$this->getProductRating($vendor_products);
            if ($rating_sum_prod>0) {
                $rating_details['Product']['rating']=$rating_sum_prod;
                $rating_details['Product']['count']=1;
            }
        }
            
            
        foreach ($review_data as $key => $value) {
            foreach ($rating as $k => $val) {
                if ($value[$val['rating_code']] > 0) {
                    if (isset($rating_details[$val['rating_label']]['rating'])) {
                        $rating_details[$val['rating_label']]['rating']=$rating_details[$val['rating_label']]['rating']+$value[$val['rating_code']];
                    } else {
                        $rating_details[$val['rating_label']]['rating']=$value[$val['rating_code']];
                    }
                    if (isset($rating_details[$val['rating_label']]['count'])) {
                        $rating_details[$val['rating_label']]['count']=$rating_details[$val['rating_label']]['count']+1;
                    } else {
                        $rating_details[$val['rating_label']]['count']=1;
                    }
                }
            }
        }
        
        if (!empty($rating_details)) {
            return $rating_details;
        } else {
            return false;
        }
    }
    public function getVendorId()
    {
        return $this->getVendor()->getId();
    }
    public function getProductRating($vendor_products)
    {
        $ratingFactory = $this->_objectManager->create('Magento\Review\Model\Rating');
        $rating_sum=0;
        foreach ($vendor_products as $product) {
            $rating = $ratingFactory->getEntitySummary($product['product_id']);
            if ($rating->getSum()!=null) {
                $rating_sum+=($rating->getSum()/$rating->getCount());
            }
        }
        return $rating_sum;
    }
    public function getVendorProducts($vendor_id)
    {
        $products = $this->_objectManager->create('Ced\CsMarketplace\Model\Vproducts')->getCollection()
            ->addFieldToFilter('vendor_id', $vendor_id)
            ->addFieldToFilter('check_status', 1)
            ->addFieldToSelect('product_id');
        return $products->getData();
    }
}

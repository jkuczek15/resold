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

class ListBlock extends \Ced\CsMarketplace\Block\Vshops\ListBlock
{
    
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
    
    /*public function getVendorProducts($vendor_id)
    {
        $products = $this->_objectManager->create('Ced\CsMarketplace\Model\Vproducts')->getCollection()
            ->addFieldToFilter('vendor_id', $vendor_id)
            ->addFieldToFilter('check_status', 1)
            ->addFieldToSelect('product_id');
        return $products->getData();
    }*/

    public function getReviewsSummaryHtml($vendor)
    {
        //echo $vendor->getId();
        if ($this->_scopeConfig->getValue('ced_csmarketplace/vendorreview/activation')) {
            $review_data = $this->_objectManager->create('Ced\CsVendorReview\Model\Review')->getCollection()
                ->addFieldToFilter('vendor_id', $vendor->getId())
                ->addFieldToFilter('status', 1);
            
            $rating = $this->_objectManager->create('Ced\CsVendorReview\Model\Rating')->getCollection()
                ->addFieldToSelect('rating_code');
            $count = 0;
            $rating_sum = 0;
            
            /*$vendor_products = $this->getVendorProducts($vendor->getId());
            if (count($vendor_products)>0) {
                $rating_sum_prod=$this->getProductRating($vendor_products);
                if ($rating_sum_prod>0) {
                    $rating_sum = $rating_sum_prod;
                    $count++;
                }
            }*/
            
            foreach ($review_data as $key => $value) {
                foreach ($rating as $k => $val) {
                    if ($value[$val['rating_code']] > 0) {
                        $rating_sum += $value[$val['rating_code']];
                        $count++;
                    }
                }
            }
            if ($count > 0 && $rating_sum > 0) {
                $width = ceil($rating_sum/$count);
                return '<div class="rating-summary">     
							 <div title="'.$width.'%" class="rating-result">
								 <span style="width:'.$width.'%;"><span>'.$width.'%</span></span>
							 </div>
							</div>';
            } else {
                $width = 0;
                return '<div class="rating-summary">     
                             <div title="'.$width.'%" class="rating-result">
                                 <span style="width:'.$width.'%;"><span>'.$width.'%</span></span>
                             </div>
                            </div>';
            }
        } else {
            return '';
        }
    }
}

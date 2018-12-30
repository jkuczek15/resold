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
namespace Custom\Api\Controller\Categories;

use Magento\Customer\Model\Session;
use \Magento\Framework\App\Action\Context;
use \Magento\Catalog\Helper\Category;
use \Magento\Framework\Controller\Result\JsonFactory;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var Session
     */
    protected $session;

    /**
     * @var categoryHelper
     */
    protected $categoryHelper;

    /**
     * @var resultJsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\categoryHelper categoryHelper
     */
     public function __construct(
        Context $context,
        Session $customerSession,
        Category $categoryHelper,
        JsonFactory $resultJsonFactory
    )
    {
        $this->session = $customerSession;
        $this->categoryHelper = $categoryHelper;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    /**
     * Return all categories and subcategories
     *
     * @return void
     */
    public function execute()
    {
      $result = [];
      $categories = $this->getStoreCategories();

      foreach($categories as $category){
        // grab the root level name and subcategories
        $category_name = $category->getName();
        $subcategories = $category->getChildren();

        foreach($subcategories as $subcategory){
          // grab the subcategory name and sub-sub categories
          $subcategory_name = $subcategory->getName();
          $lowest_categories = $subcategory->getChildren();

          if(!$subcategory->hasChildren()){
            $result[$category_name][$subcategory_name] = $subcategory->getId();
          }else{
            foreach($lowest_categories as $lowest_category){
              $result[$category_name][$subcategory_name][] = ['id' => $lowest_category->getId(), 'name' => $lowest_category->getName()];
            }// end foreach lowest category
          }// end if subcategory has no sub-sub categories

        }// end foreach subcategory

      }// end foreach root category

      return $this->resultJsonFactory->create()->setData($result);
    }// end function execute

    /**
     * Retrieve current store level 2 category
     *
     * @param bool|string $sorted (if true display collection sorted as name otherwise sorted as based on id asc)
     * @param bool $asCollection (if true display all category otherwise display second level category menu visible category for current store)
     * @param bool $toLoad
     */
    public function getStoreCategories($sorted = false, $asCollection = false, $toLoad = true)
    {
        return $this->categoryHelper->getStoreCategories($sorted , $asCollection, $toLoad);
    }
}

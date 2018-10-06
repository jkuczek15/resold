<?php
namespace Package\CustomToolbar\Plugin\Model;
use Magento\Store\Model\StoreManagerInterface;

class Config
{
    protected $_storeManager;

    public function __construct(
        StoreManagerInterface $storeManager
    ) {
        $this->_storeManager = $storeManager;

    }

  /**
   * Adding custom options and changing labels
   *
   * @param \Magento\Catalog\Model\Config $catalogConfig
   * @param [] $options
   * @return []
   */
  public function afterGetAttributeUsedForSortByArray(\Magento\Catalog\Model\Config $catalogConfig, $options)
  {
      $store = $this->_storeManager->getStore();
      $currencySymbol = $store->getCurrentCurrency()->getCurrencySymbol();

      //Remove specific default sorting options
      unset($options['position']);
      unset($options['name']);
      unset($options['price']);
      unset($options['date']);

      //Changing label
      $customOption['position'] = __('Relevance');

      //New sorting options
      $customOption['date'] = 'Newest';
      $customOption['price_asc'] = __('('.$currencySymbol.') Low to High');
      $customOption['price_desc'] = __('('.$currencySymbol.') High to Low');

      //Merge default sorting options with custom options
      $options = array_merge($customOption, $options);

      return $options;
  }
}

<?php
namespace Magento\Review\Block\Product\View;

/**
 * Interceptor class for @see \Magento\Review\Block\Product\View
 */
class Interceptor extends \Magento\Review\Block\Product\View implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Catalog\Block\Product\Context $context, \Magento\Framework\Url\EncoderInterface $urlEncoder, \Magento\Framework\Json\EncoderInterface $jsonEncoder, \Magento\Framework\Stdlib\StringUtils $string, \Magento\Catalog\Helper\Product $productHelper, \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig, \Magento\Framework\Locale\FormatInterface $localeFormat, \Magento\Customer\Model\Session $customerSession, \Magento\Catalog\Api\ProductRepositoryInterface $productRepository, \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency, \Magento\Review\Model\ResourceModel\Review\CollectionFactory $collectionFactory, \Magento\Sales\Model\Order\Config $orderConfig, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $urlEncoder, $jsonEncoder, $string, $productHelper, $productTypeConfig, $localeFormat, $customerSession, $productRepository, $priceCurrency, $collectionFactory, $orderConfig, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function canEmailToFriend()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'canEmailToFriend');
        if (!$pluginInfo) {
            return parent::canEmailToFriend();
        } else {
            return $this->___callPlugins('canEmailToFriend', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getQuantityValidators()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getQuantityValidators');
        if (!$pluginInfo) {
            return parent::getQuantityValidators();
        } else {
            return $this->___callPlugins('getQuantityValidators', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getImage($product, $imageId, $attributes = [])
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getImage');
        if (!$pluginInfo) {
            return parent::getImage($product, $imageId, $attributes);
        } else {
            return $this->___callPlugins('getImage', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function fetchView($fileName)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'fetchView');
        if (!$pluginInfo) {
            return parent::fetchView($fileName);
        } else {
            return $this->___callPlugins('fetchView', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function escapeHtml($data, $allowedTags = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'escapeHtml');
        if (!$pluginInfo) {
            return parent::escapeHtml($data, $allowedTags);
        } else {
            return $this->___callPlugins('escapeHtml', func_get_args(), $pluginInfo);
        }
    }
}

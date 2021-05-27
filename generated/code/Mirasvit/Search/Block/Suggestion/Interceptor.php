<?php
namespace Mirasvit\Search\Block\Suggestion;

/**
 * Interceptor class for @see \Mirasvit\Search\Block\Suggestion
 */
class Interceptor extends \Mirasvit\Search\Block\Suggestion implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Search\Model\ResourceModel\Query\CollectionFactory $queryCollectionFactory, \Magento\Framework\View\Element\Template\Context $context, \Magento\Search\Model\QueryFactory $queryFactory, \Magento\Framework\DB\Helper $dbHelper, \Mirasvit\Search\Service\StemmingService $stemmingService)
    {
        $this->___init();
        parent::__construct($queryCollectionFactory, $context, $queryFactory, $dbHelper, $stemmingService);
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

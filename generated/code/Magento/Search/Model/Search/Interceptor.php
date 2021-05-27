<?php
namespace Magento\Search\Model\Search;

/**
 * Interceptor class for @see \Magento\Search\Model\Search
 */
class Interceptor extends \Magento\Search\Model\Search implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\Search\Request\Builder $requestBuilder, \Magento\Framework\App\ScopeResolverInterface $scopeResolver, \Magento\Framework\Search\SearchEngineInterface $searchEngine, \Magento\Framework\Search\SearchResponseBuilder $searchResponseBuilder)
    {
        $this->___init();
        parent::__construct($requestBuilder, $scopeResolver, $searchEngine, $searchResponseBuilder);
    }

    /**
     * {@inheritdoc}
     */
    public function search(\Magento\Framework\Api\Search\SearchCriteriaInterface $searchCriteria)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'search');
        if (!$pluginInfo) {
            return parent::search($searchCriteria);
        } else {
            return $this->___callPlugins('search', func_get_args(), $pluginInfo);
        }
    }
}

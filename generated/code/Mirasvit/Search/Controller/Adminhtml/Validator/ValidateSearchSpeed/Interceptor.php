<?php
namespace Mirasvit\Search\Controller\Adminhtml\Validator\ValidateSearchSpeed;

/**
 * Interceptor class for @see \Mirasvit\Search\Controller\Adminhtml\Validator\ValidateSearchSpeed
 */
class Interceptor extends \Mirasvit\Search\Controller\Adminhtml\Validator\ValidateSearchSpeed implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory, \Magento\Search\Model\SearchEngine $searchEngine, \Magento\CatalogSearch\Model\Advanced\Request\BuilderFactory $requestBuilderFactory, \Magento\Framework\App\ScopeResolverInterface $scopeResolver, \Magento\Backend\App\Action\Context $context)
    {
        $this->___init();
        parent::__construct($resultJsonFactory, $searchEngine, $requestBuilderFactory, $scopeResolver, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'dispatch');
        if (!$pluginInfo) {
            return parent::dispatch($request);
        } else {
            return $this->___callPlugins('dispatch', func_get_args(), $pluginInfo);
        }
    }
}

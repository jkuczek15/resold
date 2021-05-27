<?php
namespace Magezon\PageBuilderPreview\Controller\Preview\Index;

/**
 * Interceptor class for @see \Magezon\PageBuilderPreview\Controller\Preview\Index
 */
class Interceptor extends \Magezon\PageBuilderPreview\Controller\Preview\Index implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory, \Magento\Cms\Model\Page\Source\PageLayout $pageLayout, \Magento\Framework\Registry $coreRegistry, \Magezon\PageBuilderPreview\Model\ResourceModel\Profile\CollectionFactory $collectionFactory)
    {
        $this->___init();
        parent::__construct($context, $resultForwardFactory, $pageLayout, $coreRegistry, $collectionFactory);
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

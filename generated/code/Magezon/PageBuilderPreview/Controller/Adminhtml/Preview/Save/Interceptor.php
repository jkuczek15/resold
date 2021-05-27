<?php
namespace Magezon\PageBuilderPreview\Controller\Adminhtml\Preview\Save;

/**
 * Interceptor class for @see \Magezon\PageBuilderPreview\Controller\Adminhtml\Preview\Save
 */
class Interceptor extends \Magezon\PageBuilderPreview\Controller\Adminhtml\Preview\Save implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magezon\PageBuilderPreview\Model\ResourceModel\Profile\CollectionFactory $collectionFactory)
    {
        $this->___init();
        parent::__construct($context, $collectionFactory);
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

<?php
namespace Ced\CsMarketplace\Controller\Adminhtml\Vpayments\LoadBlock;

/**
 * Interceptor class for @see \Ced\CsMarketplace\Controller\Adminhtml\Vpayments\LoadBlock
 */
class Interceptor extends \Ced\CsMarketplace\Controller\Adminhtml\Vpayments\LoadBlock implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Framework\View\Layout\Builder $builder)
    {
        $this->___init();
        parent::__construct($context, $resultPageFactory, $builder);
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

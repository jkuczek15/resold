<?php
namespace Mageplaza\Sitemap\Controller\Index\Index;

/**
 * Interceptor class for @see \Mageplaza\Sitemap\Controller\Index\Index
 */
class Interceptor extends \Mageplaza\Sitemap\Controller\Index\Index implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $pageFactory, \Mageplaza\Sitemap\Helper\Data $helperConfig)
    {
        $this->___init();
        parent::__construct($context, $pageFactory, $helperConfig);
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

<?php
namespace MSP\SecuritySuiteCommon\Controller\Stop\Index;

/**
 * Interceptor class for @see \MSP\SecuritySuiteCommon\Controller\Stop\Index
 */
class Interceptor extends \MSP\SecuritySuiteCommon\Controller\Stop\Index implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\MSP\SecuritySuiteCommon\Api\SessionInterface $session, \Magento\Framework\View\Result\PageFactory $pageFactory, \Magento\Framework\Registry $registry, \Magento\Framework\App\Action\Context $context)
    {
        $this->___init();
        parent::__construct($session, $pageFactory, $registry, $context);
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

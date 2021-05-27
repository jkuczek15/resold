<?php
namespace Mirasvit\Search\Controller\Adminhtml\Synonym\NewAction;

/**
 * Interceptor class for @see \Mirasvit\Search\Controller\Adminhtml\Synonym\NewAction
 */
class Interceptor extends \Mirasvit\Search\Controller\Adminhtml\Synonym\NewAction implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Mirasvit\Search\Api\Repository\SynonymRepositoryInterface $synonymRepository, \Mirasvit\Search\Api\Service\SynonymServiceInterface $synonymService, \Magento\Backend\App\Action\Context $context)
    {
        $this->___init();
        parent::__construct($synonymRepository, $synonymService, $context);
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

<?php
namespace Mirasvit\Search\Controller\Adminhtml\Synonym\Delete;

/**
 * Interceptor class for @see \Mirasvit\Search\Controller\Adminhtml\Synonym\Delete
 */
class Interceptor extends \Mirasvit\Search\Controller\Adminhtml\Synonym\Delete implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Ui\Component\MassAction\Filter $filter, \Mirasvit\Search\Api\Repository\SynonymRepositoryInterface $synonymRepository, \Mirasvit\Search\Api\Service\SynonymServiceInterface $synonymService, \Magento\Backend\App\Action\Context $context)
    {
        $this->___init();
        parent::__construct($filter, $synonymRepository, $synonymService, $context);
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

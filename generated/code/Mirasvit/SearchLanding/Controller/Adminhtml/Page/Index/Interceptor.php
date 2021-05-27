<?php
namespace Mirasvit\SearchLanding\Controller\Adminhtml\Page\Index;

/**
 * Interceptor class for @see \Mirasvit\SearchLanding\Controller\Adminhtml\Page\Index
 */
class Interceptor extends \Mirasvit\SearchLanding\Controller\Adminhtml\Page\Index implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Mirasvit\SearchLanding\Api\Repository\PageRepositoryInterface $pageRepository, \Magento\Backend\App\Action\Context $context)
    {
        $this->___init();
        parent::__construct($pageRepository, $context);
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

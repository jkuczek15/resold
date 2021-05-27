<?php
namespace Mirasvit\SearchLanding\Controller\Adminhtml\Page\Save;

/**
 * Interceptor class for @see \Mirasvit\SearchLanding\Controller\Adminhtml\Page\Save
 */
class Interceptor extends \Mirasvit\SearchLanding\Controller\Adminhtml\Page\Save implements \Magento\Framework\Interception\InterceptorInterface
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

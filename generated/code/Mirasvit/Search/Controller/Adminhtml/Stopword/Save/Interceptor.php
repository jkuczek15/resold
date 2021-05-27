<?php
namespace Mirasvit\Search\Controller\Adminhtml\Stopword\Save;

/**
 * Interceptor class for @see \Mirasvit\Search\Controller\Adminhtml\Stopword\Save
 */
class Interceptor extends \Mirasvit\Search\Controller\Adminhtml\Stopword\Save implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Mirasvit\Search\Api\Repository\StopwordRepositoryInterface $stopwordRepository, \Mirasvit\Search\Api\Service\StopwordServiceInterface $stopwordService, \Magento\Backend\App\Action\Context $context)
    {
        $this->___init();
        parent::__construct($stopwordRepository, $stopwordService, $context);
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

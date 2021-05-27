<?php
namespace Mirasvit\Search\Controller\Adminhtml\ScoreRule\NewConditionHtml;

/**
 * Interceptor class for @see \Mirasvit\Search\Controller\Adminhtml\ScoreRule\NewConditionHtml
 */
class Interceptor extends \Mirasvit\Search\Controller\Adminhtml\ScoreRule\NewConditionHtml implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Mirasvit\Search\Api\Repository\ScoreRuleRepositoryInterface $scoreRuleRepository, \Magento\Framework\Registry $registry, \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory, \Magento\Backend\App\Action\Context $context)
    {
        $this->___init();
        parent::__construct($scoreRuleRepository, $registry, $resultForwardFactory, $context);
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

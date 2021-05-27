<?php
namespace Mirasvit\SearchLanding\Controller\Page\View;

/**
 * Interceptor class for @see \Mirasvit\SearchLanding\Controller\Page\View
 */
class Interceptor extends \Mirasvit\SearchLanding\Controller\Page\View implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Mirasvit\SearchLanding\Api\Repository\PageRepositoryInterface $pageRepository, \Magento\Framework\Registry $registry, \Magento\Framework\View\Result\PageFactory $pageFactory, \Magento\Catalog\Model\Session $catalogSession, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Search\Model\QueryFactory $queryFactory, \Magento\Catalog\Model\Layer\Resolver $layerResolver, \Magento\Framework\App\Action\Context $context)
    {
        $this->___init();
        parent::__construct($pageRepository, $registry, $pageFactory, $catalogSession, $storeManager, $queryFactory, $layerResolver, $context);
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

<?php
namespace Ced\CsMarketplace\Controller\Adminhtml\Vendor\Edit;

/**
 * Interceptor class for @see \Ced\CsMarketplace\Controller\Adminhtml\Vendor\Edit
 */
class Interceptor extends \Ced\CsMarketplace\Controller\Adminhtml\Vendor\Edit implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\Registry $coreRegistry, \Magento\Framework\App\Response\Http\FileFactory $fileFactory, \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter)
    {
        $this->___init();
        parent::__construct($context, $coreRegistry, $fileFactory, $dateFilter);
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

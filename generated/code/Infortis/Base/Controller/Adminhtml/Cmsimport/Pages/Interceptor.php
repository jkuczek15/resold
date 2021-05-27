<?php
namespace Infortis\Base\Controller\Adminhtml\Cmsimport\Pages;

/**
 * Interceptor class for @see \Infortis\Base\Controller\Adminhtml\Cmsimport\Pages
 */
class Interceptor extends \Infortis\Base\Controller\Adminhtml\Cmsimport\Pages implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Infortis\Base\Helper\Data $helperData, \Infortis\Base\Model\Import\Cms $importCms)
    {
        $this->___init();
        parent::__construct($context, $helperData, $importCms);
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

<?php
namespace Magezon\Core\Model\Eav\Attribute\Backend\Image;

/**
 * Interceptor class for @see \Magezon\Core\Model\Eav\Attribute\Backend\Image
 */
class Interceptor extends \Magezon\Core\Model\Eav\Attribute\Backend\Image implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Model\UrlInterface $backendUrl, \Magento\Framework\Filesystem $filesystem)
    {
        $this->___init();
        parent::__construct($backendUrl, $filesystem);
    }

    /**
     * {@inheritdoc}
     */
    public function validate($object)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'validate');
        if (!$pluginInfo) {
            return parent::validate($object);
        } else {
            return $this->___callPlugins('validate', func_get_args(), $pluginInfo);
        }
    }
}

<?php
namespace Magento\MediaStorage\Model\Config\Source\Storage\Media\Storage;

/**
 * Interceptor class for @see \Magento\MediaStorage\Model\Config\Source\Storage\Media\Storage
 */
class Interceptor extends \Magento\MediaStorage\Model\Config\Source\Storage\Media\Storage implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct()
    {
        $this->___init();
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'toOptionArray');
        if (!$pluginInfo) {
            return parent::toOptionArray();
        } else {
            return $this->___callPlugins('toOptionArray', func_get_args(), $pluginInfo);
        }
    }
}

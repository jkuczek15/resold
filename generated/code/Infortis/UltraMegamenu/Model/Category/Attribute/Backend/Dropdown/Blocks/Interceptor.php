<?php
namespace Infortis\UltraMegamenu\Model\Category\Attribute\Backend\Dropdown\Blocks;

/**
 * Interceptor class for @see \Infortis\UltraMegamenu\Model\Category\Attribute\Backend\Dropdown\Blocks
 */
class Interceptor extends \Infortis\UltraMegamenu\Model\Category\Attribute\Backend\Dropdown\Blocks implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct()
    {
        $this->___init();
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

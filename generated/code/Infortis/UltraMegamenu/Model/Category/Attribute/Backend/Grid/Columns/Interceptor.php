<?php
namespace Infortis\UltraMegamenu\Model\Category\Attribute\Backend\Grid\Columns;

/**
 * Interceptor class for @see \Infortis\UltraMegamenu\Model\Category\Attribute\Backend\Grid\Columns
 */
class Interceptor extends \Infortis\UltraMegamenu\Model\Category\Attribute\Backend\Grid\Columns implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\Message\ManagerInterface $messageManagerInterface)
    {
        $this->___init();
        parent::__construct($messageManagerInterface);
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

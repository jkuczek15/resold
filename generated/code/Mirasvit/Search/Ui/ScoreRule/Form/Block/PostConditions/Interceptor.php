<?php
namespace Mirasvit\Search\Ui\ScoreRule\Form\Block\PostConditions;

/**
 * Interceptor class for @see \Mirasvit\Search\Ui\ScoreRule\Form\Block\PostConditions
 */
class Interceptor extends \Mirasvit\Search\Ui\ScoreRule\Form\Block\PostConditions implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Mirasvit\Search\Ui\ScoreRule\Form\Block\PostConditionsRenderer $postConditionsRenderer, \Magento\Backend\Block\Widget\Form\Renderer\Fieldset $fieldsetRenderer, \Magento\Backend\Block\Template\Context $context, \Magento\Framework\Registry $registry, \Magento\Framework\Data\FormFactory $formFactory)
    {
        $this->___init();
        parent::__construct($postConditionsRenderer, $fieldsetRenderer, $context, $registry, $formFactory);
    }

    /**
     * {@inheritdoc}
     */
    public function fetchView($fileName)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'fetchView');
        if (!$pluginInfo) {
            return parent::fetchView($fileName);
        } else {
            return $this->___callPlugins('fetchView', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function escapeHtml($data, $allowedTags = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'escapeHtml');
        if (!$pluginInfo) {
            return parent::escapeHtml($data, $allowedTags);
        } else {
            return $this->___callPlugins('escapeHtml', func_get_args(), $pluginInfo);
        }
    }
}

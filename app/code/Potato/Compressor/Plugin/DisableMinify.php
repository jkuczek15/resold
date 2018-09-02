<?php
namespace Potato\Compressor\Plugin;

use Magento\Framework\View\Asset\Minification;
use Potato\Compressor\Model\Config;

class DisableMinify
{
    /** @var Config */
    protected $config;

    /**
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * @param Minification $subject
     * @param \Closure $proceed
     * @param $contentType
     *
     * @return mixed
     */
    public function aroundIsEnabled(
        Minification $subject,
        \Closure $proceed,
        $contentType
    ) {
        if ($this->config->isEnabled()) {
            return false;
        }
        return $proceed($contentType);
    }
}

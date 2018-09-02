<?php
namespace Potato\Compressor\Plugin;

use Magento\Theme\Model\Url\Plugin\Signature;

class DisableSignature extends Signature
{
    /** @var \Magento\Framework\View\Url\ConfigInterface */
    private $config;

    /** @var \Magento\Framework\App\View\Deployment\Version */
    private $deploymentVersion;

    /** @var  \Potato\Compressor\Model\Config */
    protected $compressorConfig;

    /**
     * @param \Magento\Framework\View\Url\ConfigInterface $config
     * @param \Magento\Framework\App\View\Deployment\Version $deploymentVersion
     * @param \Potato\Compressor\Model\Config $compressorConfig
     */
    public function __construct(
        \Magento\Framework\View\Url\ConfigInterface $config,
        \Magento\Framework\App\View\Deployment\Version $deploymentVersion,
        \Potato\Compressor\Model\Config $compressorConfig
    ) {
        $this->config = $config;
        $this->deploymentVersion = $deploymentVersion;
        $this->compressorConfig = $compressorConfig;
        parent::__construct($config, $deploymentVersion);
    }

    /**
     * Whether signing of URLs is enabled or not
     *
     * @return bool
     */
    protected function isUrlSignatureEnabled()
    {
        if ($this->compressorConfig->isEnabled()) {
            return false;
        }
        return (bool)$this->config->getValue(self::XML_PATH_STATIC_FILE_SIGNATURE);
    }
}

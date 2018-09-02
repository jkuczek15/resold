<?php
namespace Potato\Compressor\Plugin;

use Magento\Framework\App\Response\Http;
use Potato\Compressor\Model\Config;
use Potato\Compressor\Model\Optimisation\Processor;
use Potato\Compressor\Helper\Log as LogHelper;

class ProcessResponse
{
    /** @var Config  */
    protected $config;

    /** @var Processor  */
    protected $processor;

    /** @var LogHelper  */
    protected $logHelper;

    /**
     * ProcessResponse constructor.
     * @param Config $config
     * @param Processor $processor
     * @param LogHelper $logHelper
     */
    public function __construct(
        Config $config,
        Processor $processor,
        LogHelper $logHelper
    ) {
        $this->config = $config;
        $this->processor = $processor;
        $this->logHelper = $logHelper;
    }

    /**
     * @param Http $subject
     * @return void
     */
    public function beforeSendResponse(Http $subject)
    {
        if (!$this->config->isEnabled()) {
            return;
        }
        try {
            $this->processor->processHtmlResponse($subject);
        } catch (\Exception $e) {
            $this->logHelper->processorLog($e->getMessage());
        }
    }
}

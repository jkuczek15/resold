<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_GDPR
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\GDPR\Plugin\Magento\Checkout;

use Magento\Checkout\Model\Session as CheckoutSession;
use Plumrocket\GDPR\Helper\Checkboxes;
use Plumrocket\GDPR\Model\Config\Source\ConsentLocations;

/**
 * Class AbstractValidation
 */
abstract class AbstractValidation
{
    const DEFAULT_STEP_NAME = 'prgdpr_validated_consents';

    /**
     * @var Checkboxes
     */
    private $checkboxes;

    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @param Checkboxes $checkboxes
     * @param CheckoutSession $checkoutSession
     */
    public function __construct(
        Checkboxes $checkboxes,
        CheckoutSession $checkoutSession
    ) {
        $this->checkboxes = $checkboxes;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @param $data
     * @return CheckoutSession
     */
    public function setCheckoutData($data)
    {
        return $this->checkoutSession->setStepData(self::DEFAULT_STEP_NAME, $data);
    }

    /**
     * @return CheckoutSession
     */
    public function clearCheckoutData()
    {
        return $this->setCheckoutData([]);
    }

    /**
     * @param \Magento\Quote\Api\Data\PaymentInterface $paymentMethod
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function validateConsents(
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod
    ) {
        $this->clearCheckoutData();

        /** @var \Magento\Quote\Api\Data\PaymentExtensionInterface $extensionAttributes */
        $extensionAttributes = $paymentMethod->getExtensionAttributes();
        $consentIds = null !== $extensionAttributes ? $extensionAttributes->getConsentIds() : null;

        if (! $this->checkboxes->isValidConsents($consentIds, ConsentLocations::CHECKOUT, null, true)) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                __('Please provide your consent to all terms before placing the order.')
            );
        } else {
            $this->setCheckoutData($consentIds);
        }
    }
}

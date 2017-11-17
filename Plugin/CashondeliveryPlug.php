<?php

namespace Ethos\GlobalFixes\Plugin;

use Magento\Payment\Model\Method\AbstractMethod;
use Magento\Quote\Model\Quote;
use Ethos\GlobalFixes\Helper\Data as ShippingHelper;

class CashondeliveryPlug
{
    /**
     * @var ShippingHelper $shippingHelper
     */
    protected $shippingHelper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * Constructor
     *
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param ShippingHelper $shippingHelper
     */
    public function __construct
    (
        \Psr\Log\LoggerInterface $logger,
        \Magento\Checkout\Model\Session $checkoutSession,
        ShippingHelper $shippingHelper
    )
    {
        $this->logger = $logger;
        $this->_checkoutSession = $checkoutSession;
        $this->shippingHelper = $shippingHelper;

        return;
    }

    public function aroundIsAvailable(\Magento\Payment\Model\Method\AbstractMethod $subject, callable $proceed)
    {
        $shippingMethod = $this->_checkoutSession->getQuote()->getShippingAddress()->getShippingMethod();
        $country = $this->_checkoutSession->getQuote()->getShippingAddress()->getCountryId();
        $postcode = $this->_checkoutSession->getQuote()->getShippingAddress()->getPostcode();
        $realCountry = $this->shippingHelper->getCountry($country, $postcode);
        if ($realCountry != "FR" || $shippingMethod == 'colissimo_pickup') {
            return false;
        }
        $result = $proceed();
        return $result;
    }
}

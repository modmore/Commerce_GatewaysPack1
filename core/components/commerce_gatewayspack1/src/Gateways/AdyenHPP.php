<?php

namespace modmore\Commerce\GatewaysPack1\Gateways;

use modmore\Commerce\Admin\Widgets\Form\PasswordField;
use modmore\Commerce\Admin\Widgets\Form\TextField;
use modmore\Commerce\Gateways\BaseGateway;
use modmore\Commerce\GatewaysPack1\Fields\AdyenContextOptions;
use Omnipay\Adyen\Gateway;

class AdyenHPP extends BaseGateway {
    protected $omnipayGateway = 'Adyen';

    public function __construct(\Commerce $commerce, \comPaymentMethod $method)
    {
        parent::__construct($commerce, $method);
        if ($this->instance instanceof Gateway) {
            // Override the different configuration values from the context overrides for the current context
            $ctx = $this->commerce->wctx ? $this->commerce->wctx->get('key') : null;
            $contexts = $this->getProperty('contexts');
            if ($ctx !== null && is_array($contexts) && array_key_exists($ctx, $contexts)) {
                if ((string)$contexts[$ctx]['secret'] !== '') {
                    $this->instance->setSecret($contexts[$ctx]['secret']);
                }
                if ((string)$contexts[$ctx]['skinCode'] !== '') {
                    $this->instance->setSkinCode($contexts[$ctx]['skinCode']);
                }
                if ((string)$contexts[$ctx]['merchantAccount'] !== '') {
                    $this->instance->setMerchantAccount($contexts[$ctx]['merchantAccount']);
                }
            }
        }
    }

    public function getGatewayProperties(\comPaymentMethod $method)
    {
        $fields = [];

        $fields[] = new TextField($this->commerce, [
            'name' => 'properties[merchantAccount]',
            'label' => $this->adapter->lexicon('commerce_gatewayspack1.adyenhpp.merchant_account'),
            'description' => $this->adapter->lexicon('commerce_gatewayspack1.adyenhpp.merchant_account_desc'),
            'value' => $method->getProperty('merchantAccount'),
        ]);

        $fields[] = new TextField($this->commerce, [
            'name' => 'properties[skinCode]',
            'label' => $this->adapter->lexicon('commerce_gatewayspack1.adyenhpp.skin_code'),
            'description' => $this->adapter->lexicon('commerce_gatewayspack1.adyenhpp.skin_code_desc'),
            'value' => $method->getProperty('skinCode'),
        ]);

        $fields[] = new PasswordField($this->commerce, [
            'name' => 'properties[secret]',
            'label' => $this->adapter->lexicon('commerce_gatewayspack1.adyenhpp.secret'),
            'description' => $this->adapter->lexicon('commerce_gatewayspack1.adyenhpp.secret_desc'),
            'value' => $method->getProperty('secret'),
        ]);

        $fields[] = new PasswordField($this->commerce, [
            'name' => 'properties[notification_hmac]',
            'label' => $this->adapter->lexicon('commerce_gatewayspack1.adyenhpp.notification_hmac'),
            'description' => $this->adapter->lexicon('commerce_gatewayspack1.adyenhpp.notification_hmac_desc'),
            'value' => $method->getProperty('notification_hmac'),
        ]);

        $fields[] = new AdyenContextOptions($this->commerce, [
            'name' => 'properties[contexts]',
            'label' => $this->adapter->lexicon('commerce_gatewayspack1.adyenhpp.contexts'),
            'description' => $this->adapter->lexicon('commerce_gatewayspack1.adyenhpp.contexts_desc'),
            'value' => $method->getProperty('contexts'),
        ]);

        return $fields;
    }

    public function preparePurchaseOptions(array $options = [])
    {
        $options = parent::preparePurchaseOptions($options);
        $options['merchantReference'] = $this->transaction->get('id');
        $options['sessionValidity'] = date('c', time() + $this->adapter->getOption('session_cookie_lifetime'));
        $options['shipBeforeDate'] = date('c', time() + $this->adapter->getOption('session_cookie_lifetime'));

        if ($billing = $this->order->getBillingAddress()) {
            $options['shopperEmail'] = $billing->get('email');
            $options['countryCode'] = $billing->get('country');
        }
        if ($this->order->get('user') > 0) {
            $options['shopperReference'] = $this->order->get('user');
        }

        // Override the different configuration values from the context overrides for the current context
        $ctx = $this->commerce->wctx ? $this->commerce->wctx->get('key') : null;
        $contexts = $this->getProperty('contexts');
        if ($ctx !== null && array_key_exists($ctx, $contexts)) {
            if ((string)$contexts[$ctx]['secret'] !== '') {
                $options['secret'] = $contexts[$ctx]['secret'];
            }
            if ((string)$contexts[$ctx]['skinCode'] !== '') {
                $options['skinCode'] = $contexts[$ctx]['skinCode'];
            }
            if ((string)$contexts[$ctx]['merchantAccount'] !== '') {
                $options['merchantAccount'] = $contexts[$ctx]['merchantAccount'];
            }
        }
        return $options;
    }

    public static function calculateSha256Signature($hmacKey, $params)
    {
        // validate if hmacKey is provided
        if (empty($hmacKey)) {
            throw new \RuntimeException('You did not provide a HMAC key');
        }

        // validate if hmacKey contains only hexadecimal chars to be packed with H*
        if (!ctype_xdigit($hmacKey)) {
            throw new RuntimeException("Invalid HMAC key: $hmacKey");
        }

        if (empty($params)) {
            throw new RuntimeException('You did not provide any parameters');
        }

        // The character escape function
        $escapeval = function ($val) {
            return str_replace(':', '\\:', str_replace('\\', '\\\\', $val));
        };


        // Generate the signing data string
        $signData = implode(":", array_map($escapeval, array_values($params)));

        // base64-encode the binary result of the HMAC computation
        $merchantSig = base64_encode(hash_hmac('sha256', $signData, pack("H*", $hmacKey), true));
        return $merchantSig;
    }
}
<?php

use modmore\Commerce\GatewaysPack1\Gateways\AdyenHPP;

define('MODX_REQP',false);
$_REQUEST['ctx'] = 'web';

require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
require_once MODX_CONNECTORS_PATH . 'index.php';

$corePath = $modx->getOption('commerce.core_path', null, $modx->getOption('core_path') . 'components/commerce/');
require_once $corePath . 'model/commerce/commerce.class.php';
$mode = $modx->getOption('commerce.mode', null, Commerce::MODE_TEST);
$commerce = new Commerce($modx, array(
    'mode' => $mode
));

$modx->lexicon->load('commerce:default');

$modx->setLogLevel(MODx::LOG_LEVEL_INFO);
$modx->setLogTarget([
    'target' => 'FILE',
    'options' => [
        'filename' => 'adyen_notifications.log'
    ]
]);

if (!class_exists('modmore\Commerce\GatewaysPack1\Gateways\AdyenHPP')) {
    echo json_encode(['notificationResponse' => 'invalid', 'message' => 'Module not enabled.']);
    @session_write_close();
    exit();
}

$mode = $commerce->isTestMode() ? 'enabled_in_test' : 'enabled_in_live';
$method = $modx->getObject('comPaymentMethod', [
    'removed' => false,
    $mode => true,
    'gateway' => 'modmore\Commerce\GatewaysPack1\Gateways\AdyenHPP'
]);

if (!$method instanceof comPaymentMethod) {
    echo json_encode(['notificationResponse' => 'invalid', 'message' => 'Adyen is not enabled.']);
    @session_write_close();
    exit();
}

$hmacKey = $method->getProperty('notification_hmac');

$body = file_get_contents('php://input');
$body = json_decode($body, true);
if (!is_array($body)) {
    $modx->log(modX::LOG_LEVEL_ERROR, 'Invalid response body in notification, can\'t decode JSON.');
    echo json_encode(['notificationResponse' => 'invalid', 'message' => 'Invalid body']);
    @session_write_close();
    exit();
}

if (!array_key_exists('notificationItems', $body) || !is_array($body['notificationItems'])) {
    $modx->log(modX::LOG_LEVEL_ERROR, 'notificationItems missing in notification body.');
    echo json_encode(['notificationResponse' => 'invalid', 'message' => 'Invalid items']);
    @session_write_close();
    exit();
}

foreach ($body['notificationItems'] as $i => $item) {
    $innerItem = array_key_exists('NotificationRequestItem', $item) && is_array($item['NotificationRequestItem']) ? $item['NotificationRequestItem'] : false;
    if (!is_array($innerItem)) {
        $modx->log(modX::LOG_LEVEL_WARN, 'Inner item missing in ' . print_r($item, true));
        continue;
    }

    $actualHmac = $innerItem['additionalData']['hmacSignature'];

    try {
        $params = [
            'pspReference' => isset($innerItem['pspReference']) ? $innerItem['pspReference'] : '',
            'originalReference' => isset($innerItem['originalReference']) ? $innerItem['originalReference'] : '',
            'merchantAccountCode' => isset($innerItem['merchantAccountCode']) ? $innerItem['merchantAccountCode'] : '',
            'merchantReference' => isset($innerItem['merchantReference']) ? $innerItem['merchantReference'] : '',
            'value' => isset($innerItem['amount']['value']) ? $innerItem['amount']['value'] : '',
            'currency' => isset($innerItem['amount']['currency']) ? $innerItem['amount']['currency'] : '',
            'eventCode' => isset($innerItem['eventCode']) ? $innerItem['eventCode'] : '',
            'success' => isset($innerItem['success']) ? $innerItem['success'] : '',
        ];
        $expectedHmac = AdyenHPP::calculateSha256Signature($hmacKey, $params);
    }
    catch (RuntimeException $e) {
        $modx->log(modX::LOG_LEVEL_ERROR, 'hmacSignature for request could not be generated with message: ' . $e->getMessage() . ' // Item: ' . print_r($innerItem, true));

        echo json_encode(['notificationResponse' => 'invalid', 'message' => 'Could not generate HMAC signature for verification']);
        @session_write_close();
        exit();
    }

    if ($expectedHmac !== $actualHmac) {
        $modx->log(modX::LOG_LEVEL_ERROR, 'hmacSignature for request does not match expected signature. Received: ' . $actualHmac . ' - expected: ' . $expectedHmac . ' used parameters: ' . print_r($params, true));
        echo json_encode(['notificationResponse' => 'invalid', 'message' => 'Invalid HMAC signature.']);
        @session_write_close();
        exit();
    }

    $modx->log(modX::LOG_LEVEL_INFO,
        'Received ' . $innerItem['eventCode'] . ' from ' . ($body['live'] === 'true' ? 'LIVE' : 'TEST') . ': '  . json_encode($innerItem),
    '',
    '',
    'Adyen'
    );

    if ($innerItem['eventCode'] === 'AUTHORISATION') {
        $transactionId = (int)$innerItem['merchantReference'];
        $transaction = $modx->getObject('comTransaction', [
            'test' => $commerce->isTestMode(),
            'id' => $transactionId,
        ]);

        if ($transaction instanceof comTransaction) {
            $order = $transaction->getOrder();

            if ($innerItem['success'] === 'true') {
                if (!$transaction->isCompleted()) {
                    $transaction->setProperties([
                        'paymentMethod' => isset($innerItem['paymentMethod']) ? $innerItem['paymentMethod'] : '',
                    ]);
                    $transaction->set('status', \comTransaction::STATUS_COMPLETED);
                    $transaction->set('reference', $innerItem['pspReference']);
                    $transaction->set('completed_on', strtotime($innerItem['eventDate']));
                    $transaction->save();
                    $order->calculate();
                    $order->save();
                    $order->log('commerce_gatewaypack1.log.adyen_notification_paid', false, ['transaction' => $transaction->get('id')]);
                    $order->log('commerce.log.completed_transaction', false, ['method' => $method->get('id'), 'method_name' => $method->get('name'), 'transaction' => $transaction->get('id')]);

                    $modx->log(modX::LOG_LEVEL_INFO,
                        'Marked transaction ' . $transactionId . ' for order ' . $order->get('id') . ' as completed.',
                        '',
                        '',
                        'Adyen'
                    );

                    // @todo We can't currently trigger this because we have no OmniPay Response for the request
                    // When we do have a response, this entire block of code should be replaced with BaseGateway->markTransactionAsPaid()
//                    $this->commerce->dispatcher->dispatch(\Commerce::EVENT_ORDER_PAYMENT_RECEIVED, new Payment($this, $this->method, $this->transaction, $this->order, $response));

                    // Update the order status if this completes the order
                    if ($order->isPaid()) {
                        $order->triggerPaidStatusChange();
                    }
                }
                else {
                    $modx->log(modX::LOG_LEVEL_INFO,
                        'Transaction ' . $transactionId . ' for order ' . $order->get('id') . ' was already marked as completed; no action taken.',
                        '',
                        '',
                        'Adyen'
                    );
                }
            }
            else {
                $transaction->set('status', \comTransaction::STATUS_FAILED);
                $transaction->setProperties([
                    'error' => $innerItem['reason'],
                ]);
                $transaction->save();
                $order->calculate(true);
                $order->log('commerce_gatewaypack1.log.adyen_failed_transaction', false, ['method' => $method->get('id'), 'method_name' => $method->get('name'), 'transaction' => $transaction->get('id'), 'message' => $innerItem['reason']]);

                $modx->log(modX::LOG_LEVEL_INFO,
                    'Marked transaction ' . $transactionId . ' for order ' . $order->get('id') . ' as failed.',
                    '',
                    '',
                    'Adyen'
                );
            }

        }
        else {
            $modx->log(modX::LOG_LEVEL_ERROR,
                'Can\'t find transaction ' .  $transactionId . ' that was referred to in the Notification; no action taken.',
                '',
                '',
                'Adyen'
            );

        }
    }
    else {
        $modx->log(modX::LOG_LEVEL_INFO,
            'Unsupported eventCode ' . $innerItem['eventCode'],
            '',
            '',
            'Adyen'
        );
    }
}

//$modx->log(modX::LOG_LEVEL_INFO, print_r($body, true));


echo json_encode(['notificationResponse' => '[accepted]']);
@session_write_close();
exit();





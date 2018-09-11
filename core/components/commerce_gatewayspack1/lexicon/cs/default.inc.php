<?php

$_lang['commerce_gatewayspack1'] = 'GatewaysPack1';
$_lang['commerce_gatewayspack1.description'] = 'Adds additional payment gateways.';

// Potentially shared keys
$_lang['commerce_gatewayspack1.secret'] = 'Secret';

/* Adyen Hosted Payment Page */
$_lang['commerce_gatewayspack1.adyenhpp.secret'] = 'HMAC Key';
$_lang['commerce_gatewayspack1.adyenhpp.secret_desc'] = 'The HMAC Key for the chosen platform (test or live) as managed in the Adyen dashboard under Skins.';
$_lang['commerce_gatewayspack1.adyenhpp.merchant_account'] = 'Merchant Account';
$_lang['commerce_gatewayspack1.adyenhpp.merchant_account_desc'] = 'The name of the merchant account the skin is enabled for. This can be seen when editing a skin in the Adyen dashboard, under the "Valid Accounts" header.';
$_lang['commerce_gatewayspack1.adyenhpp.skin_code'] = 'Skin Code';
$_lang['commerce_gatewayspack1.adyenhpp.skin_code_desc'] = 'The unique code of the skin you\'d like to use.';
$_lang['commerce_gatewayspack1.adyenhpp.notification_hmac'] = 'Notifications HMAC Key';
$_lang['commerce_gatewayspack1.adyenhpp.notification_hmac_desc'] = 'The HMAC Key for Adyen Notifications as configured under Settings > Server settings > Additonal Settings. When receiving a notification, the Adyen integration will look for the enabled Adyen payment method in the current mode (test or live) and use the HMAC key from that payment method.';
$_lang['commerce_gatewayspack1.adyenhpp.contexts'] = 'Context Overrides';
$_lang['commerce_gatewayspack1.adyenhpp.contexts_desc'] = 'Allows you to override the values per context, to present different skins for different shops.';

$_lang['commerce_gatewaypack1.log.adyen_notification_paid'] = 'Received Adyen notification indicating authorization was successful.';
$_lang['commerce_gatewaypack1.log.adyen_failed_transaction'] = 'Adyen notification for [[+method_name]] said transaction #[[+transaction]] failed with message [[+message]]';

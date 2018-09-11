<?php

$_lang['commerce_gatewayspack1'] = 'Sistema de Conexion por Puerta de Salida y Entrada';
$_lang['commerce_gatewayspack1.description'] = 'Va anadir multiples sistemas de pago conectados.';

// Potentially shared keys
$_lang['commerce_gatewayspack1.secret'] = 'Segredo';

/* Adyen Hosted Payment Page */
$_lang['commerce_gatewayspack1.adyenhpp.secret'] = 'HMAC Key';
$_lang['commerce_gatewayspack1.adyenhpp.secret_desc'] = 'El Clave HMAC de la platforma eligida (test o live) como son administrados en la tabla central Adyen de Skins.';
$_lang['commerce_gatewayspack1.adyenhpp.merchant_account'] = 'Cuenta de Mercante';
$_lang['commerce_gatewayspack1.adyenhpp.merchant_account_desc'] = 'El nombre de cuentas de mercantes para cuales el skin es activado. Puede ver esto editando un skin en la tabla central Adyen, bajo del titulo \'\'Cuentas Validas\'\'.';
$_lang['commerce_gatewayspack1.adyenhpp.skin_code'] = 'Codigo Skin';
$_lang['commerce_gatewayspack1.adyenhpp.skin_code_desc'] = 'El unico codigo del skin que puede utilizar.';
$_lang['commerce_gatewayspack1.adyenhpp.notification_hmac'] = 'Notifications HMAC Key';
$_lang['commerce_gatewayspack1.adyenhpp.notification_hmac_desc'] = 'The HMAC Key for Adyen Notifications as configured under Settings > Server settings > Additonal Settings. When receiving a notification, the Adyen integration will look for the enabled Adyen payment method in the current mode (test or live) and use the HMAC key from that payment method.';
$_lang['commerce_gatewayspack1.adyenhpp.contexts'] = 'Context Overrides';
$_lang['commerce_gatewayspack1.adyenhpp.contexts_desc'] = 'Allows you to override the values per context, to present different skins for different shops.';

$_lang['commerce_gatewaypack1.log.adyen_notification_paid'] = 'Received Adyen notification indicating authorization was successful.';
$_lang['commerce_gatewaypack1.log.adyen_failed_transaction'] = 'Adyen notification for [[+method_name]] said transaction #[[+transaction]] failed with message [[+message]]';

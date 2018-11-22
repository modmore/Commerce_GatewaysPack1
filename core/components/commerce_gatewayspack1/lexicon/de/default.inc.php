<?php

$_lang['commerce_gatewayspack1'] = 'GatewaysPack1';
$_lang['commerce_gatewayspack1.description'] = 'Fügt zusätzliche Zahlungs-Gateways hinzu.';

// Potentially shared keys
$_lang['commerce_gatewayspack1.secret'] = 'Geheimnis';

/* Adyen Hosted Payment Page */
$_lang['commerce_gatewayspack1.adyenhpp.secret'] = 'HMAC-Schlüssel';
$_lang['commerce_gatewayspack1.adyenhpp.secret_desc'] = 'Der HMAC-Schlüssel für die gewählte Plattform (Test oder Live) wie im Adyen Dashboard unter Skins verwaltet.';
$_lang['commerce_gatewayspack1.adyenhpp.merchant_account'] = 'Händler-Konto';
$_lang['commerce_gatewayspack1.adyenhpp.merchant_account_desc'] = 'Der Name des Händler-Kontos für welches die Skin aktiviert ist. Dies ist ersichtlich, wenn Sie eine Skin im Adyen Dashboard unter Punkt "Gültige Konten" bearbeiten.';
$_lang['commerce_gatewayspack1.adyenhpp.skin_code'] = 'Skin Code';
$_lang['commerce_gatewayspack1.adyenhpp.skin_code_desc'] = 'Der eindeutige Code der Skin, die Sie verwenden möchten.';
$_lang['commerce_gatewayspack1.adyenhpp.notification_hmac'] = 'HMAC-Schlüssel-Benachrichtigungen';
$_lang['commerce_gatewayspack1.adyenhpp.notification_hmac_desc'] = 'The HMAC Key for Adyen Notifications as configured under Settings > Server settings > Additonal Settings. When receiving a notification, the Adyen integration will look for the enabled Adyen payment method in the current mode (test or live) and use the HMAC key from that payment method.';
$_lang['commerce_gatewayspack1.adyenhpp.contexts'] = 'Kontext-Überschreibungen';
$_lang['commerce_gatewayspack1.adyenhpp.contexts_desc'] = 'Allows you to override the values per context, to present different skins for different shops.';

$_lang['commerce_gatewaypack1.log.adyen_notification_paid'] = 'Received Adyen notification indicating authorization was successful.';
$_lang['commerce_gatewaypack1.log.adyen_failed_transaction'] = 'Adyen Benachrichtigung für [[+method_name]] sagte Transaktion #[[+transaction]] mit Nachricht [[+message]] gescheitert';

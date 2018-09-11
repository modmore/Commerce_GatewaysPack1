<?php

$_lang['commerce_gatewayspack1'] = 'Gateways Pakket 1';
$_lang['commerce_gatewayspack1.description'] = 'Voegt extra betaalmethoden toe.';

// Potentially shared keys
$_lang['commerce_gatewayspack1.secret'] = 'Geheime code';

/* Adyen Hosted Payment Page */
$_lang['commerce_gatewayspack1.adyenhpp.secret'] = 'HMAC Sleutel';
$_lang['commerce_gatewayspack1.adyenhpp.secret_desc'] = 'De HMAC-code voor het gekozen platform (test of live). Dit is te beheren in het Adyen dashboard bij Skins.';
$_lang['commerce_gatewayspack1.adyenhpp.merchant_account'] = 'Merchant Account';
$_lang['commerce_gatewayspack1.adyenhpp.merchant_account_desc'] = 'De naam van de merchant waar de skin voor is ingeschakeld. Dit is te vinden in het Adyen dashboard, bij het bewerken van een skin, onder de "Valid Accounts" kop.';
$_lang['commerce_gatewayspack1.adyenhpp.skin_code'] = 'Skin code';
$_lang['commerce_gatewayspack1.adyenhpp.skin_code_desc'] = 'De unieke code van de skin die je wilt gebruiken.';
$_lang['commerce_gatewayspack1.adyenhpp.notification_hmac'] = 'Notificatie HMAC Sleutel';
$_lang['commerce_gatewayspack1.adyenhpp.notification_hmac_desc'] = 'De HMAC sleutel voor de Adyen notificaties zoals ingesteld onder Instellingen > Server Instellingen > Extra instellingen. Bij het ontvangen van een notificatie zal de Adyen integratie kijken naar de ingeschakelde Adyen betaalmethode in de huidige modus (test of live) en de HMAC sleutel van die betaalmethode gebruiken.';
$_lang['commerce_gatewayspack1.adyenhpp.contexts'] = 'Context overrides';
$_lang['commerce_gatewayspack1.adyenhpp.contexts_desc'] = 'Maakt het mogelijk om per context configuratie waarden te overschrijven, bijvoorbeeld om verschillende skins per shop te gebruiken.';

$_lang['commerce_gatewaypack1.log.adyen_notification_paid'] = 'Adyen notificatie ontvangen waaruit bleek dat de authorisatie succesvol was.';
$_lang['commerce_gatewaypack1.log.adyen_failed_transaction'] = 'Adyen notificatie voor [[+method_name]] gaf aan transactie #[[+transaction]] is mislukt met bericht [[+message]]';

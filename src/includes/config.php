<?php

/**
 * Flexplorer - Application config file.
 *
 * @author     Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright  2016 Spoje.Net
 */

define('LOG_NAME', 'Flexplorer');
define('LOG_TYPE', 'syslog');
define('CONFIGS', '../configurations/');

/*
 * Výchozí odesilatel zpráv
 * Default mail sender
 */
define('EMAIL_FROM', 'flexplorer@localhost');

if (file_exists(constant('CONFIGS') . '/default.json')) {
    $clientConfig = json_decode(
        file_get_contents(constant('CONFIGS') . '/default.json'),
        true
    );
} else {
    $clientConfig = [
        'ABRAFLEXI_URL' => 'https://demo.flexibee.eu:5434',
        'ABRAFLEXI_LOGIN' => 'winstrom',
        'ABRAFLEXI_PASSWORD' => 'winstrom',
        'ABRAFLEXI_COMPANY' => 'demo'
    ];
}

/*
 * URL AbraFlexi API
 */
define('DEFAULT_ABRAFLEXI_URL', $clientConfig['ABRAFLEXI_URL']);
/*
 * AbraFlexi API User
 */
define('DEFAULT_ABRAFLEXI_LOGIN', $clientConfig['ABRAFLEXI_LOGIN']);
/*
 * AbraFlexi API Password
 */

define('DEFAULT_ABRAFLEXI_PASSWORD', $clientConfig['ABRAFLEXI_PASSWORD']);
/*
 * Společnost v AbraFlexi
 */

define('DEFAULT_ABRAFLEXI_COMPANY', $clientConfig['ABRAFLEXI_COMPANY']);

/*
 * Where store database backups
 */
define('BACKUP_DIRECTORY', '../backups');

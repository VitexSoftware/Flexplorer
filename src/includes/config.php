<?php
/**
 * Flexplorer - Application config file.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
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

if (file_exists(constant('CONFIGS').'/default.json')) {
    $clientConfig = json_decode(file_get_contents(constant('CONFIGS').'/default.json'),
        true);
} else {
    $clientConfig = [
        'FLEXIBEE_URL' => 'https://demo.flexibee.eu:5434',
        'FLEXIBEE_LOGIN' => 'winstrom',
        'FLEXIBEE_PASSWORD' => 'winstrom',
        'FLEXIBEE_COMPANY' => 'demo'
    ];
}

/*
 * URL Flexibee API
 */
define('DEFAULT_FLEXIBEE_URL', $clientConfig['FLEXIBEE_URL']);
/*
 * FlexiBee API User
 */
define('DEFAULT_FLEXIBEE_LOGIN', $clientConfig['FLEXIBEE_LOGIN']);
/*
 * FlexiBee API Password
 */

define('DEFAULT_FLEXIBEE_PASSWORD', $clientConfig['FLEXIBEE_PASSWORD']);
/*
 * Společnost v FlexiBee
 */

define('DEFAULT_FLEXIBEE_COMPANY', $clientConfig['FLEXIBEE_COMPANY']);

/*
 * Where store database backups
 */
define('BACKUP_DIRECTORY', '../backups');

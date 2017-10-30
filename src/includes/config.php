<?php
/**
 * Flexplorer - Application config file.
 *
 * @author     Vítězslav Dvořák <vitex@arachne.cz>
 * @copyright  2016 Spoje.Net
 */
define('LOG_NAME', 'Flexplorer');
define('LOG_TYPE', 'syslog');

/*
 * Výchozí odesilatel zpráv
 * Default mail sender
 */
define('EMAIL_FROM', 'flexplorer@localhost');

/*
 * URL Flexibee API
 */
define('DEFAULT_FLEXIBEE_URL', 'https://demo.flexibee.eu');
/*
 * FlexiBee API User
 */
define('DEFAULT_FLEXIBEE_LOGIN', 'winstrom');
/*
 * FlexiBee API Password
 */

define('DEFAULT_FLEXIBEE_PASSWORD', 'winstrom');
/*
 * Společnost v FlexiBee
 */

define('DEFAULT_FLEXIBEE_COMPANY', 'demo');

/*
 * Where store database backups
 */
define('BACKUP_DIRECTORY', '../backups');

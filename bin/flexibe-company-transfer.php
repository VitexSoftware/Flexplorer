<?php
require_once '../vendor/autoload.php';
define('BACKUP_DIRECTORY', '/tmp/');
define('EASE_APPNAME', 'FlexiBee Company Transfer');
define('EASE_LOGGER', 'syslog|console');

function urlToOptions($url)
{
    $optionsRaw          = parse_url($url);
    $options['url']      = $optionsRaw['scheme'].'://'.$optionsRaw['host'].':'.$optionsRaw['port'];
    $options['company']  = str_replace('/c/', '', $optionsRaw['path']);
    $options['user']     = $optionsRaw['user'];
    $options['password'] = $optionsRaw['pass'];
    return $options;
}
if ($argc == 1) {
    echo "flexibee-company-transfer https://user:password@flexibee.source.cz:5434/c/firma_a_s_  https://user:password@flexibee.source.cz:5434/c/firma_a_s_  \n";
} else {
    $srcOptions = urlToOptions($argv[1]);
    $source     = new \FlexiPeeHP\Company(['dbNazev' => $srcOptions['company']],
        $srcOptions);
    $backupFile = constant('BACKUP_DIRECTORY').$srcOptions['company'].'.winstorm-backup';
    $dstOptions = urlToOptions($argv[2]);
    $target     = new \FlexiPeeHP\Company(['dbNazev' => $dstOptions['company']],
        $dstOptions);
    $source->addStatusMessage(_('saving backup'), 'info');
    if ($source->saveBackupTo($backupFile)) {
        $source->addStatusMessage(_('backup saved'), 'success');
        $target->addStatusMessage(_('Remove company before restore'), 'info');
        if ($target->deleteFromFlexiBee() || ($target->lastResponseCode == 404)) {
            if ($target->lastResponseCode == 201) {
                $target->addStatusMessage(_('company removed before restore'));
            }
            $target->addStatusMessage(_('restore begin'), 'success');
            if ($target->restoreBackupFrom($backupFile)) {
                $target->addStatusMessage(_('backup restored'), 'success');
            } else {
                $target->addStatusMessage(sprintf(_('company %s was not restored'),
                        $dstOptions['company']), 'warning');
            }
        } else {
            $target->addStatusMessage(_('company cleanup failed'), 'warning');
        }
        unlink($backupFile);
    } else {
        $source->addStatusMessage(_('error saving backup'), 'error');
    }
}

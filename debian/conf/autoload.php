<?php
// Autoloader shim for package: flexplorer
// Includes dependency autoloaders and registers PSR-4 for Flexplorer classes.

require_once '/usr/share/php/EaseTWB5WidgetsAbraFlexi/autoload.php';
require_once '/usr/share/php/EaseTWB5Widgets/autoload.php';
require_once '/usr/share/php/EaseHtmlWidgets/autoload.php';

// Register PSR-4 autoloader for Flexplorer\ namespace
spl_autoload_register(function ($class) {
    $prefix = 'Flexplorer\\';
    $base_dir = '/usr/lib/flexplorer/classes/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

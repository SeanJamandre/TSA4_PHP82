<?php
if (version_compare(PHP_VERSION, '8.2.12', '<')) { fwrite(STDERR, "PHP 8.2.12 or newer is required.\n"); exit(1); }
echo 'PHP '.PHP_VERSION.PHP_EOL;
foreach (['pdo_sqlite','sqlite3','mbstring','openssl','fileinfo','xml','curl'] as $ext) {
    if (!extension_loaded($ext)) { fwrite(STDERR, 'Enable extension '.$ext.' in '.php_ini_loaded_file().' then retry.'.PHP_EOL); exit(1); }
}

<?php
// Local development configuration only. Not used for production deployment.
$path = __DIR__.'/.env';
$text = file_get_contents($path);
$values = ['DB_CONNECTION'=>'sqlite', 'DB_DATABASE'=>'"'.str_replace('\\','/',__DIR__.'/database/database.sqlite').'"',
    'MAIL_MAILER'=>'log', 'TASK_NOTIFICATION_RECIPIENT'=>'tasks@example.test', 'SESSION_DRIVER'=>'file',
    'CACHE_STORE'=>'file', 'QUEUE_CONNECTION'=>'sync', 'APP_URL'=>'http://127.0.0.1:8000'];
foreach ($values as $key=>$value) {
    $pattern = '/^'.preg_quote($key,'/').'=.*/m';
    $text = preg_match($pattern,$text) ? preg_replace_callback($pattern,fn()=> $key.'='.$value,$text) : $text."\n".$key.'='.$value;
}
file_put_contents($path,$text);

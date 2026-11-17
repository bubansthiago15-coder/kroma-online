<?php
declare(strict_types=1);
require_once __DIR__.'/config.php';
$driver=strtolower((string)config('DB_DRIVER','mysql'));
if(!in_array($driver,['mysql','pgsql'],true)){
    throw new RuntimeException('DB_DRIVER debe ser mysql o pgsql.');
}
return [
    'driver'=>$driver,
    'host'=>config('DB_HOST','127.0.0.1'),
    'port'=>config('DB_PORT',$driver==='pgsql'?'5432':'3306'),
    'name'=>config('DB_NAME',$driver==='pgsql'?'postgres':'kroma'),
    'user'=>config('DB_USER',$driver==='pgsql'?'postgres':'root'),
    'pass'=>config('DB_PASS',''),
    'charset'=>'utf8mb4',
    'sslmode'=>config('DB_SSLMODE',$driver==='pgsql'?'require':'disable'),
];

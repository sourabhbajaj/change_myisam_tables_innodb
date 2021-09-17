<?php
    require_once 'vendor/autoload.php';
    include_once 'config.php';

    $databaseName="rta_sync";
    $username="root";
    $password="root";    
    use Medoo\Medoo;


    $infSchema = new Medoo([
        // [required]
        'type' => 'mysql',
        'host' => '127.0.0.1',
        'database' => 'INFORMATION_SCHEMA',
        'username' => $username,
        'password' => $password,
     
        // [optional]
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_general_ci',
        'port' => 3306,
          
        // [optional] Enable logging, it is disabled by default for better performance.
        'logging' => false,
    ]);

    $db = new Medoo([
        // [required]
        'type' => 'mysql',
        'host' => '127.0.0.1',
        'database' => $databaseName,
        'username' => $username,
        'password' => $password,
     
        // [optional]
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_general_ci',
        'port' => 3306,
          
        // [optional] Enable logging, it is disabled by default for better performance.
        'logging' => false,
    ]);

?>
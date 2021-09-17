<?php
    require_once 'vendor/autoload.php';
    include_once 'config.php';

    function RandomString()
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randstring = '';
        for ($i = 0; $i < 10; $i++) {
            $randstring = $characters[rand(0, strlen($characters)-1)];
        }
        return $randstring;
    }

    for($i=0;$i<10758;$i++){
        $db->insert("holding", ["amount"=>rand(10,100)]);
        $db->insert("transaction", ["name"=>RandomString()]);
    }
?>
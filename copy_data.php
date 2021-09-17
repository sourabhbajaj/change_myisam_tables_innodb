<?php
    require_once 'vendor/autoload.php';
    include_once 'config.php';

    if(!isset($argv[1])){
        exit("Table name not passed.\n");
    }



    $limit=1000;
    $offset=0;

    if(isset($argv[2])){
        $offset=(int)$argv[2];
    }

    $tableName=$argv[1];
    ini_set('max_execution_time', 0);

//    $db->query("insert into ".$tableName."_innodb select * from ");
    $indexes=$infSchema->select("KEY_COLUMN_USAGE", ["COLUMN_NAME"], [
        "TABLE_SCHEMA"=>$databaseName,
        "TABLE_NAME"=>$tableName,
        "CONSTRAINT_NAME"=>"PRIMARY"
    ]);

    $orderBy=[];
    foreach($indexes as $ind){
        $orderBy[]=$ind["COLUMN_NAME"]." ASC";
    }
    $orderBy=join(", ", $orderBy);

    do{
        $query="insert into $tableName"."_innodb select * from $tableName order by $orderBy";
        $response=$db->query($query);
        print_r($db->error);
        file_put_contents("offset.txt",$offset);
        echo $offset."\n";
        $offset+=$limit;
    }while($response->rowCount()>0);
    file_put_contents("$tableName.txt","All done at ".date("Y-m-d H:i:s"));
?>
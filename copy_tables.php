<?php
    require_once 'vendor/autoload.php';
    include_once 'config.php';
    use Medoo\Medoo;    

    $tables=$infSchema->select("TABLES", ["TABLE_NAME", "ENGINE"], [
        "TABLE_SCHEMA"=>$databaseName,
        "engine"=>"MyISAM"
    ]);

    foreach($tables as $table){
        $struct=$db->query("SHOW CREATE TABLE ".$table["TABLE_NAME"])->fetch();
        $createTable=$struct["Create Table"];
        $createTable=str_replace("MyISAM","InnoDB",$createTable);
        $createTable=str_replace($table["TABLE_NAME"],$table["TABLE_NAME"]."_innodb",$createTable);

        /*$foreignKeys=$infSchema->select("KEY_COLUMN_USAGE", 
                [
                    "TABLE_NAME", 
                    "COLUMN_NAME", 
                    "CONSTRAINT_NAME", 
                    "REFERENCED_TABLE_NAME", 
                    "REFERENCED_COLUMN_NAME"
                ],
                [
                    "OR"=>[
                        "TABLE_NAME"=>$table["TABLE_NAME"],
                        "REFERENCED_TABLE_NAME"=>$table["TABLE_NAME"]
                    ],
                    "AND"=>[
                        "REFERENCED_TABLE_NAME[~]"=>null
                    ]
                ]
            );*/

        echo "\nProcessing ".$table["TABLE_NAME"]."\n";
        echo "--------------------------------------\n";
        $response=$db->query($createTable)->fetch();
                
        /*$createTable=$struct[0]["Create Table"];
        print_r($createTable);*/
    }


?>
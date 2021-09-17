<?php
    require_once 'vendor/autoload.php';
    include_once 'config.php';
    use Medoo\Medoo;

    $tables=$infSchema->select("TABLES", ["TABLE_NAME", "ENGINE"], [
        "TABLE_SCHEMA"=>$databaseName,
        "engine"=>"MyISAM"
    ]);

    foreach($tables as $table){
        $tableName=$table["TABLE_NAME"];
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

        echo "\nProcessing ".$table["TABLE_NAME"]." starting at ".date("Y-m-d H:i:s")."\n";
        echo "--------------------------------------\n";
        $response=$db->query($createTable)->fetch();

        if (!$db->error) {
            echo "\nCopying data from old to new table: \n";
            $indexes=$infSchema->select("KEY_COLUMN_USAGE", ["COLUMN_NAME"], [
                "TABLE_SCHEMA"=>$databaseName,
                "TABLE_NAME"=>$tableName,
                "CONSTRAINT_NAME"=>"PRIMARY"
            ]);
            $orderBy=[];
            foreach ($indexes as $ind) {
                $orderBy[]=$ind["COLUMN_NAME"]." ASC";
            }
            $orderBy=join(", ", $orderBy);

            $query="insert into $tableName"."_innodb select * from $tableName order by $orderBy";
            $response=$db->query($query);
            if ($db->error) {
                echo "\nError in copying data of $tableName: \n";
                print_r($db->error);
                echo "\nAborting for table $tableName\n";
                echo "\n------------------------------------------\n";
            } else {
                echo "\nEnd: Copying data from old to new table at ".date("Y-m-d H:i:s")."\n";

                echo "\n\nRenaming table from $tableName to $tableName"."_myisam at ".date("Y-m-d H:i:s");
                $renameQuery="RENAME TABLE `$tableName` TO `$tableName"."_myisam`";

                $response=$db->query($renameQuery);
                if ($db->error) {
                    echo "\n\nError in renaming table $tableName. \n";
                    print_r($db->error);
                } else {
                    echo "\n\nEnd: Renaming table from $tableName to $tableName"."_myisam at ".date("Y-m-d H:i:s")."\n";

                    echo "\n\nRenaming table from $tableName"."_innodb to $tableName at ".date("Y-m-d H:i:s")."\n";
                    $renameQuery="RENAME TABLE `$tableName"."_innodb` TO `$tableName`";
                    $response=$db->query($renameQuery);

                    if ($db->error) {
                        echo "\n\nError in renaming table $tableName. \n";
                        print_r($db->error);
                    } else {
                        echo "\n\nEnd: Renaming table from $tableName"."_innodb to $tableName at ".date("Y-m-d H:i:s")."\n";
                    }
                }
                echo "\n\n";
            }
        }else{
            echo "\n\nError in processing $tableName: \n";
            print_r($db->error);
        }

        
        echo "\nEnd: Processing ".$table["TABLE_NAME"]." starting at ".date("Y-m-d H:i:s")."\n";
        echo "--------------------------------------\n";

        /*$createTable=$struct[0]["Create Table"];
        print_r($createTable);*/
    }
?>
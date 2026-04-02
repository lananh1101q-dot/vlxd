<?php
$host = '127.0.0.1'; $u = 'root'; $p = '';
$dbs = ['vlxd_user', 'vlxd_product', 'vlxd_warehouse', 'vlxd_customer', 'vlxd_manufacturing'];
foreach($dbs as $dbName){
    echo "\n=== Database: $dbName ===\n";
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbName", $u, $p);
        $resT = $pdo->query("SHOW TABLES");
        while($t = $resT->fetch(PDO::FETCH_NUM)){
            echo "Table: $t[0]\n";
            $resC = $pdo->query("DESCRIBE $t[0]");
            while($c = $resC->fetch(PDO::FETCH_ASSOC)) {
                echo " - " . $c['Field'] . " (" . $c['Type'] . ")\n";
            }
        }
    } catch(Exception $e) { echo "Loi: $dbName - " . $e->getMessage() . "\n"; }
}

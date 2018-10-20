<?php
/**
 * Created by PhpStorm.
 * User: Conor Howland
 * Date: 19/10/2018
 * Time: 21:39
 */

if (isset($_GET["username"]) and $_GET["username"] != "") {
    $host = '127.0.0.1';
    $db   = 'allposts';
    $user = 'root';
    $pass = '';
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    try {
        $pdo = new PDO($dsn, $user, $pass, $options);
    } catch (\PDOException $e) {
        throw new \PDOException($e->getMessage(), (int)$e->getCode());
    }

    $ppd = $pdo->prepare("SELECT * FROM `indexedusers` where name=?");

    $ppd->execute([$_GET["username"]]);

    if ($ppd->rowCount() > 0) {
        echo "indexed";
    } else {
        echo "unindexed";
    }

} else {
    echo "invalid";
}
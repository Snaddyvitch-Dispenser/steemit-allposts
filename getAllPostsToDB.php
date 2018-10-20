<?php

require_once 'vendor/autoload.php';
$config['webservice_url'] = 'steemd.privex.io';
$api = new DragosRoua\PHPSteemTools\SteemApi($config);
date_default_timezone_set('UTC');
$dateNow = (new \DateTime())->format('Y-m-d\TH:i:s');

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

$checkdownloaded = $pdo->prepare("SELECT id,permlink FROM posts WHERE author=? ORDER BY id ASC");

$checkdownloaded->execute([$_GET["username"]]);

if ($checkdownloaded->rowCount() > 0) {
    $start = $checkdownloaded->fetch()["permlink"];
} else {
    $start = "";
}

$params = [$_GET['username'], $start, $dateNow, 100];
$discuss100 = $api->getDiscussionsByAuthorBeforeDate($params);
$qry = $pdo->prepare("INSERT IGNORE INTO posts VALUES (?,?,?,?,?,?,?,?)");
$limit = 0;
$prev = "";
foreach ($discuss100 as $key=>$value) {
    if (isset($value["permlink"])) {
        $qry->execute([$value["id"], $value["author"], $value["permlink"], $value["category"], $value["title"], $value["body"], $value["json_metadata"], strtotime($value["created"])]);
        $prev = $value["permlink"];
    } else {
        die();
    }
    $limit++;
}

if ($limit >= 100) {

    echo "{'doreload': true}";

    /*$params = [$_GET['username'], $prev, $dateNow, 100];
    $discuss100 = $api->getDiscussionsByAuthorBeforeDate($params);

    foreach ($discuss100 as $key=>$value) {
        if ($key != 0) {
            $qry->execute([$value["id"], $value["author"], $value["permlink"], $value["category"], $value["title"], $value["body"], $value["json_metadata"]]);
        }
        $limit++;
        $prev = $value["permlink"];
    }*/
} else {
    $markdone = $pdo->prepare("INSERT into indexedusers VALUES (?)");
    $markdone->execute([$_GET["username"]]);
    echo "{'doreload': false}";
}
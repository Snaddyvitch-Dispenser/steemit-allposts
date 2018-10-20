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

//Check if posts up to date!
$checkdownloaded = $pdo->prepare("SELECT id,permlink FROM posts WHERE author=? ORDER BY id DESC");

$checkdownloaded->execute([$_GET["username"]]);

if ($checkdownloaded->rowCount() > 0) {
    $end = $checkdownloaded->fetch()["permlink"];
} else {
    $end = "";
}


$params = [$_GET['username'], "", $dateNow, 1];
$discuss100 = $api->getDiscussionsByAuthorBeforeDate($params);

//If no changes made
foreach ($discuss100 as $key=>$value) {
    if($value["permlink"] == $end) {
        echo "{'doreload': false}";
        die();
    } else {
        $stdb = $pdo->prepare("SELECT * FROM needsfilling WHERE username=?");
        $stdb->execute([$_GET["username"]]);
        if ($stdb->rowCount() <= 0) {

            $settodb = $pdo->prepare("INSERT INTO needsfilling VALUES(?,?,?)");

            $settodb->execute([$_GET["username"], $end, $value["permlink"]]);
        } else {

            //Get posts to grab

            $stdb = $pdo->prepare("SELECT * FROM needsfilling WHERE username=?");
            $stdb->execute([$_GET["username"]]);

            $data_fetch = $stdb->fetch();

            $params = [$_GET['username'], "", $dateNow, 100];
            $discuss100 = $api->getDiscussionsByAuthorBeforeDate($params);
            $qry = $pdo->prepare("INSERT IGNORE INTO posts VALUES (?,?,?,?,?,?,?,?)");
            $limit = 0;
            $prev = "";
            foreach ($discuss100 as $key=>$value) {
                $qry->execute([$value["id"],$value["author"],$value["permlink"],$value["category"],$value["title"],$value["body"],$value["json_metadata"],strtotime($value["created"])]);
                $limit++;
                $prev = $value["permlink"];

                if ($prev == $end) {

                    //Done

                    $delete = $pdo->prepare("DELETE FROM `needsfilling` WHERE username = ?");

                    $delete->execute([$_GET["username"]]);

                    $markdone = $pdo->prepare("INSERT into indexedusers VALUES (?)");
                    $markdone->execute([$_GET["username"]]);

                    echo "{'doreload': false}";

                    die();

                }
            }

            //If not killed, do reload

            echo "{'doreload': true}";

        }
    }
}

/*
$params = [$_GET['username'], "", $dateNow, 100];
$discuss100 = $api->getDiscussionsByAuthorBeforeDate($params);
$qry = $pdo->prepare("INSERT IGNORE INTO posts VALUES (?,?,?,?,?,?,?,?)");
$limit = 0;
$prev = "";
foreach ($discuss100 as $key=>$value) {
    $qry->execute([$value["id"],$value["author"],$value["permlink"],$value["category"],$value["title"],$value["body"],$value["json_metadata"],strtotime($value["created"])]);
    $limit++;
    $prev = $value["permlink"];
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
    }
} else {
    $markdone = $pdo->prepare("INSERT into indexedusers VALUES (?)");
    $markdone->execute([$_GET["username"]]);
    echo "{'doreload': false}";
}*/
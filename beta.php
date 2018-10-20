<?php
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

//BETA SOFTWARE//

?>

<!DOCTYPE HTML>
<html>

<head>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-45168180-10"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'UA-45168180-10');
    </script>

    <title>Steemit All-Posts</title>
    <meta name="description" content="Steemit Allposts is the best way to go back in time and filter posts efficiently!"/>
    <meta name="keywords" content="steem,steemit,post,make,money,blockchain,history,filter,rewards,voting,split,share"/>
    <meta charset="utf-8"/>
    <meta lang="en"/>
    <meta name="author" content="Conor Howland (@cadawg)"/>
    <meta name="generator" content="@cadawg"/>
    <meta name="copyright" content="Copyright Conor Howland 2018"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>

    <link type="text/css" rel="stylesheet" href="style.css"/>
    <script
        src="https://code.jquery.com/jquery-3.3.1.min.js"
        integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
        crossorigin="anonymous"></script>

    <script>
        function toBeta() {
            return false;
        }
    </script>

    <script src="sync.js"></script>
    <link href="https://fonts.googleapis.com/css?family=Montserrat|Pacifico" rel="stylesheet">

</head>



<body class="gr__pacificoduck">

<div id="wrapper">

    <div id="header">

        <div id="id">

            <p>Steemit<br>All Posts</p>

        </div>
        <div id="nav">

            <ul>
                <li><form><label id="ckky-lbl" for="ckky">Newest First</label><input id="ckky" type="checkbox" <?php echo isset($_GET["reverse"]) ? "checked='true'" : ""; ?> name="reverse"><input type="text" placeholder="Steemit Username without @" value="<?php if(isset($_GET["username"])) {echo $_GET["username"];} ?>" name="username"><input type="text" placeholder="Tag to filter for (one only)" name="tags" value="<?php if(isset($_GET["tags"])) {echo $_GET["tags"];} ?>"><input type="submit"></form></li>
            </ul>

        </div>

    </div>

    <div id="banner">

        <h1> Enter Search Above To Get Posts</h1>
        <p>You must enter a username, but then optionally you can also specify a tag to get all posts of a certain theme. Thank <a href="https://steemit.com/@playfulfoodie">@playfulfoodie</a> for the idea!</p>
        <a href="index.php" style="text-align: center; font-size: 50px; margin: 0 auto; display: block; color: orangered;">EXIT BETA!</a>
    </div>
    <div id="content">

        <div class="postby">
            <?php
            if(isset($_GET["username"]) and $_GET["username"] != "") {
                ?>
                <h1>Posts By @<?php echo $_GET["username"]; ?> (<?php echo isset($_GET["reverse"]) ? "New" : "Old" ?>est First)</h1>
                <?php


                if (isset($_GET["reverse"])) {
                    $posts = $pdo->prepare("SELECT * FROM posts WHERE author=? ORDER BY id DESC");
                } else {
                    $posts = $pdo->prepare("SELECT * FROM posts WHERE author=? ORDER BY id ASC");
                }

                $posts->execute([$_GET["username"]]);

                //Optimise loop

                if (isset($_GET["tags"]) and $_GET["tags"] != "") {

                    foreach ($posts->fetchAll() as $key=>$value) {

                            $metaraw = json_decode($value["jsonmeta"]);

                            $search = (isset($metaraw->tags)) ? $metaraw->tags : [];

                            if (in_array($_GET["tags"], $search)) {
                                echo("<a class='ip-link' target='_blank' href='https://steemit.com/@" . $value['author'] . "/" . $value["permlink"] . "'>" . $value['title'] . "</a>");
                            }


                    }

                } else {

                    foreach ($posts->fetchAll() as $key=>$value) {
                        echo("<a class='ip-link' target='_blank' href='https://steemit.com/@" . $value['author'] . "/" . $value["permlink"] . "'>" . $value['title'] . "</a>");
                    }

                }

                /*
                if(isset($_GET["prevpage"])) {
                    echo "<a class='navbutton' href='?username=" . $_GET['username'] . "&startat=" . $_GET["prevpage"] . "&tags=" . $_GET["tags"] . "'>Previous Page!</a>";
                }
                if (isset($discuss100[24])) {
                    echo "<a class='navbutton' href='?username=" . $_GET['username'] . "&startat=" . $discuss100[24]["permlink"] . "&prevpage=" . $_GET["startat"] . "&tags=" . $_GET["tags"] . "'>Next Page!</a>";
                } else {
                    echo "<a class='backtostart' href='?username=" . $_GET['username'] . "&tags=" . $_GET["tags"] . "'>Back to start!</a>";
                }*/
                echo "<a class='disabled'>DISABLED</a>";
            }
            ?>
            <p>Copyright Conor Howland 2018 - <a href="https://steemit.com/@cadawg">@cadawg</a> - <a href="https://conor.icu">My Website</a></p>
        </div>

    </div>

    <script async="async">
        sync("<?php echo (isset($_GET["username"]) ? $_GET["username"] : ""); ?>");
    </script>

</div>

</body>

</html>

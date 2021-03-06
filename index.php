<?php

$actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

require_once 'vendor/autoload.php';
$config['webservice_url'] = 'steemd.privex.io';
$api = new DragosRoua\PHPSteemTools\SteemApi($config);
date_default_timezone_set('UTC');
$dateNow = (new \DateTime())->format('Y-m-d\TH:i:s');
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
            $(".warning").addClass("visible");
        }
    </script>

    <script src="sync.js"></script>
    <link href="https://fonts.googleapis.com/css?family=Montserrat|Pacifico" rel="stylesheet">

</head>



<body class="gr__pacificoduck">

<div class="warning">
    <h3>Hey Steemian,</h3>
    <p>You deserve better! We have your data downloaded, so now you can use the new version of this which is faster and allows for easier searching! Try it <a href="beta.php?reverse=on&username=<?php echo isset($_GET["username"]) ? $_GET["username"] : "" ; ?>&tags=<?php echo isset($_GET["tags"]) ? $_GET["tags"] : "" ; ?>"> here</a> &lt;&dash;&dash;&dash;&dash;&dash;&dash;&dash;&dash;&dash;</p>
    <button class="close" onclick="this.parentNode.parentNode.removeChild(this.parentNode); return false;">&times;</button>
</div>

<div id="wrapper">

    <div id="header">

        <div id="id">

            <p>Steemit<br>All Posts</p>

        </div>
        <div id="nav">

            <ul>
                <li><form><input type="text" placeholder="Steemit Username without @" value="<?php if(isset($_GET["username"])) {echo $_GET["username"];} ?>" name="username"><input type="text" placeholder="Tag to filter for (one only)" name="tags" value="<?php if(isset($_GET["tags"])) {echo $_GET["tags"];} ?>"><input type="submit"></form></li>
            </ul>

        </div>

    </div>

    <div id="banner">

        <h1> Enter Search Above To Get Posts</h1>
        <p>You must enter a username, but then optionally you can also specify a tag to get all posts of a certain theme. Thank <a href="https://steemit.com/@playfulfoodie">@playfulfoodie</a> for the idea!<br/>Please note, there is a new version, but for it to work, we need to download all your posts (which takes a while & a lot of resources [So Please Consider Donating to @cadawg]), we will notify you when it is ready. So after entering a username, keep this page open if you can :) It is all done on our server and will not impact on your device's performance</p>
    </div>
    <div id="content">

        <div class="postby">
            <?php
                if(isset($_GET["username"]) and $_GET["username"] != "") {
                    ?>
                    <h1>Posts By @<?php echo $_GET["username"]; ?> (Newest First)</h1>
            <?php
                    if (!isset($_GET['startat'])) {
                        $_GET['startat'] = "";
                    }
                    $params = [$_GET['username'], $_GET['startat'], $dateNow, 25];
                    $discuss100 = $api->getDiscussionsByAuthorBeforeDate($params);
                    foreach ($discuss100 as $key=>$value) {
                        if (isset($_GET["tags"]) and $_GET["tags"] != "") {

                            if (in_array($_GET["tags"], json_decode($value["json_metadata"])->tags)) {
                                echo("<a class='ip-link' target='_blank' href='https://steemit.com/@" . $value['author'] . "/" . $value["permlink"] . "'>" . $value['title'] . "</a>");
                            }

                        } elseif (!($_GET['startat'] !== "" and $key === 0)) {
                                echo("<a class='ip-link' target='_blank' href='https://steemit.com/@" . $value['author'] . "/" . $value["permlink"] . "'>" . $value['title'] . "</a>");
                        }
                    }

                    if (!isset($_GET["tags"])) {
                        $_GET["tags"] = "";
                    }
                    if(isset($_GET["prevpage"])) {
                        echo "<a class='navbutton' href='?username=" . $_GET['username'] . "&startat=" . $_GET["prevpage"] . "&tags=" . $_GET["tags"] . "'>Previous Page!</a>";
                    }
                    if (isset($discuss100[24])) {
                        echo "<a class='navbutton' href='?username=" . $_GET['username'] . "&startat=" . $discuss100[24]["permlink"] . "&prevpage=" . $_GET["startat"] . "&tags=" . $_GET["tags"] . "'>Next Page!</a>";
                    } else {
                        echo "<a class='backtostart' href='?username=" . $_GET['username'] . "&tags=" . $_GET["tags"] . "'>Back to start!</a>";
                    }
                    echo "<a class='ascsv' href='csv.php?user=" . $_GET["username"] . "&startat=" . $_GET["startat"] ."'>Download As CSV</a>";
                }
            ?>
            <p>Copyright Conor Howland 2018 - <a href="https://steemit.com/@cadawg">@cadawg</a> - <a href="https://conor.icu">My Website</a></p>
        </div>

    </div>

</div>

<script async="async">
   sync("<?php echo (isset($_GET["username"]) ? $_GET["username"] : ""); ?>");
</script>

</body>

</html>
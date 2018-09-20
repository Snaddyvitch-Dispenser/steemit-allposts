<?php

$actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

function array_to_csv_download($array, $filename = "export.csv", $delimiter=";") {
    // open raw memory as file so no temp files needed, you might run out of memory though
    $f = fopen('php://memory', 'w');
    // loop over the input array
    foreach ($array as $line) {
        // generate csv lines from the inner arrays
        fputcsv($f, $line, $delimiter);
    }
    // reset the file pointer to the start of the file
    fseek($f, 0);
    // tell the browser it's going to be a csv file
    header('Content-Type: application/csv');
    // tell the browser we want to save it instead of displaying it
    header('Content-Disposition: attachment; filename="'.$filename.'";');
    // make php send the generated csv lines to the browser
    fpassthru($f);
}


require_once 'vendor/autoload.php';
$config['webservice_url'] = 'api.steemit.com';
$api = new DragosRoua\PHPSteemTools\SteemApi($config);
date_default_timezone_set('UTC');
$dateNow = (new \DateTime())->format('Y-m-d\TH:i:s');
if(!isset($_GET['csv'])) {
?>
<!DOCTYPE HTML>
<html>
<head>
    <title>Get All of An Authors Posts</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<form action="index.php">
    <input type="text" name="user" value="<?php if(isset($_GET['user'])) {echo $_GET['user'];} ?>" placeholder="Username without @ (hit enter to submit)" class="w3-input">
    If you see errors/pages with 0 items, It COULD mean that the node we use is down! <?php if(isset($_GET['user'])) {echo '<a href="' . $actual_link . "&csv" . '">Get Page Data as CSV</a>';} ?>
</form>
<?php };
if(isset($_GET['user'])) {
    $arr_csv = [["steem link", "busy link", "title", "date"]];
    if (!isset($_GET['startat'])) {
        $_GET['startat'] = "";
    }
    $params = [$_GET['user'], $_GET['startat'], $dateNow, 25];
    $discuss100 = $api->getDiscussionsByAuthorBeforeDate($params);
    foreach ($discuss100 as $key => $value) {
        if (!($_GET['startat'] !== "" and $key === 0)) {
            if (!isset($_GET['csv'])) {
                echo("<a class='ip-link' href='https://steemit.com/@" . $value['author'] . "/" . $value["permlink"] . "'>" . $value['title'] . "</a><br>");
            } else {
                $arr_csv[] = ["https://steemit.com/@" . $value['author'] . "/" . $value["permlink"], "https://busy.org/@" . $value['author'] . "/" . $value["permlink"], $value['title'], $value['created']];
            }
        }
    }
    if (isset($_GET['csv'])) {
        array_to_csv_download($arr_csv, $_GET['user'] . "-page-" . $_GET['startat'] . ".csv", ",");
    } else {
        if (isset($discuss100[24])) {
            if ($_GET['startat'] == "") {
                echo "25 Posts<br>";
            } else {
                echo "24 Posts<br>";
            }
            echo "<a class='w3-button w3-blue' href='/allposts/?user=" . $_GET['user'] . "&startat=" . $discuss100[24]["permlink"] . "'>Next Page!</a>";
        } else {
            $postqty = count($discuss100);
            if ($_GET['startat'] == "") {
                $postqty--;
            }
            echo $postqty . " Posts <br>";
            echo 'That\'s all folks!<br>';
            echo "<a class='w3-button w3-red' href='/allposts/?user=" . $_GET['user'] . "'>Back to start!</a>";
        }
    }
}

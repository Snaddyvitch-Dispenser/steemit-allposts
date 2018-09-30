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
if(isset($_GET['user'])) {
    $arr_csv = [["link", "tags", "title", "date"]];
    if (!isset($_GET['startat'])) {
        $_GET['startat'] = "";
    }
    $params = [$_GET['user'], $_GET['startat'], $dateNow, 25];
    $discuss100 = $api->getDiscussionsByAuthorBeforeDate($params);
    foreach ($discuss100 as $key => $value) {
        if (!($_GET['startat'] !== "" and $key === 0)) {
                $arr_csv[] = ["https://steemit.com/@" . $value['author'] . "/" . $value["permlink"], implode(" ",json_decode($value["json_metadata"])->tags), $value['title'], $value['created']];
        }
    }
    array_to_csv_download($arr_csv, $_GET['user'] . "-page-" . $_GET['startat'] . ".csv", ",");
}

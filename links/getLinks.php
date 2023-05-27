<?php
$from_ajax = true;
require_once('include_files/init.php');
ini_set('display_errors', 1);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
ini_set('max_execution_time', 3400);
$db_link = Database::getConnection();
$url = '';
$depth = '';
if (isset($_POST['url'])) {
    $url = trim($_POST['url']);
}
if (isset($_POST['depth'])) {
    $depth = trim($_POST['depth']);
}
if ($url == '') {
//    $url = 'https://stackoverflow.com/questions/38392267/php-how-to-get-all-hyperlinks-from-a-specific-div-of-a-given-page';
//    $url = 'https://sportshub.fan/event/world_championships_sheffield_132108992/';
//    $url = 'https://www.google.com/search?client=firefox-b-d&q=%D7%94%D7%9B%D7%A0%D7%AA+%D7%98%D7%97%D7%99%D7%A0%D7%94#fpstate=ive&vld=cid:7f9609d7,vid:4nrYqnK7lrs';
//    $url = 'https://www.google.com/search?client=firefox-b-d&q=android+button';
//    $url = 'https://www.10dakot.co.il/recipe/%D7%98%D7%97%D7%99%D7%A0%D7%94/';
//    $depth = 2;
}
if ($url == '') {
    echo json_encode(['result' => 1, 'msg' => 'לא נשלח לינק']);
    exit();
}
if ($depth == '') {
    echo json_encode(['result' => 1, 'msg' => 'לא נשלח עומק חיפוש']);
    exit();
}
if (!is_numeric($depth)) {
    echo json_encode(['result' => 1, 'msg' => 'יש להזין מספר בעומק חיפוש']);
    exit();
}
$all_pages_arr = [];
$stmt = $db_link->prepare("
                                SELECT *
                                FROM links l
                                WHERE l.main_link = ?
                                AND l.depth = ?
                                ORDER BY l.id
                              ");
$stmt->bind_param('si', $url, $depth);
$stmt->execute();
$result = $stmt->get_result();
if ($result) {
    while ($row = $result->fetch_object()) {
        $all_pages_arr[] = $row->sub_link;
    }
}
if (count($all_pages_arr)) {
    echo json_encode(['result' => 0, 'all_sub_links' => $all_pages_arr]);
    exit();
}
$all_pages_arr = findAllLinksInUrl($url, $depth);
//echo '<pre>';
//print_r($all_pages_arr);
//echo '</pre>';
$insert_str = '';
foreach ($all_pages_arr as $link) {
    if ($insert_str != '') {
        $insert_str .= ',';
    }
    $insert_str .= '(\'' . $url. '\', \'' . $depth . '\', \'' . $link . '\')';
}
$query = "INSERT INTO links
          (main_link, depth, sub_link)
          VALUES
        " . $insert_str;
//echo '-' . $query . '-';
mysqli_query($db_link, $query);
$stmt = $db_link->prepare("
                            DELETE FROM main_links
                            WHERE main_link = ?
                            AND depth = ?
                          ");
$stmt->bind_param('si', $url, $depth);
$stmt->execute();
$stmt->close();
$stmt = $db_link->prepare("
                            INSERT INTO main_links
                            (main_link, depth)
                            VALUES (?, ?)
                          ");
$stmt->bind_param('si', $url, $depth);
$stmt->execute();
$stmt->close();
echo json_encode(['result' => 0, 'all_sub_links' => $all_pages_arr]);
exit();
function findAllLinksInUrl($url, $depth) {
    global $all_pages_arr;
    static $finished = false;
    static $url_org = '';
    static $depth_org = '';
    if ($url_org == '') {
        $url_org = $url;
    }
    if ($depth_org == '') {
        $depth_org = $depth;
    }
//    echo $url_org . '-' . $depth_org . '<br />';
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-type: text/plain']);
    $resp = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);
    if ($httpCode != 200) {
        return;
    }
    $url_arr = parse_url($url);
    $base_url = $url_arr['scheme'] . '://' . $url_arr['host'];
//    $links = preg_match_all ("/href=\"([^\"]+)\"/i", $content, $matches, PREG_SET_ORDER);
    preg_match_all('/<a\s+.*?href=[\"\']?([^\"\' >]*)[\"\']?[^>]*>(.*?)<\/a>/i', $resp, $matches, PREG_SET_ORDER);
    $new_matches = [];
    foreach ($matches as $match) {
        if ($match[1] == '') {
            continue;
        }
        if (strpos($match[1], '#') !== false) {
            continue;
        }
        if (substr($match[1], 0, 2) == '//') {
            continue;
        }
        if (substr($match[1], 0, 10) == 'javascript') {
            continue;
        }
        if (substr($match[1], 0, 1) == '/') {
            $match[1] = $base_url . $match[1];
        }
        $match_arr = explode('?', $match[1]);
        $match_for_check = $match_arr[0];
        if (isset($all_pages_arr[$match_for_check])) {
            continue;
        }
        $all_pages_arr[$match_for_check] = $match[1];
        $new_matches[] = $match[1];
    }
    foreach ($new_matches as $match) {
        if ($depth > 1) {
            findAllLinksInUrl($match, $depth - 1);
        }
        else {
            $finished = true;
        }
    }
    if ($finished) {
        return $all_pages_arr;
    }
}

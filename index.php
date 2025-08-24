<?php 
$curl = curl_init();

curl_setopt($curl, CURLOPT_URL, 'https://news.ycombinator.com/');

curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');

curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($curl);

// echo $response;

curl_close($curl);

$response = mb_convert_encoding($response, 'HTML-ENTITIES', 'UTF-8');

$dom = new DOMDocument();
$dom->loadHTML($response);

$xpath = new DOMXPath($dom);
$rows = $xpath->query('//tr[contains(@class, "athing")]');

$scrap_total = array();
foreach ($rows as $row) {

    $rank = $xpath->query('.//span[contains(@class, "rank")]', $row)->item(0)?->textContent ?? null;
    $title = $xpath->query('.//span[contains(@class, "titleline")]/a', $row)->item(0)?->textContent ?? null;

    $nextRow = $row->nextSibling;
    $score = null;
    $comments = null;
    if ($nextRow && $nextRow->nodeType === XML_ELEMENT_NODE) {
        $score = $xpath->query('.//span[contains(@class, "score")]', $nextRow)->item(0)?->textContent ?? null;
        $comments = $xpath->query('.//span[contains(@class, "subline")]/a[contains(@href, "item?id=") and not(contains(@href, "goto=news"))]', $nextRow)->item(0)?->textContent ?? null;
    }

    $scrap_total[] = array(
        'rank' => $rank,
        'title' => $title,
        'npalabras' => str_word_count($title),
        'score' => $score,
        'comment' => $comments
    );
}

print_r($scrap_total);
?>
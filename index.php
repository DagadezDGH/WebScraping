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
$scrap_title_5_words = array();
$scrap_title_less_5_words = array();
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
        'npalabras' => str_word_count($title, 0, '0..9.'),
        'score' => $score,
        'comment' => $comments
    );
    if (str_word_count($title, 0, '0..9.') >= 5) {
        $scrap_title_5_words[] = array(
            'rank' => $rank,
            'title' => $title,
            'score' => $score,
            'comment' => $comments
        );
    } else {
        $scrap_title_less_5_words[] = array(
            'rank' => $rank,
            'title' => $title,
            'score' => $score,
            'comment' => $comments
        );
    }  
}

print_r($scrap_total);
print_r($scrap_title_5_words);
print_r($scrap_title_less_5_words);
?>
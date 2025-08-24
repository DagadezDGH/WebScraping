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
$ranks = $xpath->query('//span[contains(@class, "rank")]');
$titles = $xpath->query('//span[contains(@class, "titleline")]/a');
$scores = $xpath->query('//span[contains(@class, "score")]');

$scrap_total = array();
for ($i=0; $i < $ranks->length; $i++) {
    $rank = trim($ranks->item($i)->nodeValue);
    $title = trim($titles->item($i)->nodeValue);
    $score = trim($scores->item($i)->nodeValue);

    $scrap_total[] = array(
        'rank' => $rank,
        'title' => $title,
        'npalabras' => str_word_count($title),
        'score' => $score,
    );
}

print_r($scrap_total);
?>
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

// print_r($scrap_total);
// print_r($scrap_title_5_words);
// print_r($scrap_title_less_5_words);
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Web Scraping</title>
        <style>
            .result-box {
                border: 2px solid #333;
                border-radius: 8px;
                padding: 15px;
                margin: 20px auto;
                width: 60%;
                background: #f9f9f9;
                font-family: Arial, sans-serif;
            }
            .result-item {
                padding: 8px;
                border-bottom: 1px solid #ddd;
            }
            .result-item:last-child {
                border-bottom: none;
            }
        </style>
    </head>
    <body>
        <h1 style="text-align: center;" >Web Scraping Filters</h1>
        <div style="text-align: center;">
            <h2>Filters</h2>
            <form method="post">
                <button type="submit" name="filter" value="comments">Show Titles with More Than 5 Words</button>
                <br><br>
                <button type="submit" name="filter" value="points">Show Titles with Less Than 5 Words</button>
            </form>
        </div>
        <?php if(!empty($results)){?>
        <h2 style="text-align: center;">Results</h2>
        <div class="result-box" style="text-align: center;">
            <?php foreach ($results as $r) { ?>
                <div class="result-item">
                    <span class="rank">Rank: <?= $r["rank"] ?></span> |
                    <span class="title">Title: <?= $r["title"] ?></span> |
                    <span class="points">Points: <?= $r["score"] ?></span> |
                    <span class="comments">Comments: <?= $r["comments"] ?></span>
                </div>
            <?php } ?>
        </div>
        <?php } ?>
    </body>
</html>
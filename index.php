<?php 
$curl = curl_init();

curl_setopt($curl, CURLOPT_URL, 'https://news.ycombinator.com/');

curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');

curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($curl);

echo $response;

curl_close($curl);
?>
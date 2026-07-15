<?php

use WishgranterProject\MusicRadar\Radar;
use WishgranterProject\MusicProbe\Description;
use WishgranterProject\YouTubeProbe\YouTubeApi;
use WishgranterProject\YouTubeProbe\YouTubeProbe;

if (!file_exists('../vendor/autoload.php')) {
    die('Autoload file not found');
}

require '../vendor/autoload.php';

//-----------------------------------------------------------------------------

$youtubeApiKey = 'your-youtube-api-key-goes-here';

if (file_exists('./.youtube-api-key')) {
    $youtubeApiKey = file_get_contents('./.youtube-api-key');
}

$apiYouTube = new YouTubeApi($youtubeApiKey);
$youTube    = new YouTubeProbe($apiYouTube);
$aether     = new Radar();
$aether->addProbe($youTube, 1);

//-----------------------------------------------------------------------------

$description = Description::createFromArray([
    'title'  => 'Stolen waters',
    'artist' => 'Cain\'s Offering'
]);

//-----------------------------------------------------------------------------

$results = $aether
  ->searchFor($description)
  ->addDefaultCriteria()
  ->find();


header('Content-Type: application/json');
echo json_encode($results->toArray());

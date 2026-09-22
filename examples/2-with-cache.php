<?php

use AdinanCenci\FileCache\Cache;
use WishgranterProject\MusicRadar\Radar;
use WishgranterProject\MusicProbe\Description;
use WishgranterProject\LocalFilesProbe\LocalFilesProbe;
use WishgranterProject\YouTubeProbe\YouTubeApi;
use WishgranterProject\YouTubeProbe\YouTubeProbe;

if (!file_exists('../vendor/autoload.php')) {
    die('Autoload file not found');
}

require '../vendor/autoload.php';

//-----------------------------------------------------------------------------

if (!file_exists('./local_files')) {
    mkdir('./local_files'); // put your mp3 files here.
}

if (!file_exists('./cache')) {
    mkdir('./cache');
}

$cache = new Cache('./cache');

//-----------------------------------------------------------------------------

$youtubeApiKey = 'your-youtube-api-key-goes-here';

if (file_exists('./.youtube-api-key')) {
    $youtubeApiKey = file_get_contents('./.youtube-api-key');
}

$youtubeApi = new YouTubeApi($youtubeApiKey, [], $cache);
$youTube    = new YouTubeProbe($youtubeApi);
$localFiles = new LocalFilesProbe(rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/local_files/', 'https://' . $_SERVER['HTTP_HOST'] . '/local_files/');

$probe     = new Radar();
$probe->addProbe($localFiles, 2);
$probe->addProbe($youTube, 1);

//-----------------------------------------------------------------------------

$title      = $_GET['title'] ?? '';
$artist     = $_GET['artist'] ?? '';
$featuring  = $_GET['featuring'] ?? '';
$genre      = $_GET['genre'] ?? '';
$soundtrack = $_GET['soundtrack'] ?? '';
$cover      = $_GET['cover'] ?? '';

$description = Description::createFromArray([
    'title'      => $title,
    'artist'     => $artist,
    'featuring'  => $featuring,
    'genre'      => $genre,
    'soundtrack' => $soundtrack,
    'cover'      => $cover
]);

//-----------------------------------------------------------------------------

$search = $probe
    ->searchFor($description)
    ->addDefaultCriteria();

$results = $search->find();

//-----------------------------------------------------------------------------

require 'head.html';
?>
<h1>Search</h1>
<form action="">
    <input type="search" name="title" placeholder="Title" title="Title" value="<?php echo $title;?>" />
    <input type="search" name="artist" placeholder="Artist" title="Artist" value="<?php echo $artist;?>" />
    <input type="search" name="featuring" placeholder="Featuring" title="Featuring" value="<?php echo $featuring;?>" />
    <input type="search" name="genre" placeholder="Genre" title="Genre" value="<?php echo $genre;?>" />
    <input type="search" name="soundtrack" placeholder="Soundtrack" title="Soundtrack" value="<?php echo $soundtrack;?>" />
    <input type="search" name="cover" placeholder="Cover" title="Cover" value="<?php echo $cover;?>" />
    <input type="submit" value="Search" />
</form>
<?php
if (empty($title) || empty($artist)) {
?>
<ul>
    <li><a href="?title=Stolen waters&artist=Cain's Offering">Stolen waters - Cain's Offering</a></li>
    <li><a href="?title=Blood of the Morning Star&artist=Dreamtale">Blood of the Morning Star - Dreamtale</a></li>
    <li><a href="?title=Sonne&artist=Rammstein">Sonne - Rammstein</a></li>
    <li><a href="?title=Good %26 Bad&artist=Wonders&genre=Power Metal">Good & Bad - Wonders ( Power Metal )</a></li>

    <li><a href="?genre=Metal&artist=Firewind&title=My+loneliness">My loneliness - Firewind</a></li>
    <li><a href="?genre=Metal&artist=Firewind&title=Losing+my+mind">Losing my mind - Firewind</a></li>
    <li><a href="?genre=Power+metal&artist=Heavenly&title=Time+machine">Time machine - Heavenly</a></li>
    <li><a href="?genre=Metal&artist=Helloween&title=I+want+out">I want out - Helloween</a></li>
    <li><a href="?genre=Power+Metal&artist=Avantasia&title=Moonglow">Moonglow - Avantasia</a></li>
    <li><a href="?genre=Power+metal&artist=Luca+Turilli&title=Ascending to Infinity">Ascending to Infinity - Luca Turilli</a></li>
    <li><a href="?genre=Power+Metal&artist=Elvenking&title=Pagan+Purity">Pagan Purity - Elvenking</a></li>
    <li><a href="?genre=Power+Metal&artist=Elvenking&title=The+Voynich+Manuscript">The Voynich Manuscript - Elvenking</a></li>
    <li><a href="?genre=Power+metal&artist=Memoira&title=Coupe+du+Graal">Coupe du Graal - Memoira</a></li>
    <li><a href="?genre=Metal&artist=Everfrost&title=Above+the+Treeline">Above the Treeline - Everfrost</a></li>
    <li><a href="?genre=Metal&artist=HammerFall&title=Crimson+Thunder">Crimson Thunder - HammerFall</a></li>
    <li><a href="?genre=Metal&artist=Leaves'+Eyes&title=Jomsborg">Jomsborg - Leaves' Eyes</a></li>
    <li><a href="?genre=Power+Metal&artist=Beast+in+Black&title=Heart of Steel">Heart of Steel - Beast+in+Black</a></li>
    <li><a href="?genre=Power+metal&artist=Barok&title=Nobla+Dezir">Nobla Dezir - Barok</a></li>
    <li><a href="?genre=Metal&artist=Leaves'+Eyes&title=Sacred+Vow">Sacred Vow - Leaves' Eyes</a></li>
    <li><a href="?genre=Metal&artist=Black+Sabbath&title=Iron+Man">Iron Man - Black Sabbath</a></li>
    <li><a href="?genre=Metal&artist=Gamma+ray&title=Lost+Angels">Lost Angels - Gamma ray</a></li>
    <li><a href="?genre=Power+Metal&artist=Wonders&title=Good+&+Bad">Good & Bad - Wonders</a></li>
    <li><a href="?genre=Metal&artist=Crystal+Gates&title=Nightmares">Nightmares - Crystal Gates</a></li>
    <li><a href="?artist=Left+Hand+Solution&album=Light+Shines+Black&title=Persistence+Of+Memory&album=Light+Shines+Black">Persistence Of Memory - Left Hand Solution</a></li>
    <li><a href="?genre=Power+Metal&artist=Firewind&title=Burning+Earth">Burning Earth - Firewind</a></li>
    <li><a href="?genre=Power+Metal&artist=Claymorean&title=Battle+in+the+Sky">Battle in the Sky - Claymorean</a></li>
    <li><a href="?genre=Doom+Metal&artist=Pallbearer&title=Worlds+Apart">Worlds Apart - Pallbearer</a></li>
    <li><a href="?genre=Doom+Metal&artist=Pallbearer&title=The+Legend">The Legend - Pallbearer</a></li>
    <li><a href="?genre=Doom+Metal&artist=Pallbearer&title=The+Ghost+I+Used+to+Be">The Ghost I Used to Be - Pallbearer</a></li>
    <li><a href="?genre=Power+Metal&artist=Hammer+King&title=Glory+to+the+Hammer+King">Glory to the Hammer King - Hammer King</a></li>
    <li><a href="?genre=Power+Metal&artist=Space+Cadets&title=The+Human+Condition">The Human Condition - Space Cadets</a></li>
    <li><a href="?genre=Power+Metal&artist=Hysterica&title=Breaking+the+walls">Breaking the walls - Hysterica</a></li>
    <li><a href="?genre=Metal&artist=Davester2296&title=Painful+Dreams&cover=Robert+Prince&soundtrack=D64D2">Painful Dreams - Davester2296</a></li>
    <li><a href="?artist=Gotye&featuring=The+Basics&title=Somebody+That+I+Used+To+Know">Somebody That I Used To Know - Gotye & The Basics</a></li>
</ul>
<?php
die();
}
echo '<a href="2-with-cache.php">go back</a>';

$table = '<table>
    <tr>
        <th colspan="2">
            Result
        </th>
        <th>
            Sorting criteria
        </th>
    </tr>';
foreach ($results as $result) {

    $tally = $result->getRelevance()->toArray();
    $resource = $result->getResource();

    $table .=
    '<tr>
        <td>';

        if ($resource->getThumbnail()) {
            $img = '<img src="' . $resource->getThumbnail() . '" />';

            if ($resource->getHref()) {
                $img = '<a href="' . $resource->getHref() . '" target="_blank">' . $img . '</a>';
            }

            $table .= $img;
        }

        $table .= '<br>' . $resource->getSourceId();

    $table .=
        '</td>
        <td>' .
            '<h2>' . $resource->getTitle() . '</h2>' .
            ( $resource->getArtist() ? $resource->getArtist() . '<br>' : '' ).
            $resource->getDescription() . '<br>' .
            $resource->getSrc() .
        '</td>
        <td>';



    $factors = [];
    foreach ($tally['scores'] as $s) {
        if (!$s['total']) {
            continue;
        }
        $factors[] =
        '<div title="' . $s['points'] . ' points * ' . $s['weight'] . ' weight = ' . $s['total'] . ' total">' .
            $s['criteria'] . '<br>' .
            $s['points'] . ' * ' . $s['weight'] . ' = ' . $s['total'] .
        '</div>';
    }

    $table .= implode('<br>', $factors) . '<hr>';


    $table .= 'Total: ' . $tally['total'];

    $table .=
        '</td>
    </tr>';
}
$table .= '</table>';


echo $table;

echo "</body></html>\n\n\n";

<?php

$baseDir = 'insta-download';
$folders = ['digifyce', 'ragav_biztalks'];

$items = [];

foreach ($folders as $folder) {

    $dir = $baseDir . '/' . $folder;
    if (!is_dir($dir)) continue;

    $files = scandir($dir);

    foreach ($files as $file) {

        if (preg_match('/^(.*)_UTC\.mp4$/', $file, $m)) {

            $basename = $m[1] . '_UTC';

            $thumb = "$dir/$basename.jpg";
            $video = "$dir/$basename.mp4";

            if (file_exists($thumb) && file_exists($video)) {
                $items[] = [
                    'thumb' => '/' . $thumb,
                    'video' => '/' . $video
                ];
            }
        }
    }
}

file_put_contents('media-cache.json', json_encode(array_reverse($items)));

echo "Cache built successfully.";

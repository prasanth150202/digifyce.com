<?php

function getMediaItems($baseDir) {
    $items = [];
    $folders = ['digifyce', 'ragav_biztalks'];

    foreach ($folders as $folder) {
        $dir = "$baseDir/$folder";
        if (!is_dir($dir)) continue;

        $files = scandir($dir);

        foreach ($files as $file) {
            if (preg_match('/^(.*)_UTC\.mp4$/', $file, $m)) {
                $basename = $m[1] . '_UTC';

                $thumb = "$dir/$basename.jpg";
                $video = "$dir/$basename.mp4";

                if (file_exists($thumb) && file_exists($video)) {
                    $items[] = [
                        'thumb'  => $thumb,
                        'video'  => $video,
                        'folder' => $folder
                    ];
                }
            }
        }
    }

    return array_reverse($items);
}

$mediaItems = getMediaItems('insta-download');

$pageTitle = 'Educational Marketing Videos & Strategy Insights';
$pageDescription = 'Watch free educational marketing videos from Digifyce covering digital strategies, performance growth tips, and industry trends.';
$bodyClass = 'bg-[#05070a] text-white';

include __DIR__ . '/app/views/header.php';
?>

<style>
main {
    padding-top: 80px;
    height: calc(100vh - 80px);
}

@media (max-width: 1024px) {
    main {
        padding-top: 70px;
        height: calc(100vh - 70px);
    }
}

.playlist-item.active {
    opacity: 1 !important;
    border-color: white !important;
    transform: scale(1.05);
}
</style>

<main id="reelLayout" class="h-screen flex flex-col lg:flex-row overflow-hidden relative">

    <!-- MAIN VIDEO -->
    <div class="flex-1 flex items-center justify-center bg-black relative z-10">

        <video 
            id="mainVideo"
            class="h-[65vh] lg:h-[92vh] aspect-[9/16] object-cover rounded-xl shadow-2xl"
            controls
            autoplay
        >
            <source id="mainSource" src="" type="video/mp4">
        </video>

    </div>

    <!-- SIDEBAR / THUMBNAIL SLIDER -->
    <div 
        id="sidebar"
        class="
            lg:w-[220px]
            w-full
            bg-[#0f1115]
            border-t lg:border-l border-white/10
            lg:static
            relative
            z-20
            lg:overflow-y-auto
            overflow-x-auto
            lg:py-6
            py-4
            lg:px-3
            px-4
            flex
            lg:flex-col
            gap-4
        "
    >

        <?php foreach ($mediaItems as $idx => $item): ?>
            <img 
                src="<?= str_replace($_SERVER['DOCUMENT_ROOT'], '', $item['thumb']) ?>"
                class="playlist-item
                       aspect-[9/16]
                       lg:w-full
                       w-[110px]
                       object-cover
                       rounded-lg
                       cursor-pointer
                       opacity-60
                       transition-all
                       duration-200
                       border-2
                       border-transparent
                       flex-shrink-0"
                onclick="selectVideo(<?= $idx ?>)"
            >
        <?php endforeach; ?>

    </div>

</main>

<script>
var mediaItems = <?php echo json_encode(array_map(function($item) {
    return [
        'video' => str_replace($_SERVER['DOCUMENT_ROOT'], '', $item['video'])
    ];
}, $mediaItems)); ?>;

var currentIdx = 0;
var video = document.getElementById('mainVideo');
var source = document.getElementById('mainSource');

function selectVideo(idx) {
    currentIdx = idx;
    source.src = mediaItems[idx].video;
    video.load();
    video.play();
    highlightActive();
}

function highlightActive() {
    document.querySelectorAll('.playlist-item').forEach((item, i) => {
        item.classList.toggle('active', i === currentIdx);
    });
}

video.addEventListener('ended', function() {
    currentIdx = (currentIdx + 1) % mediaItems.length;
    selectVideo(currentIdx);
});

if (mediaItems.length > 0) {
    selectVideo(0);
}
</script>

<?php include __DIR__ . '/app/views/footer.php'; ?>
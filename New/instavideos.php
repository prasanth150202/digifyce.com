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

$pageTitle = 'Educational Content - Digifyce';
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

#sidebar.open {
    transform: translateY(0);
}
</style>
<main id="reelLayout" class="h-screen flex flex-col lg:flex-row overflow-hidden relative">

    <!-- MAIN VIDEO -->
    <div class="flex-1 flex items-center justify-center bg-black relative z-10">

        <!-- Mobile Toggle Button -->
        <button 
            id="toggleSidebar"
            class="lg:hidden absolute top-4 right-4 z-30 bg-white/20 backdrop-blur-md text-white px-4 py-2 rounded-lg"
        >
            More
        </button>

        <video 
            id="mainVideo"
            class="h-[85vh] lg:h-[92vh] aspect-[9/16] object-cover rounded-xl shadow-2xl"
            controls
            autoplay
        >
            <source id="mainSource" src="" type="video/mp4">
        </video>

    </div>

    <!-- SIDEBAR -->
    <div 
        id="sidebar"
        class="
            lg:w-[220px]
            w-full
            bg-[#0f1115]
            border-l border-white/10
            lg:static
            fixed
            bottom-0
            left-0
            right-0
            z-20
            transform translate-y-full
            lg:translate-y-0
            transition-transform duration-300
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
                       border-transparent"
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
var sidebar = document.getElementById('sidebar');
var toggleBtn = document.getElementById('toggleSidebar');

function selectVideo(idx) {
    currentIdx = idx;
    source.src = mediaItems[idx].video;
    video.load();
    video.play();
    highlightActive();

    // Close sidebar after selecting (mobile)
    if (window.innerWidth < 1024) {
        sidebar.classList.remove('open');
    }
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

// Mobile toggle
toggleBtn.addEventListener('click', function() {
    sidebar.classList.toggle('open');
});
</script>

<?php include __DIR__ . '/app/views/footer.php'; ?>
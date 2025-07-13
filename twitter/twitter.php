<?php
// 1. twitter.json 읽기
$json = file_get_contents('twitter.json');
$list = json_decode($json, true);

// 2. 페이지네이션: 20개씩
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 20;
$offset = ($page-1)*$perPage;
$slice = array_slice($list, $offset, $perPage);

// 3. AJAX 요청이면 JSON으로 반환
if(isset($_GET['ajax']) && $_GET['ajax'] == 1) {
    header('Content-Type: application/json');
    echo json_encode($slice);
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Twitter Video Gallery</title>
    <style>
        body { background: #111; color: #fff; margin: 0; font-family: sans-serif; }
        .container { display: flex; flex-wrap: wrap; justify-content: center; }
        .item { margin: 10px; background: #222; border-radius: 8px; overflow: hidden; width: 320px; }
        .thumb { width: 100%; cursor: pointer; display: block; }
        .video-container { display:none; position:relative; width:100%; }
        .video-container video { width:100%; background:#000; }
        .twitter-link { display:block; text-align:center; color:#1da1f2; padding:6px 0; text-decoration:none;}
        #blackscreen {
            display:none;
            position:fixed;
            left:0; top:0; width:100vw; height:100vh;
            background:#000;
            z-index:9999;
        }
    </style>
</head>
<body>
    <div id="blackscreen"></div>
    <div class="container" id="gallery">
        <?php foreach($slice as $item): ?>
            <div class="item">
                <img src="<?= htmlspecialchars($item['img']) ?>" class="thumb" data-video="<?= htmlspecialchars($item['video']) ?>">
                <div class="video-container">
                    <video controls src="<?= htmlspecialchars($item['video']) ?>"></video>
                </div>
                <a href="<?= htmlspecialchars($item['twitter']) ?>" target="_blank" class="twitter-link">View Tweet</a>
            </div>
        <?php endforeach; ?>
    </div>
    <script>
        let page = 1;
        let loading = false;
        let finished = false;
        let idleTimer = null;

        function resetIdleTimer() {
            clearTimeout(idleTimer);
            document.getElementById('blackscreen').style.display = 'none';
            idleTimer = setTimeout(() => {
                document.getElementById('blackscreen').style.display = 'block';
            }, 10000); // 10초
        }

        function appendItems(items) {
            const gallery = document.getElementById('gallery');
            for (const item of items) {
                const div = document.createElement('div');
                div.className = 'item';
                div.innerHTML = `
                    <img src="${item.img}" class="thumb" data-video="${item.video}">
                    <div class="video-container">
                        <video controls src="${item.video}"></video>
                    </div>
                    <a href="${item.twitter}" target="_blank" class="twitter-link">View Tweet</a>
                `;
                gallery.appendChild(div);
            }
        }

        // 썸네일 클릭 시 비디오 embed + 전체화면
        document.addEventListener('click', async function(e) {
            if(e.target.classList.contains('thumb')) {
                const parent = e.target.parentNode;
                const videoContainer = parent.querySelector('.video-container');
                const video = videoContainer.querySelector('video');
                videoContainer.style.display = 'block';
                e.target.style.display = 'none';

                // 비디오 로드
                video.load();

                // 전체화면 진입
                if (video.requestFullscreen) {
                    video.requestFullscreen();
                } else if (video.webkitRequestFullscreen) { // Safari
                    video.webkitRequestFullscreen();
                } else if (video.msRequestFullscreen) { // IE11
                    video.msRequestFullscreen();
                }

                // 자동재생
                try {
                    await video.play();
                } catch(e){}

                // 전체화면 종료 시 썸네일로 복귀
                function onFullscreenChange() {
                    if (
                        !(document.fullscreenElement ||
                          document.webkitFullscreenElement ||
                          document.msFullscreenElement)
                    ) {
                        videoContainer.style.display = 'none';
                        e.target.style.display = 'block';
                        video.pause();
                        video.currentTime = 0;
                        document.removeEventListener('fullscreenchange', onFullscreenChange);
                        document.removeEventListener('webkitfullscreenchange', onFullscreenChange);
                        document.removeEventListener('msfullscreenchange', onFullscreenChange);
                    }
                }
                document.addEventListener('fullscreenchange', onFullscreenChange);
                document.addEventListener('webkitfullscreenchange', onFullscreenChange);
                document.addEventListener('msfullscreenchange', onFullscreenChange);
            }
            resetIdleTimer();
        });

        // 스크롤 또는 터치 끝까지: 다음 20개 더 불러오기
        async function loadMoreIfNeeded() {
            if (loading || finished) return;
            if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 50) {
                loading = true;
                page++;
                let r = await fetch('?ajax=1&page=' + page);
                let items = await r.json();
                if(items.length === 0) { finished = true; return; }
                appendItems(items);
                loading = false;
            }
        }

        window.addEventListener('scroll', loadMoreIfNeeded);
        window.addEventListener('touchend', loadMoreIfNeeded);

        // 아무 입력(클릭/스크롤/터치) 없으면 블랙스크린
        ['mousemove','mousedown','scroll','touchstart','keydown'].forEach(event => {
            window.addEventListener(event, resetIdleTimer);
        });
        resetIdleTimer(); // 최초 시작

    </script>
</body>
</html>

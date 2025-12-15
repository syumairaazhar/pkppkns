<?php
$year = isset($_GET['year']) ? $_GET['year'] : '';
$event = isset($_GET['event']) ? $_GET['event'] : '';

$folder = "img/$year/$event/";

// Ensure folder exists
if (!is_dir($folder)) {
    die("Folder not found: $folder");
}

// Get all images
$images = glob($folder . "*.{jpg,jpeg,png,JPG,JPEG,PNG}", GLOB_BRACE);
if (!$images) die("No images found in folder: $folder");

// Malay translations
$daysMalay = [
    'Monday' => 'ISNIN',
    'Tuesday' => 'SELASA',
    'Wednesday' => 'RABU',
    'Thursday' => 'KHAMIS',
    'Friday' => 'JUMAAT',
    'Saturday' => 'SABTU',
    'Sunday' => 'AHAD'
];
$monthsMalay = [
    'January' => 'JANUARI',
    'February' => 'FEBRUARI',
    'March' => 'MAC',
    'April' => 'APRIL',
    'May' => 'MEI',
    'June' => 'JUN',
    'July' => 'JULAI',
    'August' => 'OGOS',
    'September' => 'SEPTEMBER',
    'October' => 'OKTOBER',
    'November' => 'NOVEMBER',
    'December' => 'DISEMBER'
];
$malayToEnglish = [
    'JANUARI' => 'January',
    'FEBRUARI' => 'February',
    'MAC' => 'March',
    'APRIL' => 'April',
    'MEI' => 'May',
    'JUN' => 'June',
    'JULAI' => 'July',
    'OGOS' => 'August',
    'SEPTEMBER' => 'September',
    'OKTOBER' => 'October',
    'NOVEMBER' => 'November',
    'DISEMBER' => 'December'
];

// Extract date from title
preg_match('/\((.*?)\)\s*$/', $event, $m);
$eventDateRaw = $m ? strtoupper($m[1]) : "";
$eventDate = "";

if ($eventDateRaw) {
    $dateStr = strtoupper($eventDateRaw);
    foreach ($malayToEnglish as $malay => $english) {
        $dateStr = str_replace($malay, $english, $dateStr);
    }
    $timestamp = strtotime($dateStr);
    if ($timestamp) {
        $day = date("l", $timestamp);
        $month = date("F", $timestamp);
        $eventDate = $daysMalay[$day] . ', ' . date("d", $timestamp) . ' ' . $monthsMalay[$month] . ' ' . date("Y", $timestamp);
    } else {
        $eventDate = strtoupper($eventDateRaw); // fallback if parsing fails
    }
}

// Clean event title
$eventTitle = preg_replace('/\s*\(.*?\)\s*$/', '', $event);
$eventTitle = trim($eventTitle);
?>

<!DOCTYPE html>
<html lang="ms">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PKP PKNS</title>
    <link rel="icon" href="img/logo pkp.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
</head>

<body>

    <div id="header"></div>

    <header class="bg-light text-center py-3" style="margin-top: 80px;">
        <div class="section-header">
            <div class="section-line"></div>
            <h2 class="section-title">AKTIVITI PKP</h2>
            <div class="section-line-right"></div>
        </div>
    </header>

    <section class="info-section" data-aos="fade-up">
        <div class="membership-item" data-aos="fade-up" data-aos-delay="200">

            <!-- Back Button -->
            <a href="album.php?year=<?php echo urlencode($year); ?>" class="btn btn-theme mb-3">
                ← Back to <?php echo $year; ?>
            </a>

            <small class="text-muted d-block"><?php echo $year; ?></small>

            <h2 class="fw-bold"><?php echo strtoupper($eventTitle); ?></h2>

            <?php if ($eventDate): ?>
                <p class="fw-bold text-secondary"><?php echo $eventDate; ?></p>
            <?php endif; ?>

            <!-- Image Grid -->
            <div class="row g-4 mt-3">
                <?php foreach ($images as $img): ?>
                    <div class="col-md-4 col-sm-6">
                        <div class="gallery-item">
                            <img src="<?php echo $img; ?>" loading="lazy"
                                class="gallery-img"
                                data-img="<?php echo $img; ?>">
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        </div>
    </section>

    <!-- Custom Image Viewer -->
    <div id="imageViewer" class="image-viewer">
        <button class="close-img-btn" id="closeViewer">×</button>

        <button class="nav-btn prev-btn" id="prevImg">‹</button>


        <img id="viewerImage" class="viewer-image" src="">

        <button class="nav-btn next-btn" id="nextImg">›</button>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="include.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            AOS.init();

            const modalImage = document.getElementById("modalImage");
            document.querySelectorAll(".gallery-img").forEach(img => {
                img.addEventListener("click", function() {
                    modalImage.src = this.dataset.img;
                });
            });
        });
    </script>

    <script>
        (function() {
            const viewer = document.getElementById('imageViewer');
            const viewerImage = document.getElementById('viewerImage');
            const closeBtn = document.getElementById('closeViewer');
            const prevBtn = document.getElementById('prevImg');
            const nextBtn = document.getElementById('nextImg');
            const galleryImages = Array.from(document.querySelectorAll('.gallery-img'));
            let currentIndex = 0;

            // Zoom state
            let scale = 1;
            let startScale = 1;
            let initialDistance = 0;

            // Swipe state
            let touchStartX = 0;
            let touchStartY = 0;
            const SWIPE_MIN_DISTANCE = 50; // px
            const MIN_SCALE = 1;
            const MAX_SCALE = 3;

            // utility distance between touches
            function getDistance(t1, t2) {
                const dx = t1.clientX - t2.clientX;
                const dy = t1.clientY - t2.clientY;
                return Math.hypot(dx, dy);
            }

            // open / close
            galleryImages.forEach((img, idx) => {
                img.addEventListener('click', () => {
                    currentIndex = idx;
                    openViewer(img.dataset.img || img.src);
                }, {
                    passive: true
                });
            });

            function openViewer(src) {
                viewerImage.src = src;
                resetZoom();
                viewer.classList.add('active');
                document.body.style.overflow = 'hidden';
            }

            function closeViewer() {
                viewer.classList.remove('active');
                document.body.style.overflow = '';
                resetZoom();
            }
            closeBtn.addEventListener('click', closeViewer);
            viewer.addEventListener('click', (e) => {
                if (e.target === viewer) closeViewer();
            });

            prevBtn.addEventListener('click', prevImage);
            nextBtn.addEventListener('click', nextImage);

            function nextImage() {
                currentIndex = (currentIndex + 1) % galleryImages.length;
                viewerImage.src = galleryImages[currentIndex].dataset.img;
                resetZoom();
            }

            function prevImage() {
                currentIndex = (currentIndex - 1 + galleryImages.length) % galleryImages.length;
                viewerImage.src = galleryImages[currentIndex].dataset.img;
                resetZoom();
            }

            // reset zoom
            function resetZoom() {
                scale = 1;
                startScale = 1;
                initialDistance = 0;
                viewerImage.style.transition = 'transform 140ms ease';
                viewerImage.style.transform = 'scale(1)';
                setTimeout(() => viewerImage.style.transition = '', 160);
            }

            // Touch handlers are on the viewer (overlay) so swipes work anywhere.
            // Ignore touches that start on control buttons
            function touchStartsOnControl(target) {
                return !!target.closest && (target.closest('.nav-btn') || target.closest('.close-img-btn'));
            }

            // touchstart: record either pinch start or single-finger start
            viewer.addEventListener('touchstart', function(e) {
                if (!viewer.classList.contains('active')) return;

                // if touch started on a control, do nothing here (let button handle it)
                if (touchStartsOnControl(e.target)) return;

                if (e.touches.length === 2) {
                    // start pinch
                    initialDistance = getDistance(e.touches[0], e.touches[1]);
                    startScale = scale || 1;
                } else if (e.touches.length === 1) {
                    // single-finger: start for swipe detection
                    touchStartX = e.touches[0].clientX;
                    touchStartY = e.touches[0].clientY;
                }
            }, {
                passive: true
            });

            // touchmove: only need to preventDefault for pinch (two-finger)
            viewer.addEventListener('touchmove', function(e) {
                if (!viewer.classList.contains('active')) return;

                if (e.touches.length === 2) {
                    // pinch: prevent default to stop page pinch/scroll
                    e.preventDefault(); // <-- passive: false not required on viewer here because listener is passive:false by default in many browsers; to be safe, we will set passive:false below when adding this listener (see note)
                    const dist = getDistance(e.touches[0], e.touches[1]);
                    if (initialDistance > 0) {
                        let newScale = (dist / initialDistance) * startScale;
                        newScale = Math.max(MIN_SCALE, Math.min(MAX_SCALE, newScale));
                        scale = newScale;
                        viewerImage.style.transform = `scale(${scale})`;
                    }
                }
                // single-finger move: do not preventDefault here — we want to allow normal behavior until touchend
            }, {
                passive: false
            }); // passive:false is IMPORTANT for e.preventDefault()

            // touchend: evaluate swipe for single-finger touches (only when not zoomed)
            viewer.addEventListener('touchend', function(e) {
                if (!viewer.classList.contains('active')) return;

                // Reset pinch distance when touches go below two
                if (e.touches && e.touches.length < 2) {
                    initialDistance = 0;
                    startScale = scale;
                }

                // Look at changedTouches to get the finger that ended
                const touch = e.changedTouches && e.changedTouches[0];
                if (!touch) return;

                // If the user started touch on a control, ignore
                // (we already checked touchstart target, but this is extra safety)
                if (touchStartsOnControl(e.target)) return;

                const touchEndX = touch.clientX;
                const touchEndY = touch.clientY;
                const deltaX = touchEndX - touchStartX;
                const deltaY = touchEndY - touchStartY;

                // Only treat as horizontal swipe when:
                // - image is not zoomed (scale approx 1)
                // - horizontal movement > min distance
                // - horizontal movement is predominant vs vertical movement
                if (Math.abs(scale - 1) < 0.001 && Math.abs(deltaX) > SWIPE_MIN_DISTANCE && Math.abs(deltaX) > Math.abs(deltaY)) {
                    if (deltaX < 0) nextImage();
                    else prevImage();
                }
            }, {
                passive: true
            });

            // Prevent image dragging in desktop browsers
            viewerImage.addEventListener('dragstart', (e) => e.preventDefault());

            // Keyboard support
            document.addEventListener('keydown', (e) => {
                if (!viewer.classList.contains('active')) return;
                if (e.key === 'ArrowRight') nextImage();
                if (e.key === 'ArrowLeft') prevImage();
                if (e.key === 'Escape') closeViewer();
            });

            // handle image load errors
            viewerImage.addEventListener('error', () => {
                console.warn('Viewer image failed to load.');
                closeViewer();
            });

        })();
    </script>


    <div id="footer"></div>

    <!-- Back to Top Button -->
    <button id="backToTopBtn" title="Go to top">↑</button>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const backToTopBtn = document.getElementById("backToTopBtn");

            // Make the button always visible
            backToTopBtn.style.display = "block";
            backToTopBtn.style.opacity = "1";

            // Smooth scroll to top when clicked
            backToTopBtn.addEventListener("click", function() {
                window.scrollTo({
                    top: 0,
                    behavior: "smooth"
                });
            });
        });
    </script>

</body>

</html>
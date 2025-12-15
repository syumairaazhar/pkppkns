<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="img/logo pkp.png" type="image/png">
    <title>PKP PKNS</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
</head>

<body>
    <!-- Navbar -->
    <!-- Load Navbar -->
    <div id="header"></div>

    <!-- Hero Section -->
    <header class="bg-light text-center py-3" style="margin-top: 80px;">
        <div class="section-header">
            <div class="section-line"></div>
            <h2 class="section-title">Album PKP</h2>
            <div class="section-line-right"></div>
        </div>
    </header>

    <!-- Album Section -->
    <section class="info-section" data-aos="fade-up">
        <div class="membership-item" data-aos="fade-up" data-aos-delay="200">
            <h1 class="fw-bold border-bottom border-warning pb-2">ALBUM </h1>

            <!-- Year Tabs -->
            <ul class="nav nav-pills justify-content-center mb-4" id="albumTabs">
                <li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#year2025">2025</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#year2024">2024</a></li>
                <li class="nav-item"><a class="nav-link active" data-bs-toggle="pill" href="#year2023">2023</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#year2022">2022</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#year2020">2020</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#year2019">2019</a></li>
                <li class="nav-item"><a class="nav-link" data-bs-toggle="pill" href="#year2018">2018</a></li>
            </ul>

            <!-- Yearly Albums -->
            <?php
            function loadImages($year)
            {
                $baseFolder = "img/$year/";

                // If the YEAR folder doesn't exist at all
                if (!is_dir($baseFolder)) {
                    echo "<p class='text-center text-muted'>No record for $year.</p>";
                    return;
                }

                $events = array_filter(glob($baseFolder . "*"), 'is_dir');

                // If there are no event folders
                if (empty($events)) {
                    echo "<p class='text-center text-muted'>No record for $year.</p>";
                    return;
                }

                $hasImages = false;

                foreach ($events as $eventFolder) {
                    $eventName = basename($eventFolder);

                    // Extract date in (...), else show only year
                    preg_match('/\((.*?)\)$/', $eventName, $matches);
                    $eventDate = isset($matches[1]) ? $matches[1] : $year;

                    // Remove date from title
                    $cleanTitle = preg_replace('/\s*\(.*?\)$/', '', $eventName);

                    // Get first image
                    $images = glob("$eventFolder/*.{jpg,jpeg,png,JPG,JPEG,PNG}", GLOB_BRACE);

                    if (empty($images)) continue; // Skip empty folders

                    $hasImages = true;
                    $cover = $images[0];

                    // Event page link
                    $link = "event.php?year=$year&event=" . urlencode($eventName);

                    echo "
        <div class='col-md-4'>
            <a href='$link' class='text-decoration-none'>
                <div class='album-card'>
                    <img src='$cover' style='height:250px; object-fit:cover;'>
                    <div class='album-overlay'>
                        <div class='album-title'>$cleanTitle</div>
                        <div class='album-date'>$eventDate</div>
                    </div>
                </div>
            </a>
        </div>";
                }

                // If event folders exist but none contain images
                if (!$hasImages) {
                    echo "<p class='text-center text-muted'>No record for $year.</p>";
                }
            }
            ?>

            <div class="tab-content">
                <!-- 2025 Album -->
                <div class="tab-pane fade" id="year2025">
                    <div class="row g-4">
                        <?php loadImages(2025); ?>
                    </div>
                </div>

                <!-- 2024 Album -->
                <div class="tab-pane fade" id="year2024">
                    <div class="row g-4">
                        <?php loadImages(2024); ?>
                    </div>
                </div>

                <!-- 2023 Album -->
                <div class="tab-pane fade show active" id="year2023">
                    <div class="row g-4">
                        <?php loadImages(2023); ?>
                    </div>
                </div>


                <!-- 2022 Album -->
                <div class="tab-pane fade" id="year2022">
                    <div class="row g-4">
                        <?php loadImages(2022); ?>
                    </div>
                </div>

                <!-- 2020 Album -->
                <div class="tab-pane fade" id="year2020">
                    <div class="row g-4">
                        <?php loadImages(2020); ?>
                    </div>
                </div>

                <!-- 2019 Album -->
                <div class="tab-pane fade" id="year2019">
                    <div class="row g-4">
                        <?php loadImages(2019); ?>
                    </div>
                </div>

                <!-- 2018 Album -->
                <div class="tab-pane fade" id="year2018">
                    <div class="row g-4">
                        <?php loadImages(2018); ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- AOS Animation JS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            AOS.init({
                duration: 800, // smoother animation
                easing: 'ease-in-out', // smoother curve
                once: false // allow animation every time visible
            });

            // Re-initialize AOS whenever tab content changes
            const albumTabs = document.querySelectorAll('#albumTabs a[data-bs-toggle="pill"]');
            albumTabs.forEach(tab => {
                tab.addEventListener('shown.bs.tab', function() {
                    // Give a short delay to allow Bootstrap to show the tab before reinitializing AOS
                    setTimeout(() => {
                        AOS.refresh();
                    }, 300);
                });
            });
        });
    </script>

    <!-- Footer -->
    <div id="footer"></div>

    <!-- Back to Top Button -->
    <button id="backToTopBtn" title="Go to top">↑</button>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="include.js"></script>
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
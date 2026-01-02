<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#712cf9">
    <title>{$siteName}</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
          rel="stylesheet">

    <style>
        body {
            padding-top: 3rem;
            padding-bottom: 3rem;
            color: rgb(var(--bs-tertiary-color-rgb));
            font-family: 'Poppins', sans-serif;
        }

        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
        }

        .b-example-divider {
            width: 100%;
            height: 3rem;
            background-color: #0000001a;
            border: solid rgba(0, 0, 0, .15);
            border-width: 1px 0;
            box-shadow: inset 0 .5em 1.5em #0000001a, inset 0 .125em .5em #00000026;
        }

        .b-example-vr {
            flex-shrink: 0;
            width: 1.5rem;
            height: 100vh;
        }

        .bi {
            vertical-align: -.125em;
            fill: currentColor;
        }

        .nav-scroller {
            position: relative;
            z-index: 2;
            height: 2.75rem;
            overflow-y: hidden;
        }

        .nav-scroller .nav {
            display: flex;
            flex-wrap: nowrap;
            padding-bottom: 1rem;
            margin-top: -1px;
            overflow-x: auto;
            text-align: center;
            white-space: nowrap;
            -webkit-overflow-scrolling: touch;
        }

        .btn-bd-primary {
            --bd-violet-bg: #712cf9;
            --bd-violet-rgb: 112.520718, 44.062154, 249.437846;
            --bs-btn-font-weight: 600;
            --bs-btn-color: var(--bs-white);
            --bs-btn-bg: var(--bd-violet-bg);
            --bs-btn-border-color: var(--bd-violet-bg);
            --bs-btn-hover-color: var(--bs-white);
            --bs-btn-hover-bg: #6528e0;
            --bs-btn-hover-border-color: #6528e0;
            --bs-btn-focus-shadow-rgb: var(--bd-violet-rgb);
            --bs-btn-active-color: var(--bs-btn-hover-color);
            --bs-btn-active-bg: #5a23c8;
            --bs-btn-active-border-color: #5a23c8;
        }

        .bd-mode-toggle {
            z-index: 1500;
        }

        .bd-mode-toggle .bi {
            width: 1em;
            height: 1em;
        }

        .bd-mode-toggle .dropdown-menu .active .bi {
            display: block !important;
        }

        .navbar-dark.bg-dark {
            background-color: #212529 !important;
        }

        .navbar-brand {
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .nav-link {
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .nav-link:hover {
            color: #fff !important;
        }

        .carousel {
            margin-bottom: 4rem;
            overflow: hidden;
        }

        .carousel-indicators {
            bottom: 10px;
            z-index: 15;
        }

        .carousel-indicators button {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin: 0 4px;
        }

        .carousel-caption {
            bottom: 1.5rem;
            z-index: 10;
            padding: 1.25rem;
            border-radius: 0.75rem;
            left: 5%;
            right: 5%;
            width: 90%;
            text-align: center;
        }

        .carousel-item {
            height: 500px;
            position: relative;
        }

        .carousel-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
        }

        .carousel-control-prev,
        .carousel-control-next {
            width: 50px;
            height: 50px;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0, 0, 0, 0.3);
            border-radius: 50%;
            margin: 0 15px;
            z-index: 20;
        }

        .carousel-control-prev {
            left: 10px;
        }

        .carousel-control-next {
            right: 10px;
        }

        .carousel-control-prev-icon,
        .carousel-control-next-icon {
            width: 2rem;
            height: 2rem;
        }

        .marketing .col-lg-4 {
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .marketing .col-lg-4 p {
            margin-right: .75rem;
            margin-left: .75rem;
        }

        .featurette-divider {
            margin: 5rem 0;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
        }

        .featurette-heading {
            letter-spacing: -.05rem;
            font-weight: 600;
        }

        .collapse.bg-dark {
            background-color: #343a40 !important;
        }

        .login-form input[type="text"],
        .login-form input[type="password"],
        .form-control.bg-secondary {
            background-color: #495057 !important;
            border-color: #6c757d !important;
            color: #fff !important;
        }

        .login-form input[type="text"]::placeholder,
        .login-form input[type="password"]::placeholder {
            color: #adb5bd !important;
        }

        footer.container {
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid #dee2e6;
        }

        footer a {
            text-decoration: none;
            color: #712cf9;
        }

        footer a:hover {
            text-decoration: underline;
        }

        @media (min-width: 768px) {
            .bd-placeholder-img-lg {
                font-size: 3.5rem;
            }
        }

        @media (max-width: 768px) {
            .carousel-item {
                height: 350px;
                min-height: 350px;
            }

            .carousel-caption {
                bottom: 0.75rem;
                padding: 1rem;
            }

            .carousel-caption h1 {
                font-size: 1.5rem;
                margin-bottom: 0.5rem;
            }

            .carousel-caption p {
                font-size: 0.9rem;
                margin-bottom: 0.75rem;
                line-height: 1.4;
            }

            .carousel-caption .btn {
                padding: 0.5rem 1rem;
                font-size: 0.9rem;
            }

            .carousel-control-prev,
            .carousel-control-next {
                width: 40px;
                height: 40px;
                margin: 0 10px;
            }

            .carousel-control-prev-icon,
            .carousel-control-next-icon {
                width: 1.5rem;
                height: 1.5rem;
            }

            body {
                padding-top: 2.5rem;
            }

            .navbar {
                padding-top: 0.5rem;
                padding-bottom: 0.5rem;
            }
        }

        @media (max-width: 576px) {
            .carousel-item {
                height: 280px;
                min-height: 280px;
            }

            .carousel-caption {
                bottom: 0.5rem;
                padding: 0.75rem;
                left: 3%;
                right: 3%;
                width: 94%;
            }

            .carousel-caption h1 {
                font-size: 1.25rem;
                margin-bottom: 0.25rem;
            }

            .carousel-caption p {
                font-size: 0.8rem;
                margin-bottom: 0.5rem;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }

            .carousel-caption .btn {
                padding: 0.375rem 0.75rem;
                font-size: 0.8rem;
            }

            .carousel-indicators {
                bottom: 5px;
            }

            .carousel-indicators button {
                width: 8px;
                height: 8px;
                margin: 0 3px;
            }

            .carousel-control-prev,
            .carousel-control-next {
                width: 35px;
                height: 35px;
                margin: 0 5px;
            }

            .carousel-control-prev-icon,
            .carousel-control-next-icon {
                width: 1.25rem;
                height: 1.25rem;
            }

            .featurette-heading {
                font-size: 1.75rem;
            }

            .featurette-divider {
                margin: 3rem 0;
            }
        }

        @media (max-width: 400px) {
            .carousel-item {
                height: 220px;
                min-height: 220px;
            }

            .carousel-caption {
                padding: 0.5rem;
            }

            .carousel-caption h1 {
                font-size: 1rem;
            }

            .carousel-caption p {
                font-size: 0.7rem;
                -webkit-line-clamp: 1;
            }

            .carousel-caption .btn {
                padding: 0.25rem 0.5rem;
                font-size: 0.7rem;
            }
        }

        @media (min-width: 768px) {
            .carousel-caption p {
                margin-bottom: 1.25rem;
                line-height: 1.4;
                font-size: 1.1rem;
            }

            .carousel-caption h1 {
                font-size: 2.5rem;
            }

            .featurette-heading {
                font-size: 50px;
            }

            .carousel-control-prev,
            .carousel-control-next {
                width: 60px;
                height: 60px;
            }

            .carousel-control-prev-icon,
            .carousel-control-next-icon {
                width: 2.5rem;
                height: 2.5rem;
            }
        }

        @media (min-width: 992px) {
            .carousel-item {
                height: 600px;
            }

            .carousel-caption {
                bottom: 3rem;
                padding: 1.5rem;
            }
        }

        @media (min-width: 1200px) {
            .carousel-item {
                height: 650px;
            }
        }

        @media (min-width: 62em) {
            .featurette-heading {
                margin-top: 7rem;
            }
        }

        @media (max-width: 768px) {
            .login-form .d-flex {
                flex-direction: column;
                gap: 0.5rem;
            }

            .login-form .d-flex > * {
                width: 100%;
                margin-right: 0 !important;
            }
        }

        .carousel-item img {
            max-width: 100%;
            max-height: 100%;
        }

        @media (max-width: 350px) {
            .carousel {
                margin-bottom: 2rem;
            }

            .carousel-item {
                height: 200px;
                min-height: 200px;
            }

            .carousel-caption {
                display: none;
            }
        }

        @media (min-width: 40em) {
            .carousel-caption p {
                margin-bottom: 1.25rem;
                line-height: 1.4;
            }

            .featurette-heading {
                font-size: 50px;
            }
        }
    </style>
</head>

<body>
<header data-bs-theme="dark">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top shadow">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold text-uppercase" href="{$smarty.server.PHP_SELF|dirname}">
                {if $logo neq ''}
                    <img src="{$logo}" alt="{$siteName}" class="img-fluid" style="max-height:40px;">

                    {$siteName}
                {/if}
                {if showSiteName eq '1'}
                {$siteName}
                {/if}
            </a>

            {include_php file="blocks/bmenu/index.php"}
        </div>

    </nav>

    <!-- Search Collapse -->
    <div class="collapse bg-dark border-top border-secondary py-4" id="searchCollapse">
        <div class="container">
            <form action="module.php" method="get" class="row g-3">
                <input type="hidden" name="modname" value="search">
                <div class="col-12">
                    <label for="keyword" class="form-label text-light">Keyword</label>
                    <input type="text" class="form-control bg-secondary text-light border-0" id="keyword"
                           name="keyword">
                </div>
                <div class="col-12">
                    <label for="item" class="form-label text-light">Locate in</label>
                    <select name="item" id="item" class="form-select bg-secondary text-light border-0 mb-2">
                        <option value="content">Content</option>
                        <option value="news">News</option>
                        <option value="forum">Forum</option>
                    </select>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="method" value="word" id="methodWord">
                        <label class="form-check-label text-light" for="methodWord">Word</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="method" value="phase" id="methodPhase"
                               checked>
                        <label class="form-check-label text-light" for="methodPhase">Phrase</label>
                    </div>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-outline-light w-100">Search</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Login Collapse -->
    <div class="collapse bg-dark border-top border-secondary py-4" id="loginCollapse">
        <div class="container">
            {include_php file="theme/bootstrap/block/login.inc.php"}
        </div>
    </div>

    {literal}
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const searchCollapseEl = document.getElementById('searchCollapse');
                const loginCollapseEl = document.getElementById('loginCollapse');
                const navbarCollapseEl = document.getElementById('navbarToggler');

                const searchCollapse = new bootstrap.Collapse(searchCollapseEl, {toggle: false});
                const loginCollapse = new bootstrap.Collapse(loginCollapseEl, {toggle: false});
                const navbarCollapse = new bootstrap.Collapse(navbarCollapseEl, {toggle: false});

                // Mutual collapse: search
                searchCollapseEl.addEventListener('show.bs.collapse', () => {
                    loginCollapse.hide();
                    navbarCollapse.hide();
                });

                // Mutual collapse: login
                loginCollapseEl.addEventListener('show.bs.collapse', () => {
                    searchCollapse.hide();
                    navbarCollapse.hide();
                });

                // Mutual collapse: navbar menu
                navbarCollapseEl.addEventListener('show.bs.collapse', () => {
                    searchCollapse.hide();
                    loginCollapse.hide();
                });
            });
        </script>
    {/literal}
</header>

<main>
    <div id="myCarousel" class="carousel slide mb-6" data-bs-ride="carousel">

        {* Carousel indicators *}
        <div class="carousel-indicators">
            {foreach $banners as $i => $banner}
                <button type="button" data-bs-target="#myCarousel" data-bs-slide-to="{$i}" {if $i == 0}class="active"
                        aria-current="true"{/if} aria-label="Slide {$i+1}"></button>
            {/foreach}
        </div>

        {* Carousel slides *}
        <div class="carousel-inner">
            {foreach $banners as $i => $banner}
                <div class="carousel-item {if $i == 0}active{/if}">
                    <img src="{$banner.image}" class="d-block w-100" alt="{$banner.title}">
                    <div class="container">
                        <div class="carousel-caption {if $i == 0}text-start{elseif $i == count($banners)-1}text-end{else}text-center{/if}">
                            <h1>{$banner.title}</h1>
                            <p class="opacity-75">{$banner.description}</p>
                            {if $banner.url}
                                <p><a class="btn btn-lg btn-primary" href="{$banner.url}">Learn more</a></p>
                            {/if}
                        </div>
                    </div>
                </div>
            {/foreach}
        </div>

        {* Carousel controls *}
        <button class="carousel-control-prev" type="button" data-bs-target="#myCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#myCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>


    <div class="container marketing">
        <div class="row">
            {section name=i loop=$setBlockTop[0]}
                {$setBlockTop[0][i]}
            {/section}
        </div>
        <hr class="featurette-divider">
        <div class="row">
            {section name=i loop=$setBlockCenter[0]}
                {$setBlockCenter[0][i]}
            {/section}
        </div>
        <hr class="featurette-divider">
        <div class="row">
            {section name=i loop=$setBlockBottom[0]}
                {$setBlockBottom[0][i]}
            {/section}
        </div>
    </div>

    <footer class="container">
        <p class="float-end"><a href="#">Back to top</a></p>
        <p>&copy; 2017–2025 powered by <a href="https://lanaicms.com/">LanaiCMS</a></p>
    </footer>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        function adjustCarouselHeight() {
            const carouselItems = document.querySelectorAll('.carousel-item');
            const screenWidth = window.innerWidth;

            carouselItems.forEach(item => {

                item.style.height = '';


                const img = item.querySelector('img');
                if (img && img.complete) {
                    img.style.objectFit = 'cover';
                }
            });
        }


        adjustCarouselHeight();


        window.addEventListener('resize', adjustCarouselHeight);

        const myCarousel = document.getElementById('myCarousel');
        if (myCarousel) {
            myCarousel.addEventListener('slid.bs.carousel', adjustCarouselHeight);
        }
    });
</script>
</body>
</html>
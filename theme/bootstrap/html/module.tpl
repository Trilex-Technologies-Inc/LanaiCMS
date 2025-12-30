<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style type="text/css">
  @import url("theme/{$cfgTheme}/style/dhtml-horiz.css");
</style>

<!--[if gte IE 5.5]>
    <script language="JavaScript" src="theme/{$cfgTheme}/style/dhtml.js" type="text/JavaScript"></script>
    <![endif]-->

<style>
  body {
    font-family: 'Poppins', sans-serif;
    background-color: #f8f9fa;
  }
  .article-hero {
    background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('images/article-bg.jpg') center/cover no-repeat;
    color: #fff;
    padding: 6rem 0;
    text-align: center;
  }
  .article-meta {
    font-size: 0.9rem;
    color: #ccc;
  }
  .article-content p {
    font-size: 1.1rem;
    line-height: 1.8;
    margin-bottom: 1rem;
  }
  .sidebar {
    background: #fff;
    border-radius: 0.5rem;
    padding: 1.5rem;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
  }
  footer {
    border-top: 1px solid #ddd;
    padding: 2rem 0;
    text-align: center;
    color: #777;
  }
</style>



<header data-bs-theme="dark">
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top shadow">
    <div class="container-fluid">
      <a class="navbar-brand fw-bold text-uppercase" href="{$smarty.server.PHP_SELF|dirname}">{$siteName}</a>
      {include_php file="blocks/bmenu/index.php"}
    </div>
  </nav>

  <!-- Search Collapse -->
  <div class="collapse bg-dark border-top border-secondary py-4 mt-5" id="searchCollapse">
    <div class="container">
      <form action="module.php" method="get" class="row g-3">
        <input type="hidden" name="modname" value="search">
        <div class="col-12">
          <label for="keyword" class="form-label text-light">Keyword</label>
          <input type="text" class="form-control bg-secondary text-light border-0" id="keyword" name="keyword">
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
            <input class="form-check-input" type="radio" name="method" value="phase" id="methodPhase" checked>
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
  <div class="collapse bg-dark border-top border-secondary py-4 mt-5" id="loginCollapse">
    <div class="container">
      {include_php file="theme/bootstrap/block/login.inc.php"}
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const searchCollapseEl = document.getElementById('searchCollapse');
      const loginCollapseEl = document.getElementById('loginCollapse');
      const navbarCollapseEl = document.getElementById('navbarToggler');

      const searchCollapse = new bootstrap.Collapse(searchCollapseEl, { toggle: false });
      const loginCollapse = new bootstrap.Collapse(loginCollapseEl, { toggle: false });
      const navbarCollapse = new bootstrap.Collapse(navbarCollapseEl, { toggle: false });

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
</header>

<main>
  <!-- Article Hero -->
  {if $nameModule|strstr:'content' || ($nameModule|strstr:'news' && $mf=='nwsview')}
    {$setModule}
  {else}
    <section class="article-hero">
      <div class="container">
        <h1 class="display-5 fw-bold">The Future of Technology</h1>
        <div class="article-meta mt-2">
          <i class="fas fa-calendar-alt"></i> October 25, 2025 &nbsp;|&nbsp;
          <i class="fas fa-user"></i> By Admin &nbsp;|&nbsp;{$nameModule}
          <i class="fas fa-folder"></i> Tech
        </div>
      </div>
    </section>

    <!-- Main Article Content -->
    <div class="container my-5">
      <div class="row g-5">
        {if $nameModule|strstr:'forum'}
          <div class="col-md-12">
            <div class="article-content bg-white p-4 rounded shadow-sm">
              {$setModule}
            </div>
          </div>

        {elseif $nameModule|strstr:'contact' || $nameModule|strstr:'ezshopingcart'}
          {$setModule}
        {else}
          <div class="col-md-8">
            <div class="article-content bg-white p-4 rounded shadow-sm">
              {$setModule}
            </div>
          </div>

          <!-- Sidebar -->
          <div class="col-md-4">
            <div class="sidebar">
              <h5 class="fw-bold mb-3">Related Articles</h5>
              <ul class="list-unstyled">
                <li><a href="#" class="text-decoration-none">How AI is Changing the World</a></li>
                <li><a href="#" class="text-decoration-none">Top 10 Gadgets of 2025</a></li>
                <li><a href="#" class="text-decoration-none">The Rise of Quantum Computing</a></li>
              </ul>
              <hr>
              <h5 class="fw-bold mb-3">Categories</h5>
              <span class="badge bg-secondary me-1">Tech</span>
              <span class="badge bg-secondary me-1">Science</span>
              <span class="badge bg-secondary me-1">Innovation</span>
            </div>
          </div>
        {/if}
      </div>
    </div>
  {/if}
</main>

<footer class="container">
  <p class="float-end">
    <a href="#">Back to top</a>
  </p>
  <p>&copy; 2017–2025 powered by <a href="https://lanaicms.com/">LanaiCMS</a> </p>

</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

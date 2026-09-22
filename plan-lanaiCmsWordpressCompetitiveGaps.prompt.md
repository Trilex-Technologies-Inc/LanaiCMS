# LanaiCMS vs WordPress — Feature Gap Analysis & Roadmap

## Gap Analysis

| Area | LanaiCMS today | WordPress | Gap severity |
|---|---|---|---|
| **Plugin ecosystem** | ~22 built-in modules/blocks, ZIP install, no marketplace/versioning/dependencies | 60,000+ plugins, auto-update, dependency management | 🔴 Critical |
| **E-commerce** | Removed (`ezshop` was dropped) | WooCommerce ecosystem | 🔴 Critical if targeting SMB sites |
| **User roles/permissions** | Single `a`/user privilege flag | Admin/Editor/Author/Contributor/Subscriber + capabilities | 🔴 Critical |
| **Media library** | Raw upload folders, no UI, no cropping/resizing/WebP | Full media library, editing, responsive images | 🔴 Critical |
| **REST/headless API** | None — server-rendered only | Full REST API + Gutenberg/JS ecosystem | 🟠 High (blocks modern integrations) |
| **SEO** | Friendly URLs only for news, no schema.org/canonical/auto sitemap | Yoast/RankMath-class tooling | 🟠 High |
| **Block/page builder** | Static Smarty templates + fixed block positions | Gutenberg blocks / drag-drop builders (Elementor etc.) | 🟠 High |
| **Comments** | Flat, unmoderated, no spam filtering | Threaded, moderation, Akismet | 🟡 Medium |
| **Multi-language** | 2 hardcoded languages, no translation UI | WPML/Polylang plugins, i18n APIs | 🟡 Medium |
| **Caching/performance** | Smarty template cache only, no object/page cache or HTTP headers | Full-page caching plugins, object cache, CDN support | 🟡 Medium |
| **Backups** | Manual DB export to XML, no scheduling/offsite | Automated + cloud (UpdraftPlus etc.) | 🟡 Medium |
| **Security** | MD5 password hashing, mixed legacy SQL patterns | bcrypt, 2FA plugins, extensive hardening | 🔴 Critical (security debt, not just feature gap) |
| **Custom content types** | Fixed: News, Content, Contact | Arbitrary custom post types + fields (ACF) | 🟠 High |
| **Dashboard/UX** | Minimal admin UI | Rich, extensible dashboard/widgets | 🟡 Medium |

## Priority Recommendation (if aiming to genuinely compete)

1. **Security foundation first** — replace MD5 with password_hash/bcrypt, audit remaining raw SQL, add CSRF tokens consistently (some modules already do this, e.g. carousel).
2. **User roles/capabilities system** — prerequisite for almost everything else (multi-author blogs, e-commerce, plugin permissions).
3. **Media library** — high visible impact, needed before content authoring feels modern.
4. **Custom content types / flexible fields** — turns "News + Content + Contact" into a generic CMS core, unlocking most other verticals (portfolios, products, testimonials) without new modules each time.
5. **REST API layer** — unlocks headless use, JS-driven admin UI, and mobile apps later.
6. **Plugin marketplace/versioning** — needed only once the module system is stable and worth external contributions.

E-commerce, page builder, and SEO tooling are best done as modules built *on top of* items 1–4 rather than bolted onto the current architecture.

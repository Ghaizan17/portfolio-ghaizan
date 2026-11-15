# Digital Dimension

A production-ready personal portfolio and content platform built with Laravel 13, Livewire 4, Blade, Tailwind CSS 4, Filament 5, GSAP, and Three.js.

The public site is an editorial, dark-first portfolio with a cinematic chrome sphere, a full-screen navigation index, project case studies, development stories, a journal, certificates, experience, testimonials, a moderated guestbook, and a private CV download. Every piece of content is managed from a Filament admin panel at `/admin`.

> **Content honesty:** the application ships with no claimed identity, education, experience, certificates, testimonials, skills, or project outcomes. Seeded demo content is explicitly labelled as demonstration content and is only created when `SEED_DEMO_CONTENT=true`. Replace every placeholder from the admin panel before publishing.

---

## Contents

- [Stack](#stack)
- [Features](#features)
- [Requirements](#requirements)
- [Quick start](#quick-start)
- [Environment configuration](#environment-configuration)
- [Admin panel](#admin-panel)
- [Demo content](#demo-content)
- [Public routes](#public-routes)
- [Content model](#content-model)
- [Security](#security)
- [Accessibility, SEO, and performance](#accessibility-seo-and-performance)
- [Testing](#testing)
- [Production deployment](#production-deployment)
- [Laravel Cloud](#laravel-cloud)
- [Troubleshooting](#troubleshooting)
- [Project structure](#project-structure)

---

## Stack

| Layer | Technology |
| --- | --- |
| Backend | PHP 8.3+ (developed on PHP 8.4) |
| Framework | Laravel 13 |
| Interactive UI | Livewire 4 single-file components |
| Templates | Blade |
| Styling | Tailwind CSS 4 with CSS custom properties |
| Admin | Filament 5 at `/admin` |
| Animation | GSAP 3 + ScrollTrigger |
| 3D | Three.js (lazy-loaded chrome sphere) |
| Build | Vite 8 |
| Database | SQLite locally, MySQL in production |
| Rich text | HTMLPurifier via `mews/purifier` |
| Fonts | Self-hosted Inter and Space Grotesk variable fonts |

No React, Vue, Inertia, or additional frontend framework is used. Livewire handles interactive forms and project filtering; plain Blade and controllers render the rest.

---

## Features

### Public experience

- Dark-first design with a persistent light theme toggle and no flash of the wrong theme.
- Cinematic hero with a lazy-loaded Three.js chrome sphere, pointer parallax, and scroll response.
- WebGL fallback and a static frame under `prefers-reduced-motion`.
- Full-screen navigation index dialog with keyboard and focus handling.
- Editorial project archive with URL-synced search, category, technology, and sort filters.
- Project case studies with problem statement, objectives, role, features, process, gallery lightbox, published feedback, testimonials, and next-project navigation.
- **Project Development Stories**: structured, ordered story sections with optional imagery and code snippets.
- About, skills (no fabricated percentages), certificates, experience, resume, testimonials, and a developer journal.
- Contact form and moderated guestbook with validation, honeypots, time traps, and rate limiting.
- Private CV storage: PDFs are streamed through controller routes, never linked directly.
- Dynamic sitemap, `robots.txt`, canonical URLs, Open Graph/Twitter metadata, and JSON-LD for Person, WebSite, CreativeWork, and BlogPosting.
- Print styles for a clean resume printout.

### Admin experience

Filament resources cover:

- Profile and social links
- Education and experience
- Skill categories and skills
- Projects, project categories, and technologies
- Project media, features, processes, development stories, and feedback
- Certificates and certificate media
- Testimonials
- Resumes
- Messages
- Guestbook entries with moderation
- Journal posts, categories, and tags
- Site settings

The dashboard includes portfolio statistics and recent messages.

### Security

- Filament panel restricted to users with `is_admin = true`.
- Server-side validation on every public form.
- CSRF protection on all state-changing requests.
- Rate limiting on contact and guestbook submissions.
- Honeypot and minimum-time spam checks.
- HTMLPurifier sanitization in model save hooks for all rich-text fields.
- Restricted upload MIME types and size limits.
- Private CV disk with explicit download/preview routes.
- `X-Content-Type-Options: nosniff` on document responses.

---

## Requirements

- PHP 8.3 or newer with the usual Laravel extensions (`pdo`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`)
- Composer 2
- Node.js 20+ and npm
- SQLite (local) or MySQL 8+ (production)
- A writable `storage/` and `bootstrap/cache/` directory

---

## Quick start

```bash
# 1. Install PHP dependencies
composer install

# 2. Create the environment file and application key
cp .env.example .env
php artisan key:generate

# 3. Create the local SQLite database
touch database/database.sqlite

# 4. Create the schema and seed settings, the admin user, and optional demo content
php artisan migrate --seed

# 5. Link public storage for uploaded images and certificates
php artisan storage:link

# 6. Install frontend dependencies and build assets
npm install
npm run build

# 7. Run the application
php artisan serve
```

The site is available at `http://localhost:8000` and the admin panel at `http://localhost:8000/admin`.

For active development, `composer run dev` starts the Laravel server, queue listener, log viewer, and Vite dev server together.

### Shortcut

```bash
composer run setup
```

This installs dependencies, creates `.env`, generates a key, runs migrations, installs npm packages, and builds frontend assets. It does not seed data or create the storage link, so run:

```bash
php artisan db:seed
php artisan storage:link
```

---

## Environment configuration

Copy `.env.example` to `.env` and adjust the following.

### Application

```dotenv
APP_NAME="Digital Dimension"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000
```

Set `APP_DEBUG=false` and `APP_ENV=production` in production.

### Database

Local SQLite:

```dotenv
DB_CONNECTION=sqlite
```

The database file defaults to `database/database.sqlite`.

Production MySQL:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=digital_dimension
DB_USERNAME=your_user
DB_PASSWORD=your_password
```

### Admin account

The seeder creates an administrator from these values:

```dotenv
ADMIN_EMAIL=admin@digitaldimension.test
ADMIN_PASSWORD=password
```

Set a strong password **before** the first `php artisan db:seed`. The seeder does not overwrite an existing account with the same email. To reset access, update the password directly or recreate the database with `php artisan migrate:fresh --seed`.

### Demo content

```dotenv
SEED_DEMO_CONTENT=false
```

Set to `true` only when you want demonstration records and the public demo banner. See [Demo content](#demo-content).

### Uploads

```dotenv
UPLOAD_MAX_IMAGE_KB=4096
UPLOAD_MAX_DOCUMENT_KB=10240
```

Image uploads accept JPEG, PNG, WebP, and AVIF. Documents accept PDF only.

### Sessions, cache, and queues

The example environment uses database-backed sessions, cache, and queues:

```dotenv
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

Run a queue worker if you add queued jobs:

```bash
php artisan queue:work
```

---

## Admin panel

1. Visit `/admin`.
2. Sign in with the seeded administrator account.
3. Change the password and update the profile before publishing.

Default local credentials:

```text
Email:    admin@digitaldimension.test
Password: password
```

Only users with `is_admin = true` can access the panel. Create additional administrators with:

```bash
php artisan tinker --execute 'App\Models\User::create(["name" => "Editor", "email" => "editor@example.com", "password" => "change-me", "is_admin" => true]);'
```

### Recommended first-run checklist

1. Update **Profile** with your real name, headline, biography, location, and availability.
2. Replace the placeholder avatar.
3. Add genuine education, experience, skills, projects, certificates, and testimonials.
4. Upload an active resume PDF under **Resumes**.
5. Add social links and contact details.
6. Review **Settings** for site title, tagline, SEO defaults, theme, and footer text.
7. Set `SEED_DEMO_CONTENT=false` and remove any demonstration records before launch.
8. Confirm the demo banner is gone.

---

## Demo content

Demo content exists so the layout can be reviewed without inventing a real identity.

- It is created only when `SEED_DEMO_CONTENT=true`.
- Every record is labelled `Demonstration` or `Sample` in its title or copy.
- Setting `demo_mode` enables a public banner explaining that the content is placeholder data.
- The banner is controlled by the `demo_mode` setting, not by the environment flag alone.

To enable demo content from a clean database:

```bash
# .env
SEED_DEMO_CONTENT=true

php artisan migrate:fresh --seed
```

To remove it later:

```bash
# .env
SEED_DEMO_CONTENT=false

php artisan migrate:fresh --seed
```

Do not publish demonstration records as real achievements, certificates, employment, or testimonials.

---

## Public routes

| Method | URI | Name | Purpose |
| --- | --- | --- | --- |
| GET | `/` | `home` | Landing page, featured work, stats, latest writing |
| GET | `/about` | `about` | Biography, education, experience summary |
| GET | `/skills` | `skills` | Skill categories with evidence |
| GET | `/projects` | `projects.index` | Searchable/filterable project archive |
| GET | `/projects/{slug}` | `projects.show` | Full project case study and development stories |
| GET | `/certificates` | `certificates` | Grouped certificates |
| GET | `/experience` | `experience` | Experience and education timeline |
| GET | `/resume` | `resume` | Printable resume page and active CV preview |
| GET | `/resume/download` | `resume.download` | Private PDF download |
| GET | `/resume/preview` | `resume.preview` | Private inline PDF preview |
| GET | `/contact` | `contact` | Contact form |
| GET | `/guestbook` | `guestbook` | Moderated guestbook |
| GET | `/testimonials` | `testimonials` | Published testimonials |
| GET | `/journal` | `journal.index` | Developer journal |
| GET | `/journal/{slug}` | `journal.show` | Article |
| GET | `/sitemap.xml` | `sitemap` | Dynamic XML sitemap |
| GET | `/robots.txt` | `robots` | Crawler rules |
| GET | `/admin` | — | Filament admin panel |

Draft and unpublished records return `404` on public detail routes.

---

## Content model

Core models and relationships:

- `Profile` — single public identity record.
- `SocialLink` — ordered, activatable links.
- `Education`, `Experience`, `ExperienceMedia`.
- `SkillCategory` → `Skill` → optional linked `Project`.
- `ProjectCategory` → `Project` → `Technology` (many-to-many).
- `Project` → `ProjectMedia`, `ProjectFeature`, `ProjectProcess`, `ProjectStory`, `ProjectFeedback`, `Testimonial`.
- `Certificate` → `CertificateMedia`.
- `Resume` — one active PDF at a time.
- `Testimonial` — optionally linked to a project.
- `Message`, `GuestbookEntry`.
- `PostCategory`, `Post`, `PostTag` (pivot `post_tag`).
- `Setting` — cached key/value site configuration.
- `User` — Filament administrator.

Rich-text fields are sanitized in model `saving` hooks:

- `Profile::bio_long`, `Profile::career_goals`
- `Project::description`, `problem_statement`, `objectives`, `team_collaboration`
- `ProjectStory::content`
- `Post::content`

---

## Security

- **Admin access:** `User::canAccessPanel()` requires the `admin` panel and `is_admin`.
- **Validation:** all public submissions use server-side validation rules and attribute names.
- **Rate limits:** three contact messages and three guestbook entries per IP per ten minutes.
- **Spam:** hidden honeypot fields and a three-second minimum completion time.
- **Rich text:** HTMLPurifier removes scripts, event handlers, iframes, and unsafe markup.
- **Uploads:** MIME allow-lists and configurable size limits; images are served from the public disk, CVs from the private local disk.
- **Document streaming:** CV routes abort with `404` when no active file exists and set `nosniff`.
- **Secrets:** never commit `.env`; rotate `ADMIN_PASSWORD` and `APP_KEY` per environment.

---

## Accessibility, SEO, and performance

- Semantic landmarks, a skip link, visible focus states, and labelled controls.
- Keyboard-operable navigation dialog and lightbox.
- `prefers-reduced-motion` support in CSS, GSAP setup, and the Three.js scene.
- WebGL capability detection with a styled fallback.
- Animation and Three.js cleanup on Livewire navigation.
- Responsive layouts with no horizontal overflow at mobile widths.
- Canonical URLs, Open Graph, Twitter cards, and JSON-LD.
- Dynamic sitemap that includes only published content.
- Lazy-loaded Three.js chunk so the main bundle stays small.
- Eager-loaded relationships on list and detail pages to avoid N+1 queries.

---

## Testing

The suite uses PHPUnit with an in-memory SQLite database.

```bash
# Full suite
php artisan test --compact

# A focused file
php artisan test --compact tests/Feature/ContactFormTest.php

# A focused test
php artisan test --compact --filter=test_it_stores_a_valid_message
```

Coverage includes:

- Every public page renders.
- Draft projects and posts are hidden and return `404`.
- Guestbook visibility follows moderation status.
- Sitemap includes published records and excludes drafts.
- Contact and guestbook validation, storage, spam traps, and rate limits.
- Project search, category/technology filters, clearing, and pagination.
- Admin access control and smoke coverage for every Filament resource page.
- Private resume download and preview behavior.
- Rich-text sanitization on save and render.

Current status: **45 tests, 195 assertions, all passing.**

Format PHP before committing:

```bash
vendor/bin/pint --format agent
```

Rebuild assets after frontend changes:

```bash
npm run build
```

---

## Production deployment

### 1. Server requirements

- PHP 8.3+ with required extensions
- MySQL 8+
- Composer and Node.js available during deployment
- Web server document root pointing at `public/`
- HTTPS certificate

### 2. Deploy

```bash
composer install --no-dev --optimize-autoloader
cp .env.example .env
php artisan key:generate

# Configure production values in .env:
# APP_ENV=production
# APP_DEBUG=false
# APP_URL=https://your-domain.com
# DB_CONNECTION=mysql
# ADMIN_EMAIL=...
# ADMIN_PASSWORD=...
# SEED_DEMO_CONTENT=false

php artisan migrate --force
php artisan db:seed --force
php artisan storage:link

npm ci
npm run build

php artisan optimize
```

`php artisan optimize` caches configuration, routes, events, and views. Clear it after environment changes:

```bash
php artisan optimize:clear
```

### 3. File permissions

Ensure the web server can write to:

```text
storage/
bootstrap/cache/
```

Uploaded public media is stored in `storage/app/public` and exposed through the `public/storage` symlink. CVs remain in `storage/app/private` (the `local` disk) and are streamed by controllers.

### 4. Web server

Point Nginx or Apache at `public/`. Example Nginx location:

```nginx
root /var/www/digital-dimension/public;

location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

### 5. Queue and scheduler

If you enable queued jobs or scheduled tasks:

```bash
php artisan queue:work --tries=3
```

```cron
* * * * * cd /var/www/digital-dimension && php artisan schedule:run >> /dev/null 2>&1
```

### 6. Post-deploy checks

- `/` and every public route return `200`.
- `/admin` redirects guests to `/admin/login`.
- A non-admin account receives `403`.
- Images load from `/storage/...`.
- An uploaded CV downloads and previews correctly.
- `APP_DEBUG=false` and the demo banner is disabled.
- `sitemap.xml` and `robots.txt` use the production domain.

---

## Laravel Cloud

The application is compatible with Laravel Cloud.

1. Create an application and connect the repository.
2. Attach a MySQL database.
3. Set the production environment variables listed above.
4. Add a build command that installs dependencies and builds assets:

```bash
composer install --no-dev --optimize-autoloader && npm ci && npm run build
```

5. Add a deploy command:

```bash
php artisan migrate --force && php artisan optimize
```

6. Add object storage if you prefer remote media; update the `public` disk configuration accordingly.
7. Configure the domain and enable HTTPS.

Use the [`deploying-to-cloud`](../.agents/skills/deploying-to-cloud) skill and the `cloud` CLI when managing Cloud resources from the command line.

---

## Troubleshooting

### `Unable to locate file in Vite manifest`

Frontend assets are missing or stale. Run:

```bash
npm run build
```

During development, run `npm run dev` or `composer run dev`.

### Uploaded images return `404`

Create the public storage link:

```bash
php artisan storage:link
```

### The resume page shows “No active CV”

Upload a PDF under **Admin → Resumes** and mark it active. The download route intentionally returns `404` when no active file exists.

### `no such table` during tests

The test suite uses an in-memory SQLite database and `RefreshDatabase`. Re-run:

```bash
php artisan test --compact
```

### Changes do not appear after editing CSS or JS

Rebuild assets or keep the Vite dev server running.

### Permission errors

```bash
chmod -R ug+rwX storage bootstrap/cache
```

### Admin login fails after changing `ADMIN_PASSWORD`

The seeder does not update an existing user. Reset the password directly or recreate the database in a non-production environment.

---

## Project structure

```text
app/
  Filament/                 Admin resources, schemas, tables, relation managers, widgets
  Http/Controllers/         Public page, project, journal, sitemap, resume controllers
  Models/                   Domain models, scopes, traits, sanitization hooks
  Providers/                App and Filament panel providers
database/
  factories/                Model factories for tests and demos
  migrations/               Schema
  seeders/                  Settings, admin user, optional demo content
resources/
  css/app.css               Design tokens, components, responsive and print styles
  js/                       Theme, navigation, lightbox, animations, Three.js scene
  views/
    components/             Blade and Livewire single-file components
    layouts/                Public shell
    pages/                  Public page templates
    partials/               Header and footer
routes/web.php              Public routes; Filament registers /admin
tests/Feature/              Public, Livewire, admin, resume, and security tests
```

---

## License

This portfolio application is provided for personal use. Replace all demonstration content before publishing.

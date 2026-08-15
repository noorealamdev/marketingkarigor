# Deployment Guide — Marketing Karigor HQ

Deploy target: **cPanel / shared hosting** at `marketingkarigor.com`.
Stack: Laravel 12 · PHP 8.2 · MySQL · `QUEUE_CONNECTION=sync`.

---

## 1. First-time / full deploy

Run from the application root on the server (via SSH or cPanel Terminal):

```bash
git pull origin main
composer install --no-dev --optimize-autoloader
npm install && npm run build              # public/build is gitignored — must be built on the server
php artisan storage:link                 # harmless if the link already exists
php artisan migrate --force              # see "Database" below for the fresh-rebuild case
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan config:cache
```

> `--force` is required on production — Laravel blocks destructive/migration
> commands there unless you confirm with it.

---

## 2. Routine deploy (code changes, no schema wipe)

```bash
git pull origin main
composer install --no-dev --optimize-autoloader   # only if composer.lock changed
npm run build                                       # only if anything under resources/ changed
php artisan migrate --force                        # applies any NEW migrations only
php artisan config:clear && php artisan view:clear && php artisan config:cache
```

> **`public/build/` is gitignored** and never travels with `git pull`. Any change
> to a Blade layout's `<style>`/`<script>`, `resources/css/app.css`, or
> `resources/js/app.js` needs `npm run build` re-run on the server, or the live
> site keeps serving the old bundle. If `npm`/Node isn't available on the
> shared-hosting shell, build locally (`npm run build`) and commit/upload the
> resulting `public/build/` folder instead — check it isn't silently excluded
> by `.gitignore` if you go that route.

That's the normal case. Plain `migrate` applies only migrations the server
hasn't run yet and **never touches existing data**.

---

## 3. Database

### Seeding a brand-new / empty site

Use the **ProductionSeeder** — it creates ONLY roles, permissions, and the
super-admin account. It does **not** create the demo team/clients/projects that
`DatabaseSeeder` (local dev) creates.

```bash
php artisan migrate:fresh --seeder=ProductionSeeder --force
```

⚠️ `migrate:fresh` **drops every table and all data**. Only use it on a site
with no real data you need to keep.

**Admin login after seeding:** `noorealamdev@gmail.com` / `password`
→ **change this password immediately** from the profile page.

### Changing schema on a site that HAS real data

Never edit an already-run migration and never `migrate:fresh` a live site with
real data. Instead add a **new** migration that alters the table in place, then:

```bash
php artisan migrate --force
```

---

## 4. Environment (`.env`) checklist

| Key | Live value |
|-----|-----------|
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_URL` | `https://marketingkarigor.com` |
| `APP_TIMEZONE` | `Asia/Dhaka` (Bangladesh) |
| `QUEUE_CONNECTION` | `sync` (no worker process on shared hosting) |
| `MAIL_MAILER` | `resend` |
| `MAIL_FROM_NAME` | `Marketing Karigor` |
| `MAIL_FROM_ADDRESS` | your verified Resend sender |
| `MYSQLDUMP_PATH` | **unset / removed** (not available on shared hosting) |

After any `.env` change:

```bash
php artisan config:clear && php artisan config:cache
```

---

## 5. Shared-hosting gotchas

- **`proc_open` is disabled.** Image conversions use `nonOptimized()` so
  spatie/image-optimizer (which shells out via `proc_open`) is never called.
  Don't re-enable optimization.
- **No queue worker.** `QUEUE_CONNECTION=sync` means notifications/emails send
  inline during the request. Notification classes implement `ShouldQueue`, so if
  you later move to a VPS you can switch to `database` + `php artisan queue:work`
  with no code changes.
- **`storage:link`** — the `public/storage` symlink must exist for uploaded
  logos/media to be reachable. Re-run `php artisan storage:link` if
  `https://marketingkarigor.com/storage/setting/1/logo.webp` 404s.
- **Backups** — `spatie/laravel-backup` DB dumps need `mysqldump`, which shared
  hosting usually lacks. Keep `MYSQLDUMP_PATH` unset and rely on cPanel backups
  instead.

---

## 6. Post-deploy verification

- [ ] Log in as admin, change the default password.
- [ ] Re-upload the logo in Settings (a `migrate:fresh` clears the media record).
- [ ] Generate one **invoice PDF** and one **report PDF** — logo shows on both.
- [ ] Create a test report with a non-Facebook service (Google Ads / Web Dev),
      confirm universal metrics + PDF, then delete it.
- [ ] Log in as a client, check the 🔔 notification bell and portal report view.
- [ ] Send a report to a client, confirm the email actually arrives.
- [ ] Confirm `https://marketingkarigor.com/storage/setting/1/logo.webp` loads.

---

## 7. Git author note

Commits for this repo are authored as **Noor E Alam** via per-commit env vars
(no global `git config` is set on the dev machine):

```bash
GIT_AUTHOR_NAME="Noor E Alam" GIT_AUTHOR_EMAIL="noorealamdev@gmail.com" \
GIT_COMMITTER_NAME="Noor E Alam" GIT_COMMITTER_EMAIL="noorealamdev@gmail.com" \
git commit -m "..."
```

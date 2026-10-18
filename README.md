# Inkwell

A multi-author blog built with Laravel. People sign up, write posts in Markdown, save drafts or publish, and readers can comment, like and browse by tag or author.

**Live:** https://hibah-inkwell.vercel.app (a Vercel rewrite in front of the server at https://66-135-1-187.sslip.io, which also still works)

## Features

- **Accounts:** register, log in and out, a public author page at `/@username`, profile and password settings
- **Posts:** Markdown body, cover images, tags, drafts and publishing, edit and delete (only the author can)
- **Reading:** home feed with a latest/popular sort, tag pages, author pages
- **Engagement:** comments (the comment author or the post author can delete them), likes, and a "Liked" page of posts you've liked
- **Dashboard:** your own posts, drafts included
- **Rate limits** on sign-up, log-in, password changes, comments and likes

## Stack

- PHP 8.3, Laravel 13
- PostgreSQL in production (SQLite works locally)
- Blade views, Tailwind CSS 4, Vite
- PHPUnit feature tests in `tests/Feature`

## Run it locally

```sh
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan storage:link   # makes uploaded cover images reachable
npm run build              # or `npm run dev` while working
php artisan serve
```

Run the tests with `php artisan test`.

## Deploying

Production runs on the devbox (`ssh devbox`): code in `/var/www/app` (a clone of this repo), nginx site `app`, PHP 8.3-FPM, and a Let's Encrypt cert renewed by `certbot.timer`. Config and routes are cached, so after pulling:

```sh
ssh devbox
cd /var/www/app
git pull
composer install --no-dev --optimize-autoloader
php artisan migrate --force
npm ci && npm run build
php artisan optimize
```

Any `.env` change also needs `php artisan optimize` before it takes effect.

## Related

The CaseFile detective game used to live in this app at `/casefile`. It has its own repo now, [HibahNafees/CaseFile](https://github.com/HibahNafees/CaseFile), and the server serves it from a separate clone.

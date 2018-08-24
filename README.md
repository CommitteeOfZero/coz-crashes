# CoZCrashes

Crash report collector we use. Unsupported, but if you want to set one up yourself:

- Needs PHP 5.6+, MySQL 5.5+, and the ability to run scheduled tasks
- `composer install`
- Import `schema.sql`
- Copy `config.php.sample` to `config.php`
- Adjust settings
- Make `public/` accessible via web
- Run `src/cron.php` regularly (we have it set to once an hour)
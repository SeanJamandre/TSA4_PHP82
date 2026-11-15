# Tasks for Today for PHP 8.2.12

Jamandre, Sean James A. — IT0049 TSA4

## Start on Windows

Extract the whole ZIP into a NEW folder. Double-click START.cmd. Leave its terminal open. The script installs dependencies, configures the local SQLite database, generates authentication, applies the assignment source, runs checks, and starts the server at http://127.0.0.1:8000. Register an account to manage tasks.

This package targets your XAMPP PHP 8.2.12 and Composer. Node and npm are not required: authentication pages and account forms use inline CSS and ordinary HTML controls instead of Vite, Tailwind compilation, or Alpine. Laravel 12 and Breeze are retained. Composer resolves dependencies for PHP 8.2.12 via platform.php; actual extensions must also be available.

Composer downloads the official skeleton and Breeze during setup; internet is required. PHP extensions pdo_sqlite, sqlite3, mbstring, openssl, fileinfo, xml, and curl are checked before installation. If one is missing, enable it in the PHP CLI configuration and retry. The script does not edit your php.ini.

The full raw Laravel project is generated in app. The source directory is the assignment overlay, not a standalone Laravel installation. Use a fresh extraction to avoid modifying your previous project. Re-running START.cmd in this package reapplies supplied source and local configuration; back up your edits before rerunning. Existing SQLite data is preserved and sample seeding is idempotent.

Breeze's installer attempts its standard npm build, whose output is saved to app/breeze-install.log. The custom layouts replace every Vite-dependent layout afterward, so that frontend build is unnecessary. Actual installer failures stop setup.

Runtime limitation: PHP and Composer are unavailable in the authoring workspace, so the application and automated tests have not been executed here. Setup runs them on your machine and stops on errors. No hosted site or GitHub repository has been created.

The earlier TSA2/TSA3 and TFA7 documents were not supplied. Standard task fields are assumed; compare with your previous assessment before submission.

## Included behavior

- Public Welcome, Task List, task details, student Profile, and About pages.
- Breeze registration, login, password reset, logout, and private account settings.
- All authenticated users manage a shared task list, as specified by the assignment; tasks are not private per user.
- Required title (150 characters maximum), due date, valid priority and status; optional description (5,000 characters maximum).
- CSRF tokens, PATCH/DELETE method spoofing, server validation, old input, escaped output, and authentication middleware.
- Archive uses `is_archived`; records are retained and hidden from public details and listings and cannot be edited.
- A Mailable and Blade email template send through the Mail facade when an update changes a non-done task to done. Saving an already-done task does not resend. Reopening then completing it sends a new notification. Creation with done status does not send because the requirement targets updates.
- Database migration and idempotent sample task seeder included. Breeze supplies the User migration and authentication source.

## Mail verification

Setup appends the following to `app/.env`:

```dotenv
MAIL_MAILER=log
TASK_NOTIFICATION_RECIPIENT=tasks@example.test
```

The address above is a safe placeholder. With the log mailer, email contents are written to `storage/logs/laravel.log`; nothing reaches a real inbox. After editing mail settings run `php artisan config:clear`.

1. Register and log in.
2. Create a pending task.
3. Edit its status to done.
4. Open `storage/logs/laravel.log` and find `Task completed:` and the task title.
5. Edit the description while retaining done status. Confirm there is no additional completion email log entry.
6. Archive the task and confirm it disappears while its database row remains.

For a test SMTP inbox, enter the credentials supplied by your test service:

```dotenv
MAIL_MAILER=smtp
MAIL_HOST=your-test-smtp-host
MAIL_PORT=2525
MAIL_USERNAME=your-test-username
MAIL_PASSWORD=your-test-password
MAIL_FROM_ADDRESS=tasks@example.test
MAIL_FROM_NAME="Tasks for Today"
TASK_NOTIFICATION_RECIPIENT=your-test-recipient@example.test
```

Set transport encryption/scheme as required by your SMTP provider and Laravel mail configuration. Never commit `.env` or credentials.

Mail is synchronous inside the update transaction: transport failures fail the request and roll back the status change. A row lock serializes updates on databases supporting row locks. This is suitable for the assignment's safe test transport; it is not a durable exactly-once mail delivery system. A production application should use an outbox and retries to handle transport/commit failures.

## Tests

From the generated app directory:

```sh
php artisan test
```

The included feature tests cover public access, guest restrictions on every management action, creation, update, validation, archive retention and access restrictions, completion email recipient, suppression on already-done updates, and a second notification after reopening and recompleting. Breeze also installs its authentication tests. Profile tests are adapted to /account; /profile is the public student page. These tests must pass on your machine; they have not been run in the authoring environment.

## GitHub submission

Run these commands INSIDE the generated `app` folder after setup and tests pass:

```sh
git init
git add .
git commit -m "Implement Tasks for Today CRUD authentication and mail"
git branch -M main
git remote add origin YOUR_GITHUB_REPOSITORY_URL
git push -u origin main
```

Create an empty GitHub repository first and replace the placeholder with its URL. Commit `composer.lock`, migrations, seeders, tests, and the README. Keep `.env`, `vendor`, `node_modules`, logs, and database files out of Git. The migrations and seeder satisfy the database submission requirement.

## Hosting

Use a PHP Laravel-capable host with PHP 8.2+, Composer support, a database, HTTPS, and a persistent writable `storage` directory. Static-only hosting cannot execute this application.

Set the web document root to `public`. Install dependencies with `composer install --no-dev --optimize-autoloader` ; no asset build is needed for the supplied inline CSS layouts. Configure production environment variables:

- `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL` set to the hosted HTTPS URL.
- A persistent `APP_KEY` generated with `php artisan key:generate --show`.
- Database connection variables for the host's database. For SQLite, use a persistent absolute `DB_DATABASE` path and ensure the file exists.
- `MAIL_MAILER=log` for the safe assessment demonstration, plus `TASK_NOTIFICATION_RECIPIENT`.

Run `php artisan migrate --force`, optionally `php artisan db:seed --force` for sample tasks, and `php artisan optimize`. Give the web process write access to `storage` and `bootstrap/cache`. Register an account through the site, then verify all public pages, login, CRUD, archive, and email behavior on the hosted instance. Use a persistent database so tasks survive redeployment.

Submit the actual GitHub repository URL and working hosted URL. Neither link exists yet; do not submit placeholder URLs.

## Project map

`app/Http/Controllers/TaskController.php`: CRUD, archive, and mail trigger.  
`app/Http/Requests/SaveTaskRequest.php`: validated input.  
`app/Models/Task.php`: active scope and date/boolean casts.  
`app/Mail/TaskCompleted.php`: Mailable.  
`config/tasks.php`: notification recipient.  
`database/migrations`: task schema.  
`database/seeders/DatabaseSeeder.php`: sample data.  
`resources/views/tasks`: CRUD templates.  
`resources/views/emails/task-completed.blade.php`: email body.  
`routes/web.php`: public and protected routes.  
`tests/Feature/TaskManagementTest.php`: feature checks.

## References

https://laravel.com/docs/12.x/mail  
https://github.com/laravel/breeze

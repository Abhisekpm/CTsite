## Deployment to GoDaddy/cPanel

SSH through Putty (password $Beta")

cd CTsite
git status

Discard Local Changes:
git reset --hard HEAD

Pull Latest Code from GitHub:
git pull origin master


Revised Deployment Sequence
1. Preparation & Local Build

Backup live files & DB.

Commit all local changes to Git.

Locally run:

bash
Copy
Edit
npm install
npm run build
composer install --optimize-autoloader --no-dev
php artisan config:clear
php artisan route:clear
php artisan view:clear
Commit your built public/build (or compiled assets) to Git.

2. Put Site into Maintenance Mode

If you have SSH:

bash
Copy
Edit
php artisan down


3. Database Prep

In cPanel → MySQL® Databases, verify (or recreate) your live database & user.

In cPanel → phpMyAdmin, DROP or truncate all tables in the live DB (you’ve already backed up).


4. Code Deployment

Clear out your public_html directory contents (but keep the folder).

Via SSH/Git (preferred):

bash
Copy
Edit
cd ~
git clone git@github.com:you/your_repo.git your_laravel_app
Or via SFTP/File Manager: upload your zipped project and extract.


5. Environment & Storage

Copy or upload your .env to ~/your_laravel_app/.env.

Edit it with your live domain, DB_* creds, APP_ENV=production, APP_DEBUG=false, API keys, mail settings, etc.

In that same folder, run (SSH/cPanel Terminal):

bash
Copy
Edit
php artisan storage:link


6. Import Local Database

In cPanel → phpMyAdmin, select your live database → Import → choose your local-exported .sql → Go.



7. Server-Side Dependencies & Caching

SSH into ~/your_laravel_app:

bash
Copy
Edit
composer install --optimize-autoloader --no-dev
Permissions:

bash
Copy
Edit
chmod -R 775 storage bootstrap/cache
chown -R your_cpanel_user:your_cpanel_user storage bootstrap/cache
Caches & optimization:

bash
Copy
Edit
php artisan config:cache
php artisan route:cache
php artisan view:cache
Optional: only if you have new migrations since your SQL dump:

bash
Copy
Edit
php artisan migrate --force
Finalize & Test


8. Exit maintenance mode:

bash
Copy
Edit
php artisan up
Visit your site, click through key pages, forms, admin, etc.

Tail your logs (storage/logs/laravel.log) for any errors.


9. Cleanup

Remove any stray backup zips or SQL dumps from your server.

Confirm your cron jobs (if you have scheduled tasks) are still in place.
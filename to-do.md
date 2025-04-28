# Custom Cake Order Feature To-Do List

This list tracks the implementation steps for the custom cake order form and confirmation workflow.

## Frontend (View - `resources/views/custom_order.blade.php`)

- [x] Create basic view structure (extend layout, hero header).
- [x] Add HTML form with all required fields (Name, Email, Phone, Pickup Date/Time, Size, Flavor, Eggs, Message, Decorations, Allergies).
- [x] Display validation errors.
- [x] Display success message.
- [x] Add JavaScript for date/time picker enhancement (e.g., disable past dates, format restrictions). (Using Flatpickr)
- [x] Consider adding client-side validation for better UX (optional). (Basic tab validation exists)
- [x] Implement image upload for custom decoration ideas (input field added, form enctype updated).

## Backend (Controller - `app/Http/Controllers/OrderController.php`)

- [x] Create `create` method to display the form.
- [x] Fetch necessary data (`Settings`, `Testimonials`, `cakeFlavors`) for the view/layout.
- [x] Implement `store` method:
    - [x] Define validation rules for all form fields (required, email, date, etc.).
    - [x] Handle validation failure (redirect back with errors and `old()` input - automatic via `validate()`).
    - [x] Create a `CustomOrder` model and corresponding migration (via `make:model -m`).
    - [x] Store the validated order data into the `custom_orders` database table (using `CustomOrder::create()`).
    - [x] Handle optional image upload storage (using `Storage::disk('public')->put()`).
    - [x] Trigger Notifications (Email & SMS) after successful order save.
    - [x] Redirect back to the form with a success message upon successful submission (actual message added).

## Routing (`routes/web.php`)

- [x] Define `GET` route for displaying the form (`custom-order.create`).
- [x] Define `POST` route for submitting the form (`custom-order.store`).
- [x] Ensure route names are correct and used consistently (Fixed `cakes-menu` route name).

## Database

- [x] Create `custom_orders` table migration.
    - [x] Define initial columns.
- [x] Run the initial migration.
- [x] **Create new migration to add `price` column** (nullable decimal/float/integer) to `custom_orders` table.
- [x] **Run the new migration.**
- [ ] Review `status` column values/defaults (e.g., pending, priced, confirmed, cancelled).

## Model

- [x] Create `CustomOrder` Eloquent model (`app/Models/CustomOrder.php`).
    - [x] Define `$fillable` property for mass assignment security.
    - [x] Define any necessary relationships (if applicable later).
- [x] **Add `price` to `$fillable` property.**

## Configuration (`.env`, `config/*`)

- [x] Configure Twilio credentials (`TWILIO_SID`, `TWILIO_TOKEN`, `TWILIO_FROM`) in `.env`.
- [x] Add Admin contact info (`ADMIN_PHONE`) to `.env` (or decide to fetch from settings).
- [ ] Verify `config/services.php` uses Twilio `.env` variables (if placing them there).

## Initial Order Submission (`OrderController@store`)

- [x] Validate submitted data.
- [x] Handle optional image upload storage.
- [x] Save order with `status` = 'pending'.
- [x] Implement **initial Customer SMS** (Pending Status) sending.
- [x] Implement **Admin Notification SMS** (New Pending Order).
- [x] Redirect back with success message.

## SMS Notifications (Twilio)

- [x] Install Twilio SDK (`composer require twilio/sdk`).
- [x] Implement Twilio client instantiation.
- [x] Implement logic for: 
    - [x] Customer Initial Pending SMS (`OrderController@store`).
    - [x] Admin New Order SMS (`OrderController@store`).
    - [x] Customer Priced / Confirmation Request SMS (`Admin Order Update Logic` -> `Admin/OrderController@updatePrice`).
    - [x] (Optional) Customer Final Confirmation SMS (`Webhook Logic`).
    - [x] (Optional) Admin Order Confirmed SMS (`Webhook Logic`).
- [x] Add error handling (`try-catch`) for SMS sending.

## Admin Panel Integration (MANDATORY)

- [x] Create Routes (`/admin/orders`, `/admin/orders/{id}/price`).
- [x] Create Controller (`Admin/OrderController`).
    - [x] Method to list pending/priced orders.
    - [x] Method to show a single order.
    - [x] Method to update price & status (triggering customer price SMS).
- [x] Create Blade Views for Admin:
    - [x] Order list view.
    - [x] Order detail view with pricing form.
    - [x] Make specific fields editable on detail view (name, size, flavor, eggs, message, decorations, allergies).
- [x] Add links to admin sidebar.

## Twilio Webhook for SMS Replies

- [ ] Configure Twilio Messaging Service Webhook URL to point to Laravel app.
- [x] Define route for webhook (e.g., `/webhooks/twilio/sms`, POST, exclude CSRF).
- [x] Create Controller (`TwilioWebhookController`).
- [x] Implement webhook logic (`handle` method):
    - [x] Validate Twilio request signature. (Code commented out - **recommended for production**)
    - [x] Extract `From` number and `Body`.
    - [x] Consider Phone Number Normalization (e.g., E.164) for robust matching. 
    - [x] Find corresponding 'priced' order.
    - [x] Check for confirmation keyword (e.g., "YES").
    - [x] Update order status to 'confirmed'.
    - [x] Trigger optional final notifications (SMS). (Code commented out)
    - [x] Return empty TwiML response. 

## Multiple Image Uploads & Preview (Future Enhancement)

- [x] **Database:** Create `custom_order_images` table (migration needed: `id`, `custom_order_id`, `path`, `timestamps`).
- [x] **Model:** Create `CustomOrderImage` model (`$fillable`).
- [x] **Model:** Add `hasMany` relationship (`images()`) from `CustomOrder` to `CustomOrderImage`.
- [x] **Frontend (HTML):** Update file input in `custom_order.blade.php`:
    - [x] Add `multiple` attribute.
    - [x] Change `name` to `decoration_images[]`.
    - [x] Add `div` for preview (`id="image-preview-container"`).
- [x] **Frontend (JS):** In `custom_order.blade.php`:
    - [x] Add `change` event listener to file input.
    - [x] On change, clear preview `div`.
    - [x] Loop through selected files (`event.target.files`).
    - [x] Use `FileReader` to read each file.
    - [x] Create `img` element for thumbnail and append to preview `div`.
- [x] **Backend (`OrderController@store`):**
    - [x] Update validation rules for `decoration_images` (array) and `decoration_images.*` (each file).
    - [x] Check `request->hasFile('decoration_images')`.
    - [x] Loop through `$request->file('decoration_images')`, storing each file.
    - [x] After `CustomOrder` is saved, loop through stored paths and create associated `CustomOrderImage` records.
    - [x] Remove old single `decoration_image_path` logic/column usage.
- [x] **Admin View (`admin/orders/show.blade.php`):**
    - [x] Access images via relationship (`$order->images`).
    - [x] Loop through images and display each using `Storage::url($image->path)`.
- [x] **Database Cleanup:** Create migration to remove `decoration_image_path` column from `custom_orders` table (run after verification).

## Deployment to GoDaddy/cPanel

This plan outlines the steps to replace the existing live site with the current local/GitHub version and replace the live database.

**WARNING:** Proceed with caution. Back up your live site files and database *before* starting. These steps involve deleting live data.

**Phase 1: Preparation**

- [ ] **Backup Live Site:**
    - [ ] Files: Use cPanel File Manager (Compress `public_html` or relevant directory) or Backup Wizard.
    - [ ] Database: Use cPanel phpMyAdmin to export the current live database as an SQL file.
- [ ] **Backup Local Database:** Export your local development database as an SQL file.
- [ ] **Verify PHP Version:** Check cPanel MultiPHP Manager matches your Laravel requirement.
- [ ] **Check PHP Extensions:** Ensure required extensions are enabled in cPanel "Select PHP Version".
- [ ] **Commit Local Code:** Ensure all local changes are committed to Git.

**Phase 2: Database Deployment**

- [ ] **Export Local DB:** Export your final local database again as an `.sql` file (e.g., using phpMyAdmin, Sequel Pro, `mysqldump`).
- [ ] **Clear Live DB:** Using cPanel phpMyAdmin, select the live database and **DROP** all existing tables (ensure you have the backup!).
- [ ] **Import Local DB:** Using cPanel phpMyAdmin, select the live database and use the **Import** tab to upload and execute the `.sql` file exported from your local machine.
    - *Note: For large databases, cPanel import might time out. You may need to split the SQL file or use SSH if available.* 

**Phase 3: Code Deployment**

- [ ] **Choose Method:** Decide how to upload files (cPanel File Manager, FTP/SFTP client like FileZilla, or `git clone` via SSH if available).
- [ ] **Clear Live Files (Carefully):** 
    - Using File Manager or FTP/SFTP, delete the contents of your existing website directory (often `public_html`, but **confirm your domain's document root**).
    - **Do NOT delete the `public_html` folder itself unless your Laravel project root will become the new document root (less common).**
- [ ] **Upload Project Files:**
    - **Method 1 (No SSH):** Upload your *entire* local project folder (except maybe `node_modules`) via FTP/SFTP or File Manager zip upload. Place it *outside* `public_html` (e.g., in your home directory `/home/your_cpanel_user/your_laravel_app`). Upload your local `vendor` folder if you cannot run composer on the server.
    - **Method 2 (SSH):** Navigate to the directory *outside* `public_html` via SSH and clone your repository: `git clone your_repo_url.git your_laravel_app`.

**Phase 4: Configuration**

- [ ] **Configure `.env` File:**
    - Copy your local `.env.example` to `.env` **on the server** (or upload your local `.env` and **immediately edit it**).
    - **Crucially, update `.env` with:**
        - `APP_NAME`, `APP_URL` (your live domain)
        - `APP_ENV=production`
        - `APP_DEBUG=false`
        - **Live** Database connection details (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` - find these in cPanel "MySQL Databases").
        - Mail driver settings, Twilio keys, any other production API keys.
    - **DO NOT USE YOUR LOCAL DATABASE DETAILS OR `APP_DEBUG=true` IN PRODUCTION.**
- [ ] **Set Document Root:**
    - Using cPanel "Domains" or "Subdomains", ensure the **Document Root** for your domain points to the `public` directory inside your uploaded Laravel project (e.g., `/home/your_cpanel_user/your_laravel_app/public`).
    - *Alternative (Less Ideal):* If you uploaded inside `public_html`, you might need to configure `.htaccess` - consult Laravel deployment docs.
- [ ] **Storage Link:**
    - **Method 1 (SSH):** Navigate to your Laravel project root via SSH and run `php artisan storage:link`.
    - **Method 2 (No SSH):** You may need to manually create a symbolic link from `public/storage` to `storage/app/public` using cPanel File Manager (if it allows symlinks) or adjust file upload paths/disk configurations.

**Phase 5: Dependencies & Optimizations**

- [ ] **Install Composer Dependencies (if not uploaded):**
    - **Method 1 (SSH):** Navigate to project root via SSH and run `composer install --optimize-autoloader --no-dev`.
    - **Method 2 (No SSH):** Ensure you uploaded the correct `vendor` directory from your local machine (matching the server PHP version).
- [ ] **Set Permissions:** Using cPanel File Manager or SSH (`chmod`), ensure the `storage` and `bootstrap/cache` directories are writable by the web server (e.g., permissions `775`).
- [ ] **Run Artisan Optimization Commands (Highly Recommended):**
    - **Method 1 (SSH):** Run these commands:
        - `php artisan config:cache`
        - `php artisan route:cache`
        - `php artisan view:cache`
        - `php artisan optimize` (Combines the above)
        - `php artisan migrate --force` (If you have *new* migrations since the DB import - use with caution)
    - **Method 2 (No SSH):** You may need to skip these, or run them locally *before* uploading (less ideal, paths might differ).
- [ ] **Clear Caches (if needed):**
    - `php artisan cache:clear`
    - `php artisan config:clear`
    - `php artisan route:clear`
    - `php artisan view:clear`

**Phase 6: Testing**

- [ ] **Browse Live Site:** Open your website in a browser.
- [ ] **Test Core Features:** Check logins, forms (contact, custom order), menu display, admin panel access.
- [ ] **Check for Errors:** Look for any visual errors or Laravel error pages.
- [ ] **Check Logs:** Check `storage/logs/laravel.log` via File Manager or SSH for any server-side errors.

**Phase 7: Post-Deployment**

- [ ] **Monitor:** Keep an eye on the site and logs for a while.
- [ ] **Cron Jobs:** If your application needs scheduled tasks, set them up using cPanel "Cron Jobs".
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
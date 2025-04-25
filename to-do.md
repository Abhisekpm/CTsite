# Custom Cake Order Feature To-Do List

This list tracks the implementation steps for the custom cake order form and confirmation workflow.

## Frontend (View - `resources/views/custom_order.blade.php`)

- [x] Create basic view structure (extend layout, hero header).
- [x] Add HTML form with all required fields (Name, Email, Phone, Pickup Date/Time, Size, Flavor, Eggs, Message, Decorations, Allergies).
- [x] Display validation errors.
- [x] Display success message.
- [ ] Add JavaScript for date/time picker enhancement (e.g., disable past dates, format restrictions).
- [ ] Consider adding client-side validation for better UX (optional).
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
    - [ ] Trigger Notifications (Email & SMS) after successful order save.
    - [x] Redirect back to the form with a success message upon successful submission (actual message added).

## Routing (`routes/web.php`)

- [x] Define `GET` route for displaying the form (`custom-order.create`).
- [x] Define `POST` route for submitting the form (`custom-order.store`).
- [x] Ensure route names are correct and used consistently (Fixed `cakes-menu` route name).

## Database

- [x] Create `custom_orders` table migration.
    - [x] Define initial columns.
- [x] Run the initial migration.
- [ ] **Create new migration to add `price` column** (nullable decimal/float/integer) to `custom_orders` table.
- [ ] **Run the new migration.**
- [ ] Review `status` column values/defaults (e.g., pending, priced, confirmed, cancelled).

## Model

- [x] Create `CustomOrder` Eloquent model (`app/Models/CustomOrder.php`).
    - [x] Define `$fillable` property for mass assignment security.
    - [ ] Define any necessary relationships (if applicable later).
- [ ] **Add `price` to `$fillable` property.**

## Configuration (`.env`, `config/*`)

- [ ] Configure Twilio credentials (`TWILIO_SID`, `TWILIO_TOKEN`, `TWILIO_FROM`) in `.env`.
- [ ] Add Admin contact info (`ADMIN_PHONE`) to `.env` (or decide to fetch from settings).
- [ ] Verify `config/services.php` uses Twilio `.env` variables (if placing them there).

## Initial Order Submission (`OrderController@store`)

- [x] Validate submitted data.
- [x] Handle optional image upload storage.
- [x] Save order with `status` = 'pending'.
- [ ] Implement **initial Customer SMS** (Pending Status) sending.
- [ ] Implement **Admin Notification SMS** (New Pending Order).
- [x] Redirect back with success message.

## SMS Notifications (Twilio)

- [ ] Install Twilio SDK (`composer require twilio/sdk`).
- [ ] Implement Twilio client instantiation.
- [ ] Implement logic for: 
    - [ ] Customer Initial Pending SMS (`OrderController@store`).
    - [ ] Admin New Order SMS (`OrderController@store`).
    - [ ] Customer Priced / Confirmation Request SMS (`Admin Order Update Logic`).
    - [ ] (Optional) Customer Final Confirmation SMS (`Webhook Logic`).
    - [ ] (Optional) Admin Order Confirmed SMS (`Webhook Logic`).
- [ ] Add error handling (`try-catch`) for SMS sending.

## Admin Panel Integration (MANDATORY)

- [ ] Create Routes (`/admin/orders`, `/admin/orders/{id}/price`).
- [ ] Create Controller (`Admin/OrderController`).
    - [ ] Method to list pending/priced orders.
    - [ ] Method to show a single order.
    - [ ] Method to update price & status (triggering customer price SMS).
- [ ] Create Blade Views for Admin:
    - [ ] Order list view.
    - [ ] Order detail view with pricing form.
- [ ] Add links to admin sidebar.

## Twilio Webhook for SMS Replies

- [ ] Configure Twilio Messaging Service Webhook URL to point to Laravel app.
- [ ] Define route for webhook (e.g., `/webhooks/twilio/sms/reply`, POST, exclude CSRF).
- [ ] Create Controller (`TwilioWebhookController`).
- [ ] Implement webhook logic:
    - [ ] Validate Twilio request signature.
    - [ ] Extract `From` number and `Body`.
    - [ ] Find corresponding 'priced' order.
    - [ ] Check for confirmation keyword (e.g., "YES").
    - [ ] Update order status to 'confirmed'.
    - [ ] Trigger optional final notifications (SMS).
    - [ ] Return empty TwiML response. 
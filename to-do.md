# Custom Cake Order Feature To-Do List

This list tracks the implementation steps for the custom cake order form.

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
    - [ ] Implement logic to send an email notification to the admin/shop owner.
    - [ ] Implement logic to send a confirmation email to the customer (optional).
    - [x] Redirect back to the form with a success message upon successful submission (actual message added).

## Routing (`routes/web.php`)

- [x] Define `GET` route for displaying the form (`custom-order.create`).
- [x] Define `POST` route for submitting the form (`custom-order.store`).
- [x] Ensure route names are correct and used consistently (Fixed `cakes-menu` route name).

## Database

- [x] Create `custom_orders` table migration.
    - [x] Define columns for: `customer_name`, `email`, `phone`, `pickup_date`, `pickup_time`, `cake_size`, `cake_flavor`, `eggs_ok`, `message_on_cake`, `custom_decoration`, `decoration_image_path`, `allergies`, `status`, timestamps.
- [x] Run the migration (`php artisan migrate`).

## Model

- [x] Create `CustomOrder` Eloquent model (`app/Models/CustomOrder.php`).
    - [x] Define `$fillable` property for mass assignment security.
    - [ ] Define any necessary relationships (if applicable later).

## Email

- [ ] Create a Mailable class for the admin notification (e.g., `AdminOrderNotification`).
    - [ ] Define email content (subject, view).
    - [ ] Create a Blade view for the admin email.
- [ ] Create a Mailable class for the customer confirmation (e.g., `CustomerOrderConfirmation` - optional).
    - [ ] Define email content (subject, view).
    - [ ] Create a Blade view for the customer email.
- [ ] Configure mail settings in the `.env` file.

## Admin Panel Integration (Optional - Future Enhancement)

- [ ] Create routes for viewing/managing custom orders in the admin area (e.g., `/admin/custom-orders`).
- [ ] Create controller methods in an admin controller (e.g., `Admin/OrderController.php`) to list, view, update status, and delete orders.
- [ ] Create Blade views for the admin order management interface.
- [ ] Add links to the admin sidebar. 
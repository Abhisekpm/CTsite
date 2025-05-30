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

## MCP (Model Context Protocol) Server Implementation

This section covers creating an MCP server to expose the custom cake ordering functionality as an API for AI assistants and external integrations.

### 1. REST API Foundation

- [ ] **API Routes Setup (`routes/api.php`)**:
    - [ ] Add custom order endpoints:
        - [ ] `POST /api/orders` - Create new cake order
        - [ ] `GET /api/orders/{id}` - Get specific order details
        - [ ] `GET /api/orders` - List orders (with filtering)
        - [ ] `PUT/PATCH /api/orders/{id}` - Update order (admin only)
        - [ ] `DELETE /api/orders/{id}` - Cancel order
    - [ ] Add support endpoints:
        - [ ] `GET /api/cake-flavors` - List available cake flavors
        - [ ] `GET /api/cake-sizes` - List available cake sizes and pricing
        - [ ] `GET /api/order-status/{id}` - Check order status
        - [ ] `POST /api/orders/{id}/upload-images` - Upload decoration images

- [ ] **API Controller (`app/Http/Controllers/Api/OrderController.php`)**:
    - [ ] Create dedicated API controller extending base Controller
    - [ ] Implement `store()` method for order creation with JSON responses
    - [ ] Implement `show()` method for order retrieval
    - [ ] Implement `index()` method for order listing with pagination
    - [ ] Implement `update()` method for order modifications
    - [ ] Implement `destroy()` method for order cancellation
    - [ ] Add proper HTTP status codes (200, 201, 400, 404, 422, 500)
    - [ ] Implement consistent JSON response format

- [ ] **API Resource Classes**:
    - [ ] Create `OrderResource` (`php artisan make:resource OrderResource`)
    - [ ] Create `OrderCollection` for paginated responses
    - [ ] Create `CakeFlavorResource` for flavor listings
    - [ ] Define transformation logic for clean API responses

### 2. Authentication & Authorization

- [ ] **API Authentication Setup**:
    - [ ] Configure Laravel Sanctum for API token authentication
    - [ ] Create API token generation endpoint (`POST /api/auth/login`)
    - [ ] Create API token revocation endpoint (`POST /api/auth/logout`)
    - [ ] Add middleware groups for different access levels

- [ ] **Authorization Policies**:
    - [ ] Create `OrderPolicy` (`php artisan make:policy OrderPolicy`)
    - [ ] Define policy methods: `view`, `create`, `update`, `delete`
    - [ ] Implement customer vs admin permission logic
    - [ ] Apply policies to API controller methods

- [ ] **Rate Limiting**:
    - [ ] Configure API rate limiting in `RouteServiceProvider`
    - [ ] Set different limits for authenticated vs unauthenticated users
    - [ ] Add specific limits for order creation endpoints

### 3. Data Validation & Transformation

- [ ] **API Request Classes**:
    - [ ] Create `StoreOrderRequest` (`php artisan make:request StoreOrderRequest`)
    - [ ] Create `UpdateOrderRequest` for order modifications
    - [ ] Define validation rules specific to API usage
    - [ ] Add custom validation messages for API responses

- [ ] **Data Transformation**:
    - [ ] Handle image uploads via API (base64 or multipart)
    - [ ] Implement proper date/time formatting for API
    - [ ] Add data sanitization for API inputs
    - [ ] Ensure consistent field naming conventions

### 4. MCP Server Setup

- [ ] **MCP Server Architecture**:
    - [ ] Create MCP server directory structure (`mcp-server/`)
    - [ ] Choose MCP server framework (Node.js/Python based)
    - [ ] Set up server configuration files
    - [ ] Create Docker configuration for containerization

- [ ] **MCP Tool Definitions**:
    - [ ] Define `create_cake_order` tool with parameters:
        - [ ] customer_name, email, phone (required)
        - [ ] pickup_date, pickup_time (required)
        - [ ] cake_size, cake_flavor (required)
        - [ ] message_on_cake, custom_decoration (optional)
        - [ ] allergies, eggs_ok (optional)
    - [ ] Define `get_order_status` tool with order_id parameter
    - [ ] Define `list_cake_flavors` tool (no parameters)
    - [ ] Define `list_cake_sizes` tool (no parameters)
    - [ ] Define `estimate_cake_price` tool with size/flavor parameters

- [ ] **MCP Server Implementation**:
    - [ ] Implement HTTP client for Laravel API communication
    - [ ] Add error handling and retry logic
    - [ ] Implement authentication token management
    - [ ] Add request/response logging for debugging

### 5. Supporting Endpoints

- [ ] **Cake Information API**:
    - [ ] Create `CakeFlavorController` for flavor management
    - [ ] Implement flavor listing with descriptions and pricing
    - [ ] Add cake size information with serving suggestions
    - [ ] Include allergen information for each flavor

- [ ] **Order Status & Tracking**:
    - [ ] Create status check endpoint with detailed information
    - [ ] Add order history retrieval
    - [ ] Implement order modification tracking
    - [ ] Add estimated completion time calculations

### 6. Error Handling & Responses

- [ ] **Global Error Handling**:
    - [ ] Create API exception handler
    - [ ] Define standard error response format
    - [ ] Implement validation error formatting
    - [ ] Add error logging for API failures

- [ ] **Response Standards**:
    - [ ] Define consistent JSON response structure
    - [ ] Implement success/error response helpers
    - [ ] Add metadata to responses (timestamps, request_id)
    - [ ] Include helpful error messages for MCP clients

### 7. Documentation & Testing

- [ ] **API Documentation**:
    - [ ] Set up OpenAPI/Swagger documentation
    - [ ] Document all endpoints with examples
    - [ ] Include authentication instructions
    - [ ] Add MCP tool usage examples

- [ ] **MCP Documentation**:
    - [ ] Create MCP server setup instructions
    - [ ] Document tool definitions and parameters
    - [ ] Add integration examples for popular AI assistants
    - [ ] Include troubleshooting guide

- [ ] **Testing**:
    - [ ] Create API feature tests (`tests/Feature/Api/`)
    - [ ] Test order creation flow end-to-end
    - [ ] Test authentication and authorization
    - [ ] Test error scenarios and edge cases
    - [ ] Create MCP server integration tests

### 8. Deployment & Configuration

- [ ] **Environment Configuration**:
    - [ ] Add API-specific environment variables
    - [ ] Configure CORS settings for API access
    - [ ] Set up separate API subdomain if needed
    - [ ] Configure rate limiting and throttling

- [ ] **MCP Server Deployment**:
    - [ ] Create production Docker images
    - [ ] Set up health check endpoints
    - [ ] Configure logging and monitoring
    - [ ] Add SSL/TLS certificate management

### 9. Security Considerations

- [ ] **API Security**:
    - [ ] Implement request signing/verification
    - [ ] Add input sanitization for all endpoints
    - [ ] Configure proper CORS headers
    - [ ] Add API versioning strategy

- [ ] **MCP Security**:
    - [ ] Implement secure token storage
    - [ ] Add request validation and sanitization
    - [ ] Configure network security (firewalls, etc.)
    - [ ] Implement audit logging for all actions

### 10. Integration & Testing

- [ ] **MCP Client Testing**:
    - [ ] Test with Claude/ChatGPT MCP clients
    - [ ] Verify tool calling functionality
    - [ ] Test error handling and edge cases
    - [ ] Validate response formatting

- [ ] **End-to-End Integration**:
    - [ ] Test complete order flow via MCP
    - [ ] Verify SMS notifications work with API orders
    - [ ] Test admin panel integration with API orders
    - [ ] Validate image upload functionality

### 11. Monitoring & Maintenance

- [ ] **API Monitoring**:
    - [ ] Set up API performance monitoring
    - [ ] Add endpoint usage analytics
    - [ ] Configure error rate alerting
    - [ ] Monitor rate limiting effectiveness

- [ ] **MCP Server Monitoring**:
    - [ ] Add health check monitoring
    - [ ] Configure uptime monitoring
    - [ ] Set up log aggregation
    - [ ] Monitor resource usage and scaling needs


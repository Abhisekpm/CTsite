# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Technology Stack

This is a Laravel 9 application for a custom cake ordering bakery website with the following key components:

- **Backend**: Laravel 9 (PHP 8.0+)
- **Frontend**: Blade templates with Bootstrap 5, Alpine.js, Vue.js 3
- **Database**: MySQL/SQLite (with SQLite for testing)
- **Build Tools**: Vite for asset compilation
- **SMS Integration**: Twilio SDK and SimpleTexting webhook
- **Additional Libraries**: Laravel Breeze (auth), Toastr notifications, libphonenumber

## Core Application Architecture

### Main Business Logic
- **Custom Cake Orders**: The primary feature allowing customers to order custom cakes with specifications like size, flavor, decorations, pickup date/time
- **Menu Management**: Organized by categories (cakes, cupcakes, etc.) with admin backend for management
- **Content Management**: Blogs, galleries, testimonials, and pages manageable via admin panel
- **SMS/Email Notifications**: Order confirmations and admin notifications

### Key Models & Controllers
- `CustomOrder` - Handles custom cake order requests with image uploads
- `Menu`/`MenuCategory` - Product catalog structure  
- `OrderController` - Frontend order submission with validation and SMS/email notifications
- `Admin\OrderController` - Backend order management and status updates
- Backend controllers in `app/Http/Controllers/Backend/` for admin content management

### Directory Structure
- `app/Http/Controllers/` - Frontend controllers
- `app/Http/Controllers/Admin/` - Admin-specific controllers  
- `app/Http/Controllers/Backend/` - CMS backend controllers
- `resources/views/admin/` - Admin panel views
- `resources/views/frontend/` - Customer-facing views
- `public/assets/` - Images and static assets organized by type (menu, gallery, testimonials, etc.)

## Development Commands

### Essential Commands
```bash
# Start development server
php artisan serve

# Asset compilation and watching
npm run dev          # Development with file watching
npm run build        # Production build

# Database operations
php artisan migrate          # Run migrations
php artisan migrate:fresh    # Fresh migration (drops all tables)
php artisan db:seed         # Run seeders

# Testing
./vendor/bin/phpunit                    # Run all tests
./vendor/bin/phpunit tests/Feature/     # Feature tests only
./vendor/bin/phpunit tests/Unit/        # Unit tests only
./run_custom_order_tests.sh            # Custom comprehensive test suite
```

### Custom Test Runner
The project includes a comprehensive test runner script (`run_custom_order_tests.sh`) with options:
- `./run_custom_order_tests.sh all` - All tests (default)
- `./run_custom_order_tests.sh feature` - Feature tests only
- `./run_custom_order_tests.sh unit` - Unit tests only  
- `./run_custom_order_tests.sh performance` - Performance tests
- `./run_custom_order_tests.sh quick` - Quick tests (excludes performance)

### Cache & Configuration
```bash
# Clear application caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

# Generate application key (for new installations)
php artisan key:generate
```

## Key Features & Workflows

### Custom Order Processing
1. Customer submits order via `OrderController@create` form
2. Validation includes phone number formatting with libphonenumber
3. Images stored in `storage/app/public/custom_orders/`
4. Email confirmations sent to customer and admin
5. SMS notifications via Twilio integration
6. Admin manages orders through `Admin\OrderController`

### Menu & Content Management
- Admin backend at `/admin/` routes for managing menus, categories, blogs, galleries
- Image uploads handled for menu items, gallery images, testimonials
- Content organized by categories with slug-based routing

### Testing Configuration
- Uses SQLite in-memory database for testing (`DB_CONNECTION=testing`)
- Test environment configured in `phpunit.xml`
- Factory patterns for test data generation
- Comprehensive feature tests for order submission and admin workflows

## Important File Locations

### Configuration
- `.env` - Environment configuration
- `config/` - Laravel configuration files
- `routes/web.php` - Application routes

### Key Views
- `resources/views/custom_order.blade.php` - Order submission form
- `resources/views/admin/orders/` - Admin order management
- `resources/views/frontend/layout/main.blade.php` - Main layout template

### Storage
- `storage/app/public/custom_orders/` - Customer uploaded images
- `public/assets/menu/` - Menu item images
- `public/assets/gallery/` - Gallery images

## Development Notes

- Phone number validation uses international format with libphonenumber
- Image uploads use Laravel's Storage facade with proper validation
- SMS functionality integrated with both Twilio and SimpleTexting services
- Admin authentication separate from customer-facing features
- Responsive design with Bootstrap 5 and custom SCSS

## Claude rules
1. First think through the problem, read the codebase for relevant files, and write a plan to todo.md.
2. The plan should have a list of todo items that you can check off as you complete them
3. Before you begin working, check in with me and I will verify the plan.
4. Then, begin working on the todo items, marking them as complete as you go.
5. Please every step of the way just give me a high level explanation of what changes you made
6. Make every task and code change you do as simple as possible. We want to avoid making any massive or complex changes. Every change should impact as little code as possible. Everything is about simplicity.
7. Finally, add a review section to the todo.md file with a summary of the changes you made and any other relevant information.
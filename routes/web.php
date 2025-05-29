<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Request;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TypeFormController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Backend\BlogsController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\Backend\CategoryController;
use App\Http\Controllers\Backend\SettingsController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Backend\GallCatController;
use App\Http\Controllers\Backend\GalleryController;
use App\Http\Controllers\Backend\MenuCategoryController;
use App\Http\Controllers\Backend\MenuController;
use App\Http\Controllers\Backend\PagesController as BackendPagesController;
use App\Http\Controllers\Backend\TestimonialController;
use App\Http\Controllers\BlogsController as ControllersBlogsController;
use App\Http\Controllers\MainHomeController;
use App\Http\Controllers\MenuController as ControllersMenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\TwilioWebhookController;

// use App\Http\Controllers\TestimonialController as ControllersTestimonialController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/** for side bar menu active */
function set_active($route)
{
    if (is_array($route)) {
        return in_array(Request::path(), $route) ? 'active' : '';
    }
    return Request::path() == $route ? 'active' : '';
}

Route::controller(MainHomeController::class)->group(function () {
    Route::get('/', 'index');
});

// Route::group(['middleware' => 'auth'], function () { // Comment out group using default auth
//     Route::get('home', function () {
//         return view('home');
//     });
//     Route::get('home', function () {
//         return view('home');
//     });
// });

Route::controller(ControllersBlogsController::class)->group(function () {
    Route::get('blogs', 'index');
    Route::get('blogs/{slug}', 'show');
    Route::post('comment/{id}', 'comment');
});

Route::controller(PagesController::class)->group(function () {
    Route::get('about-us', 'index');
    Route::get('contact-us', 'contact');
    Route::get('cakes-menu', 'cakes')->name('cakes-menu');
});

Route::controller(ControllersMenuController::class)->group(function () {
    Route::get('our-category', 'category');
});

Route::controller(ControllersMenuController::class)->group(function () {
    Route::get('our-menu', 'category');
});

// Route::controller(ControllersTestimonialController::class)->group(function () {
//     Route::get('about-us', 'index');
//     Route::get('our-menu', 'ourmenu');
//     Route::get('contact-us', 'contact');
// });

Route::controller(EmailController::class)->group(function () {
    Route::post('send-email', 'store')->name('contact.us.store');
});

// Auth::routes(); // Comment out standard auth routes

// ----------------------------- user controller -------------------------//
// Comment out routes that required default user auth
// Route::controller(UserManagementController::class)->group(function () {
//     Route::get('list/users', 'index')->middleware('auth')->name('list/users');
//     Route::post('change/password', 'changePassword')->name('change/password');
//     Route::get('view/user/edit/{id}', 'userView')->middleware('auth');
//     Route::post('user/update', 'userUpdate')->name('user/update');
//     Route::post('user/delete', 'userDelete')->name('user/delete');
// });

// ------------------------ admin panel ----------------------- //
// Keep admin routes as they are (using auth:admin)
Route::group(['middleware' => ['auth:admin'], 'prefix' => 'admin'], function () {
    Route::controller(SettingsController::class)->group(function () {
        Route::get('/setting/page', 'index')->name('setting/page');
        Route::get('/setting/contact', 'contact')->name('setting/contact');
        Route::post('/setting/contact/{id}', 'updatecontact')->name('setting/contact/update');
        Route::post('/setting/page/updatebasic/{id}', 'updatebasic');
        Route::post('/setting/page/updateseo/{id}', 'updateseo');
    });
    
    // ----------------------------- register -------------------------//
    Route::controller(RegisterController::class)->group(function () {
        Route::get('/register', 'register')->name('register');
        Route::post('/register', 'storeUser')->name('register');
    });

    Route::controller(LoginController::class)->group(function () {
        Route::post('/logout', 'logout')->name('logout');
    });
    
    // ----------------------------- user controller -------------------------//
    Route::controller(UserManagementController::class)->group(function () {
        Route::get('list/users', 'index')->name('list/users'); // Remove ->middleware('auth') as group provides auth:admin
        Route::post('change/password', 'changePassword')->name('change/password'); // Should this be admin changing user pass?
        Route::get('view/user/edit/{id}', 'userView')->name('view/user/edit'); // Remove ->middleware('auth')
        Route::post('user/update', 'userUpdate')->name('user/update');
        Route::post('user/delete', 'userDelete')->name('user/delete');
    });

    Route::controller(CategoryController::class)->group(function () {
        Route::get('/categories', 'index')->name('category');
        Route::get('/category/create', 'create')->name('category/create');
        Route::get('/category/edit/{id}', 'edit')->name('category/edit');
        Route::post('/category/update', 'update')->name('category/update');
        Route::post('/category/store', 'store')->name('category/store');
        Route::post('/category/delete', 'destroy')->name('category/delete');
    });

    Route::controller(MenuController::class)->group(function () {
        Route::get('/menu', 'index')->name('menu');
        Route::get('/menu/create', 'create')->name('menu/create');
        Route::get('/menu/edit/{id}', 'edit')->name('menu/edit');
        Route::post('/menu/update/{id}', 'update')->name('menu/update');
        Route::post('/menu/store', 'store')->name('menu/store');
        Route::post('/menu/delete', 'destroy')->name('menu/delete');
    });

    Route::controller(MenuCategoryController::class)->group(function () {
        Route::get('/menu/category', 'index')->name('menu-category');
        Route::get('/menu/category/create', 'create')->name('menu/category/create');
        Route::get('/menu/category/edit/{id}', 'edit')->name('menu/category/edit');
        Route::post('/menu/category/update/{id}', 'update')->name('menu/category/update');
        Route::post('/menu/category/store', 'store')->name('menu/category/store');
        Route::post('/menu/category/delete', 'destroy')->name('menu/category/delete');
    });

    Route::controller(BlogsController::class)->group(function () {
        Route::get('/blogs', 'index')->name('blogs');
        Route::get('/blog/create', 'create')->name('blog/create');
        Route::get('/blog/edit/{id}', 'edit')->name('blog/edit');
        Route::post('/blog/update/{id}', 'update')->name('blog/update');
        Route::post('/blog/store', 'store')->name('blog/store');
        Route::post('/blog/delete', 'destroy')->name('blog/delete');

        Route::get('/comments', 'comments')->name('comments');
        Route::post('/comment/delete', 'commentDelete')->name('comment/delete');
        Route::post('/comment/update/{id}', 'commentUpdate')->name('comment/update');
    });

    Route::controller(HomeController::class)->group(function () {
        Route::get('/', 'index')->name('admin.home');
        Route::get('user/profile/page', 'userProfile')->name('user/profile/page');
        Route::get('teacher/dashboard', 'teacherDashboardIndex')->name('teacher/dashboard');
        Route::get('student/dashboard', 'studentDashboardIndex')->name('student/dashboard');
    });

    Route::controller(BackendPagesController::class)->group(function () {
        Route::get('pages', 'index')->name('pages');
        Route::get('pages/create', 'create')->name('pages/create');
        Route::post('pages/store', 'store')->name('pages/store');
        Route::get('page/edit/{id}', 'edit')->name('pages/edit');
        Route::post('page/update', 'update')->name('page/update');
        Route::post('page/delete', 'destroy')->name('page/delete');
    });

    Route::controller(TestimonialController::class)->group(function () {
        Route::get('testimonials', 'index')->name('testimonials');
        Route::get('testimonials/create', 'create')->name('testimonials/create');
        Route::post('testimonials/store', 'store')->name('testimonials/store');
        Route::get('testimonial/edit/{id}', 'edit')->name('testimonial/edit');
        Route::post('testimonial/update', 'update')->name('testimonial/update');
        Route::post('testimonial/delete', 'destroy')->name('testimonial/delete');
    });

    Route::controller(GalleryController::class)->group(function () {
        Route::get('collection', 'index')->name('collection');
        Route::get('collection/create', 'create')->name('collection/create');
        Route::post('collection/store', 'store')->name('collection/store');
        Route::get('collection/edit/{id}', 'edit')->name('collection/edit');
        Route::post('collection/update/{id}', 'update'); // Keep middleware if specifically needed beyond group?
        Route::post('collection/delete', 'destroy')->name('collection/delete');
    });

    Route::controller(GallCatController::class)->group(function () {
        Route::get('ccat', 'index')->name('ccat');
        Route::get('ccat/create', 'create')->name('ccat/create');
        Route::post('ccat/store', 'store')->name('ccat/store');
        Route::get('ccat/edit/{id}', 'edit')->name('ccat/edit');
        Route::post('ccat/update/{id}', 'update'); // Keep middleware if specifically needed beyond group?
        Route::post('ccat/delete', 'destroy')->name('ccat/delete');
    });

    // Custom Cake Order Admin Routes
    Route::controller(AdminOrderController::class)->prefix('orders')->name('admin.orders.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create'); // If you need a create page for orders in admin
        Route::post('/', 'store')->name('store');       // If you need to store orders from admin
        Route::get('/{order}', 'show')->name('show');
        Route::get('/{order}/edit', 'edit')->name('edit'); // If you have an edit view
        Route::put('/{order}', 'update')->name('update');
        Route::delete('/{order}', 'destroy')->name('destroy'); // If you need delete functionality
        Route::patch('/{order}/price', 'updatePrice')->name('updatePrice');
        Route::patch('/{order}/confirm', 'confirm')->name('confirm');
        Route::patch('/{order}/cancel', 'cancel')->name('cancel');
        Route::post('/{order}/pickup-reminder', 'sendPickupReminder')->name('sendPickupReminder');
        
        // Route for printing today's dispatch
        Route::get('/print/todays-dispatch', 'printTodaysDispatch')->name('printTodaysDispatch');
        // Route for printing dispatch for a selected date
        Route::get('/print-dispatch/{date}', 'printDispatchForDate')->name('printDispatchForDate')->where('date', '[0-9]{4}-[0-9]{2}-[0-9]{2}');
    });
});

Route::controller(CollectionController::class)->group(function () {
    Route::get('album', 'index');
    Route::get('album/{slug}', 'show');
});

// Add Custom Order Routes
Route::controller(OrderController::class)->group(function () {
    Route::get('custom-cake-order', 'create')->name('custom-order.create');
    Route::post('custom-cake-order', 'store')->name('custom-order.store');
});

Route::controller(ControllersMenuController::class)->group(function () {
    Route::get('{slug}', 'show')->name('single-menu');
});

// Admin Login routes (Keep these)
Route::group(['prefix' => 'admin'], function () {
    Route::controller(LoginController::class)->group(function () {
        Route::get('/login', 'login')->name('admin.login');
        Route::post('/login', 'authenticate')->name('admin.login.post');
    });
});

// --- Twilio Webhook Route --- 
// Note: This route is outside the default 'web' middleware group by default
// which usually includes CSRF protection. If placing inside `web` group,
// ensure CSRF is excluded for this route in App/Http/Middleware/VerifyCsrfToken.php
Route::post('/webhooks/twilio/sms', [TwilioWebhookController::class, 'handle'])->name('webhooks.twilio.sms');

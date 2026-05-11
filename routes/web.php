<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ManagementController;
use App\Http\Controllers\Admin\SubManageMentController;
use App\Http\Controllers\Frontend\Auth\UserAuthController;
use App\Http\Controllers\Admin\ModuleController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Frontend\UserPageController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\AiChatController;


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

// Route::get('/', function () {
//     return view('welcome');
// });

Route::prefix('admin')->group(function () {

    // Login Routes
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [LoginController::class, 'login'])->name('admin.login.submit');
    Route::get('/logout', [LoginController::class, 'logout'])->name('admin.logout');

    // =======================
    // OTP ROUTES (NEW)
    // =======================
    Route::get('/otp', [LoginController::class, 'showOtpForm'])->name('admin.otp.form');
    Route::post('/otp', [LoginController::class, 'verifyOtp'])->name('admin.otp.verify');
    Route::post('/otp/resend', [LoginController::class, 'resendOtp'])->name('admin.otp.resend');

    // Protected Routes
    Route::middleware(['admin.auth'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/ckeditor', [DashboardController::class, 'ckeditor'])->name('ckeditor');
        Route::get('/markdown', [DashboardController::class, 'markdown'])->name('markdown');
        Route::resource('management', ManagementController::class);
        Route::resource('users', UserController::class);
        // Route::resource('settings', SettingController::class);
        Route::get('/settings', [SettingController::class, 'index'])
            ->name('settings.index');

        Route::post('/settings/update', [SettingController::class, 'update'])
            ->name('settings.update');
        Route::resource('submanagement', SubManageMentController::class);
        Route::resource('notification', NotificationController::class);
        // web.php

        Route::post('profile-update', [DashboardController::class, 'updateProfile'])
            ->name('admin.profile.update');
        // ROUTE
        Route::post('avatar-update', [DashboardController::class, 'avatarUpdate'])
            ->name('admin.avatar.update');
        Route::post('/admin/ai-chat/send', [AiChatController::class, 'send'])
            ->name('admin.ai.chat.send');


        Route::prefix('module/{sub_slug}')->group(function () {

            Route::resource('/', ModuleController::class)
                ->names([
                    'index' => 'module.index',
                    'create' => 'module.create',
                    'store' => 'module.store',
                    'show' => 'module.show',
                    'edit' => 'module.edit',
                    'update' => 'module.update',
                    'destroy' => 'module.destroy',
                ])
                ->parameters(['' => 'id']); // 👈 CHANGE HERE

        });


        // AJAX ROUTES (IMPORTANT)
        Route::get('get-submanagement/{id}', [ModuleController::class, 'getSubManagement']);
        Route::post('get-modules', [ModuleController::class, 'getModules']);
        Route::get('module', [ModuleController::class, 'indexPage'])->name('module.main');

        Route::post('/upload-image', [ModuleController::class, 'uploadImage'])->name('upload.image');
    });
});



Route::prefix('user')->group(function () {

    Route::get('/login', [UserAuthController::class, 'showLogin'])->name('user.login');
    Route::post('/login', [UserAuthController::class, 'login'])->name('user.login.submit');

    Route::get('/register', [UserAuthController::class, 'showRegister'])->name('user.register');
    Route::post('/register', [UserAuthController::class, 'register'])->name('user.register.submit');

    Route::get('/logout', [UserAuthController::class, 'logout'])->name('user.logout');
    // ✅ About Page Route (NEW)
    Route::get('/about', [UserPageController::class, 'about'])->name('user.about');
    Route::get('/terms', [UserPageController::class, 'terms'])->name('user.terms');
    Route::get('/privacy', [UserPageController::class, 'privacy'])->name('user.privacy');

    Route::middleware(['user.auth'])->group(function () {
        Route::get('/dashboard', function () {
            return "User Dashboard";
        })->name('user.dashboard');
    });
});

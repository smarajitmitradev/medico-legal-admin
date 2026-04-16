<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ManagementController;
use App\Http\Controllers\Admin\SubManageMentController;
use App\Http\Controllers\Frontend\Auth\UserAuthController;
use App\Http\Controllers\Admin\ModuleController;
use App\Http\Controllers\Admin\NotificationController;

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

    // Protected Routes
    Route::middleware(['admin.auth'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/ckeditor', [DashboardController::class, 'ckeditor'])->name('ckeditor');
        Route::get('/markdown', [DashboardController::class, 'markdown'])->name('markdown');
        Route::resource('management', ManagementController::class);
        Route::resource('submanagement', SubManageMentController::class);
        Route::resource('notification', NotificationController::class);


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

    Route::middleware(['user.auth'])->group(function () {
        Route::get('/dashboard', function () {
            return "User Dashboard";
        })->name('user.dashboard');
    });
});

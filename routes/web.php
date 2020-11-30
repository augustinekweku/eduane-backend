<?php

use App\Http\Middleware\adminCheck;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;

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

Route::prefix('app')->middleware(adminCheck::class)->group(function(){
    Route::post('/create_role', [AdminController::class, 'createRole']);
    Route::get('/get_roles', [AdminController::class, 'getRoles']);
    Route::post('/edit_role', [AdminController::class, 'editRole']);
    Route::post('/delete_role', [AdminController::class, 'deleteRole']);
    Route::get('/get_users', [AdminController::class, 'getUsers']);
    Route::post('/create_user', [AdminController::class, 'createUser']);
    Route::post('/edit_user', [AdminController::class, 'editUser']);
    Route::post('/delete_user', [AdminController::class, 'deleteUser']);
    Route::post('/admin_login', [AdminController::class, 'adminLogin']);
    Route::post('/assign_roles', [AdminController::class, 'assignRole']);
    Route::post('/upload', [AdminController::class, 'upload']);
    Route::post('/delete_image', [AdminController::class, 'deleteImage']);
    Route::post('/create_plan', [AdminController::class, 'createPlan']);
    Route::get('/get_plans', [AdminController::class, 'getPlans']);



});

Route::get('/logout',[AdminController::class, 'logout']);
Route::get('/', [AdminController::class, 'index']);
Route::any('{slug}', [AdminController::class, 'index'])->where('slug', '([A-z\d\-\/_.]+)?');

// Route::get('/', function () {
//     return view('welcome');
// });

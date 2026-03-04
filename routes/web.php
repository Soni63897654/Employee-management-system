<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\EmployeeController;


Route::get('/', [EmployeeController::class, 'index'])->name('employees.index');
Route::get('/employees/fetch', [EmployeeController::class, 'fetchEmployees'])->name('employees.fetch');

Route::post('/employees/store', [EmployeeController::class, 'store'])->name('employees.store');
Route::post('/employees/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
Route::post('/employees/update', [EmployeeController::class, 'update'])->name('employees.update');
Route::post('/employees/delete', [EmployeeController::class, 'destroy'])->name('employees.destroy');
Route::post('/employees/profile', [EmployeeController::class, 'show'])->name('employees.show');
Route::get('/get-managers-by-dept', [EmployeeController::class, 'getManagersByDept'])->name('get.managers.by.dept');

Route::prefix('managers')->group(function () {
    Route::get('/', [ManagerController::class, 'index'])->name('managers.index');
    Route::get('/fetch', [ManagerController::class, 'fetchManagers'])->name('managers.fetch');
    Route::post('/store', [ManagerController::class, 'store'])->name('managers.store');
    Route::post('/edit', [ManagerController::class, 'edit'])->name('managers.edit');
    Route::post('/update', [ManagerController::class, 'update'])->name('managers.update');
    Route::post('/destroy', [ManagerController::class, 'destroy'])->name('managers.destroy');
});
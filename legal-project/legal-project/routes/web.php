<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB; 
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ConsultationsController; 

// ====================================================
// 1. روابط تسجيل الدخول والإنشاء (المتاحة للجميع بدون حماية)
// ====================================================
Route::get('/register', [RegisterController::class, 'showRegisterForm']);
Route::post('/register', [RegisterController::class, 'register']);

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


// ====================================================
// 2. الروابط المحمية بالـ Auth وبوابات الصلاحيات الأمنية
// ====================================================
Route::middleware(['auth'])->group(function () {

    // 👑 [مجموعة المدير القانوني] - حصرية لـ role_id = 1 فقط
    Route::middleware(['role:1'])->group(function () {
        Route::get('/manager/dashboard', [ConsultationsController::class, 'managerDashboard'])->name('manager.dashboard');
        Route::get('/manager/tasks', [ConsultationsController::class, 'managerTasks'])->name('manager.tasks');
        
        // 🌟 تم تعديل هذا الراوت ليتطابق بالملي مع الأكشن المولد من الـ JavaScript في صفحة الإسناد ومع دالة الكنترولر المحدثة
        Route::put('/legal-manager/consultations/assign/{id}', [ConsultationsController::class, 'storeTask'])->name('manager.tasks.store');
        
        Route::get('/manager/records', [ConsultationsController::class, 'managerRecords'])->name('manager.records');
        
        // مسار جدول مراجعة واعتماد الاستشارات والـ Modals داخل نفس الصفحة للمدير
        Route::get('/manager/consultations', [ConsultationsController::class, 'managerIncomingConsultations'])->name('manager.consultations.incoming');
        Route::post('/manager/consultations/{id}/approve', [ConsultationsController::class, 'approveConsultation'])->name('manager.consultations.approve');
    });

    // 📊 [مجموعة الموظف القانوني] - حصرية لـ role_id = 2 فقط
    Route::middleware(['role:2'])->group(function () {
        Route::get('/employee/dashboard', [ConsultationsController::class, 'employeeDashboard'])->name('employee.dashboard');
        Route::get('/employee/consultations', [ConsultationsController::class, 'employeeIncomingConsultations'])->name('legal.consultations.index');
        
        // 🌟 الحل الهندسي الصحيح: تم تعديل اسم الدالة هنا من reply إلى submitResponse لتطابق الكنترولر الأصلي بالملي
        Route::post('/consultations/{id}/reply', [ConsultationsController::class, 'submitResponse'])->name('consultations.reply');
        
        Route::get('/employee/records', [ConsultationsController::class, 'employeeRecord'])->name('legal.employee.record');
        Route::get('/employee/tasks', [ConsultationsController::class, 'employeeTasks'])->name('employee.tasks');
        Route::put('/employee/tasks/{id}/complete', [ConsultationsController::class, 'completeTask'])->name('employee.tasks.complete');
    });

    // 🏢 [مجموعة المستخدم / الموظف الداخلي] - حصرية لـ role_id = 3 فقط
    Route::middleware(['role:3'])->group(function () {
        Route::get('/internal/dashboard', [ConsultationsController::class, 'internalDashboard'])->name('internal.dashboard');
        Route::get('/consultations/create', function() { return view('Consultations.userPage.RequestConsultation'); })->name('internal.consultations.create');
        Route::post('/consultations', [ConsultationsController::class, 'store'])->name('consultations.store');
        Route::get('/consultations/{id}/edit', [ConsultationsController::class, 'edit'])->name('internal.consultations.edit');
        Route::put('/consultations/{id}', [ConsultationsController::class, 'update'])->name('consultations.update');
        Route::delete('/consultations/{id}', [ConsultationsController::class, 'destroy'])->name('consultations.destroy');
        Route::get('/internal/orders-records', [ConsultationsController::class, 'internalRecords'])->name('internal.orders.records');
    });

    // 💻 [مجموعة تقنية المعلومات] - حصرية لـ role_id = 4
    Route::get('/it/dashboard', function () { return view('Consultations.itPage.InterfacesIt'); })->name('it.dashboard')->middleware('role:4');

    // 📈 [مجموعة الإدارة العليا] - حصرية لـ role_id = 5
    Route::get('/top-management/dashboard', function () { return view('Consultations.topManagementPage.InterfacesTopManagement'); })->name('top-management.dashboard')->middleware('role:5');

    // --- العمليات المشتركة الآمنة (الملف الشخصي) ---
    Route::get('/profile', function () { return view('profile.profile'); })->name('profile.show');
    
    // 🛠️ تم تعديل الاسم هنا ليطابق المكتوب في صفحة البروفايل بالملي لحل الخطأ تماماً
    Route::post('/profile/update-photo', [LoginController::class, 'updatePhoto'])->name('profile.photo.update');
    
    Route::put('/profile', [LoginController::class, 'updateProfile'])->name('profile.update'); 
    Route::post('/employee/notifications/dismiss/{type}', function ($type) {
        session(['dismissed_notifications.' . $type => true]);
        return response()->json(['success' => true]);

    
    });
});
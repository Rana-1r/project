<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB; // تأكدي من وجود هذا السطر في أعلى الكنترولر

class LoginController extends Controller
{
    // 1. دالة عرض شاشة تسجيل الدخول حقك
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // 2. دالة استقبال البيانات والتحقق من الحساب والتوجيه حسب الدور
    public function login(Request $request)
    {
        // التحقق من الحقول قادمة من شاشة الدخول حقتك
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        // محاولة تسجيل الدخول (لارافيل يفحص الإيميل والباسورد المشفر تلقائياً)
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            // تجديد الجلسة للحماية الأكواد
            $request->session()->regenerate();

            // 👈 جلب الـ role_id الخاص بالمستخدم الحالي الذي سجل دخوله للتو
            $role = Auth::user()->role_id;

            // 👈 التوجيه القسري والمباشر حسب الدور الوظيفي الخماسي فوراً بعد النجاح
            switch ($role) {
                case 1:
                    return redirect('/manager/dashboard');        // مدير الإدارة القانونية
                case 2:
                    return redirect('/employee/dashboard');       // الموظف القانوني
                case 3:
                    return redirect('/internal/dashboard');       // موظف داخلي
                case 4:
                    return redirect('/it/dashboard');             // تقنية المعلومات
                case 5:
                    return redirect('/top-management/dashboard');   // الإدارة العليا
                default:
                    // إذا لم يتطابق مع أي دور، يتم توجيهه للرابط الافتراضي الآمن أو تسجيل الخروج
                    return redirect()->intended('/dashboard');
            }
        }

        // إذا كانت البيانات خطأ، يرجعه مع رسالة تنبيه
        return back()->withErrors([
            'email' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة.',
        ])->onlyInput('email');
    }

    // 3. دالة تسجيل الخروج (Logout) للاحتياط مستقبلاً
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    // دالة تحديث الصورة الشخصية للمستخدم
public function updatePhoto(Request $request)
{
    $request->validate([
        'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    $user = Auth::user();

    // حذف الصورة القديمة من السيرفر إذا كانت موجودة للاستغلال الأمثل للمساحة
    if ($user->photo) {
        Storage::disk('public')->delete($user->photo);
    }

    // حفظ الصورة الجديدة داخل مجلد storage/app/public/profile_photos
    $path = $request->file('photo')->store('profile_photos', 'public');

    // تحديث مسار الصورة في جدول المستخدمين بقاعدة البيانات
    // ملاحظة: تأكدي أن موديل User يحتوي على حقل photo في الـ fillable
    $user->update([
        'photo' => $path
    ]);

    return back()->with('success', 'تم تحديث الصورة الشخصية بنجاح!');
}

// دالة تحديث البروفايل (الاسم، الإيميل، الهاتف) للمستخدم
public function updateProfile(Request $request)
{
    // 1. التحقق من البيانات المرسلة
    $request->validate([
        'name'  => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . auth()->id() . ',user_id',
        'phone' => 'nullable|string|max:15',
    ]);

    // 2. تحديث بيانات المستخدم الحالي في قاعدة البيانات مباشرة بالملي
    DB::table('users')
        ->where('user_id', auth()->id())
        ->update([
            'name'       => $request->name,
            'email'      => $request->email,
            'phone'      => $request->phone,
            'updated_at' => now(),
        ]);

    // 3. العودة للملف الشخصي مع رسالة نجاح خضراء
    return back()->with('success', 'تم تحديث بيانات ملفك الشخصي بنجاح!');
}

}
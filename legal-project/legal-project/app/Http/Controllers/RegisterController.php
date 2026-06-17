<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; 
use Illuminate\Support\Facades\Hash; 
use Illuminate\Support\Facades\Auth; 

class RegisterController extends Controller
{
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        // 1. التحقق من الحقول مع تحديد طول كلمة المرور (بين 8 إلى 10 خانات)
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 
                'string', 
                'email', 
                'max:255', 
                'unique:users,email',
                'regex:/^[a-zA-Z0-9._%+-]+@wadimakkah\.sa$/'
            ],
            'password' => ['required', 'string', 'min:8', 'max:10', 'confirmed'], //يجب ان تكون كلمة المرور بين 8 إلى 10 خانات و تتطابق مع حقل التأكيد
            'role_id' => ['required', 'integer'],
        ], [
            'name.required' => 'حقل الاسم الكامل مطلوب.',
            'email.required' => 'حقل البريد الإلكتروني مطلوب.',
            'email.email' => 'يرجى إدخال بريد إلكتروني بشكل صحيح.',
            'email.regex' => 'الإيميل يجب أن ينتهي بـ @wadimakkah.sa',
            'email.unique' => 'هذا الإيميل مستخدم مسبقاً',
            'password.required' => 'حقل كلمة المرور مطلوب.',
            'password.min' => 'كلمة المرور يجب ألا تقل عن 8 خانات.',
            'password.max' => 'كلمة المرور يجب ألا تزيد عن 10 خانات.',
            'password.confirmed' => 'كلمة المرور غير مطابقة لحقل التأكيد.',
            'role_id.required' => 'يرجى اختيار الدور الوظيفي لتسجيل الحساب.',
        ]);

        // 2. إنشاء الحساب
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
        ]);

        // 3. التحويل لصفحة الـ login
        return redirect('/login')->with('success', 'تم إنشاء الحساب بنجاح! يمكنك الآن تسجيل الدخول.');
    }
}
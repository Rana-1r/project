<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth; // 🔐 هذا هو السطر السحري الناقص الذي تم إضافته لتأمين الملف

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // 1. التحقق من أن المستخدم مسجل دخول
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // 2. التحقق من أن دور المستخدم (role_id) موجود ضمن الأدوار المسموح لها بالدخول
        if (in_array($user->role_id, $roles)) {
            return $next($request);
        }

        // 3. إذا حاول موظف (Role 2) دخول صفحة مدير (Role 1)، يتم إرجاعه للوحة تحكمه مع رسالة تنبيه
        if ($user->role_id == 2) {
            return redirect()->route('employee.dashboard')->with('error', 'عذراً، لا تملك صلاحية لدخول شاشات الإدارة القانونية.');
        }

        // للموظف الداخلي (Role 3)
        if ($user->role_id == 3) {
            return redirect()->route('internal.dashboard')->with('error', 'عذراً، لا تملك صلاحية لدخول هذه الصفحة.');
        }

        return redirect('/')->with('error', 'صلاحية غير مصرح بها.');
    }
}
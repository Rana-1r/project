<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل جديد - منصة الإدارة القانونية</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Cairo', sans-serif;
        }
        .custom-bg {
            background: linear-gradient(180deg, #f4fbff 0%, #d9f1ff 50%, #9ed9f3 100%);
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-4 md:p-6 custom-bg overflow-hidden">

    <div class="flex flex-col md:flex-row items-center justify-center w-full max-w-6xl gap-6 md:gap-16">

        <div class="bg-white/90 backdrop-blur-sm p-4 md:p-5 rounded-[28px] shadow-2xl w-full max-w-[360px] md:max-w-[420px]">

            <img src="{{ asset('images/Wadi Makkah Logo.png') }}" class="w-24 mx-auto block mb-2" alt="Logo">

            <h2 class="text-md font-bold text-[#1a1a1a] text-center mb-3">
                منصة الإدارة القانونية
            </h2>

            <form action="{{ url('/register') }}" method="POST" class="space-y-2 text-right">
                @csrf

                <div>
                    <label class="text-[#8e8e8e] text-[11px] mb-0.5 block mr-2">الاسم الكامل</label>
                    <input
                        type="text"
                        name="name" placeholder="الاسم الكامل"
                        value="{{ old('name') }}"
                        required
                        class="w-full bg-[#eeeeee] rounded-xl py-2 px-4 text-sm outline-none focus:ring-2 focus:ring-blue-400 transition @error('name') border-2 border-red-500 @enderror"
                    >
                    @error('name')
                        <span class="text-red-500 text-xs mr-2 mt-1 block font-semibold">⚠️ {{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="text-[#8e8e8e] text-[11px] mb-0.5 block mr-2">البريد الإلكتروني</label>
                    <input
                        type="email"
                        name="email"
                        placeholder="example123@wadimakkah.sa"
                        value="{{ old('email') }}"
                        required
                        class="w-full bg-[#eeeeee] rounded-xl py-2 px-4 text-sm outline-none focus:ring-2 focus:ring-blue-400 transition @error('email') border-2 border-red-500 @enderror"
                    >
                    @error('email')
                        <span class="text-red-500 text-xs mr-2 mt-1 block font-semibold">⚠️ {{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="text-[#8e8e8e] text-[11px] mb-0.5 block mr-2">كلمة المرور</label>
                    <input
                        type="password"
                        name="password"
                        placeholder="********"
                        required
                        class="w-full bg-[#eeeeee] rounded-xl py-2 px-4 text-sm outline-none focus:ring-2 focus:ring-blue-400 transition @error('password') border-2 border-red-500 @enderror"
                    >
                    <span class="text-gray-500 text-[10px] mr-2 mt-0.5 block">يجب أن تتكون كلمة المرور من 8 إلى 10 خانات فقط.</span>
                    
                    @error('password')
                        <span class="text-red-500 text-xs mr-2 mt-1 block font-semibold">⚠️ {{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="text-[#8e8e8e] text-[11px] mb-0.5 block mr-2">تأكيد كلمة المرور</label>
                    <input
                        type="password"
                        name="password_confirmation"
                        placeholder="********"
                        required
                        class="w-full bg-[#eeeeee] rounded-xl py-2 px-4 text-sm outline-none focus:ring-2 focus:ring-blue-400 transition"
                    >
                </div>

                <div>
                    <label class="text-[#8e8e8e] text-[11px] mb-0.5 block mr-2">الدور الوظيفي</label>
                    <div class="relative">
                        <select name="role_id" required
                        class="w-full bg-[#eeeeee] rounded-xl py-2 px-4 text-sm outline-none appearance-none focus:ring-2 focus:ring-blue-400 transition @error('role_id') border-2 border-red-500 @enderror">
                            <option value="" disabled selected>اختر الدور الوظيفي</option>
                            // يتم جلب الأدوار من قاعدة البيانات وعرضها في القائمة المنسدلة
                            @foreach(App\Models\Role::all() as $role)
                                <option value="{{ $role->role_id }}" {{ old('role_id') == $role->role_id ? 'selected' : '' }}>
                                    @if($role->role_name == 'manager') مدير الإدارة القانونية
                                    @elseif($role->role_name == 'employee') الموظف القانوني
                                    @elseif($role->role_name == 'internal_employee') موظف داخلي
                                    @elseif($role->role_name == 'it') تقنية المعلومات
                                    @elseif($role->role_name == 'top_management') الإدارة العليا
                                    @else {{ $role->role_name }}
                                    @endif
                                </option>
                            @endforeach
                        </select>

                        <div class="pointer-events-none absolute inset-y-0 left-3 flex items-center">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                    @error('role_id')
                        <span class="text-red-500 text-xs mr-2 mt-1 block font-semibold">⚠️ {{ $message }}</span>
                    @enderror
                </div>

                <div class="pt-2">
                    <button
                        type="submit"
                        class="w-full bg-[#4c5df4] hover:bg-[#3c4dd4] text-white font-bold py-2.5 rounded-xl text-sm transition-all shadow-lg active:scale-95"
                    >
                        إنشاء الحساب
                    </button>
                </div>
                
                <p class="text-center text-[11px] text-gray-500 mt-2">لديك حساب بالفعل؟ <a href="{{ url('/login') }}" class="text-blue-600 hover:underline font-semibold">سجل الدخول</a></p>
            </form>
        </div>

        <div class="flex flex-col items-center justify-center text-center flex-1 order-1 md:order-2 md:mr-10 hidden md:flex">
            <img src="{{ asset('images/Wadi Makkah Logo.png') }}" class="w-[260px] md:w-[320px] mb-8" alt="Logo">
            <h1 class="text-2xl md:text-4xl font-extrabold text-[#1a1a1a] whitespace-nowrap">
                منصة الإدارة القانونية
            </h1>
            <p class="text-gray-600 mt-2 text-md md:text-lg">
                يرجى إنشاء حساب جديد للوصول إلى المنصة
            </p>
        </div>

    </div>
</body>
</html>
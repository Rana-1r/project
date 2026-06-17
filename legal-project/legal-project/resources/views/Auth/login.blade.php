<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - منصة الإدارة القانونية</title>

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

        {{-- كارد تسجيل الدخول - متطابق تماماً مع أبعاد كارد التسجيل --}}
        <div class="bg-white/90 backdrop-blur-sm p-4 md:p-5 rounded-[28px] shadow-2xl w-full max-w-[360px] md:max-w-[420px]">
            
            <img src="{{ asset('images/Wadi Makkah Logo.png') }}" class="w-24 mx-auto block mb-2" alt="Logo">
            
            <h2 class="text-md font-bold text-[#1a1a1a] text-center mb-3">
                منصة الإدارة القانونية
            </h2>

            @if(session('success'))
                <div class="flex items-center gap-2.5 bg-emerald-50/80 backdrop-blur-sm border border-emerald-200/60 text-emerald-800 px-3.5 py-3 rounded-xl text-xs text-right mb-4 shadow-sm animate-fade-in">
                    <div class="bg-emerald-500 rounded-full p-1 text-white shrink-0 shadow-sm shadow-emerald-200">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"></path>
                        </svg>
                    </div>
                    <span class="font-semibold leading-relaxed">{{ session('success') }}</span>
                </div>
            @endif

            <form action="{{ url('/login') }}" method="POST" class="space-y-2 text-right">
                @csrf

                <div>
                    <label class="text-[#8e8e8e] text-[11px] mb-0.5 block mr-2">
                        البريد الإلكتروني
                    </label>

                    <input
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="example123@wadimakkah.sa"
                        required
                        class="w-full bg-[#eeeeee] rounded-xl py-2 px-4 text-sm outline-none focus:ring-2 focus:ring-blue-400 transition @error('email') border-2 border-red-500 @enderror"
                    >

                    @error('email')
                        <span class="text-red-500 text-xs mr-2 mt-1 block font-semibold">
                            ⚠️ {{ $message }}
                        </span>
                    @enderror
                </div>

                <div>
                    <label class="text-[#8e8e8e] text-[11px] mb-0.5 block mr-2">
                        كلمة المرور
                    </label>

                    <input
                        type="password"
                        name="password"
                        placeholder="********"
                        required
                        class="w-full bg-[#eeeeee] rounded-xl py-2 px-4 text-sm outline-none focus:ring-2 focus:ring-blue-400 transition"
                    >
                </div>

                <div class="flex items-center justify-start mr-2 pt-1">
                    <input
                        type="checkbox"
                        name="remember"
                        id="remember"
                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                    >

                    <label for="remember" class="mr-2 text-xs text-gray-600">
                        تذكرني على هذا الجهاز
                    </label>
                </div>

                <div class="pt-2">
                    <button
                        type="submit"
                        class="w-full bg-[#4c5df4] hover:bg-[#3c4dd4] text-white font-bold py-2.5 rounded-xl text-sm transition-all shadow-lg active:scale-95"
                    >
                        تسجيل الدخول
                    </button>
                </div>

                <p class="text-center text-[11px] text-gray-500 mt-2">
                    ليس لديك حساب؟
                    <a href="{{ url('/register') }}" class="text-blue-600 hover:underline font-semibold">
                        أنشئ حساباً جديداً
                    </a>
                </p>

            </form>

        </div>

        {{-- كتلة الشعار والنصوص الجانبية - متطابقة ومحاذية تماماً لملف الريجستر --}}
        <div class="flex flex-col items-center justify-center text-center flex-1 order-1 md:order-2 md:mr-10 hidden md:flex">

            <img
                src="{{ asset('images/Wadi Makkah Logo.png') }}"
                class="w-[260px] md:w-[320px] mb-8"
                alt="Logo"
            >

            <h1 class="text-2xl md:text-4xl font-extrabold text-[#1a1a1a] whitespace-nowrap">
                منصة الإدارة القانونية
            </h1>

            <p class="text-gray-600 mt-2 text-md md:text-lg">
                مرحباً بك مجدداً، يرجى تسجيل الدخول للوصول إلى لوحة التحكم
            </p>

        </div>

    </div>

</body>
</html>
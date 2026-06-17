<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'لوحة التحكم | منصة الإدارة القانونية')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Cairo', sans-serif; }
    </style>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        'wadimakkah-dark': '#1e3a8a',
                        'wadimakkah-light': '#60a5fa',
                        'wadimakkah-bg': '#f9fafb',
                    }
                }
            }
        }

        if (localStorage.getItem('theme') === 'dark') {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>

<body class="bg-wadimakkah-bg dark:bg-gray-900 min-h-screen flex flex-col transition-colors duration-200">

    @php
        // 🌟 جلب آخر 15 تنبيه من قاعدة البيانات لمعالجتها وتصفيتها حياً بداخل الـ JavaScript بناءً على الـ localStorage
        $dbNotifications = [];
        if (auth()->check()) {
            $dbNotifications = DB::table('user_consultations')
                ->where('user_id', auth()->user()->user_id)
                ->whereIn('status', ['تم الرد', 'معتمد', 'مقبول', 'مرفوض'])
                ->orderBy('updated_at', 'desc')
                ->take(15)
                ->get();
        }
    @endphp

    {{-- الهيدر الموحد --}}
    <header class="bg-wadimakkah-dark text-white shadow-lg flex-shrink-0">
        <div class="px-16 py-6 flex items-center justify-between flex-wrap gap-4">
            
            <img src="{{ asset('images/Wadi Makkah Logo.png') }}" class="h-20">
            
            {{-- قائمة التنقل الخاضعة للشرط القانوني --}}
            <div class="flex gap-8 text-sm font-medium">
                <a href="{{ route('internal.dashboard') }}" class="hover:text-wadimakkah-light transition text-white">الرئيسية</a>
                <a href="#" class="hover:text-wadimakkah-light transition text-white">القضايا</a>
                <a href="#" class="hover:text-wadimakkah-light transition text-white">العقود</a>
                <a href="{{ route('internal.consultations.create') }}" class="hover:text-wadimakkah-light transition text-white">الاستشارات</a>
                
                @if(auth()->check() && auth()->user()->role_id != 3)
                    <a href="{{ Route::has('employee.tasks') ? route('employee.tasks') : '#' }}" class="hover:text-wadimakkah-light transition text-white">المهام</a>
                @endif
                
                <a href="{{ route('internal.orders.records') }}" class="hover:text-wadimakkah-light transition text-white">السجل</a>
            </div>

            <div class="flex items-center gap-6 relative">
                {{-- الملف الشخصي --}}
                <a href="{{ route('profile.show') }}" class="hover:text-blue-300 transition flex items-center gap-2 group">
                    @if(Auth::check() && Auth::user()->photo)
                        <img src="{{ asset('storage/' . Auth::user()->photo) }}" class="w-8 h-8 rounded-full object-cover border border-white/40 group-hover:border-blue-300 transition shadow-sm">
                    @else
                        <i class="fas fa-user-circle text-2xl"></i>
                    @endif
                </a>
                
                {{-- 🔔 نافذة الإشعارات الحية والمطورة بنظام الآيفون المستدام المستند على الـ LocalStorage 🔔 --}}
                <div class="relative">
                    <button onclick="toggleNotificationDropdown()" id="noti-btn" class="relative hover:text-wadimakkah-light transition text-xl p-1 focus:outline-none flex items-center justify-center">
                        <i class="fas fa-bell"></i>
                        {{-- نقطة التنبيه الحمراء الذكية يتم التحكم بها حياً بواسطة الـ JS --}}
                        <span id="noti-badge" class="hidden absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full ring-2 ring-wadimakkah-dark"></span>
                    </button>
                    
                    <div id="noti-dropdown" class="hidden absolute left-0 mt-3.5 w-80 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/60 rounded-2xl shadow-xl z-50 overflow-hidden text-right text-gray-800 dark:text-gray-200 animate-fadeIn">
                        <div class="p-3 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                            <span class="font-black text-xs text-gray-700 dark:text-gray-300">التنبيهات</span>
                            <button type="button" onclick="clearAllNotifications()" class="text-[10px] text-gray-400 hover:text-red-500 font-bold transition cursor-pointer">مسح الكل</button>
                        </div>
                        
                        <div id="noti-list-container" class="max-h-64 overflow-y-auto divide-y divide-gray-50 dark:divide-gray-700/40">
                            {{-- سيتم بناء وحقن عناصر الإشعارات ديناميكياً بواسطة الجافا سكريبت --}}
                        </div>
                    </div>
                </div>

                {{-- ⚙️ نافذة الإعدادات الحديثة والمطورة --}}
                <div class="relative">
                    <button onclick="toggleSettingsDropdown()" id="settings-btn" class="hover:text-blue-300 transition text-xl p-1 focus:outline-none flex items-center justify-center">
                        <i class="fas fa-cog"></i>
                    </button>

                    <div id="settings-dropdown" class="hidden absolute left-0 mt-3.5 w-64 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl shadow-xl z-50 overflow-hidden text-right text-gray-800 dark:text-gray-200 animate-fadeIn">
                        <div class="p-3 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700">
                            <span class="font-black text-xs flex items-center gap-1.5 text-gray-700 dark:text-gray-300">
                                <i class="fas fa-sliders-h text-wadimakkah-light text-[11px]"></i> إعدادات الحساب
                            </span>
                        </div>
                        <div class="p-2 flex flex-col gap-1 text-xs font-semibold">
                            <button onclick="toggleTheme()" class="w-full flex items-center justify-between p-2.5 rounded-xl hover:bg-blue-50/40 dark:hover:bg-gray-700/40 text-gray-600 dark:text-gray-300 transition duration-150">
                                <div class="flex items-center gap-2.5">
                                    <i id="theme-icon" class="fas fa-moon text-gray-400 text-sm w-4 text-center"></i>
                                    <span id="theme-text" class="text-[11px] font-bold">المظهر الداكن</span>
                                </div>
                                <span id="theme-badge" class="bg-gray-100 dark:bg-gray-700 text-[9px] px-2 py-0.5 rounded-md text-gray-500 dark:text-gray-400 font-black">مغلق</span>
                            </button>

                            <hr class="my-1 border-gray-100/80 dark:border-gray-700/50">
                            
                            <form action="/logout" method="POST" class="m-0">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <button type="submit" class="w-full flex items-center gap-2.5 p-2.5 rounded-xl text-red-600 hover:bg-red-50 dark:hover:bg-red-950/20 transition duration-150 text-right cursor-pointer font-bold text-[11px]">
                                    <i class="fas fa-sign-out-alt text-sm w-4 text-center opacity-80"></i>تسجيل الخروج
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </header>

    {{-- المحتوى الرئيسي --}}
    @yield('content')

    {{-- الفوتر الموحد --}}
    <footer class="bg-wadimakkah-dark text-white py-12 mt-16 border-t border-gray-700 flex-shrink-0">
        <div class="container mx-auto px-6 grid grid-cols-1 md:grid-cols-4 gap-10 text-sm">
            <div>
                <h5 class="font-bold mb-4">روابط مهمة</h5>
                <ul class="space-y-2 text-gray-300">
                    <li><a href="#" class="hover:text-wadimakkah-light transition">سياسة الخصوصية</a></li>
                    <li><a href="#" class="hover:text-wadimakkah-light transition">الشروط والأحكام</a></li>
                </ul>
            </div>

            <div>
                <h5 class="font-bold mb-4">المساعدة والدعم</h5>
                <ul class="space-y-2 text-gray-300">
                    <li><a href="#" class="hover:text-wadimakkah-light transition">الدعم الفني</a></li>
                    <li><a href="#" class="hover:text-wadimakkah-light transition">تواصل معنا</a></li>
                </ul>
            </div>

            <div>
                <h5 class="font-bold mb-4">وسائل التواصل الاجتماعي</h5>
                <div class="flex gap-4 text-2xl text-gray-300">
                    <a href="#" class="hover:text-wadimakkah-light"><i class="fab fa-linkedin"></i></a>
                    <a href="#" class="hover:text-wadimakkah-light"><i class="fab fa-youtube"></i></a>
                    <a href="#" class="hover:text-wadimakkah-light"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="hover:text-wadimakkah-light"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="hover:text-wadimakkah-light"><i class="fab fa-facebook"></i></a>
                </div>
            </div>

            <div class="flex flex-col items-center text-center px-6 -mt-4">
                <img src="{{ asset('images/Wadi Makkah Logo.png') }}" alt="Wadi Makkah Logo" class="h-20 mb-4 opacity-80">
                <p class="text-xs text-gray-400">شركة وادي مكة للتقنية</p>
                <p class="text-xs text-gray-400">جميع الحقوق محفوظة @ 2026</p>
            </div>
        </div>
    </footer>

    <script>
        // مصفوفة البيانات الأساسية القادمة من الواجهة الخلفية
        const rawNotifications = @json($dbNotifications);

        // 🌟 بناء وتصفية واجهة التنبيهات باستخدام بصمات الـ localStorage المستدامة للأبد
        function renderNotificationsUI() {
            const container = document.getElementById('noti-list-container');
            const badge = document.getElementById('noti-badge');
            
            // جلب قائمة مفاتيح البصمات المحذوفة قديماً من المتصفح مباشرة
            const dismissedKeys = JSON.parse(localStorage.getItem('dismissed_notifications_keys')) || [];
            
            // تصفية المصفوفة لضم الإشعارات النشطة التي لم تُمسح بصمتها الحالية بعد
            const activeNotifications = rawNotifications.filter(noti => {
                const notiKey = noti.consultation_id + '_' + noti.status + '_' + new Date(noti.updated_at).getTime()/1000;
                return !dismissedKeys.includes(notiKey);
            });

            container.innerHTML = '';

            // أخذ أول 5 تنبيهات نشطة ومصفاة فقط لعرضها
            const displayLimitList = activeNotifications.slice(0, 5);

            if (displayLimitList.length > 0) {
                if (badge) badge.classList.remove('hidden');

                displayLimitList.forEach(noti => {
                    const notiKey = noti.consultation_id + '_' + noti.status + '_' + new Date(noti.updated_at).getTime()/1000;
                    const isApproved = ['تم الرد', 'معتمد', 'مقبول'].includes(noti.status);
                    
                    let iconStyle = isApproved 
                        ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 border-emerald-100/30' 
                        : 'bg-red-50 dark:bg-red-950/40 text-red-500 border-red-100/30';
                    let iconFas = isApproved ? 'fa-check-circle' : 'fa-times-circle';
                    let headline = isApproved ? 'تم الرد على الاستشارة من الإدارة القانونية:' : 'تم رفض طلب الاستشارة من الإدارة القانونية:';

                    const itemHtml = `
                        <div id="noti-item-${noti.consultation_id}" class="flex items-center justify-between p-3.5 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition duration-150 group animate-fadeIn">
                            <a href="{{ route('internal.orders.records') }}" class="flex gap-3 items-start flex-1 min-w-0">
                                <div class="w-7 h-7 rounded-xl flex items-center justify-center text-xs flex-shrink-0 border ${iconStyle}">
                                    <i class="fas ${iconFas}"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-[11px] font-bold text-gray-700 dark:text-gray-200 leading-snug">${headline}</p>
                                    <p class="text-[10px] text-gray-400 mt-0.5 truncate font-medium">"${noti.title}"</p>
                                </div>
                            </a>
                            <button type="button" onclick="dismissNotification(event, '${noti.consultation_id}', '${notiKey}')" class="mr-2 text-gray-400 hover:text-red-500 p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700/50 transition cursor-pointer flex items-center justify-center w-6 h-6 flex-shrink-0 opacity-0 group-hover:opacity-100 focus:opacity-100" title="إخفاء هذا التنبيه">
                                <i class="fas fa-times text-xs"></i>
                            </button>
                        </div>
                    `;
                    container.innerHTML += itemHtml;
                });
            } else {
                if (badge) badge.classList.add('hidden');
                container.innerHTML = `
                    <div id="empty-noti-view" class="p-6 text-center text-gray-400 dark:text-gray-500">
                        <i class="fas fa-bell-slash text-xl block mb-1.5 opacity-60"></i>
                        <p class="text-[11px] font-semibold">لا توجد إشعارات جديدة حالياً.</p>
                    </div>
                `;
            }
        }

        // ❌ دالة الحذف الأبدي بنظام الآيفون المستند على حفظ مفتاح البصمة الفريد بذاكرة المتصفح للابد
        function dismissNotification(event, id, notiKey) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }

            let dismissedKeys = JSON.parse(localStorage.getItem('dismissed_notifications_keys')) || [];
            if (!dismissedKeys.includes(notiKey)) {
                dismissedKeys.push(notiKey);
                localStorage.setItem('dismissed_notifications_keys', JSON.stringify(dismissedKeys));
            }

            // إعادة البناء الفوري للواجهة
            renderNotificationsUI();
        }

        // دالة مسح كافة الإشعارات الحالية دفعة واحدة وبشكل دائم للأبد
        function clearAllNotifications() {
            let dismissedKeys = JSON.parse(localStorage.getItem('dismissed_notifications_keys')) || [];
            rawNotifications.forEach(noti => {
                const notiKey = noti.consultation_id + '_' + noti.status + '_' + new Date(noti.updated_at).getTime()/1000;
                if (!dismissedKeys.includes(notiKey)) {
                    dismissedKeys.push(notiKey);
                }
            });
            localStorage.setItem('dismissed_notifications_keys', JSON.stringify(dismissedKeys));
            renderNotificationsUI();
        }

        // تشغيل وفحص الإشعارات وتصفيتها تلقائياً بمجرد اكتمال تحميل عناصر الصفحة
        document.addEventListener("DOMContentLoaded", function() {
            renderNotificationsUI();
        });

        function toggleNotificationDropdown() {
            document.getElementById('settings-dropdown').classList.add('hidden');
            document.getElementById('noti-dropdown').classList.toggle('hidden');
            renderNotificationsUI();
        }

        function toggleSettingsDropdown() {
            document.getElementById('noti-dropdown').classList.add('hidden');
            document.getElementById('settings-dropdown').classList.toggle('hidden');
            updateThemeDropdownUI();
        }

        function toggleTheme() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            }
            updateThemeDropdownUI();
        }

        function updateThemeDropdownUI() {
            const isDark = document.documentElement.classList.contains('dark');
            const icon = document.getElementById('theme-icon');
            const text = document.getElementById('theme-text');
            const badge = document.getElementById('theme-badge');

            if (isDark) {
                icon.className = "fas fa-moon text-indigo-400 text-sm w-4 text-center";
                text.innerText = "المظهر الداكن";
                badge.innerText = "مفعّل";
                badge.className = "bg-green-100 text-green-700 dark:bg-green-950/50 dark:text-green-400 text-[10px] px-2 py-0.5 rounded-md font-black";
            } else {
                icon.className = "fas fa-moon text-gray-400 text-sm w-4 text-center";
                text.innerText = "المظهر الداكن";
                badge.innerText = "مغلق";
                badge.className = "bg-gray-100 text-gray-500 text-[10px] px-2 py-0.5 rounded-md font-black";
            }
        }

        window.onclick = function(event) {
            const notiDropdown = document.getElementById('noti-dropdown');
            const notiBtn = document.getElementById('noti-btn');
            const settingsDropdown = document.getElementById('settings-dropdown');
            const settingsBtn = document.getElementById('settings-btn');

            if (notiDropdown && !notiDropdown.classList.contains('hidden') && !notiBtn.contains(event.target) && !notiDropdown.contains(event.target)) {
                notiDropdown.classList.add('hidden');
            }
            if (settingsDropdown && !settingsDropdown.classList.contains('hidden') && !settingsBtn.contains(event.target) && !settingsDropdown.contains(event.target)) {
                settingsDropdown.classList.add('hidden');
            }
        }
    </script>
    @stack('scripts')
</body>
</html>
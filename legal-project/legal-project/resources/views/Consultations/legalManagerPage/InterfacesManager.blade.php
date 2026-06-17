@extends('layouts.LegalManager')

@section('title', 'لوحة التحكم | منصة الإدارة القانونية')

@section('content')
    <main class="container mx-auto px-6 py-10 flex-grow">
        
        <div class="mt-10 text-center">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">لوحة تحكم المدير القانوني</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2 mb-12 text-sm">متابعة كاملة للمهام والإشراف الشامل على الاستشارات، العقود، والقضايا الواردة</p>
        </div>

        @php
            $pendingManagerAction = 0;
            $totalConsultations = 0;
            
            if (auth()->check()) {
                // 1. حساب المعاملات التي تنتظر إجراء فعلي من المدير (إسناد أو اعتماد)
                $pendingManagerAction = DB::table('user_consultations')
                    ->whereIn('status', ['قيد المراجعة', 'بانتظار الاعتماد'])
                    ->count();

                // 2. حساب إجمالي الاستشارات القانونية الواردة بالكامل (باستثناء المسودات)
                $totalConsultations = DB::table('user_consultations')
                    ->where('status', '!=', 'مسودة')
                    ->count();
            }
        @endphp

        {{-- 🌟 كروت الإحصائيات الشاملة المطورة (4 كروت متوازنة ومكتملة البيانات) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
            
            {{-- 1. كارت القضايا --}}
            <div class="bg-blue-50 dark:bg-gray-800 border-2 border-blue-200 dark:border-gray-700 p-6 rounded-2xl shadow-sm text-center">
                <p class="text-gray-600 dark:text-gray-400 font-bold mb-2 text-xs uppercase tracking-wider">إجمالي القضايا المنظورة</p>
                <span class="text-5xl font-black text-wadimakkah-dark dark:text-wadimakkah-light font-mono">{{ $stats['total_cases'] ?? 0 }}</span>
            </div>
            
            {{-- 2. كارت العقود --}}
            <div class="bg-blue-50 dark:bg-gray-800 border-2 border-blue-200 dark:border-gray-700 p-6 rounded-2xl shadow-sm text-center">
                <p class="text-gray-600 dark:text-gray-400 font-bold mb-2 text-xs uppercase tracking-wider">إجمالي العقود والمذكرات</p>
                <span class="text-5xl font-black text-wadimakkah-dark dark:text-wadimakkah-light font-mono">{{ $stats['total_contracts'] ?? 0 }}</span>
            </div>

            {{-- 3. كارت إجمالي الاستشارات (الكارد الجديد المضاف لتحقيق التكامل المعرفي) --}}
            <div class="bg-blue-50 dark:bg-gray-800 border-2 border-blue-200 dark:border-gray-700 p-6 rounded-2xl shadow-sm text-center">
                <p class="text-gray-600 dark:text-gray-400 font-bold mb-2 text-xs uppercase tracking-wider">إجمالي الاستشارات الواردة</p>
                <span class="text-5xl font-black text-wadimakkah-dark dark:text-wadimakkah-light font-mono">{{ $totalConsultations }}</span>
            </div>
            
            {{-- 4. كارت المعاملات المعلقة المنتظرة للإجراء --}}
            <div class="bg-blue-50 dark:bg-gray-800 border-2 border-blue-200 dark:border-gray-700 p-6 rounded-2xl shadow-sm text-center">
                <p class="text-gray-600 dark:text-gray-400 font-bold mb-2 text-xs uppercase tracking-wider">معاملات تنتظر الإجراء</p>
                <span class="text-5xl font-black text-wadimakkah-dark dark:text-wadimakkah-light font-mono">{{ $pendingManagerAction }}</span>
            </div>
        </div>

        {{-- أقسام ومراكز القرار السريع المتاحة لمدير الإدارة (4 كروت متوازية مع الإحصائيات) --}}
        <div class="mb-12">
            <h2 class="text-xl font-bold text-gray-700 dark:text-gray-300 mb-6">الخدمات القانونية المتاحة</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                
                {{-- 1. كارد القضايا --}}
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 text-center hover:shadow-md transition flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 bg-blue-50 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4 border border-blue-100 dark:border-gray-600">
                            <i class="fas fa-gavel text-wadimakkah-dark dark:text-wadimakkah-light text-lg"></i>
                        </div>
                        <h3 class="font-bold text-lg mb-2 text-gray-800 dark:text-gray-200">القضايا القانونية</h3>
                        <p class="text-gray-500 dark:text-gray-400 text-xs mb-6">متابعة كافة القضايا والجلسات الحالية والقرارات الصادرة بشأنها.</p>
                    </div>
                    <a href="{{ Route::has('manager.cases') ? route('manager.cases') : '#' }}" class="block bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 py-2 rounded-lg text-sm font-semibold hover:bg-gray-200 dark:hover:bg-gray-600 transition">انتقال إلى الخدمة</a>
                </div>

                {{-- 2. كارد العقود --}}
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 text-center hover:shadow-md transition flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 bg-blue-50 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4 border border-blue-100 dark:border-gray-600">
                            <i class="fas fa-file-contract text-wadimakkah-dark dark:text-wadimakkah-light text-lg"></i>
                        </div>
                        <h3 class="font-bold text-lg mb-2 text-gray-800 dark:text-gray-200">العقود والمذكرات</h3>
                        <p class="text-gray-500 dark:text-gray-400 text-xs mb-6">الإشراف على صياغة العقود ومراجعة بنود الاتفاقيات والمذكرات القانونية المختلفة.</p>
                    </div>
                    <a href="{{ Route::has('manager.contracts') ? route('manager.contracts') : '#' }}" class="block bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 py-2 rounded-lg text-sm font-semibold hover:bg-gray-200 dark:hover:bg-gray-600 transition">انتقال إلى الخدمة</a>
                </div>

                {{-- 3. كارد إسناد وتوجيه الاستشارات الواردة --}}
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 text-center hover:shadow-md transition flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 bg-blue-50 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4 border border-blue-100 dark:border-gray-600">
                            <i class="fas fa-folder-open text-wadimakkah-dark dark:text-wadimakkah-light text-lg"></i>
                        </div>
                        <h3 class="font-bold text-lg mb-2 text-gray-800 dark:text-gray-200">إسناد وتوجيه الاستشارات الواردة</h3>
                        <p class="text-gray-500 dark:text-gray-400 text-xs mb-6">قراءة الاستشارات الواردة وإسنادها مباشرة للموظف القانوني المختص.</p>
                    </div>
                    <a href="{{ route('manager.tasks') }}" class="block bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 py-2 rounded-lg text-sm font-semibold hover:bg-gray-200 dark:hover:bg-gray-600 transition">انتقال إلى الخدمة</a>
                </div>

                {{-- 4. كارد مراجعة طلبات الاعتماد المطور --}}
                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 text-center hover:shadow-md transition flex flex-col justify-between">
                    <div>
                        <div class="w-12 h-12 bg-blue-50 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4 border border-blue-100 dark:border-gray-600">
                            <i class="fas fa-file-signature text-wadimakkah-dark dark:text-wadimakkah-light text-lg"></i>
                        </div>
                        <h3 class="font-bold text-lg mb-2 text-gray-800 dark:text-gray-200">مراجعة طلبات الاعتماد</h3>
                        <p class="text-gray-500 dark:text-gray-400 text-xs mb-6">استقبل ردود الموظفين على الاستشارات لاعتمادها أو رفضها.</p>
                    </div>
                    <a href="{{ route('manager.consultations.incoming') }}" class="block bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 py-2 rounded-lg text-sm font-semibold hover:bg-gray-200 dark:hover:bg-gray-600 transition">انتقال إلى الخدمة</a>
                </div>

            </div>
        </div>
    </main>
@endsection
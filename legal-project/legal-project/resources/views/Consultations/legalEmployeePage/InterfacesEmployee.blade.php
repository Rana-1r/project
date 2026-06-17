@extends('layouts.LegalEmployee')

@section('title', 'لوحة التحكم | منصة الإدارة القانونية')

@section('content')
    <main class="container mx-auto px-6 py-10 flex-grow">
        
        <div class="mt-10 text-center">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">لوحة تحكم الموظف القانوني</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2 mb-12 text-sm">متابعة وإدارة المهام المسندة إليك من قبل المدير القانوني في منصة شركة وادي مكة</p>
        </div>

        @php
            // 🌟 استعلام حي وآمن لحساب عدد الاستشارات المسندة للموظف القانوني الحالي مباشرة
            $totalAssignedConsultations = 0;
            if (auth()->check()) {
                $totalAssignedConsultations = DB::table('user_consultations')
                    ->where('assigned_to', auth()->user()->user_id)
                    ->count();
            }
        @endphp

        {{-- كروت الإحصائيات المسندة للمستشار القانوني الحالي --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            {{-- كارت القضايا المسندة إلي --}}
            <div class="bg-blue-50 dark:bg-gray-800 border-2 border-blue-200 dark:border-gray-700 p-6 rounded-2xl shadow-sm text-center">
                <p class="text-gray-600 dark:text-gray-400 font-bold mb-2">القضايا المسندة</p>
                <span class="text-5xl font-black text-wadimakkah-dark dark:text-wadimakkah-light font-mono">{{ $stats['total_cases'] ?? 0 }}</span>
            </div>
            
            {{-- كارت العقود المسندة إلي --}}
            <div class="bg-blue-50 dark:bg-gray-800 border-2 border-blue-200 dark:border-gray-700 p-6 rounded-2xl shadow-sm text-center">
                <p class="text-gray-600 dark:text-gray-400 font-bold mb-2">العقود الموكلة</p>
                <span class="text-5xl font-black text-wadimakkah-dark dark:text-wadimakkah-light font-mono">{{ $stats['total_contracts'] ?? 0 }}</span>
            </div>
            
            {{-- كارت الاستشارات قيد مراجعتي (تم ربطها برمجياً بالاستعلام الحي للطلبات المسندة له) --}}
            <div class="bg-blue-50 dark:bg-gray-800 border-2 border-blue-200 dark:border-gray-700 p-6 rounded-2xl shadow-sm text-center">
                <p class="text-gray-600 dark:text-gray-400 font-bold mb-2">الاستشارات الواردة</p>
                <span class="text-5xl font-black text-wadimakkah-dark dark:text-wadimakkah-light font-mono">{{ $totalAssignedConsultations }}</span>
            </div>
        </div>

        <div class="mb-12">
            <h2 class="text-xl font-bold text-gray-700 dark:text-gray-300 mb-6">الإدارات القانونية</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 text-center hover:shadow-md transition">
                    <div class="w-12 h-12 bg-blue-50 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4 border border-blue-100 dark:border-gray-600">
                        <i class="fas fa-gavel text-wadimakkah-dark dark:text-wadimakkah-light text-lg"></i>
                    </div>
                    <h3 class="font-bold text-lg mb-2 text-gray-800 dark:text-gray-200">القضايا</h3>
                    <p class="text-gray-500 dark:text-gray-400 text-sm mb-6">استعرض القضايا المسندة لك وتابع تحديثات وجلسات المحاكمة حالتها.</p>
                    <a href="#" class="block bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 py-2 rounded-lg text-sm font-semibold hover:bg-gray-200 dark:hover:bg-gray-600 transition">الانتقال إلى الخدمة</a>
                </div>

                <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 text-center hover:shadow-md transition">
                    <div class="w-12 h-12 bg-blue-50 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4 border border-blue-100 dark:border-gray-600">
                        <i class="fas fa-file-contract text-wadimakkah-dark dark:text-wadimakkah-light text-lg"></i>
                    </div>
                    <h3 class="font-bold text-lg mb-2 text-gray-800 dark:text-gray-200">العقود</h3>
                    <p class="text-gray-500 dark:text-gray-400 text-sm mb-6">راجع العقود القانونية الموكلة إليك، صياغتها، وتابع حالات الاعتماد.</p>
                    <a href="#" class="block bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 py-2 rounded-lg text-sm font-semibold hover:bg-gray-200 dark:hover:bg-gray-600 transition">الانتقال إلى الخدمة</a>
                </div>

                <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 text-center hover:shadow-md transition">
                    <div class="w-12 h-12 bg-blue-50 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4 border border-blue-100 dark:border-blue-600">
                        <i class="fas fa-balance-scale text-wadimakkah-dark dark:text-wadimakkah-light text-lg"></i>
                    </div>
                    <h3 class="font-bold text-lg mb-2 text-gray-800 dark:text-gray-200">الاستشارات القانونية</h3>
                    <p class="text-gray-500 dark:text-gray-400 text-sm mb-6">اطلع على طلبات الاستشارات المسندة لك، قدم الردود المناسبة وأرسلها للاعتماد.</p>
                    <a href="{{ route('legal.consultations.index') }}" class="block bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 py-2 rounded-lg text-sm font-semibold hover:bg-gray-200 dark:hover:bg-gray-600 transition">الانتقال إلى الخدمة</a>
                </div>

            </div>
        </div>
    </main>
@endsection
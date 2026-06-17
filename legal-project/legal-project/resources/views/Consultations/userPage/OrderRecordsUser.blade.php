@extends('layouts.InternalEmployee')
@section('title', 'سجل طلباتي | منصة الإدارة القانونية')
@section('content')

    <main class="container mx-auto px-6 py-10 flex-grow max-w-5xl">

        {{-- رأس الصفحة --}}
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">سجل طلباتي القانونية</h1>
                <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">أرشيف مرجعي شامل ومتابعة حية لكافة الاستشارات، العقود، والقضايا الخاصة بكم</p>
            </div>
            
            <span id="total-badge" class="self-start sm:self-center text-xs bg-blue-50 dark:bg-blue-900/30 text-wadimakkah-dark dark:text-wadimakkah-light font-bold px-3 py-1.5 rounded-xl border border-blue-100 dark:border-blue-800 shadow-sm">
                إجمالي المعاملات: {{ (isset($allRequests) && is_countable($allRequests)) ? count($allRequests) : 0 }}
            </span>
        </div>

        {{-- شريط التصفية والبحث المتقدم في صف واحد موحد تماماً --}}
        <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
            <div class="flex flex-wrap items-end gap-3 w-full">
                
                {{-- البحث بالعنوان --}}
                <div class="flex-1 min-w-[150px] flex flex-col gap-1">
                    <label class="text-[11px] font-bold text-gray-400 dark:text-gray-500">البحث بالعنوان</label>
                    <div class="flex items-center bg-gray-50 dark:bg-gray-700/50 px-2.5 py-1.5 rounded-xl border border-gray-100 dark:border-gray-600">
                        <i class="fas fa-search text-gray-400 ml-2 text-[10px]"></i>
                        <input type="text" id="search-title" placeholder="عنوان المعاملة..." class="w-full bg-transparent text-xs outline-none text-gray-700 dark:text-gray-200">
                    </div>
                </div>

                {{-- نوع العملية --}}
                <div class="w-32 flex flex-col gap-1">
                    <label class="text-[11px] font-bold text-gray-400 dark:text-gray-500">نوع العملية</label>
                    <select id="filter-category" class="w-full p-1.5 bg-gray-50 dark:bg-gray-700/50 text-xs font-semibold rounded-xl border border-gray-100 dark:border-gray-600 text-gray-600 dark:text-gray-300 outline-none">
                        <option value="all">كل العمليات</option>
                        <option value="استشارة">استشارات</option>
                        <option value="عقد">عقود</option>
                        <option value="قضية">قضايا</option>
                    </select>
                </div>

                {{-- حالة المعاملة المحدثة بناءً على خطة سير الإجراءات الجديدة --}}
                <div class="w-40 flex flex-col gap-1">
                    <label class="text-[11px] font-bold text-gray-400 dark:text-gray-500">حالة المعاملة</label>
                    <select id="filter-status" class="w-full p-1.5 bg-gray-50 dark:bg-gray-700/50 text-xs font-semibold rounded-xl border border-gray-100 dark:border-gray-600 text-gray-600 dark:text-gray-300 outline-none">
                        <option value="all">كل الحالات</option>
                        <option value="مسودة">مسودة</option>
                        <option value="قيد الإسناد">قيد الإسناد (عند المدير)</option>
                        <option value="قيد المراجعة">قيد المراجعة (عند الموظف)</option>
                        <option value="قيد الاعتماد">قيد الاعتماد (عند المدير القانوني)</option>
                        <option value="تم الرد">تم الرد (معتمد)</option>
                        <option value="مرفوض">مرفوض</option>
                    </select>
                </div>

                {{-- من تاريخ --}}
                <div class="w-36 flex flex-col gap-1">
                    <label class="text-[11px] font-bold text-gray-400 dark:text-gray-500">من تاريخ</label>
                    <input type="date" id="filter-date-from" class="w-full p-1.5 bg-gray-50 dark:bg-gray-700/50 text-xs font-semibold rounded-xl border border-gray-100 dark:border-gray-600 text-gray-600 dark:text-gray-300 outline-none font-mono">
                </div>

                {{-- إلى تاريخ --}}
                <div class="w-36 flex flex-col gap-1">
                    <label class="text-[11px] font-bold text-gray-400 dark:text-gray-500">إلى تاريخ</label>
                    <input type="date" id="filter-date-to" class="w-full p-1.5 bg-gray-50 dark:bg-gray-700/50 text-xs font-semibold rounded-xl border border-gray-100 dark:border-gray-600 text-gray-600 dark:text-gray-300 outline-none font-mono">
                </div>

                {{-- زر التطبيق الأفقي --}}
                <button onclick="applyFilters()" class="bg-wadimakkah-dark hover:bg-blue-800 text-white px-5 py-2 rounded-xl text-xs font-bold shadow-sm transition cursor-pointer flex items-center justify-center h-[34px]">
                    تطبيق
                </button>

            </div>
        </div>

        {{-- جدول السجل الموحد للمستخدم --}}
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="overflow-x-auto">
                <table class="w-full text-right border-collapse">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-700 text-xs font-bold text-gray-400 uppercase tracking-wider">
                            <th class="pb-3">نوع العملية</th>
                            <th class="pb-3">عنوان المعاملة</th>
                            <th class="pb-3">تاريخ ووقت الإرسال</th>
                            <th class="pb-3">المستندات المرفوعة</th>
                            <th class="pb-3 text-center">الحالة</th>
                            <th class="pb-3 text-left pl-4">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody id="records-table-body" class="text-sm font-medium divide-y divide-gray-50 dark:divide-gray-700/50">
                        @forelse($allRequests as $requestItem)
                            @php
                                $dateRaw = is_string($requestItem->created_at) ? date('Y-m-d', strtotime($requestItem->created_at)) : $requestItem->created_at->format('Y-m-d');
                                
                                // تعديل الوقت ليتوافق مع توقيت مكة المكرمة (+3 ساعات عن UTC)
                                $createdAtCarbon = \Carbon\Carbon::parse($requestItem->created_at)->timezone('Asia/Riyadh');
                                $formattedDateTimeSaudi = $createdAtCarbon->format('Y/m/d H:i');

                                // مواءمة وفحص الحالات الإجرائية المحدثة حياً بالجدول
                                $currentStatus = $requestItem->status;
                                if ($currentStatus === 'قيد المراجعة') { $currentStatus = 'قيد الإسناد'; }
                                elseif ($currentStatus === 'قيد الدراسة') { $currentStatus = 'قيد المراجعة'; }
                                elseif ($currentStatus === 'بانتظار الاعتماد') { $currentStatus = 'قيد الاعتماد'; }

                                // استبدال مسمى المستشار في حال لم يتم الإسناد بعد في مرحلة قيد الإسناد
                                $responderName = 'المستشار القانوني المختص';
                                if ($currentStatus === 'قيد الإسناد') {
                                    $responderName = 'لم يتم إسناد موظف قانوني بعد';
                                } elseif (!empty($requestItem->assigned_to)) {
                                    $legalUser = DB::table('users')->where('user_id', $requestItem->assigned_to)->first();
                                    if ($legalUser) {
                                        $responderName = $legalUser->name;
                                    }
                                }
                            @endphp
                            <tr class="record-row hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition"
                                data-title="{{ $requestItem->title }}"
                                data-category="{{ $requestItem->type_category ?? 'استشارة' }}"
                                data-status="{{ $currentStatus }}"
                                data-date="{{ $dateRaw }}">
                                
                                <td class="py-4">
                                    @if(($requestItem->type_category ?? 'استشارة') == 'قضية')
                                        <span class="px-2.5 py-1 bg-yellow-50 dark:bg-yellow-950/40 text-yellow-700 dark:text-yellow-400 text-xs font-bold rounded-lg border border-yellow-100 dark:border-yellow-900/60">
                                            قضية
                                        </span>
                                    @elseif(($requestItem->type_category ?? 'استشارة') == 'عقد')
                                        <span class="px-2.5 py-1 bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-400 text-xs font-bold rounded-lg border border-blue-100 dark:border-blue-900/60">
                                            عقد
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 bg-green-50 dark:bg-green-950/40 text-green-700 dark:text-green-400 text-xs font-bold rounded-lg border border-green-100 dark:border-green-900/60">
                                            استشارة
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 text-gray-800 dark:text-gray-200 max-w-[180px] truncate font-bold">{{ $requestItem->title }}</td>
                                <td class="py-4 text-xs text-gray-500 dark:text-gray-400 font-mono">
                                    {{ $formattedDateTimeSaudi }}
                                </td>
                                <td class="py-4 max-w-[180px]">
                                    <div class="flex flex-col gap-1">
                                        @if(!empty($requestItem->attachment))
                                            @php $filesArray = json_decode($requestItem->attachment, true); @endphp
                                            @if(is_array($filesArray) && count($filesArray) > 0)
                                                @foreach($filesArray as $fileItem)
                                                    <a href="{{ Storage::url($fileItem['path']) }}" target="_blank" class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline inline-flex items-center gap-1 truncate">
                                                        <i class="fas fa-paperclip text-gray-400 text-[10px]"></i> {{ $fileItem['name'] }}
                                                    </a>
                                                @endforeach
                                            @else
                                                <span class="text-xs text-gray-400">لا يوجد مرفقات</span>
                                            @endif
                                        @else
                                            <span class="text-xs text-gray-400">لا يوجد مرفقات</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-4 text-center">
                                    @if($currentStatus == 'مسودة')
                                        <span class="px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs font-bold rounded-md border border-gray-200 dark:border-gray-600">مسودة</span>
                                    @elseif($currentStatus == 'قيد الإسناد')
                                        <span class="px-2 py-0.5 bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 text-xs font-bold rounded-md border border-amber-200">قيد الإسناد</span>
                                    @elseif($currentStatus == 'قيد المراجعة')
                                        <span class="px-2 py-0.5 bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 text-xs font-bold rounded-md border border-indigo-200">قيد المراجعة</span>
                                    @elseif($currentStatus == 'قيد الاعتماد')
                                        <span class="px-2 py-0.5 bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400 text-xs font-bold rounded-md border border-purple-200">قيد الاعتماد</span>
                                    @elseif($currentStatus == 'تم الرد' || $currentStatus == 'معتمد' || $currentStatus == 'مقبول')
                                        <span class="px-2 py-0.5 bg-green-50 dark:bg-green-950/40 text-green-600 dark:text-green-400 text-xs font-bold rounded-md border border-green-200">تم الرد والاعتماد</span>
                                    @elseif($currentStatus == 'مرفوض' || str_contains($currentStatus, 'رفض'))
                                        <span class="px-2 py-0.5 bg-red-50 dark:bg-red-950/40 text-red-600 dark:text-red-400 text-xs font-bold rounded-md border border-red-200">مرفوض</span>
                                    @else
                                        <span class="px-2 py-0.5 bg-gray-50 text-gray-600 text-xs font-bold rounded-md border">{{ $currentStatus }}</span>
                                    @endif
                                </td>
                                <td class="py-4 text-left whitespace-nowrap pr-4">
                                    <div class="flex items-center justify-end gap-2">
                                        @if($currentStatus == 'مسودة')
                                            <a href="{{ route('internal.consultations.edit', $requestItem->consultation_id) }}"
                                                class="text-blue-600 dark:text-blue-400 hover:underline text-xs font-bold inline-flex items-center gap-1">
                                                <i class="fas fa-edit text-[11px]"></i> تعديل
                                            </a>
                                            <button onclick="confirmDeleteDraft({{ $requestItem->consultation_id }})"
                                                class="text-red-600 dark:text-red-400 hover:text-red-500 transition p-1 cursor-pointer" title="حذف المسودة">
                                                <i class="fas fa-trash-alt text-[11px]"></i>
                                            </button>
                                            <form id="delete-form-{{ $requestItem->consultation_id }}" action="{{ route('consultations.destroy', $requestItem->consultation_id) }}" method="POST" class="hidden">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        @else
                                            <button
                                                onclick="openDetailsModal('{{ $requestItem->type_category ?? 'استشارة' }}', '{{ addslashes($requestItem->title) }}', '{{ addslashes($requestItem->description ?? 'لا يوجد وصف متاح للطلب.') }}', '{{ addslashes($requestItem->reply ?? '') }}', '{{ $currentStatus }}', '{{ addslashes($requestItem->rejection_reason ?? 'تم رفض طلب الاستشارة من المدير القانوني.') }}', '{{ $requestItem->attachment ?? '' }}', '{{ addslashes($responderName) }}', '{{ $requestItem->reply_attachment ?? '' }}')"
                                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-4 py-2 rounded-xl shadow-sm transition active:scale-[0.98] cursor-pointer inline-flex items-center gap-2">
                                                <i class="fas fa-eye text-xs"></i> عرض التفاصيل
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr id="no-records-row">
                                <td colspan="6" class="py-12 text-center text-sm text-gray-400">
                                    <i class="fas fa-folder-open text-2xl block mb-2 text-gray-300 dark:text-gray-700"></i>
                                    لا توجد معاملات مسجلة في السجل حالياً.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    {{-- نافذة تفاصيل العملية المنبثقة (المودال) المحدثة بتصميم عصري راقٍ --}}
    <div id="details-modal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900/70 flex items-center justify-center backdrop-blur-md p-4">
        <div id="modal-container-box" class="bg-white dark:bg-gray-800 rounded-[2rem] shadow-2xl border border-gray-100 dark:border-gray-700 w-full max-w-4xl overflow-hidden flex flex-col my-4 transform scale-95 transition-all duration-300 animate-fadeIn relative max-h-[90vh]">
            
            {{-- الهيدر الموحد --}}
            <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between bg-gray-50/50 dark:bg-gray-800/50 flex-shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400 shadow-sm border border-blue-100 dark:border-blue-800/40">
                        <i class="fas fa-balance-scale text-xs"></i>
                    </div>
                    <h3 class="font-bold text-gray-800 dark:text-white text-sm">تفاصيل المعاملة القانونية</h3>
                </div>
                <button onclick="closeDetailsModal()" class="w-7 h-7 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 hover:bg-red-50 hover:text-red-500 dark:hover:bg-red-950/30 dark:hover:text-red-400 transition flex items-center justify-center cursor-pointer">
                    <i class="fas fa-times text-[10px]"></i>
                </button>
            </div>

            {{-- محتوى المودال المرن --}}
            <div class="p-6 overflow-y-auto flex-grow flex flex-col gap-6 text-right">
                <div id="modal-grid-layout" class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                    
                    {{-- 🔹 القسم الأيمن: تفاصيل الطلب الأصلي --}}
                    <div class="bg-gray-50/60 dark:bg-gray-900/30 p-5 rounded-2xl border border-gray-100 dark:border-gray-700/60 shadow-sm space-y-4">
                        <div class="flex items-center justify-between border-b border-gray-200/60 dark:border-gray-700 pb-3">
                            <h4 class="text-xs font-bold text-blue-600 dark:text-blue-400 flex items-center gap-2">
                                <i class="fas fa-file-invoice text-sm"></i> الطلب المرسل من طرفكم
                            </h4>
                            <div class="flex items-center gap-1.5">
                                <span id="modal-type" class="px-2.5 py-0.5 bg-blue-100/60 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 text-[10px] font-bold rounded-md"></span>
                                <span id="modal-status-badge" class="px-2.5 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-[10px] font-bold rounded-md"></span>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div>
                                <label class="text-[11px] font-semibold text-gray-400 dark:text-gray-500 block mb-1">عنوان الطلب المرفوع</label>
                                <div id="modal-title" class="text-xs font-bold text-gray-800 dark:text-gray-100 leading-relaxed bg-white dark:bg-gray-800 p-3 rounded-xl border border-gray-100 dark:border-gray-700 shadow-xs"></div>
                            </div>
                            <div>
                                <label class="text-[11px] font-semibold text-gray-400 dark:text-gray-500 block mb-1">نص الطلب</label>
                                <textarea id="modal-desc" readonly
                                    class="w-full bg-white dark:bg-gray-800 text-xs font-medium text-gray-700 dark:text-gray-300 leading-relaxed text-justify p-3 rounded-xl border border-gray-100 dark:border-gray-700 outline-none resize-none overflow-hidden min-h-[110px] shadow-xs"></textarea>
                            </div>
                            <div id="modal-doc-container" class="space-y-1.5 pt-1">
                                <label class="text-[11px] font-semibold text-gray-400 dark:text-gray-500 block">المستندات المرفقة بالطلب الأصلي</label>
                                <div id="modal-files-list" class="grid grid-cols-1 gap-2"></div>
                            </div>
                        </div>
                    </div>

                    {{-- 🔹 القسم الأيسر: تفاصيل رد الإدارة القانونية --}}
                    <div id="modal-legal-reply-section" class="bg-emerald-50/20 dark:bg-emerald-950/5 p-5 rounded-2xl border border-emerald-100/40 dark:border-emerald-900/20 shadow-sm space-y-4">
                        <div class="flex items-center justify-between border-b border-emerald-200/40 dark:border-emerald-900/30 pb-3">
                            <h4 class="text-xs font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-2">
                                <i class="fas fa-reply-all text-sm"></i> رد الإدارة القانونية
                            </h4>
                        </div>
                        <div class="space-y-3">
                            <div>
                                <label class="text-[11px] font-semibold text-gray-400 dark:text-gray-500 block mb-1">المستشار القانوني المجيب</label>
                                <div class="bg-white dark:bg-gray-800 px-3.5 py-2.5 rounded-xl border border-gray-100 dark:border-gray-700 text-xs font-bold text-gray-800 dark:text-gray-200 flex items-center gap-2 shadow-xs">
                                    <div class="w-5 h-5 rounded-full bg-emerald-50 dark:bg-emerald-950/50 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                                        <i class="fas fa-user-shield text-[10px]"></i>
                                    </div>
                                    <span id="modal-responder-name">المستشار القانوني المختص</span>
                                </div>
                            </div>
                            <div>
                                <label class="text-[11px] font-semibold text-gray-400 dark:text-gray-500 block mb-1">الرد والاستشارة الصادرة</label>
                                <textarea id="modal-reply" readonly
                                    class="w-full bg-white dark:bg-gray-800 text-xs font-medium text-gray-700 dark:text-gray-200 leading-relaxed text-justify p-3 rounded-xl border border-emerald-100/40 dark:border-emerald-900/30 outline-none resize-none overflow-hidden min-h-[110px] shadow-xs"></textarea>
                            </div>
                            {{-- عرض مستندات ومرفقات الرد الصادر من الموظف القانوني --}}
                            <div id="modal-reply-doc-container" class="space-y-1.5 pt-1 hidden">
                                <label class="text-[11px] font-semibold text-gray-400 dark:text-gray-500 block">المستندات المرفقة مع الرد القانوني</label>
                                <div id="modal-reply-files-list" class="grid grid-cols-1 gap-2"></div>
                            </div>
                            <div class="pt-2 border-t border-dashed border-gray-200 dark:border-gray-700 flex items-center justify-between text-[11px]">
                                <span class="font-semibold text-gray-400 dark:text-gray-500">حالة المعاملة الحالية:</span>
                                <div id="modal-approval-status" class="font-bold"></div>
                            </div>
                        </div>
                    </div>

                    {{-- 🔹 القسم الأيسر البديل: كرت الرفض --}}
                    <div id="modal-rejection-box" class="hidden p-5 bg-red-50/60 dark:bg-red-950/20 text-xs rounded-2xl border border-red-100 dark:border-red-900/40 text-red-700 dark:text-red-400 flex flex-col gap-4 shadow-sm h-full animate-fadeIn">
                        <div class="flex items-center justify-between border-b border-red-200/60 dark:border-red-900/40 pb-3">
                            <h4 class="text-xs font-bold text-red-600 dark:text-red-400 flex items-center gap-2">
                                <i class="fas fa-times-circle text-sm"></i> تم رفض الاستشارة وإنهاء المعاملة
                            </h4>
                        </div>
                        <div class="space-y-3 flex-grow flex flex-col justify-start">
                            <div class="text-[11px] text-gray-400 dark:text-gray-500 font-bold">سبب الرفض الرسمي الصادر عن الإدارة القانونية:</div>
                            <p id="modal-rejection-reason" class="leading-relaxed font-bold text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 p-4 rounded-xl border border-red-100/40 dark:border-red-900/20 text-justify overflow-y-auto flex-grow min-h-[150px] shadow-xs"></p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- الفوتر الثابت --}}
            <div class="flex items-center justify-end border-t border-gray-100 dark:border-gray-700 p-4 bg-gray-50/50 dark:bg-gray-800/50 flex-shrink-0">
                <button type="button" onclick="closeDetailsModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl text-xs font-bold shadow-sm transition-all cursor-pointer active:scale-[0.98]">
                    إغلاق المراجعة
                </button>
            </div>
        </div>
    </div>

    {{-- مودال تأكيد حذف المسودة المخصص --}}
    <div id="deleteConfirmModal" class="fixed inset-0 bg-black/60 hidden z-50 flex items-center justify-center backdrop-blur-sm p-4 transition-all duration-300">
        <div class="bg-white dark:bg-gray-800 rounded-3xl max-w-sm w-full shadow-2xl border border-gray-200 dark:border-gray-700 p-6 text-center transform scale-95 transition-all duration-300 animate-fadeIn">
            <div class="w-14 h-14 bg-red-50 dark:bg-red-950/30 rounded-full flex items-center justify-center mx-auto mb-4 border border-red-100 dark:border-red-900/60 text-red-500 text-xl">
                <i class="fas fa-exclamation-triangle animate-pulse"></i>
            </div>
            
            <h3 class="text-base font-bold text-gray-800 dark:text-white mb-2">تأكيد حذف المسودة</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-6 leading-relaxed">هل أنتِ متأكدة من رغبتكِ في حذف هذه المسودة نهائياً؟ لا يمكن التراجع عن هذا الإجراء لاحقاً.</p>
            
            <div class="flex items-center gap-3">
                <button id="confirm-delete-btn" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold text-xs py-2.5 rounded-xl transition shadow-md active:scale-95">
                    نعم، احذفها
                </button>
                <button onclick="closeDeleteModal()" class="flex-1 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-bold text-xs py-2.5 rounded-xl transition border border-gray-200 dark:border-gray-600 active:scale-95">
                    إلغاء
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function applyFilters() {
            const searchVal = document.getElementById('search-title').value.toLowerCase().trim();
            const categoryVal = document.getElementById('filter-category').value;
            const statusVal = document.getElementById('filter-status').value;
            const dateFrom = document.getElementById('filter-date-from').value;
            const dateTo = document.getElementById('filter-date-to').value;

            const rows = document.querySelectorAll('.record-row');
            const totalBadge = document.getElementById('total-badge');
            let matchCount = 0;

            rows.forEach(row => {
                const rTitle = row.getAttribute('data-title').toLowerCase();
                const rCategory = row.getAttribute('data-category');
                const rStatus = row.getAttribute('data-status');
                const rDate = row.getAttribute('data-date');

                const matchSearch = rTitle.includes(searchVal);
                const matchCategory = (categoryVal === 'all' || rCategory === categoryVal);
                
                let matchStatus = (statusVal === 'all');
                if (statusVal === 'قيد الإسناد') {
                    matchStatus = (rStatus === 'قيد الإسناد');
                } else if (statusVal === 'قيد المراجعة') {
                    matchStatus = (rStatus === 'قيد المراجعة');
                } else if (statusVal === 'قيد الاعتماد') {
                    matchStatus = (rStatus === 'قيد الاعتماد');
                } else if (statusVal === 'تم الرد') {
                    matchStatus = (rStatus === 'تم الرد' || rStatus === 'معتمد' || rStatus === 'مقبول');
                } else if (statusVal === 'مرفوض') {
                    matchStatus = (rStatus.includes('مرفوض'));
                } else if (statusVal === 'مسودة') {
                    matchStatus = (rStatus === 'مسودة');
                }
                
                let matchDate = true;
                if (dateFrom && rDate < dateFrom) matchDate = false;
                if (dateTo && rDate > dateTo) matchDate = false;

                if (matchSearch && matchCategory && matchStatus && matchDate) {
                    row.classList.remove('hidden');
                    matchCount++;
                } else {
                    row.classList.add('hidden');
                }
            });

            totalBadge.innerText = `المعاملات المفلترة: ${matchCount}`;

            const emptyRow = document.getElementById('filter-empty-row');
            if (rows.length > 0) {
                if (matchCount === 0) {
                    emptyRow.classList.remove('hidden');
                } else {
                    emptyRow.classList.add('hidden');
                }
            }
        }

        function openDetailsModal(type, title, description, reply, status, rejectionReason, attachmentJson, responderName, replyAttachmentJson) {
            document.getElementById('modal-type').innerText = type;
            document.getElementById('modal-status-badge').innerText = status;
            document.getElementById('modal-title').innerText = title;
            document.getElementById('modal-responder-name').innerText = responderName;

            const descTextarea = document.getElementById('modal-desc');
            const replyTextarea = document.getElementById('modal-reply');

            descTextarea.value = description ? description : 'لا يوجد تفاصيل مكتوبة بالطلب.';

            const statusBox = document.getElementById('modal-approval-status');
            const rejectionBox = document.getElementById('modal-rejection-box');
            const docContainer = document.getElementById('modal-doc-container');
            const filesList = document.getElementById('modal-files-list');
            
            const legalReplySection = document.getElementById('modal-legal-reply-section');
            const replyDocContainer = document.getElementById('modal-reply-doc-container');
            const replyFilesList = document.getElementById('modal-reply-files-list');

            // 1. معالجة وعرض مرفقات الطلب الأصلي (القسم الأيمن)
            filesList.innerHTML = '';
            if (attachmentJson && attachmentJson.trim() !== '') {
                try {
                    const filesArray = JSON.parse(attachmentJson);
                    if (Array.isArray(filesArray) && filesArray.length > 0) {
                        filesArray.forEach(file => {
                            const fileLink = document.createElement('a');
                            fileLink.href = `/storage/${file.path}`;
                            fileLink.target = '_blank';
                            fileLink.className = "inline-flex items-center gap-2 bg-white dark:bg-gray-800 hover:bg-blue-50/50 dark:hover:bg-blue-950/20 border dark:border-gray-700 px-3 py-2 rounded-xl text-[11px] font-semibold text-gray-700 dark:text-gray-300 w-full truncate transition shadow-xs";
                            fileLink.innerHTML = `<i class="fas fa-paperclip text-blue-500 text-xs"></i> <span class="truncate text-blue-600 dark:text-blue-400 hover:underline">${file.name}</span>`;
                            filesList.appendChild(fileLink);
                        });
                        docContainer.classList.remove('hidden');
                    } else {
                        docContainer.classList.add('hidden');
                    }
                } catch(e) {
                    docContainer.classList.add('hidden');
                }
            } else {
                docContainer.classList.add('hidden');
            }

            // 🌟 التعديل الجوهري الفعلي: حجب وعرض الرد والمرفقات حصراً في حالة "تم الرد" فقط 🌟
            const isApprovedAndPublished = (status === 'تم الرد');
            
            if (isApprovedAndPublished) {
                replyTextarea.value = reply ? reply : 'لا يوجد رد صادر حتى الآن.';
                
                // معالجة وإدراج مرفقات الرد القانوني
                replyFilesList.innerHTML = '';
                if (replyAttachmentJson && replyAttachmentJson.trim() !== '') {
                    try {
                        const replyFilesArray = JSON.parse(replyAttachmentJson);
                        if (Array.isArray(replyFilesArray) && replyFilesArray.length > 0) {
                            replyFilesArray.forEach(file => {
                                const fileLink = document.createElement('a');
                                fileLink.href = `/storage/${file.path}`;
                                fileLink.target = '_blank';
                                fileLink.className = "inline-flex items-center gap-2 bg-white dark:bg-gray-800 hover:bg-emerald-50/50 dark:hover:bg-emerald-950/20 border dark:border-gray-700 px-3 py-2 rounded-xl text-[11px] font-semibold text-gray-700 dark:text-gray-300 w-full truncate transition shadow-xs";
                                fileLink.innerHTML = `<i class="fas fa-paperclip text-emerald-500 text-xs"></i> <span class="truncate text-emerald-600 dark:text-emerald-400 hover:underline">${file.name}</span>`;
                                replyFilesList.appendChild(fileLink);
                            });
                            replyDocContainer.classList.remove('hidden');
                        } else {
                            replyDocContainer.classList.add('hidden');
                        }
                    } catch(e) {
                        replyDocContainer.classList.add('hidden');
                    }
                } else {
                    replyDocContainer.classList.add('hidden');
                }
            } else {
                // حجب كامل للرد والمرفقات طالما لم يتم اتخاذ إجراء "تم الرد والاعتماد" النهائي
                replyTextarea.value = 'لم يتم اعتماد الرد ونشره رسمياً بعد من الإدارة القانونية.';
                replyDocContainer.classList.add('hidden');
                replyFilesList.innerHTML = '';
            }

            // 3. إدارة تبديل وعرض الحالات الجديدة حركياً وبصرياً بالمودال
            if (status === 'مرفوض' || status.includes('رفض')) {
                statusBox.innerHTML = '<span class="text-red-500 flex items-center gap-1"><i class="fas fa-times-circle"></i> تم رفض الاستشارة وإنهاء المعاملة</span>';
                document.getElementById('modal-rejection-reason').innerText = rejectionReason ? rejectionReason : 'تم رفض الطلب المرفوع.';
                
                legalReplySection.classList.add('hidden');
                rejectionBox.classList.remove('hidden');
            } else {
                legalReplySection.classList.remove('hidden');
                rejectionBox.classList.add('hidden');

                if (status === 'تم الرد' || status === 'معتمد' || status === 'مقبول') {
                    statusBox.innerHTML = '<span class="text-green-600 dark:text-green-400 flex items-center gap-1"><i class="fas fa-check-circle"></i> تم اعتماد الرد رسمياً وإتاحته لكم</span>';
                } else if (status === 'مسودة') {
                    statusBox.innerHTML = '<span class="text-gray-400 flex items-center gap-1"><i class="fas fa-file"></i> المعاملة لا تزال مسودة غير مرسلة</span>';
                } else if (status === 'قيد الإسناد') {
                    statusBox.innerHTML = '<span class="text-amber-500 flex items-center gap-1"><i class="fas fa-clock"></i> الطلب معلق بانتظار التوزيع والإسناد من مدير الإدارة</span>';
                } else if (status === 'قيد المراجعة') {
                    statusBox.innerHTML = '<span class="text-indigo-500 flex items-center gap-1"><i class="fas fa-spinner fa-spin"></i> المعاملة أحيلت للمستشار وهو قيد دراستها وصياغتها حالياً</span>';
                } else if (status === 'قيد الاعتماد') {
                    statusBox.innerHTML = '<span class="text-purple-500 flex items-center gap-1"><i class="fas fa-signature"></i> تم صياغة الرد وهو بانتظار التدقيق والاعتماد من المدير القانوني</span>';
                } else {
                    statusBox.innerHTML = `<span class="text-gray-500 flex items-center gap-1"><i class="fas fa-info-circle"></i> ${status}</span>`;
                }
            }

            document.getElementById('details-modal').classList.remove('hidden');

            setTimeout(() => {
                adjustModalTextareaHeight(descTextarea);
                adjustModalTextareaHeight(replyTextarea);
            }, 50);
        }

        function adjustModalTextareaHeight(el) {
            el.style.height = 'auto';
            el.style.height = el.scrollHeight + 'px';
        }

        function closeDetailsModal() {
            document.getElementById('details-modal').classList.add('hidden');
        }

        let currentDeleteDraftId = null;

        function confirmDeleteDraft(id) {
            currentDeleteDraftId = id;
            const deleteModal = document.getElementById('deleteConfirmModal');
            
            document.getElementById('confirm-delete-btn').onclick = function() {
                if (currentDeleteDraftId) {
                    document.getElementById('delete-form-' + currentDeleteDraftId).submit();
                }
            };
            
            deleteModal.classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteConfirmModal').classList.add('hidden');
            currentDeleteDraftId = null;
        }

        window.addEventListener('click', function(event) {
            const detailsModal = document.getElementById('details-modal');
            const deleteModal = document.getElementById('deleteConfirmModal');
            if (event.target === detailsModal) {
                closeDetailsModal();
            }
            if (event.target === deleteModal) {
                closeDeleteModal();
            }
        });
    </script>
@endpush
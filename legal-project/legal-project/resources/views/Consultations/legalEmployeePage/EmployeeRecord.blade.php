@extends('layouts.LegalEmployee')

@section('title', 'السجل القانوني الموحد | منصة الإدارة القانونية')

@section('content')
    <main class="container mx-auto px-6 py-10 flex-grow max-w-5xl">
        
        {{-- رأس الصفحة --}}
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">السجل القانوني الموحد</h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">أرشيف مرجعي شامل لكافة الاستشارات، العقود، والقضايا التي قمت بالعمل عليها</p>
        </div>

        {{-- شريط التصفية والبحث المتقدم في صف واحد موحد --}}
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

                {{-- حالة المعاملة المطابقة للسيناريو الإجرائي للمستشار (تم تعديل بحاجة لتعديل إلى مرفوض) --}}
                <div class="w-40 flex flex-col gap-1">
                    <label class="text-[11px] font-bold text-gray-400 dark:text-gray-500">حالة المعاملة</label>
                    <select id="filter-status" class="w-full p-1.5 bg-gray-50 dark:bg-gray-700/50 text-xs font-semibold rounded-xl border border-gray-100 dark:border-gray-600 text-gray-600 dark:text-gray-300 outline-none">
                        <option value="all">كل الحالات</option>
                        <option value="قيد الدراسة">قيد الدراسة</option>
                        <option value="بانتظار الاعتماد">بانتظار الاعتماد</option>
                        <option value="تم الرد">تم الرد والاعتماد</option>
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

        {{-- جدول السجل الموحد --}}
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="overflow-x-auto">
                <table class="w-full text-right border-collapse">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-700 text-xs font-bold text-gray-400 uppercase tracking-wider">
                            <th class="pb-3">نوع العملية</th>
                            <th class="pb-3">عنوان المعاملة</th>
                            <th class="pb-3">صاحب الطلب</th>
                            <th class="pb-3">تاريخ الإرسال</th>
                            <th class="pb-3 text-center">الحالة</th>
                            <th class="pb-3 text-left pl-4">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody id="records-table-body" class="text-sm font-medium divide-y divide-gray-50 dark:divide-gray-700/50">
                        @forelse($myReplies as $reply)
                            <tr class="record-row hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition" 
                                data-title="{{ $reply->title }}" 
                                data-category="{{ $reply->type_category ?? 'استشارة' }}"
                                data-status="{{ $reply->status }}" 
                                data-date="{{ \Carbon\Carbon::parse($reply->created_at)->format('Y-m-d') }}">
                                
                                <td class="py-4">
                                    {{-- 🌟 تم التعديل هنا ليعود للون الأخضر المتناسق مع السجل العام --}}
                                    <span class="px-2.5 py-1 bg-green-50 dark:bg-green-950/40 text-green-700 dark:text-green-400 text-xs font-bold rounded-lg border border-green-100 dark:border-green-900/60">
                                        {{ $reply->type_category ?? 'استشارة' }}
                                    </span>
                                </td>
                                <td class="py-4 text-gray-800 dark:text-gray-200 max-w-[180px] truncate font-bold">{{ $reply->title }}</td>
                                <td class="py-4 text-xs text-gray-600 dark:text-gray-400 font-semibold">{{ $reply->user_name ?? 'موظف داخلي' }}</td>
                                <td class="py-4 text-xs text-gray-500 dark:text-gray-400 font-mono">{{ \Carbon\Carbon::parse($reply->created_at)->format('Y/m/d') }}</td>
                                <td class="py-4 text-center">
                                    @if($reply->status == 'قيد الدراسة')
                                        <span class="px-2 py-0.5 bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 text-xs font-bold rounded-md border border-indigo-200">قيد الدراسة</span>
                                    @elseif($reply->status == 'بانتظار الاعتماد')
                                        <span class="px-2 py-0.5 bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 text-xs font-bold rounded-md border border-amber-200">بانتظار الاعتماد</span>
                                    @elseif($reply->status == 'تم الرد' || $reply->status == 'معتمد' || $reply->status == 'مقبول')
                                        <span class="px-2 py-0.5 bg-green-50 dark:bg-green-950/40 text-green-600 dark:text-green-400 text-xs font-bold rounded-md border border-green-200">تم الرد والاعتماد</span>
                                    @else
                                        <span class="px-2 py-0.5 bg-red-50 dark:bg-red-950/40 text-red-600 dark:text-red-400 text-xs font-bold rounded-md border border-red-200">مرفوض</span>
                                    @endif
                                </td>
                                <td class="py-4 text-left pl-4 whitespace-nowrap">
                                    <button 
                                        onclick="openDetailsModal('{{ $reply->type_category ?? 'استشارة' }}', '{{ addslashes($reply->title) }}', '{{ $reply->user_name ?? 'موظف داخلي' }}', '{{ addslashes($reply->description ?? 'لا يوجد وصف متاح للمعاملة.') }}', '{{ addslashes($reply->reply) }}', '{{ $reply->status }}', '{{ addslashes($reply->rejection_reason ?? 'يرجى مراجعة الصياغة والتعديل حسب ملحوظات المدير.') }}', '{{ $reply->attachment ?? '' }}', '{{ $reply->reply_attachment ?? '' }}')"
                                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-4 py-2 rounded-xl shadow-sm transition active:scale-[0.98] cursor-pointer inline-flex items-center gap-2">
                                        <i class="fas fa-eye text-xs"></i> عرض التفاصيل
                                    </button>
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

                        {{-- صف فارغ يظهر عند انعدام مطابقة الفلاتر --}}
                        <tr id="filter-empty-row" class="hidden">
                            <td colspan="6" class="py-12 text-center text-sm text-gray-400">
                                <i class="fas fa-search block text-xl mb-2 text-gray-300 dark:text-gray-700"></i>
                                لا توجد نتائج تطابق خيارات البحث المحددة.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    {{-- 🌟 مودال عرض التفاصيل المطور بستايل صفحة المراجعة والقراءة والوضوح التام (نفس ستايل المدير تماماً) 🌟 --}}
    <div id="details-modal" class="hidden fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4 backdrop-blur-md animate-fadeIn">
        <div class="bg-white dark:bg-gray-900 rounded-[2rem] border border-gray-100 dark:border-gray-800 w-full max-w-5xl flex flex-col shadow-2xl overflow-hidden max-h-[90vh]">
            
            {{-- هيدر المودال الموحد --}}
            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-b border-gray-100 dark:border-gray-700/80 flex justify-between items-center flex-shrink-0">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 bg-blue-50 dark:bg-blue-900/30 rounded-xl flex items-center justify-center text-blue-600 dark:text-blue-400">
                        <i class="fas fa-archive text-sm"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm text-gray-800 dark:text-gray-100">تفاصيل المعاملة المؤرشفة</h3>
                        <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5">الاطلاع التاريخي الشامل على مستندات الطلب والقرار القانوني النهائي</p>
                    </div>
                </div>
                <button onclick="closeDetailsModal()" class="w-8 h-8 rounded-xl bg-gray-100 dark:bg-gray-700 hover:bg-red-500 hover:text-white dark:hover:bg-red-600 transition flex items-center justify-center text-gray-500 dark:text-gray-400">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
            
            {{-- جسم المودال المقسم لعمودين متوازيين بوضوح تام --}}
            <div class="flex-grow overflow-y-auto p-6 grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                
                {{-- العمود الأيمن: تفاصيل المعاملة والطلب الأساسي --}}
                <div class="space-y-4 bg-gray-50/50 dark:bg-gray-800/20 p-5 rounded-2xl border border-gray-100 dark:border-gray-800/60">
                    <div class="flex justify-between items-center border-b border-gray-100 dark:border-gray-700 pb-2 mb-1">
                        <div class="flex items-center gap-1.5">
                            <span class="w-1.5 h-3 bg-blue-600 rounded-sm"></span>
                            <h4 class="text-xs font-bold text-gray-700 dark:text-gray-300">تفاصيل الطلب القانوني الأساسي</h4>
                        </div>
                        <span class="text-[11px] text-gray-500 font-semibold">بواسطة: <span id="modal-sender" class="text-gray-700 dark:text-gray-300 font-bold"></span></span>
                    </div>
                    
                    <div>
                        <label class="text-[11px] font-bold text-gray-400 dark:text-gray-500 block mb-1">موضوع الطلب / العنوان</label>
                        <p id="modal-title-text" class="font-bold text-xs text-gray-800 dark:text-gray-200 bg-white dark:bg-gray-800 p-3 rounded-xl border border-gray-100 dark:border-gray-700/50 shadow-inner"></p>
                    </div>
                    
                    <div>
                        <label class="text-[11px] font-bold text-gray-400 dark:text-gray-500 block mb-1">شرح ومضمون الطلب</label>
                        <textarea id="modal-desc" readonly class="w-full bg-white dark:bg-gray-800 text-xs font-medium text-gray-600 dark:text-gray-300 leading-relaxed text-justify p-3 rounded-xl border border-gray-100 dark:border-gray-700/50 outline-none resize-none overflow-y-auto min-h-[140px] focus:outline-none"></textarea>
                    </div>
                    
                    <div id="modal-request-docs-container" class="space-y-1.5 pt-1 hidden">
                        <label class="text-[11px] font-bold text-gray-400 dark:text-gray-500 block mb-1">المستندات والمرفقات الواردة بالطلب:</label>
                        <div id="modal-attachments" class="text-xs font-semibold flex flex-col gap-2"></div>
                    </div>
                </div>
                
                {{-- العمود الأيسر: الرد القانوني المعتمد أو سبب الرفض الصادر --}}
                <div class="space-y-4 bg-white dark:bg-gray-800/40 p-5 rounded-2xl border border-gray-100 dark:border-gray-700/50">
                    <div class="flex justify-between items-center border-b border-gray-100 dark:border-gray-700 pb-2 mb-1">
                        <div class="flex items-center gap-1.5">
                            <span id="reply-section-indicator" class="w-1.5 h-3 bg-emerald-600 rounded-sm"></span>
                            <h4 id="reply-section-title" class="text-xs font-bold text-emerald-600">الرد القانوني المعتمد</h4>
                        </div>
                        <span id="responder-meta-box" class="text-[11px] text-gray-500 font-semibold">الحالة: <span id="modal-approval-status" class="font-bold"></span></span>
                    </div>

                    <div>
                        <label id="reply-box-label" class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 block mb-1">صيغة الرد والإفادة الصادرة:</label>
                        <textarea id="modal-reply" readonly class="w-full bg-emerald-50/20 dark:bg-emerald-950/10 text-xs font-medium p-3 rounded-xl border border-emerald-100 dark:border-emerald-900/40 text-justify leading-relaxed min-h-[140px] text-gray-700 dark:text-gray-300 resize-none overflow-y-auto shadow-xs"></textarea>
                    </div>
                    
                    <div id="modal-reply-docs-container" class="space-y-1.5 pt-1 hidden">
                        <label class="text-[11px] font-bold text-gray-400 dark:text-gray-500 block mb-1">المستندات والملفات المرفقة مع الرد:</label>
                        <div id="modal-reply-attachments" class="text-xs font-semibold flex flex-col gap-2"></div>
                    </div>
                </div>
                
            </div>
            
            {{-- فوتر المودال - زر إغلاق أزرق تفاعلي --}}
            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/40 border-t border-gray-100 dark:border-gray-700 flex justify-end flex-shrink-0">
                <button onclick="closeDetailsModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-xl text-xs font-bold shadow-sm transition active:scale-[0.98] cursor-pointer">
                    إغلاق الأرشيف
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // دالة الفلترة المتكاملة التي تنطلق فقط عند الضغط على زر "تطبيق"
        function applyFilters() {
            const searchVal = document.getElementById('search-title').value.toLowerCase().trim();
            const categoryVal = document.getElementById('filter-category').value;
            const statusVal = document.getElementById('filter-status').value;
            const dateFrom = document.getElementById('filter-date-from').value;
            const dateTo = document.getElementById('filter-date-to').value;

            const rows = document.querySelectorAll('.record-row');
            let matchCount = 0;

            rows.forEach(row => {
                const rTitle = row.getAttribute('data-title').toLowerCase();
                const rCategory = row.getAttribute('data-category');
                const rStatus = row.getAttribute('data-status');
                const rDate = row.getAttribute('data-date'); 

                const matchSearch = rTitle.includes(searchVal);
                const matchCategory = (categoryVal === 'all' || rCategory === categoryVal);
                
                // مطابقة الحالات مع الفلتر المعتمد للسيناريو (تعديل بحاجة لتعديل إلى مرفوض)
                let matchStatus = (statusVal === 'all');
                if (statusVal === 'قيد الدراسة') {
                    matchStatus = (rStatus === 'قيد الدراسة');
                } else if (statusVal === 'بانتظار الاعتماد') {
                    matchStatus = (rStatus === 'بانتظار الاعتماد');
                } else if (statusVal === 'تم الرد') {
                    matchStatus = (rStatus === 'تم الرد' || rStatus === 'معتمد' || rStatus === 'مقبول');
                } else if (statusVal === 'مرفوض') {
                    matchStatus = (rStatus === 'بحاجة لتعديل' || rStatus.includes('رفض') || rStatus === 'مرفوض');
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

            const emptyRow = document.getElementById('filter-empty-row');
            if (rows.length > 0) {
                if (matchCount === 0) {
                    emptyRow.classList.remove('hidden');
                } else {
                    emptyRow.classList.add('hidden');
                }
            }
        }

        // دالة فتح مودال التفاصيل وتلقي البيانات للمستشار القانوني بستايل المدير المتوازي الفخم
        function openDetailsModal(type, title, user, description, reply, status, rejectionReason, reqAttachments, replyAttachments) {
            document.getElementById('modal-sender').innerText = user;
            document.getElementById('modal-title-text').innerText = title;

            const descTextarea = document.getElementById('modal-desc');
            const replyTextarea = document.getElementById('modal-reply');

            descTextarea.value = description ? description : 'لا يوجد تفاصيل مكتوبة بالطلب.';

            const replyTitle = document.getElementById('reply-section-title');
            const replyIndicator = document.getElementById('reply-section-indicator');
            const replyLabel = document.getElementById('reply-box-label');
            const statusBox = document.getElementById('modal-approval-status');
            const replyDocsBox = document.getElementById('modal-reply-docs-container');

            // إدارة المظهر حيوياً عند وجود حالة المرفوض / بحاجة لتعديل
            if (status === 'بحاجة لتعديل' || status.includes('رفض') || status === 'مرفوض') {
                replyTitle.innerText = "سبب الرفض الرسمي";
                replyTitle.className = "text-xs font-bold text-red-600";
                replyIndicator.className = "w-1.5 h-3 bg-red-600 rounded-sm";
                replyLabel.innerText = "سبب الرفض الملحوظ من المدير القانوني:";
                replyLabel.className = "text-[11px] font-bold text-red-500 block mb-1";
                statusBox.innerHTML = '<span class="text-red-500 flex items-center gap-1"><i class="fas fa-times-circle"></i> مرفوض</span>';
                replyDocsBox.classList.add('hidden');

                replyTextarea.value = rejectionReason ? rejectionReason : 'يرجى مراجعة الصياغة والتعديل حسب ملحوظات المدير.';
                replyTextarea.className = "w-full bg-red-50/20 dark:bg-red-950/10 text-xs font-medium p-3 rounded-xl border border-red-100 dark:border-red-900/40 text-justify leading-relaxed min-h-[140px] text-gray-700 dark:text-gray-300 resize-none overflow-y-auto shadow-xs";
            } else {
                replyTitle.innerText = "الرد القانوني المعتمد";
                replyTitle.className = "text-xs font-bold text-emerald-600";
                replyIndicator.className = "w-1.5 h-3 bg-emerald-600 rounded-sm";
                replyLabel.innerText = "صيغة الرد والإفادة الصادرة:";
                replyLabel.className = "text-[11px] font-bold text-emerald-600 dark:text-emerald-400 block mb-1";
                replyDocsBox.classList.remove('hidden');

                if (status === 'تم الرد' || status === 'معتمد' || status === 'مقبول') {
                    statusBox.innerHTML = '<span class="text-green-600 dark:text-green-400 flex items-center gap-1"><i class="fas fa-check-circle"></i> تم الرد والاعتماد</span>';
                } else if (status === 'بانتظار الاعتماد') {
                    statusBox.innerHTML = '<span class="text-amber-500 flex items-center gap-1"><i class="fas fa-clock"></i> بانتظار الاعتماد</span>';
                } else {
                    statusBox.innerHTML = '<span class="text-indigo-500 flex items-center gap-1"><i class="fas fa-book-reader"></i> قيد الدراسة</span>';
                }

                replyTextarea.value = reply ? reply : 'لم يتم صياغة الرد وتدوينه بعد.';
                replyTextarea.className = "w-full bg-emerald-50/20 dark:bg-emerald-950/10 text-xs font-medium p-3 rounded-xl border border-emerald-100 dark:border-emerald-900/40 text-justify leading-relaxed min-h-[140px] text-gray-700 dark:text-gray-300 resize-none overflow-y-auto shadow-xs";
            }

            // عرض ملفات الطلب الأساسي على اليمين
            renderFilesList(reqAttachments, 'modal-request-docs-container', 'modal-attachments');

            // عرض ملفات الرد على اليسار
            if (status === 'تم الرد' || status === 'معتمد' || status === 'مقبول' || status === 'بانتظار الاعتماد') {
                renderFilesList(replyAttachments, 'modal-reply-docs-container', 'modal-reply-attachments');
            } else {
                document.getElementById('modal-reply-docs-container').classList.add('hidden');
            }

            document.getElementById('details-modal').classList.remove('hidden');

            setTimeout(() => {
                adjustModalTextareaHeight(descTextarea);
                adjustModalTextareaHeight(replyTextarea);
            }, 50);
        }

        // دالة تنظيم جلب المرفقات حياً داخل المودال الجديد المطور
        function renderFilesList(attachmentsJson, containerId, listId) {
            const container = document.getElementById(containerId);
            const list = document.getElementById(listId);
            list.innerHTML = '';
            
            if (attachmentsJson && attachmentsJson.trim() !== '') {
                try {
                    const filesArray = JSON.parse(attachmentsJson);
                    if (Array.isArray(filesArray) && filesArray.length > 0) {
                        filesArray.forEach(file => {
                            list.innerHTML += `
                                <a href="/storage/${file.path}" target="_blank" class="inline-flex items-center gap-2 bg-white dark:bg-gray-800 hover:bg-blue-50/50 dark:hover:bg-blue-950/20 border dark:border-gray-700 px-3 py-2 rounded-xl text-[11px] font-semibold text-gray-700 dark:text-gray-300 w-full truncate transition shadow-xs">
                                    <i class="fas fa-paperclip text-blue-500 text-xs"></i> <span class="truncate text-blue-600 dark:text-blue-400 hover:underline">${file.name}</span>
                                </a>`;
                        });
                        container.classList.remove('hidden');
                    } else {
                        container.classList.add('hidden');
                    }
                } catch(e) {
                    container.classList.add('hidden');
                }
            } else {
                container.classList.add('hidden');
            }
        }

        function adjustModalTextareaHeight(el) {
            el.style.height = 'auto';
            el.style.height = el.scrollHeight + 'px';
        }

        function closeDetailsModal() {
            document.getElementById('details-modal').classList.add('hidden');
        }

        window.addEventListener('click', function(event) {
            const detailsModal = document.getElementById('details-modal');
            if (event.target === detailsModal) {
                closeDetailsModal();
            }
        });
    </script>
@endpush
@extends('layouts.LegalManager')

@section('title', 'مراجعة طلبات الاعتماد | منصة الإدارة القانونية')

@section('content')
    <main class="container mx-auto px-6 py-10 flex-grow max-w-5xl">
        
        {{-- رأس الصفحة --}}
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-gray-200 dark:border-gray-700 pb-5">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">طلبات الاعتماد القانونية</h1>
                <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">استعراض ومراجعة الردود المرفوعة من الموظفين لاعتمادها أو رفضها</p>
            </div>
            
            @php
                $approvalCount = $consultations->where('status', 'بانتظار الاعتماد')->count();
            @endphp
            <span id="total-badge" class="self-start sm:self-center text-xs bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 font-bold px-3 py-1.5 rounded-xl border border-purple-100 dark:border-purple-800 shadow-sm">
                بانتظار الاعتماد النهائي: {{ $approvalCount }} معاملة
            </span>
        </div>

        {{-- شريط التصفية والبحث المتقدم الموحد --}}
        <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
            <div class="flex flex-wrap items-end gap-3 w-full">
                <div class="flex-1 min-w-[150px] flex flex-col gap-1">
                    <label class="text-[11px] font-bold text-gray-400 dark:text-gray-500">البحث بالعنوان</label>
                    <div class="flex items-center bg-gray-50 dark:bg-gray-700/50 px-2.5 py-1.5 rounded-xl border border-gray-100 dark:border-gray-600">
                        <i class="fas fa-search text-gray-400 ml-2 text-[10px]"></i>
                        <input type="text" id="search-title" placeholder="عنوان المعاملة..." class="w-full bg-transparent text-xs outline-none text-gray-700 dark:text-gray-200">
                    </div>
                </div>

                <div class="w-32 flex flex-col gap-1">
                    <label class="text-[11px] font-bold text-gray-400 dark:text-gray-500">نوع العملية</label>
                    <select id="filter-category" class="w-full p-1.5 bg-gray-50 dark:bg-gray-700/50 text-xs font-semibold rounded-xl border border-gray-100 dark:border-gray-600 text-gray-600 dark:text-gray-300 outline-none">
                        <option value="all">كل العمليات</option>
                        <option value="استشارة">استشارات</option>
                        <option value="عقد">عقود</option>
                        <option value="قضية">قضايا</option>
                    </select>
                </div>

                <div class="w-36 flex flex-col gap-1">
                    <label class="text-[11px] font-bold text-gray-400 dark:text-gray-500">من تاريخ</label>
                    <input type="date" id="filter-date-from" class="w-full p-1.5 bg-gray-50 dark:bg-gray-700/50 text-xs font-semibold rounded-xl border border-gray-100 dark:border-gray-600 text-gray-600 dark:text-gray-300 outline-none font-mono">
                </div>

                <div class="w-36 flex flex-col gap-1">
                    <label class="text-[11px] font-bold text-gray-400 dark:text-gray-500">إلى تاريخ</label>
                    <input type="date" id="filter-date-to" class="w-full p-1.5 bg-gray-50 dark:bg-gray-700/50 text-xs font-semibold rounded-xl border border-gray-100 dark:border-gray-600 text-gray-600 dark:text-gray-300 outline-none font-mono">
                </div>

                <button onclick="applyManagerApprovalFilters()" class="bg-wadimakkah-dark hover:bg-blue-800 text-white px-5 py-2 rounded-xl text-xs font-bold shadow-sm transition cursor-pointer flex items-center justify-center h-[34px]">
                    تطبيق
                </button>
            </div>
        </div>

        {{-- جدول الاستشارات المرفوعة من الموظفين القانونيين --}}
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="overflow-x-auto">
                <table class="w-full text-right border-collapse">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-700 text-xs font-bold text-gray-400 uppercase tracking-wider">
                            <th class="pb-3">نوع العملية</th>
                            <th class="pb-3">عنوان المعاملة</th>
                            <th class="pb-3">مقدم الطلب</th>
                            <th class="pb-3">تاريخ ووقت الرفع للاعتماد</th>
                            <th class="pb-3 text-center">الحالة الحالية</th>
                            <th class="pb-3 text-left pl-4">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody id="approval-table-body" class="text-sm font-medium divide-y divide-gray-50 dark:divide-gray-700/50">
                        @php $hasPendingApproval = false; @endphp
                        @foreach($consultations as $item)
                            @if($item->status === 'بانتظار الاعتماد')
                                @php
                                    $hasPendingApproval = true;
                                    $dateRaw = is_string($item->created_at) ? date('Y-m-d', strtotime($item->created_at)) : $item->created_at->format('Y-m-d');
                                    $currentCategory = $item->type_category ?? 'استشارة';

                                    $saudiTime = \Carbon\Carbon::parse($item->created_at)->timezone('Asia/Riyadh');
                                    $formattedDateTimeSaudi = $saudiTime->format('Y/m/d H:i');

                                    $legalEmployeeName = 'المستشار القانوني المختص';
                                    if (!empty($item->assigned_to)) {
                                        $legalUser = DB::table('users')->where('user_id', $item->assigned_to)->first();
                                        if ($legalUser) { $legalEmployeeName = $legalUser->name; }
                                    }
                                    $currentStatusName = 'قيد الاعتماد';
                                @endphp
                                <tr class="approval-record-row hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition"
                                    data-title="{{ $item->title }}"
                                    data-category="{{ $currentCategory }}"
                                    data-status="{{ $currentStatusName }}"
                                    data-date="{{ $dateRaw }}">
                                    
                                    <td class="py-4">
                                        <span class="px-2.5 py-1 bg-green-50 dark:bg-green-950/40 text-green-700 dark:text-green-400 text-xs font-bold rounded-lg border border-green-100 dark:border-green-900/60">استشارة</span>
                                    </td>
                                    <td class="py-4 text-gray-800 dark:text-gray-200 font-bold max-w-[200px] truncate">{{ $item->title }}</td>
                                    <td class="py-4 text-xs text-gray-600 dark:text-gray-400">{{ $item->user_name }}</td>
                                    <td class="py-4 text-xs text-gray-500 dark:text-gray-400 font-mono">{{ $formattedDateTimeSaudi }}</td>
                                    <td class="py-4 text-center">
                                        <span class="px-2.5 py-0.5 bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400 text-xs font-bold rounded-md border border-purple-200 dark:border-purple-900/40">قيد الاعتماد</span>
                                    </td>
                                    <td class="py-4 text-left whitespace-nowrap pr-4">
                                        <button onclick="openManagerModal('{{ $item->consultation_id }}', '{{ addslashes($item->title) }}', '{{ addslashes($item->type) }}', '{{ addslashes($item->description) }}', '{{ $item->attachment }}', '{{ addslashes($item->reply) }}', '{{ $currentStatusName }}', '{{ addslashes($legalEmployeeName) }}', '{{ $item->reply_attachment ?? '' }}')"
                                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-4 py-2 rounded-xl shadow-xs transition active:scale-[0.98] cursor-pointer inline-flex items-center gap-2">
                                            <i class="fas fa-balance-scale text-xs"></i> مراجعة واعتماد الرد
                                        </button>
                                    </td>
                                </tr>
                            @endif
                        @endforeach

                        @if(!$hasPendingApproval)
                            <tr id="approval-no-data">
                                <td colspan="6" class="py-16 text-center text-sm text-gray-400 dark:text-gray-500">
                                    <div class="w-16 h-16 bg-gray-50 dark:bg-gray-800/50 rounded-full flex items-center justify-center mx-auto mb-3 text-gray-300 dark:text-gray-600 border dark:border-gray-700">
                                        <i class="fas fa-check-double text-xl"></i>
                                    </div>
                                    <p class="font-bold text-gray-700 dark:text-gray-300 mb-0.5">الصندوق فارغ تماماً</p>
                                    لا توجد أي مذكرات ردود مرفوعة من المستشارين بانتظار الاعتماد حالياً.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    {{-- المودال المطور --}}
    <div id="manager-modal" class="hidden fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4 backdrop-blur-md animate-fadeIn">
        <div class="bg-white dark:bg-gray-900 rounded-[2rem] border border-gray-100 dark:border-gray-800 w-full max-w-5xl h-[85vh] flex flex-col shadow-2xl overflow-hidden transform scale-95 transition-all duration-300">
            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-b border-gray-100 dark:border-gray-700/80 flex justify-between items-center flex-shrink-0">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 bg-blue-50 dark:bg-blue-900/30 rounded-xl flex items-center justify-center text-blue-600 dark:text-blue-400"><i class="fas fa-gavel text-sm"></i></div>
                    <div>
                        <h3 class="font-bold text-sm text-gray-800 dark:text-gray-100">مراجعة مذكرات الردود واعتمادها</h3>
                        <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5">الاطلاع على تفاصيل الطلب الوارد وتدقيق رد المستشار القانوني</p>
                    </div>
                </div>
                <button type="button" onclick="closeManagerModal()" class="w-8 h-8 rounded-xl bg-gray-100 dark:bg-gray-700 hover:bg-red-500 hover:text-white dark:hover:bg-red-600 transition flex items-center justify-center text-gray-500 dark:text-gray-400"><i class="fas fa-times text-xs"></i></button>
            </div>
            
            <form id="action-form" method="POST" class="flex-grow overflow-hidden flex flex-col">
                @csrf
                <div class="flex-grow overflow-y-auto p-6 grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                    <div class="space-y-4 bg-gray-50/50 dark:bg-gray-800/20 p-5 rounded-2xl border border-gray-100 dark:border-gray-800/60">
                        <div class="flex items-center gap-1.5 border-b border-gray-100 dark:border-gray-700 pb-2 mb-1">
                            <span class="w-1.5 h-3 bg-blue-600 rounded-sm"></span>
                            <h4 class="text-xs font-bold text-gray-700 dark:text-gray-300">تفاصيل الطلب القانوني الأساسي</h4>
                        </div>
                        <div>
                            <label class="text-[11px] font-bold text-gray-400 dark:text-gray-500 block mb-1">موضوع الطلب / العنوان</label>
                            <p id="modal-title-text" class="font-bold text-xs text-gray-800 dark:text-gray-200 bg-white dark:bg-gray-800 p-3 rounded-xl border border-gray-100 dark:border-gray-700/50 shadow-inner"></p>
                        </div>
                        <div>
                            <label class="text-[11px] font-bold text-gray-400 dark:text-gray-500 block mb-1">شرح ومضمون الطلب</label>
                            <textarea id="modal-desc" readonly class="w-full bg-white dark:bg-gray-800 text-xs font-medium text-gray-600 dark:text-gray-300 leading-relaxed text-justify p-3 rounded-xl border border-gray-100 dark:border-gray-700/50 outline-none resize-none overflow-y-auto min-h-[140px] focus:outline-none"></textarea>
                        </div>
                        <div>
                            <label class="text-[11px] font-bold text-gray-400 dark:text-gray-500 block mb-1">المستندات والمرفقات الواردة بالطلب الأصلي:</label>
                            <div id="modal-attachments" class="text-xs font-semibold flex flex-col gap-2"></div>
                        </div>
                    </div>

                    <div class="space-y-4 bg-white dark:bg-gray-800/40 p-5 rounded-2xl border border-gray-100 dark:border-gray-700/50">
                        <div class="flex justify-between items-center border-b border-gray-100 dark:border-gray-700 pb-2 mb-1">
                            <div class="flex items-center gap-1.5">
                                <span class="w-1.5 h-3 bg-purple-600 rounded-sm"></span>
                                <h4 class="text-xs font-bold text-gray-700 dark:text-gray-300">مراجعة مذكرة الرد والاعتماد</h4>
                            </div>
                            <span class="text-[11px] text-gray-500 font-semibold">بواسطة: <span id="modal-legal-employee-name" class="text-gray-800 dark:text-gray-200 font-bold"></span></span>
                        </div>
                        <div>
                            <label class="text-[11px] font-bold text-purple-600 dark:text-purple-400 block mb-1">مسودة رد المستشار المرفوعة:</label>
                            <textarea id="modal-reply" readonly class="w-full bg-purple-50/10 dark:bg-purple-950/5 text-xs font-medium p-3 rounded-xl border border-purple-100/60 dark:border-purple-900/30 text-justify leading-relaxed min-h-[140px] text-gray-700 dark:text-gray-300 resize-none overflow-y-auto"></textarea>
                        </div>
                        <div id="modal-reply-attachments-section" class="hidden">
                            <label class="text-[11px] font-bold text-gray-400 dark:text-gray-500 block mb-1">المستندات والملفات المرفقة مع الرد القانوني:</label>
                            <div id="modal-reply-attachments" class="text-xs font-semibold flex flex-col gap-2"></div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-800 p-3.5 rounded-xl border border-gray-100 dark:border-gray-700 space-y-2.5">
                            <label class="text-[11px] font-bold text-gray-400 dark:text-gray-500 block">الإجراء المتخذ على المعاملة</label>
                            <div class="flex gap-5 text-xs font-bold text-gray-700 dark:text-gray-300">
                                <label class="flex items-center gap-2 cursor-pointer select-none">
                                    <input type="radio" name="manager_action" value="approve" checked onchange="toggleRejectionReason(this.value)" class="w-4 h-4 text-blue-600 accent-blue-600"> <span>اعتماد الرد ونشره</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer select-none">
                                    <input type="radio" name="manager_action" value="reject" onchange="toggleRejectionReason(this.value)" class="w-4 h-4 text-red-600 accent-red-600"> <span>رفض ومراجعة التوجيه</span>
                                </label>
                            </div>
                        </div>
                        <div id="rejection-reason-container" class="hidden transition-all duration-300">
                            <label class="text-[11px] font-bold text-gray-400 dark:text-gray-500 block mb-1">توجيهات الإدارة القانونية / مبررات الرفض: <span class="text-red-500">*</span></label>
                            <textarea id="manager_notes" name="manager_notes" placeholder="اكتب مبررات وأسباب إعادة المعاملة للمستشار القانوني هنا (إجباري)..." class="w-full p-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-xs outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 placeholder-gray-400 text-gray-700 dark:text-gray-200 min-h-[70px] shadow-sm resize-none"></textarea>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-3.5 bg-gray-50 dark:bg-gray-800/40 border-t border-gray-100 dark:border-gray-700 flex justify-end gap-3 flex-shrink-0">
                    <button type="button" onclick="closeManagerModal()" class="px-4 py-2 rounded-xl text-xs font-bold bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition cursor-pointer">إلغاء</button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-xl text-xs font-bold transition shadow-md active:scale-[0.98] cursor-pointer flex items-center gap-1.5"><i class="fas fa-check-circle"></i> حفظ وتأكيد القرار النهائي</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function applyManagerApprovalFilters() {
        const searchVal = document.getElementById('search-title').value.toLowerCase().trim();
        const categoryVal = document.getElementById('filter-category').value;
        const dateFrom = document.getElementById('filter-date-from').value;
        const dateTo = document.getElementById('filter-date-to').value;
        const rows = document.querySelectorAll('.approval-record-row');
        let matchCount = 0;

        rows.forEach(row => {
            const matches = row.getAttribute('data-title').toLowerCase().includes(searchVal) &&
                           (categoryVal === 'all' || row.getAttribute('data-category') === categoryVal) &&
                           (!dateFrom || row.getAttribute('data-date') >= dateFrom) &&
                           (!dateTo || row.getAttribute('data-date') <= dateTo);
            row.classList.toggle('hidden', !matches);
            if(matches) matchCount++;
        });
    }

    function toggleRejectionReason(actionValue) {
        const container = document.getElementById('rejection-reason-container');
        const textarea = document.getElementById('manager_notes');
        if (actionValue === 'reject') {
            container.classList.remove('hidden');
            textarea.required = true;
        } else {
            container.classList.add('hidden');
            textarea.required = false;
            textarea.value = '';
        }
    }

    function openManagerModal(id, title, type, desc, attach, reply, status, legalEmployeeName, replyAttachmentsJson) {
        document.getElementById('modal-title-text').innerText = title;
        document.getElementById('modal-desc').value = desc;
        document.getElementById('modal-legal-employee-name').innerText = legalEmployeeName;
        
        const attachDiv = document.getElementById('modal-attachments');
        attachDiv.innerHTML = '';
        if (attach && attach.trim() !== '') {
            try {
                const parsed = JSON.parse(attach);
                if (Array.isArray(parsed) && parsed.length > 0) {
                    parsed.forEach(file => {
                        attachDiv.innerHTML += `<a href="/storage/${file.path}" target="_blank" class="inline-flex items-center gap-1.5 text-blue-600 dark:text-blue-400 hover:underline bg-gray-50 dark:bg-gray-800 px-2.5 py-1.5 rounded-xl border dark:border-gray-700 text-[11px] font-bold shadow-xs w-full truncate"><i class="fas fa-paperclip text-gray-400"></i> ${file.name}</a> `;
                    });
                } else { attachDiv.innerHTML = '<span class="text-gray-400 text-[11px] font-normal">لا توجد مستندات مرفقة</span>'; }
            } catch(e) { attachDiv.innerHTML = '<span class="text-gray-400 text-[11px] font-normal">لا توجد مستندات مرفقة</span>'; }
        } else { attachDiv.innerHTML = '<span class="text-gray-400 text-[11px] font-normal">لا توجد مستندات مرفقة</span>'; }

        const replyAttachmentsSection = document.getElementById('modal-reply-attachments-section');
        const replyAttachmentsDiv = document.getElementById('modal-reply-attachments');
        replyAttachmentsDiv.innerHTML = '';

        if (replyAttachmentsJson && replyAttachmentsJson.trim() !== '') {
            try {
                const parsedReplyAttach = JSON.parse(replyAttachmentsJson);
                if (Array.isArray(parsedReplyAttach) && parsedReplyAttach.length > 0) {
                    parsedReplyAttach.forEach(file => {
                        replyAttachmentsDiv.innerHTML += `<a href="/storage/${file.path}" target="_blank" class="inline-flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400 hover:underline bg-gray-50 dark:bg-gray-800 px-2.5 py-1.5 rounded-xl border dark:border-gray-700 text-[11px] font-bold shadow-xs w-full truncate"><i class="fas fa-paperclip text-gray-400"></i> ${file.name}</a> `;
                    });
                    replyAttachmentsSection.classList.remove('hidden');
                } else { replyAttachmentsSection.classList.add('hidden'); }
            } catch(e) { replyAttachmentsSection.classList.add('hidden'); }
        } else { replyAttachmentsSection.classList.add('hidden'); }

        document.getElementById('modal-reply').value = reply;
        document.getElementById('action-form').action = `/manager/consultations/${id}/approve`;
        document.getElementsByName('manager_action')[0].checked = true;
        toggleRejectionReason('approve');
        document.getElementById('manager-modal').classList.remove('hidden');
    }
    
    function closeManagerModal() { 
        document.getElementById('manager-modal').classList.add('hidden'); 
        document.getElementById('action-form').reset();
    }
</script>
@endpush
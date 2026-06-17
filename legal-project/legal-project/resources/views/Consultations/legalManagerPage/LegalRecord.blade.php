@extends('layouts.LegalManager')

@section('title', 'الأرشيف القانوني | منصة الإدارة القانونية')

@section('content')
    <main class="container mx-auto px-6 py-10 flex-grow max-w-5xl">
        
        <div class="mb-6 border-b border-gray-200 dark:border-gray-700 pb-5">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">الأرشيف القانوني العام</h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">سجل تاريخي شامل لكافة المعاملات التي تم إغلاقها أو اعتماد ردودها</p>
        </div>

        {{-- شريط التصفية والبحث المتقدم --}}
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
                        <option value="استشارة">استشارة</option>
                        <option value="عقد">عقد</option>
                        <option value="قضية">قضية</option>
                    </select>
                </div>

                <div class="w-36 flex flex-col gap-1">
                    <label class="text-[11px] font-bold text-gray-400 dark:text-gray-500">الحالة</label>
                    <select id="filter-status" class="w-full p-1.5 bg-gray-50 dark:bg-gray-700/50 text-xs font-semibold rounded-xl border border-gray-100 dark:border-gray-600 text-gray-600 dark:text-gray-300 outline-none">
                        <option value="all">كل الحالات</option>
                        <option value="تم الرد">تم الرد والاعتماد</option>
                        <option value="مرفوض">مرفوض</option>
                    </select>
                </div>

                <div class="w-32 flex flex-col gap-1">
                    <label class="text-[11px] font-bold text-gray-400 dark:text-gray-500">من تاريخ</label>
                    <input type="date" id="filter-date-from" class="w-full p-1.5 bg-gray-50 dark:bg-gray-700/50 text-xs font-semibold rounded-xl border border-gray-100 dark:border-gray-600 text-gray-600 dark:text-gray-300 outline-none">
                </div>

                <div class="w-32 flex flex-col gap-1">
                    <label class="text-[11px] font-bold text-gray-400 dark:text-gray-500">إلى تاريخ</label>
                    <input type="date" id="filter-date-to" class="w-full p-1.5 bg-gray-50 dark:bg-gray-700/50 text-xs font-semibold rounded-xl border border-gray-100 dark:border-gray-600 text-gray-600 dark:text-gray-300 outline-none">
                </div>

                <button onclick="applyFilters()" class="bg-wadimakkah-dark hover:bg-blue-800 text-white px-5 py-2 rounded-xl text-xs font-bold shadow-sm transition cursor-pointer flex items-center justify-center h-[34px]">
                    تطبيق
                </button>
            </div>
        </div>

        {{-- جدول الأرشيف القانوني --}}
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="overflow-x-auto">
                <table class="w-full text-right border-collapse">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-700 text-xs font-bold text-gray-400 uppercase tracking-wider">
                            <th class="pb-3">نوع العملية</th>
                            <th class="pb-3">عنوان المعاملة</th>
                            <th class="pb-3">تاريخ الاعتماد</th>
                            <th class="pb-3 text-center">الحالة</th>
                            <th class="pb-3 text-left pl-4">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody id="records-table-body" class="text-sm font-medium divide-y divide-gray-50 dark:divide-gray-700/50">
                        @forelse($allRequests as $record)
                            @php 
                                $cat = $record->type_category ?? 'استشارة'; 
                                
                                // جلب حقيقي لبيانات مرسل ومستقبل الطلب لضمان دقة البيانات المعروضة في المودال
                                $senderUser = DB::table('users')->where('user_id', $record->user_id)->first();
                                $senderName = $senderUser ? $senderUser->name : 'الموظف المرسل';
                                
                                $responderName = 'المستشار القانوني المختص';
                                if (!empty($record->assigned_to)) {
                                    $legalUser = DB::table('users')->where('user_id', $record->assigned_to)->first();
                                    if ($legalUser) {
                                        $responderName = $legalUser->name;
                                    }
                                }
                            @endphp
                            <tr class="record-row hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition" 
                                data-title="{{ $record->title }}" data-category="{{ $cat }}" data-status="{{ $record->status }}" data-date="{{ \Carbon\Carbon::parse($record->updated_at)->format('Y-m-d') }}">
                                
                                <td class="py-4">
                                    <span class="px-2.5 py-1 text-xs font-bold rounded-lg border 
                                        {{ $cat == 'عقد' ? 'bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-400 border-blue-100 dark:border-blue-900/60' : 
                                          ($cat == 'قضية' ? 'bg-yellow-50 dark:bg-yellow-950/40 text-yellow-700 dark:text-yellow-400 border-yellow-100 dark:border-yellow-900/60' : 
                                          'bg-green-50 dark:bg-green-950/40 text-green-700 dark:text-green-400 border-green-100 dark:border-green-900/60') }}">
                                        {{ $cat }}
                                    </span>
                                </td>
                                <td class="py-4 text-gray-800 dark:text-gray-200 font-bold">{{ $record->title }}</td>
                                <td class="py-4 text-xs text-gray-500 dark:text-gray-400 font-mono">{{ \Carbon\Carbon::parse($record->updated_at)->format('Y/m/d') }}</td>
                                <td class="py-4 text-center">
                                    @if($record->status == 'تم الرد')
                                        <span class="px-2 py-0.5 bg-green-50 dark:bg-green-950/40 text-green-600 dark:text-green-400 text-xs font-bold rounded-md border border-green-200">تم الاعتماد</span>
                                    @else
                                        <span class="px-2 py-0.5 bg-red-50 dark:bg-red-950/40 text-red-600 dark:text-red-400 text-xs font-bold rounded-md border border-red-200">مرفوض</span>
                                    @endif
                                </td>
                                <td class="py-4 text-left pl-4">
                                    <button onclick="openDetailsModal('{{ addslashes($record->title) }}', '{{ addslashes($record->description) }}', '{{ addslashes($record->reply) }}', '{{ $record->status }}', '{{ addslashes($senderName) }}', '{{ addslashes($responderName) }}', '{{ $record->attachment ?? '' }}', '{{ $record->reply_attachment ?? '' }}', '{{ addslashes($record->rejection_reason ?? '') }}')"
                                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-4 py-2 rounded-xl shadow-sm transition active:scale-[0.98] cursor-pointer inline-flex items-center gap-2">
                                        <i class="fas fa-eye text-xs"></i> عرض التفاصيل
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr id="no-records-row"><td colspan="5" class="py-12 text-center text-gray-400">لا توجد سجلات مؤرشفة.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    {{-- 🌟 مودال عرض التفاصيل المطور بستايل صفحة المراجعة والقراءة والوضوح التام 🌟 --}}
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
                    
                    <div>
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
                        <span id="responder-meta-box" class="text-[11px] text-gray-500 font-semibold">بواسطة: <span id="modal-responder" class="text-gray-700 dark:text-gray-300 font-bold"></span></span>
                    </div>

                    <div>
                        <label id="reply-box-label" class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 block mb-1">صيغة الرد والإفادة الصادرة:</label>
                        <textarea id="modal-reply" readonly class="w-full bg-emerald-50/20 dark:bg-emerald-950/10 text-xs font-medium p-3 rounded-xl border border-emerald-100 dark:border-emerald-900/40 text-justify leading-relaxed min-h-[140px] text-gray-700 dark:text-gray-300 resize-none overflow-y-auto shadow-xs"></textarea>
                    </div>
                    
                    <div id="modal-reply-docs-container">
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
        function applyFilters() {
            const searchVal = document.getElementById('search-title').value.toLowerCase().trim();
            const categoryVal = document.getElementById('filter-category').value;
            const statusVal = document.getElementById('filter-status').value;
            const dateFrom = document.getElementById('filter-date-from').value;
            const dateTo = document.getElementById('filter-date-to').value;
            const rows = document.querySelectorAll('.record-row');
            let matchCount = 0;
            rows.forEach(row => {
                const matches = row.getAttribute('data-title').toLowerCase().includes(searchVal) &&
                               (categoryVal === 'all' || row.getAttribute('data-category') === categoryVal) &&
                               (statusVal === 'all' || row.getAttribute('data-status') === statusVal) &&
                               (!dateFrom || row.getAttribute('data-date') >= dateFrom) &&
                               (!dateTo || row.getAttribute('data-date') <= dateTo);
                row.classList.toggle('hidden', !matches);
                if(matches) matchCount++;
            });
        }
        
        function openDetailsModal(title, desc, reply, status, senderName, responderName, reqAttachments, replyAttachments, rejectionReason) {
            document.getElementById('modal-title-text').innerText = title;
            document.getElementById('modal-desc').value = desc;
            document.getElementById('modal-sender').innerText = senderName;
            document.getElementById('modal-responder').innerText = responderName;

            const replyTitle = document.getElementById('reply-section-title');
            const replyIndicator = document.getElementById('reply-section-indicator');
            const replyLabel = document.getElementById('reply-box-label');
            const responderMeta = document.getElementById('responder-meta-box');
            const replyTextarea = document.getElementById('modal-reply');
            const replyDocsBox = document.getElementById('modal-reply-docs-container');

            // التمييز الكامل عند وجود حالة الرفض (إظهار سبب الرفض فقط بدلاً من الرد القانوني)
            if (status === 'مرفوض' || status.includes('refuse') || status.includes('رفض')) {
                replyTitle.innerText = "سبب الرفض الرسمي";
                replyTitle.className = "text-xs font-bold text-red-600";
                replyIndicator.className = "w-1.5 h-3 bg-red-600 rounded-sm";
                replyLabel.innerText = "سبب الرفض الرسمي الصادر عن الإدارة:";
                replyLabel.className = "text-[11px] font-bold text-red-500 block mb-1";
                responderMeta.classList.add('hidden'); // إخفاء "بواسطة المستشار" في الرفض
                replyDocsBox.classList.add('hidden');  // إخفاء المرفقات التابعة للرد

                replyTextarea.value = rejectionReason ? rejectionReason : 'تم رفض المعاملة وإغلاقها من قبل المدير القانوني.';
                replyTextarea.className = "w-full bg-red-50/20 dark:bg-red-950/10 text-xs font-medium p-3 rounded-xl border border-red-100 dark:border-red-900/40 text-justify leading-relaxed min-h-[140px] text-gray-700 dark:text-gray-300 resize-none overflow-y-auto shadow-xs";
            } else {
                // الحالة العادية (تم الرد والاعتماد)
                replyTitle.innerText = "الرد القانوني المعتمد";
                replyTitle.className = "text-xs font-bold text-emerald-600";
                replyIndicator.className = "w-1.5 h-3 bg-emerald-600 rounded-sm";
                replyLabel.innerText = "صيغة الرد والإفادة الصادرة:";
                replyLabel.className = "text-[11px] font-bold text-emerald-600 dark:text-emerald-400 block mb-1";
                responderMeta.classList.remove('hidden');
                replyDocsBox.classList.remove('hidden');

                replyTextarea.value = reply;
                replyTextarea.className = "w-full bg-emerald-50/20 dark:bg-emerald-950/10 text-xs font-medium p-3 rounded-xl border border-emerald-100 dark:border-emerald-900/40 text-justify leading-relaxed min-h-[140px] text-gray-700 dark:text-gray-300 resize-none overflow-y-auto shadow-xs";
            }

            // عرض مرفقات الطلب الأساسي على اليمين
            renderFiles(reqAttachments, 'modal-attachments');

            // عرض مرفقات الرد القانوني على اليسار (في حال لم يكن الطلب مرفوضاً)
            if (!(status === 'مرفوض' || status.includes('رفض'))) {
                renderFiles(replyAttachments, 'modal-reply-attachments');
            }

            document.getElementById('details-modal').classList.remove('hidden');
        }

        // دالة مساعدة لتنظيم وعرض المرفقات داخل الجافا سكريبت بشكل واضح ونظيف
        function renderFiles(attachmentsJson, targetDivId) {
            const targetDiv = document.getElementById(targetDivId);
            targetDiv.innerHTML = '';
            
            if (attachmentsJson && attachmentsJson.trim() !== '') {
                try {
                    const parsed = JSON.parse(attachmentsJson);
                    if (Array.isArray(parsed) && parsed.length > 0) {
                        parsed.forEach(file => {
                            targetDiv.innerHTML += `
                                <a href="/storage/${file.path}" target="_blank" class="inline-flex items-center gap-1.5 text-blue-600 dark:text-blue-400 hover:underline bg-white dark:bg-gray-800 px-2.5 py-1.5 rounded-xl border dark:border-gray-700 text-[11px] font-bold shadow-xs w-full truncate">
                                    <i class="fas fa-paperclip text-gray-400"></i> ${file.name}
                                </a>`;
                        });
                    } else {
                        targetDiv.innerHTML = '<span class="text-gray-400 text-[11px] font-normal">لا توجد مستندات مرفقة</span>';
                    }
                } catch(e) {
                    targetDiv.innerHTML = '<span class="text-gray-400 text-[11px] font-normal">لا توجد مستندات مرفقة</span>';
                }
            } else {
                targetDiv.innerHTML = '<span class="text-gray-400 text-[11px] font-normal">لا توجد مستندات مرفقة</span>';
            }
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
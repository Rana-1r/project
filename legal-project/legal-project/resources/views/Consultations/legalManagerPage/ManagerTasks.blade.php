@extends('layouts.LegalManager')

@section('title', 'إسناد وتوجيه الاستشارات الواردة | منصة الإدارة القانونية')

@section('content')
    <main class="container mx-auto px-6 py-10 flex-grow max-w-5xl">
        
        {{-- رأس الصفحة --}}
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-gray-200 dark:border-gray-700 pb-5">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">إسناد وتوجيه الاستشارات الواردة</h1>
                <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">المعاملات الواردة من موظفي الإدارات الداخلية وإسنادها وتكليف الموظفين المختصين بها لبدء دراستها</p>
            </div>
            
            <span id="pending-badge" class="self-start sm:self-center text-xs bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 font-bold px-3 py-1.5 rounded-xl border border-amber-200 dark:border-amber-900/60 shadow-xs">
                بانتظار الإسناد: <span id="pending-count">{{ count($incomingConsultations) }}</span> معاملة
            </span>
        </div>

        {{-- شريط التصفية والبحث المتقدم الموحد تماماً بنفس حجم وستايل الموظف الداخلي --}}
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
                <button onclick="applyManagerFilters()" class="bg-wadimakkah-dark hover:bg-blue-800 text-white px-5 py-2 rounded-xl text-xs font-bold shadow-sm transition cursor-pointer flex items-center justify-center h-[34px]">
                    تطبيق
                </button>

            </div>
        </div>

        {{-- جدول الاستشارات والمعاملات الواردة الجديدة --}}
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="overflow-x-auto">
                <table id="main-tasks-table" class="w-full text-right border-collapse">
                    <thead id="main-tasks-thead">
                        <tr class="border-b border-gray-100 dark:border-gray-700 text-xs font-bold text-gray-400 uppercase tracking-wider">
                            <th class="pb-3">نوع العملية</th>
                            <th class="pb-3">عنوان المعاملة</th>
                            <th class="pb-3">اسم المرسل</th>
                            <th class="pb-3">تاريخ الورود</th>
                            <th class="pb-3 text-center">الحالة</th>
                            <th class="pb-3 text-left pl-4">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody id="manager-tasks-body" class="text-sm font-medium divide-y divide-gray-50 dark:divide-gray-700/50">
                        @forelse($incomingConsultations as $consultation)
                            @php
                                $dateRaw = is_string($consultation->created_at) ? date('Y-m-d', strtotime($consultation->created_at)) : $consultation->created_at->format('Y-m-d');
                                $timestampRaw = is_string($consultation->created_at) ? strtotime($consultation->created_at) : $consultation->created_at->timestamp;
                                
                                $senderName = 'موظف الإدارة الداخلية';
                                if (!empty($consultation->user_id)) {
                                    $senderUser = DB::table('users')->where('user_id', $consultation->user_id)->first();
                                    if ($senderUser) { $senderName = $senderUser->name; }
                                }
                                $currentCategory = $consultation->type_category ?? 'استشارة';
                            @endphp
                            {{-- 🌟 تم دمج الـ ID مع التوقيت في وسم السطر لمنع تعارض الاستشارات القديمة والجديدة نهائياً --}}
                            <tr id="task-row-{{ $consultation->consultation_id }}" class="manager-task-row hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition duration-150"
                                data-id="{{ $consultation->consultation_id }}"
                                data-timestamp="{{ $timestampRaw }}"
                                data-title="{{ $consultation->title }}"
                                data-category="{{ $currentCategory }}"
                                data-status="بانتظار الإسناد"
                                data-date="{{ $dateRaw }}">
                                
                                <td class="py-4">
                                    @if($currentCategory == 'عقد')
                                        <span class="px-2.5 py-1 bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-400 border border-blue-100 dark:border-blue-900/60 text-xs font-bold rounded-lg">
                                            عقد
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 bg-green-50 dark:bg-green-950/40 text-green-700 dark:text-green-400 border border-green-100 dark:border-green-900/60 text-xs font-bold rounded-lg">
                                            استشارة
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 text-gray-800 dark:text-gray-200 font-bold max-w-[200px] truncate">{{ $consultation->title }}</td>
                                <td class="py-4 text-xs text-gray-600 dark:text-gray-400">{{ $senderName }}</td>
                                <td class="py-4 text-xs text-gray-500 dark:text-gray-400 font-mono">
                                    {{ is_string($consultation->created_at) ? date('Y/m/d H:i', strtotime($consultation->created_at)) : $consultation->created_at->format('Y/m/d H:i') }}
                                </td>
                                <td class="py-4 text-center">
                                    <span class="px-2.5 py-0.5 bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 text-xs font-bold rounded-md border border-amber-200 dark:border-amber-900/60">بانتظار الإسناد</span>
                                </td>
                                <td class="py-4 text-left whitespace-nowrap pr-4">
                                    <button 
                                        onclick="openAssignModal({{ $consultation->consultation_id }}, {{ $timestampRaw }}, '{{ addslashes($consultation->title) }}', '{{ addslashes($consultation->description ?? 'لا يوجد تفاصيل إضافية.') }}', '{{ addslashes($senderName) }}', '{{ $consultation->attachment ?? '' }}')"
                                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-4 py-2 rounded-xl shadow-xs transition active:scale-[0.98] cursor-pointer inline-flex items-center gap-2">
                                        <i class="fas fa-file-signature text-xs"></i> قراءة وإسناد المعاملة
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr id="manager-empty-row">
                                <td colspan="6" class="py-16 text-center text-sm text-gray-400 dark:text-gray-500">
                                    <div class="w-16 h-16 bg-gray-50 dark:bg-gray-800/50 rounded-full flex items-center justify-center mx-auto mb-3 text-gray-300 dark:text-gray-600 border dark:border-gray-700">
                                        <i class="fas fa-check-double text-xl"></i>
                                    </div>
                                    <p class="font-bold text-gray-700 dark:text-gray-300 mb-0.5">الصندوق فارغ تماماً</p>
                                    لا توجد أي معاملات جديدة واردة بانتظار الإسناد حالياً.
                                </td>
                            </tr>
                        @endforelse
                        
                        <tr id="manager-filter-empty" class="hidden">
                            <td colspan="6" class="py-12 text-center text-sm text-gray-400">
                                <i class="fas fa-search block text-xl mb-2 text-gray-300 dark:text-gray-700"></i>
                                لا توجد معاملات جديدة تطابق خيارات التصفية المحددة.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    {{-- 🔹 🌟 نافذة المراجعة والإسناد المحدثة كلياً 🔹 --}}
    <div id="assign-modal" class="hidden fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4 backdrop-blur-md animate-fadeIn">
        <div class="bg-white dark:bg-gray-900 rounded-[2rem] border border-gray-100 dark:border-gray-800 w-full max-w-5xl h-[85vh] flex flex-col shadow-2xl overflow-hidden transform scale-95 transition-all duration-300">
            
            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-b border-gray-100 dark:border-gray-700/80 flex justify-between items-center flex-shrink-0">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 bg-amber-50 dark:bg-amber-900/30 rounded-xl flex items-center justify-center text-amber-600 dark:text-amber-400">
                        <i class="fas fa-glasses text-sm"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm text-gray-800 dark:text-gray-100">مراجعة المعاملات الواردة وإسنادها</h3>
                        <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5">الاطلاع على تفاصيل طلب الإدارة وتكليف المستشار القانوني المختص بدراسته</p>
                    </div>
                </div>
                <button onclick="closeAssignModal()" class="w-8 h-8 rounded-xl bg-gray-100 dark:bg-gray-700 hover:bg-red-500 hover:text-white dark:hover:bg-red-600 transition flex items-center justify-center cursor-pointer text-gray-500 dark:text-gray-400">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>

            <form id="assign-form" action="" onsubmit="handleLiveTaskRowDismissal()" method="POST" class="m-0 flex flex-col flex-grow overflow-hidden">
                @csrf
                @method('PUT')

                <div class="flex-grow overflow-y-auto p-6 grid grid-cols-1 md:grid-cols-2 gap-6 items-start text-right">
                    <div class="space-y-4 bg-gray-50/50 dark:bg-gray-800/20 p-5 rounded-2xl border border-gray-100 dark:border-gray-800/60">
                        <div class="flex items-center gap-1.5 border-b border-gray-100 dark:border-gray-700 pb-2 mb-1">
                            <span class="w-1.5 h-3 bg-blue-600 rounded-sm"></span>
                            <h4 class="text-xs font-bold text-gray-700 dark:text-gray-300">تفاصيل الطلب القانوني الوارد</h4>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-[11px] font-bold text-gray-400 dark:text-gray-500 block mb-0.5">صاحب الطلب (الجهة المرسلة)</label>
                                <span id="modal-sender" class="text-xs font-black text-gray-800 dark:text-gray-200"></span>
                            </div>
                            <div>
                                <label class="text-[11px] font-bold text-gray-400 dark:text-gray-500 block mb-0.5">الحالة الحالية</label>
                                <span class="text-xs font-black text-amber-600 dark:text-amber-400"><i class="fas fa-clock ml-1"></i> قيد المراجعة</span>
                            </div>
                        </div>

                        <hr class="border-gray-200/60 dark:border-gray-700">

                        <div>
                            <label class="text-[11px] font-bold text-gray-400 dark:text-gray-500 block mb-1">موضوع الطلب / العنوان</label>
                            <p id="modal-title" class="font-bold text-xs text-gray-800 dark:text-gray-100 bg-white dark:bg-gray-800 p-3 rounded-xl border border-gray-100 dark:border-gray-700/50 shadow-inner"></p>
                        </div>
                        
                        <div>
                            <label class="text-[11px] font-bold text-gray-400 dark:text-gray-500 block mb-1">نص ومضمون الطلب</label>
                            <textarea id="modal-desc" readonly class="w-full bg-white dark:bg-gray-800 text-xs font-medium text-gray-700 dark:text-gray-300 leading-relaxed text-justify p-3 rounded-xl border border-gray-100 dark:border-gray-700/50 outline-none resize-none overflow-y-auto min-h-[140px] shadow-xs"></textarea>
                        </div>
                        
                        <div id="modal-doc-container" class="space-y-1.5 pt-1 hidden">
                            <label class="text-[11px] font-bold text-gray-400 dark:text-gray-500 block mb-1">المستندات والمرفقات الواردة:</label>
                            <div id="modal-files-list" class="grid grid-cols-1 gap-2"></div>
                        </div>
                    </div>

                    <div class="space-y-4 bg-white dark:bg-gray-800/40 p-5 rounded-2xl border border-gray-100 dark:border-gray-700/50 h-full flex flex-col justify-start">
                        <div class="flex items-center gap-1.5 border-b border-gray-100 dark:border-gray-700 pb-2 mb-1">
                            <span class="w-1.5 h-3 bg-amber-500 rounded-sm"></span>
                            <h4 class="text-xs font-bold text-gray-700 dark:text-gray-300">قنوات التكليف والإسناد الإداري</h4>
                        </div>

                        <div class="bg-blue-50/30 dark:bg-blue-950/10 p-5 rounded-2xl border border-blue-100/50 dark:border-blue-900/40 space-y-3">
                            <label for="assigned_to" class="text-xs font-bold text-blue-700 dark:text-blue-400 flex items-center gap-1.5">
                                <i class="fas fa-user-tag text-sm"></i> اختر الموظف القانوني لتكليفه بالرد:
                            </label>
                            
                            <div class="relative">
                                <select name="assigned_to" id="assigned_to" required
                                    class="w-full p-3 bg-white text-xs font-bold rounded-xl border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 outline-none focus:border-blue-500 shadow-xs appearance-none cursor-pointer">
                                    <option value="" disabled selected>-- اختر المستشار القانوني المختص لمعالجة المعاملة --</option>
                                    @foreach($legalEmployees as $employee)
                                        <option value="{{ $employee->user_id }}">الأستاذ(ة): {{ $employee->name }}</option>
                                    @endforeach
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center px-3 text-gray-400">
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </div>
                            </div>
                        </div>

                        <div class="p-4 bg-gray-50 dark:bg-gray-800/60 rounded-xl border border-gray-100 dark:border-gray-700 text-[11px] text-gray-400 dark:text-gray-500 font-semibold leading-relaxed">
                            <p class="flex items-start gap-1.5"><i class="fas fa-info-circle text-blue-500 mt-0.5 flex-shrink-0"></i> <span>بمجرد الحفظ وتأكيد الإسناد، ستنتقل المعاملة فوراً إلى صندوق الموظف القانوني المختار وتتحول حالتها البرمجية حياً إلى <strong>"قيد المراجعة"</strong> لبدء صياغة المذكرة القانونية.</span></p>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-3.5 bg-gray-50 dark:bg-gray-800/40 border-t border-gray-100 dark:border-gray-700 flex justify-end gap-3 flex-shrink-0">
                    <button type="button" onclick="closeAssignModal()" class="px-4 py-2 rounded-xl text-xs font-bold bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition cursor-pointer">
                        إلغاء
                    </button>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-xl text-xs font-bold transition shadow-md active:scale-[0.98] cursor-pointer flex items-center gap-1.5">
                        <i class="fas fa-check-circle"></i> تأكيد وإسناد المهمة رسمياً
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let currentActiveConsultationId = null;
        let currentActiveTimestamp = null; // التوقيت الفريد لمنع الاختفاء الدائم للاستشارات الجديدة

        function applyManagerFilters() {
            const searchVal = document.getElementById('search-title').value.toLowerCase().trim();
            const categoryVal = document.getElementById('filter-category').value;
            const dateFrom = document.getElementById('filter-date-from').value;
            const dateTo = document.getElementById('filter-date-to').value;

            const rows = document.querySelectorAll('.manager-task-row');
            let matchCount = 0;

            rows.forEach(row => {
                const rTitle = row.getAttribute('data-title').toLowerCase();
                const rCategory = row.getAttribute('data-category');
                const rDate = row.getAttribute('data-date');

                const matchSearch = rTitle.includes(searchVal);
                const matchCategory = (categoryVal === 'all' || rCategory === categoryVal);
                
                let matchDate = true;
                if (dateFrom && rDate < dateFrom) matchDate = false;
                if (dateTo && rDate > dateTo) matchDate = false;

                if (matchSearch && matchCategory && matchDate) {
                    row.classList.remove('hidden');
                    matchCount++;
                } else {
                    row.classList.add('hidden');
                }
            });

            updatePendingBadgeCount(matchCount);

            const emptyRow = document.getElementById('manager-filter-empty');
            if (rows.length > 0) {
                emptyRow.classList.toggle('hidden', matchCount > 0);
            }
        }

        function updatePendingBadgeCount(count) {
            const badge = document.getElementById('pending-count');
            if (badge) {
                badge.innerText = count;
            }
        }

        function checkAndRenderEmptyTableState() {
            const remainingRows = document.querySelectorAll('.manager-task-row');
            if (remainingRows.length === 0) {
                const thead = document.getElementById('main-tasks-thead');
                if(thead) thead.remove();

                const tbody = document.getElementById('manager-tasks-body');
                tbody.innerHTML = `
                    <tr id="manager-empty-row">
                        <td colspan="6" class="py-16 text-center text-sm text-gray-400 dark:text-gray-500 animate-fadeIn">
                            <div class="w-16 h-16 bg-gray-50 dark:bg-gray-800/50 rounded-full flex items-center justify-center mx-auto mb-3 text-gray-300 dark:text-gray-600 border dark:border-gray-700">
                                <i class="fas fa-check-double text-xl"></i>
                            </div>
                            <p class="font-bold text-gray-700 dark:text-gray-300 mb-0.5">الصندوق فارغ تماماً</p>
                            لا توجد أي معاملات جديدة واردة بانتظار الإسناد حالياً.
                        </td>
                    </tr>`;
            }
        }

        function openAssignModal(id, timestamp, title, description, sender, attachmentJson) {
            currentActiveConsultationId = id; 
            currentActiveTimestamp = timestamp; // تثبيت الـ Timestamp الفريد للمعاملة الحالية
            document.getElementById('assign-form').action = `/legal-manager/consultations/assign/${id}`;
            document.getElementById('modal-sender').innerText = sender;
            document.getElementById('modal-title').innerText = title;
            
            const descTextarea = document.getElementById('modal-desc');
            descTextarea.value = description;

            const docContainer = document.getElementById('modal-doc-container');
            const filesList = document.getElementById('modal-files-list');
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

            document.getElementById('assign-modal').classList.remove('hidden');
        }

        // 🌟 تعديل عبقري: نستخدم الكاش المؤقت لربط الـ ID مع الـ Timestamp الفريد لضمان عدم حجب أي استشارة جديدة تحمل نفس الـ ID مستقبلاً!
        function handleLiveTaskRowDismissal() {
            if (currentActiveConsultationId && currentActiveTimestamp) {
                const targetRow = document.getElementById(`task-row-${currentActiveConsultationId}`);
                if (targetRow) {
                    targetRow.remove();
                    
                    const remainingRows = document.querySelectorAll('.manager-task-row');
                    updatePendingBadgeCount(remainingRows.length);
                    
                    // حفظ مفتاح دمج فريد (ID + Timestamp) للجلسة الحالية فقط لعدم تخريب الاستشارات الجديدة
                    let hiddenSessionTasks = JSON.parse(sessionStorage.getItem('hidden_session_tasks')) || [];
                    hiddenSessionTasks.push(`${currentActiveConsultationId}-${currentActiveTimestamp}`);
                    sessionStorage.setItem('hidden_session_tasks', JSON.stringify(hiddenSessionTasks));
                    
                    checkAndRenderEmptyTableState();
                }
            }
        }

        // فحص ذكي عند التحميل المباشر يعتمد على المفتاح الفريد المركّب لتأمين الاستقرار الكامل
        document.addEventListener("DOMContentLoaded", function() {
            let hiddenSessionTasks = JSON.parse(sessionStorage.getItem('hidden_session_tasks')) || [];
            
            const rows = document.querySelectorAll('.manager-task-row');
            rows.forEach(row => {
                const rowId = row.getAttribute('data-id');
                const rowTimestamp = row.getAttribute('data-timestamp');
                const matchKey = `${rowId}-${rowTimestamp}`;
                
                if (hiddenSessionTasks.includes(matchKey)) {
                    row.remove();
                }
            });
            
            const remainingRows = document.querySelectorAll('.manager-task-row');
            updatePendingBadgeCount(remainingRows.length);
            checkAndRenderEmptyTableState();
        });

        function closeAssignModal() {
            document.getElementById('assign-modal').classList.add('hidden');
            document.getElementById('assign-form').reset();
            currentActiveConsultationId = null;
            currentActiveTimestamp = null;
        }

        window.onclick = function(event) {
            const assignModal = document.getElementById('assign-modal');
            if (event.target === assignModal) {
                closeAssignModal();
            }
        }
    </script>
@endpush
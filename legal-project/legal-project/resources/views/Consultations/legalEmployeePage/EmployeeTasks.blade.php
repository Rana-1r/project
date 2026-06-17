{{-- 
@extends('layouts.LegalEmployee')

@section('title', 'المهام القانونية المسندة | منصة الإدارة القانونية')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100 transition-colors duration-200">المهام القانونية الإدارية</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 transition-colors duration-200">متابعة وإنجاز المهام المسندة إليك من قِبَل مدير الإدارة القانونية</p>
    </div>

    @if(session('success'))
        <div class="bg-green-50 dark:bg-green-950/40 border border-green-200 dark:border-green-900 text-green-700 dark:text-green-400 p-3 rounded-xl text-xs font-bold mb-4 flex items-center gap-2 shadow-sm">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div class="relative flex gap-2 items-center mb-5 w-full max-w-xl text-xs">
        <div class="relative flex-1">
            <span class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-400 dark:text-gray-500">
                <i class="fas fa-search text-[11px]"></i>
            </span>
            <input type="text" id="search-input" onkeyup="filterTasks()" placeholder="ابحث باسم المهمة أو التفاصيل..." 
                class="w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-800 dark:text-gray-100 rounded-xl pr-9 pl-3 py-2 outline-none transition focus:border-wadimakkah-light dark:focus:border-wadimakkah-light focus:ring-2 focus:ring-blue-50/50 dark:focus:ring-gray-700 shadow-sm">
        </div>
        
        <div class="relative">
            <button onclick="toggleFiltersDropdown()" id="filter-btn" class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:border-wadimakkah-light dark:hover:border-wadimakkah-light text-gray-700 dark:text-gray-300 px-3.5 py-2 rounded-xl transition flex items-center gap-1.5 font-semibold shadow-sm active:scale-95">
                <i class="fas fa-sliders-h text-wadimakkah-light"></i>
                <span>تصفية المهام</span>
                <i class="fas fa-chevron-down text-[9px] text-gray-400 mr-0.5"></i>
            </button>

            <div id="filters-dropdown" class="hidden absolute left-0 right-auto xl:left-auto xl:right-0 mt-2 w-56 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl shadow-xl p-3 z-30 flex flex-col gap-2">
                <div class="flex flex-col gap-1">
                    <label class="text-[10px] font-bold text-gray-400">حالة المهمة</label>
                    <select id="status-filter" class="w-full bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-200 rounded-lg px-2 py-1.5 outline-none text-xs focus:border-wadimakkah-light transition">
                        <option value="all">كل الحالات</option>
                        <option value="جاري العمل">جاري العمل</option>
                        <option value="مكتملة">مكتملة</option>
                    </select>
                </div>
                <button type="button" onclick="applyFiltersAndClose()" class="bg-wadimakkah-dark hover:bg-blue-800 text-white text-[11px] font-bold py-1.5 rounded-lg transition mt-1">تم</button>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-100 dark:border-gray-700 overflow-hidden transition-colors duration-200">
        <div class="bg-gray-50 dark:bg-gray-700/50 px-6 py-4 border-b dark:border-gray-700 flex justify-between items-center">
            <h3 class="font-bold text-gray-700 dark:text-gray-300 text-sm">المهام المكلف بها</h3>
            <span id="total-badge" class="bg-blue-100 dark:bg-blue-900/50 text-wadimakkah-dark dark:text-wadimakkah-light text-xs font-bold px-3 py-1 rounded-full">
                إجمالي المهام: {{ count($allTasks ?? []) }}
            </span>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-center text-sm" id="tasks-table">
                <thead class="bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 font-bold border-b dark:border-gray-600 text-center">
                    <tr>
                        <th class="p-4 text-right pr-6">اسم المهمة والموضوع</th>
                        <th class="p-4 text-center">تاريخ التكليف</th>
                        <th class="p-4 text-center">حالة المهمة</th>
                        <th class="p-4 text-center">الإجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700" id="table-body">
                    @isset($allTasks)
                        @forelse($allTasks as $task)
                        <tr class="task-row hover:bg-blue-50/50 dark:hover:bg-gray-700/40 transition duration-200 text-gray-700 dark:text-gray-200"
                            data-title="{{ $task->title }}"
                            data-status="{{ $task->status }}">
                            <td class="p-4 text-right pr-6">
                                <div class="font-semibold text-gray-800 dark:text-gray-100 text-sm">{{ $task->title }}</div>
                                <div class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $task->description ?? 'لا توجد تفاصيل إضافية للمهمة' }}</div>
                            </td>
                            <td class="p-4 text-gray-500 dark:text-gray-400 text-center text-xs">
                                {{ date('Y/m/d', strtotime($task->created_at)) }}
                            </td>
                            <td class="p-4 text-center">
                                @if($task->status === 'مكتملة')
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-green-50 dark:bg-green-950/40 text-green-700 dark:text-green-400 border border-green-200 dark:border-green-900">
                                        مكتملة
                                    </span>
                                @else
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-400 border border-blue-200 dark:border-blue-900">
                                        جاري العمل عليها
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 text-center">
                                @if($task->status !== 'مكتملة')
                                    <form action="{{ route('employee.tasks.complete', $task->task_id) }}" method="POST" class="m-0">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-4 py-2 rounded-xl transition shadow-sm">
                                            <i class="fas fa-check-circle ml-1"></i> إتمام المهمة
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-gray-400 dark:text-gray-500 font-semibold"><i class="fas fa-check-double text-green-500 ml-1"></i> تم الإنجاز</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="p-10 text-center text-gray-400 dark:text-gray-500">
                                <i class="fas fa-tasks text-3xl mb-2 block text-gray-300 dark:text-gray-600"></i> لا توجد مهام مسندة إليك حالياً.
                            </td>
                        </tr>
                        @endforelse
                    @else
                        <tr>
                            <td colspan="4" class="p-10 text-center text-gray-400 dark:text-gray-500">
                                <i class="fas fa-tasks text-3xl mb-2 block text-gray-300 dark:text-gray-600"></i> لا توجد مهام مسندة إليك حالياً.
                            </td>
                        </tr>
                    @endisset
                    
                    <tr id="no-results-row" class="hidden">
                        <td colspan="4" class="p-10 text-center text-gray-400 dark:text-gray-500">
                            <i class="fas fa-search block text-gray-300 dark:text-gray-600 text-2xl mb-2"></i> لا توجد نتائج تطابق خيارات البحث.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('page-scripts')
    <script>
        function toggleFiltersDropdown() {
            document.getElementById('filters-dropdown').classList.toggle('hidden');
        }

        function applyFiltersAndClose() {
            filterTasks();
            toggleFiltersDropdown();
        }

        function filterTasks() {
            const searchText = document.getElementById('search-input').value.toLowerCase().trim();
            const selectedStatus = document.getElementById('status-filter').value;
            const rows = document.querySelectorAll('.task-row');
            const noResultsRow = document.getElementById('no-results-row');
            let visibleCount = 0;

            rows.forEach(row => {
                const title = row.getAttribute('data-title').toLowerCase();
                const status = row.getAttribute('data-status');

                const matchesSearch = title.includes(searchText);
                const matchesStatus = (selectedStatus === 'all' || status === selectedStatus);

                if (matchesSearch && matchesStatus) {
                    row.classList.remove('hidden');
                    visibleCount++;
                } else {
                    row.classList.add('hidden');
                }
            });

            document.getElementById('total-badge').innerText = `إجمالي المهام المفلترة: ${visibleCount}`;
            noResultsRow.classList.toggle('hidden', visibleCount > 0 || rows.length === 0);
        }

        window.onclick = function(event) {
            const dropdown = document.getElementById('filters-dropdown');
            const filterBtn = document.getElementById('filter-btn');
            if (dropdown && !dropdown.classList.contains('hidden') && !filterBtn.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.classList.add('hidden');
            }
        }
    </script>
@endsection 
--}}
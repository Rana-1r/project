@extends(
    auth()->user()->role_id == 1 
    ? 'layouts.LegalManager' 
    : (auth()->user()->role_id == 2 ? 'layouts.LegalEmployee' : 'layouts.InternalEmployee')
)

@section('title', 'الملف الشخصي | منصة الإدارة القانونية')

@section('content')
    {{-- محتوى الصفحة الرئيسي --}}
    <main class="container mx-auto px-6 py-10 flex-grow max-w-5xl">
        
        <div class="mt-6 text-center">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-100">إعدادات الحساب الشخصي</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2 mb-10 text-sm">عرض وإدارة بياناتك الشخصية وتحديثها بسهولة</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
            
            {{-- كارد الصورة الشخصية --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 text-center flex flex-col items-center">
                <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-6 w-full text-right border-b border-gray-50 dark:border-gray-700 pb-3">الصورة الشخصية</h3>
                
                <form id="photo-upload-form" action="{{ route('profile.photo.update') }}" method="POST" enctype="multipart/form-data" class="relative group cursor-pointer">
                    @csrf
                    <div class="w-32 h-32 bg-gray-50 dark:bg-gray-700 p-1.5 rounded-full shadow-inner border-2 border-gray-100 dark:border-gray-600 flex items-center justify-center overflow-hidden transition group-hover:scale-105 duration-200">
                        @if(Auth::check() && Auth::user()->photo)
                            <img src="{{ asset('storage/' . Auth::user()->photo) }}" class="w-full h-full rounded-full object-cover" alt="Profile">
                        @else
                            <i class="fas fa-user-circle text-8xl text-gray-300 dark:text-gray-500"></i>
                        @endif
                    </div>
                    
                    <a href="javascript:void(0)" onclick="document.getElementById('photo-input').click()" class="absolute bottom-1 left-1 bg-wadimakkah-dark hover:bg-blue-800 text-white w-9 h-9 rounded-full flex items-center justify-center shadow-lg border-2 border-white dark:border-gray-800 transition-all active:scale-90">
                        <i class="fas fa-camera text-xs"></i>
                    </a>
                    
                    <input type="file" id="photo-input" name="photo" class="hidden" onchange="document.getElementById('photo-upload-form').submit();">
                </form>

                <div class="mt-4">
                    <h2 class="text-base font-bold text-gray-800 dark:text-white">{{ Auth::user()->name }}</h2>
                    <span class="inline-block bg-blue-50 dark:bg-blue-950/40 text-wadimakkah-dark dark:text-wadimakkah-light text-[11px] font-bold px-3 py-1 rounded-full mt-1.5 border border-blue-100/30">
                        @if(Auth::user()->role_id == 1) مدير الإدارة القانونية @elseif(Auth::user()->role_id == 2) مستشار قانوني @else موظف إدارات داخلية @endif
                    </span>
                </div>
            </div>

            {{-- كارد البيانات الشخصية المكتوبة --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-sm border border-gray-100 dark:border-gray-700 relative">
                    
                    <button onclick="openEditModal()" class="absolute top-6 left-6 bg-wadimakkah-dark hover:bg-blue-800 text-white font-bold text-xs px-4 py-2 rounded-xl shadow-md transition duration-150 active:scale-95 flex items-center gap-2 cursor-pointer">
                        <i class="fas fa-user-edit text-[11px]"></i> تعديل البيانات
                    </button>

                    <h3 class="text-base font-bold text-gray-800 dark:text-white mb-8 border-b border-gray-50 dark:border-gray-700 pb-3 flex items-center gap-2">
                        <i class="fas fa-id-card text-wadimakkah-light text-sm"></i> بيانات الملف الشخصي
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                        <div class="bg-gray-50 dark:bg-gray-700/30 p-4 rounded-xl flex items-center gap-4 border border-gray-100/50 dark:border-gray-700/50">
                            <div class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-950/60 flex items-center justify-center text-wadimakkah-dark dark:text-wadimakkah-light text-base">
                                <i class="fas fa-user"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-semibold mb-0.5">الاسم الكامل</p>
                                <p class="text-sm font-bold text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</p>
                            </div>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700/30 p-4 rounded-xl flex items-center gap-4 border border-gray-100/50 dark:border-gray-700/50">
                            <div class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-950/60 flex items-center justify-center text-wadimakkah-dark dark:text-wadimakkah-light text-base">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="overflow-hidden">
                                <p class="text-xs text-gray-400 font-semibold mb-0.5">البريد الإلكتروني</p>
                                <p class="text-sm font-bold text-gray-800 dark:text-gray-200 truncate">{{ Auth::user()->email }}</p>
                            </div>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700/30 p-4 rounded-xl flex items-center gap-4 border border-gray-100/50 dark:border-gray-700/50">
                            <div class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-950/60 flex items-center justify-center text-wadimakkah-dark dark:text-wadimakkah-light text-base">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-semibold mb-0.5">رقم الجوال</p>
                                <p class="text-sm font-bold text-gray-800 dark:text-gray-200">{{ Auth::user()->phone ?? 'غير مسجل' }}</p>
                            </div>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700/30 p-4 rounded-xl flex items-center gap-4 border border-gray-100/50 dark:border-gray-700/50">
                            <div class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-950/60 flex items-center justify-center text-wadimakkah-dark dark:text-wadimakkah-light text-base">
                                <i class="fas fa-briefcase"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-semibold mb-0.5">المسمى الوظيفي</p>
                                <p class="text-sm font-bold text-gray-800 dark:text-gray-200">{{ Auth::user()->job_title ?? 'مستشار قانوني' }}</p>
                            </div>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700/30 p-4 rounded-xl flex items-center gap-4 border border-gray-100/50 dark:border-gray-700/50 md:col-span-2">
                            <div class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-950/60 flex items-center justify-center text-wadimakkah-dark dark:text-wadimakkah-light text-base">
                                <i class="fas fa-building"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-semibold mb-0.5">القسم / الإدارة</p>
                                <p class="text-sm font-bold text-gray-800 dark:text-gray-200">{{ Auth::user()->department ?? 'الإدارة القانونية' }}</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </main>

    {{-- نافذة التعديل المنبثقة (Modal) --}}
    <div id="edit-profile-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm transition-opacity duration-300">
        <div class="bg-white dark:bg-gray-800 rounded-2xl max-w-xl w-full p-6 shadow-2xl border border-gray-100 dark:border-gray-700 text-right">
            
            <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-4 mb-5">
                <h3 class="text-base font-bold text-gray-800 dark:text-white flex items-center gap-2">
                    <i class="fas fa-user-cog text-wadimakkah-light text-sm"></i> تعديل بيانات الحساب والأمان
                </h3>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition text-lg focus:outline-none">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="edit-profile-form" action="{{ route('profile.update') }}" method="POST" class="space-y-4 text-sm">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- الاسم الكامل --}}
                    <div>
                        <label class="block text-gray-600 dark:text-gray-400 font-bold mb-1.5 text-xs">الاسم الكامل <span class="text-red-500">*</span></label>
                        <div class="relative flex items-center">
                            <i class="fas fa-user absolute right-3 text-gray-400 text-xs"></i>
                            <input type="text" name="name" value="{{ old('name', Auth::user()->name) }}" required class="w-full bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl pr-9 pl-4 py-2 outline-none focus:ring-1 focus:ring-blue-500 text-gray-800 dark:text-gray-100">
                        </div>
                    </div>

                    {{-- رقم الجوال --}}
                    <div>
                        <label class="block text-gray-600 dark:text-gray-400 font-bold mb-1.5 text-xs">رقم الجوال (10 خانات) <span class="text-red-500">*</span></label>
                        <div class="relative flex items-center">
                            <i class="fas fa-phone-alt absolute right-3 text-gray-400 text-xs"></i>
                            <input type="text" id="phone_input" name="phone" value="{{ old('phone', Auth::user()->phone) }}" required maxlength="10" placeholder="05xxxxxxxx" class="w-full bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl pr-9 pl-4 py-2 outline-none focus:ring-1 focus:ring-blue-500 text-gray-800 dark:text-gray-100">
                        </div>
                        <p id="phone_error" class="text-red-500 text-[11px] mt-1 hidden font-semibold">يجب أن يتكون رقم الجوال من 10 خانات تماماً.</p>
                    </div>

                    {{-- البريد الإلكتروني --}}
                    <div class="md:col-span-2">
                        <label class="block text-gray-600 dark:text-gray-400 font-bold mb-1.5 text-xs">البريد الإلكتروني <span class="text-red-500">*</span></label>
                        <div class="relative flex items-center">
                            <i class="fas fa-envelope absolute right-3 text-gray-400 text-xs"></i>
                            <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}" required class="w-full bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl pr-9 pl-4 py-2 outline-none focus:ring-1 focus:ring-blue-500 text-gray-800 dark:text-gray-100">
                        </div>
                    </div>

                    <div class="md:col-span-2 border-t border-gray-100 dark:border-gray-700/50 pt-3 mt-1">
                        <h4 class="text-xs font-bold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-1.5">
                            <i class="fas fa-key text-xs text-wadimakkah-light"></i> تحديث كلمة المرور
                        </h4>
                    </div>

                    {{-- كلمة المرور الحالية --}}
                    <div class="md:col-span-2">
                        <label class="block text-gray-600 dark:text-gray-400 font-bold mb-1.5 text-xs">كلمة المرور الحالية</label>
                        <div class="relative flex items-center">
                            <i class="fas fa-lock absolute right-3 text-gray-400 text-xs"></i>
                            <input type="password" id="current_password" name="current_password" placeholder="••••••••" class="w-full bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl pr-9 pl-10 py-2 outline-none focus:ring-1 focus:ring-blue-500 text-gray-800 dark:text-gray-100 placeholder-gray-300">
                            <button type="button" onclick="togglePasswordVisibility('current_password', 'current_pass_icon')" class="absolute left-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 focus:outline-none">
                                <i id="current_pass_icon" class="fas fa-eye text-xs"></i>
                            </button>
                        </div>
                    </div>

                    {{-- كلمة المرور الجديدة --}}
                    <div>
                        <label class="block text-gray-600 dark:text-gray-400 font-bold mb-1.5 text-xs">كلمة المرور الجديدة (اختياري)</label>
                        <div class="relative flex items-center">
                            <i class="fas fa-lock absolute right-3 text-gray-400 text-xs"></i>
                            <input type="password" id="new_password" name="password" placeholder="••••••••" class="w-full bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl pr-9 pl-10 py-2 outline-none focus:ring-1 focus:ring-blue-500 text-gray-800 dark:text-gray-100 placeholder-gray-300">
                            <button type="button" onclick="togglePasswordVisibility('new_password', 'new_pass_icon')" class="absolute left-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 focus:outline-none">
                                <i id="new_pass_icon" class="fas fa-eye text-xs"></i>
                            </button>
                        </div>
                    </div>

                    {{-- تأكيد كلمة المرور الجديدة --}}
                    <div>
                        <label class="block text-gray-600 dark:text-gray-400 font-bold mb-1.5 text-xs">تأكيد كلمة المرور الجديدة</label>
                        <div class="relative flex items-center">
                            <i class="fas fa-lock absolute right-3 text-gray-400 text-xs"></i>
                            <input type="password" id="new_password_confirmation" name="password_confirmation" placeholder="••••••••" class="w-full bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl pr-9 pl-10 py-2 outline-none focus:ring-1 focus:ring-blue-500 text-gray-800 dark:text-gray-100 placeholder-gray-300">
                            <button type="button" onclick="togglePasswordVisibility('new_password_confirmation', 'confirm_pass_icon')" class="absolute left-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 focus:outline-none">
                                <i id="confirm_pass_icon" class="fas fa-eye text-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- أزرار التحكم --}}
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700 mt-5">
                    <button type="button" onclick="closeEditModal()" class="bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-bold px-5 py-2 rounded-xl transition duration-150 text-xs">
                        إلغاء
                    </button>
                    <button type="submit" class="bg-wadimakkah-dark hover:bg-blue-800 text-white font-bold px-5 py-2 rounded-xl shadow-md transition duration-150 active:scale-95 flex items-center gap-2 text-xs cursor-pointer">
                        <i class="fas fa-save text-[11px]"></i> حفظ التغييرات
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // فتح وإغلاق المودال
        function openEditModal() {
            document.getElementById('edit-profile-modal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('edit-profile-modal').classList.add('hidden');
        }

        // إظهار وإخفاء كلمات المرور (العين)
        function togglePasswordVisibility(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        const phoneInput = document.getElementById('phone_input');
        const form = document.getElementById('edit-profile-form');
        const newPassword = document.getElementById('new_password');
        const currentPassword = document.getElementById('current_password');
        const confirmPassword = document.getElementById('new_password_confirmation');

        // منع كتابة أكثر من 10 أرقام أو حروف غير عددية
        phoneInput.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, ''); 
            if (this.value.length > 10) {
                this.value = this.value.slice(0, 10);
            }
        });

        // منطق التحقق الإلزامي قبل الحفظ
        form.addEventListener('submit', function(e) {
            let valid = true;

            // 1. تحقق من طول رقم الجوال
            if (phoneInput.value.length !== 10) {
                document.getElementById('phone_error').classList.remove('hidden');
                phoneInput.classList.add('border-red-500');
                valid = false;
            } else {
                document.getElementById('phone_error').classList.add('hidden');
                phoneInput.classList.remove('border-red-500');
            }

            // 2. التحقق من إلزامية حقول كلمات المرور عند كتابة كلمة مرور جديدة
            if (newPassword.value.trim() !== "") {
                if (currentPassword.value.trim() === "") {
                    alert("يرجى إدخال كلمة المرور الحالية لتتمكن من تغييرها.");
                    currentPassword.focus();
                    valid = false;
                } else if (confirmPassword.value.trim() === "") {
                    alert("يرجى تأكيد كلمة المرور الجديدة.");
                    confirmPassword.focus();
                    valid = false;
                } else if (newPassword.value !== confirmPassword.value) {
                    alert("كلمة المرور الجديدة غير متطابقة مع خانة التأكيد.");
                    confirmPassword.focus();
                    valid = false;
                }
            }

            if (!valid) {
                e.preventDefault(); 
            }
        });

        // إغلاق المودال والنوافذ عند النقر بالخارج
        window.addEventListener('click', function(event) {
            const modal = document.getElementById('edit-profile-modal');
            if (event.target === modal) {
                closeEditModal();
            }
        });
    </script>
@endpush
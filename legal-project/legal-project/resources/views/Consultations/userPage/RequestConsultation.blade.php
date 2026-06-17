@extends('layouts.InternalEmployee')

@section('title', isset($draft) ? 'تعديل مسودة الاستشارة | منصة الإدارة القانونية' : 'طلب استشارة قانونية | منصة الإدارة القانونية')

@section('content')
    <main class="container mx-auto px-6 py-10 flex-grow max-w-4xl">
        
        {{-- صندوق الفورم العصري المطور العريض --}}
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-md border border-gray-200 dark:border-gray-700 p-8 relative overflow-hidden transition-colors duration-200">
            {{-- الشريط العلوي الجمالي المتناسق --}}
            <div class="absolute top-0 left-0 right-0 h-2 bg-gradient-to-r from-wadimakkah-dark to-blue-700"></div>
            
            {{-- رأس النموذج --}}
            <div class="mb-6 border-b border-gray-100 dark:border-gray-700 pb-4 text-right">
                <h2 class="text-xl font-black text-gray-800 dark:text-white flex items-center gap-2">
                    <i class="fas fa-balance-scale text-wadimakkah-light"></i> 
                    {{ isset($draft) ? 'تعديل واستكمال مسودة الاستشارة' : 'تقديم طلب استشارة قانونية جديدة' }}
                </h2>
                <p class="text-xs text-gray-400 dark:text-gray-400 mt-1.5 leading-relaxed">يُرجى إدخال البيانات المطلوبة لتقديم طلب الاستشارة القانونية</p>
            </div>

            {{-- رسائل النجاح والتحذيرات الحية --}}
            @if(session('success'))
                <div class="bg-green-50 dark:bg-green-950/40 border border-green-200 dark:border-green-900/60 text-green-700 dark:text-green-400 p-3.5 rounded-xl text-xs font-bold mb-5 flex items-center gap-2 animate-fadeIn">
                    <i class="fas fa-check-circle text-sm"></i> {{ session('success') }}
                </div>
            @endif

            {{-- فورم استقبال ومعالجة البيانات --}}
            <form action="{{ isset($draft) ? route('consultations.update', $draft->consultation_id) : route('consultations.store') }}" method="POST" enctype="multipart/form-data" onsubmit="prepareFilesForUpload(event)" class="m-0" id="consultation-form">
                @csrf
                @if(isset($draft))
                    @method('PUT')
                @endif
                
                {{-- الحقول المخفية لتعيين نوع الإجراء --}}
                <input type="hidden" name="action" id="form-action" value="submit">
                <div id="hidden-inputs-container"></div>

                <div class="space-y-5 text-right text-xs font-semibold">
                    
                    {{-- 1. عنوان الاستشارة --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-2">عنوان الاستشارة <span class="text-red-500">*</span></label>
                        <input type="text" name="title" required value="{{ old('title', $draft->title ?? '') }}" placeholder="مثال: استفسار بشأن بنود فسخ العقد" 
                            class="w-full bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 dark:text-white rounded-xl px-3.5 py-3 text-xs outline-none transition focus:border-wadimakkah-light focus:bg-white dark:focus:bg-gray-800 focus:ring-2 focus:ring-blue-50/50">
                    </div>

                    {{-- 2. تحديد نوع الاستشارة --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-2">نوع الاستشارة القانونية <span class="text-red-500">*</span></label>
                        <select name="type" id="consultation-type" required onchange="toggleCustomTypeField()"
                            class="w-full bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 dark:text-white rounded-xl px-3.5 py-3 text-xs outline-none transition cursor-pointer focus:border-wadimakkah-light focus:bg-white dark:focus:bg-gray-800 focus:ring-2 focus:ring-blue-50/50">
                            <option value="" disabled {{ !isset($draft) ? 'selected' : '' }}>اختر التصنيف القانوني للطلب...</option>
                            <option value="شركات" {{ old('type', $draft->type ?? '') == 'شركات' ? 'selected' : '' }}>شركات</option>
                            <option value="عمالي" {{ old('type', $draft->type ?? '') == 'عمالي' ? 'selected' : '' }}>عمالي</option>
                            <option value="عقود" {{ old('type', $draft->type ?? '') == 'عقود' ? 'selected' : '' }}>مراجعة صياغة عقود</option>
                            <option value="أخرى" {{ old('type', $draft->type ?? '') == 'أخرى' || !empty($draft->custom_type) ? 'selected' : '' }}>أخرى (تحديد نوع مخصص)</option>
                        </select>
                    </div>

                    {{-- حقل مخصص يظهر ديناميكياً عند اختيار (أخرى) --}}
                    <div id="custom-type-container" class="{{ old('type', $draft->type ?? '') == 'أخرى' || !empty($draft->custom_type) ? '' : 'hidden' }} transition-all duration-300">
                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-2">حدد نوع الاستشارة بدقة <span class="text-red-500">*</span></label>
                        <input type="text" name="custom_type" id="custom-type-input" value="{{ old('custom_type', $draft->custom_type ?? '') }}" placeholder="اكتب التصنيف القانوني المخصص هنا..." 
                            class="w-full bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 dark:text-white rounded-xl px-3.5 py-3 text-xs outline-none transition focus:border-wadimakkah-light focus:bg-white dark:focus:bg-gray-800 focus:ring-2 focus:ring-blue-50/50">
                    </div>

                    {{-- 3. نص الاستشارة وموضوعها (تمت إضافة حقل المراقبة البرمجية والتمدد هنا) --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 mb-2">موضوع الاستشارة القانونية<span class="text-red-500">*</span></label>
                        <textarea name="description" id="consultation-description" rows="5" required placeholder="يرجى كتابة وسرد تفاصيل طلبكم أو تساؤلكم القانوني بشكل مفصل وواضح هنا..." 
                            class="w-full bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 dark:text-white rounded-xl px-3.5 py-3 text-xs outline-none transition focus:border-wadimakkah-light focus:bg-white dark:focus:bg-gray-800 focus:ring-2 focus:ring-blue-50/50 leading-relaxed resize-none overflow-hidden"></textarea>
                    </div>

                    {{-- 4. رفع المستندات الداعمة يخص الاستشارة --}}
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-xs font-bold text-gray-600 dark:text-gray-300">المستندات والمرفقات الداعمة للطلب <span class="text-gray-400 font-normal">(اختياري)</span></label>
                            <span id="files-counter" class="text-[10px] font-bold text-gray-400 dark:text-gray-400 bg-gray-50 dark:bg-gray-700 px-2.5 py-1 rounded-lg border dark:border-gray-600">0 / 3 ملفات</span>
                        </div>
                        
                        {{-- منطقة سحب وإفلات المستندات العصرية --}}
                        <div class="w-full min-h-[130px] border-2 border-dashed border-gray-200 dark:border-gray-600 rounded-2xl bg-gray-50 dark:bg-gray-900/20 p-4 flex flex-col items-center justify-center relative transition-all duration-300" id="main-dropzone">
                            <div id="embedded-files-container" class="w-full flex flex-col gap-2 z-10 mb-2 empty:hidden"></div>

                            <label id="upload-clickable-area" class="flex flex-col items-center justify-center w-full cursor-pointer group m-0 py-2">
                                <i class="fas fa-cloud-upload-alt text-gray-400 dark:text-gray-500 text-xl mb-1.5 group-hover:text-wadimakkah-light transition" id="upload-icon"></i>
                                <p class="text-[11px] text-gray-600 dark:text-gray-300 font-bold mb-0.5" id="upload-text">اضغط هنا لإرفاق الملفات أو المستندات ذات الصلة</p>
                                <p class="text-[10px] text-gray-400 dark:text-gray-500" id="upload-subtext">PDF, DOCX, PNG, JPG (الحد الأقصى لكل ملف: 10MB)</p>
                                <input type="file" id="attachment-input" class="hidden" onchange="addFileToCollection(this)">
                            </label>
                        </div>
                    </div>

                    {{-- أزرار التحكم والإرسال المقسمة بنسب متناسقة --}}
                    <div class="pt-4 flex flex-col sm:flex-row gap-3">
                        <button type="submit" onclick="setAction('submit')" class="flex-1 bg-wadimakkah-dark hover:bg-blue-800 text-white font-bold text-xs py-3.5 rounded-xl transition shadow-md flex items-center justify-center gap-2 active:scale-[0.99] cursor-pointer">
                            <i class="fas fa-paper-plane text-[10px]"></i> 
                            {{ isset($draft) ? 'إرسال وتوجيه الاستشارة الآن' : 'إرسال طلب الاستشارة للإدارة القانونية' }}
                        </button>
                        
                        <button type="submit" onclick="setAction('draft')" class="sm:w-52 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-bold text-xs py-3.5 rounded-xl transition border border-gray-200 dark:border-gray-600 flex items-center justify-center gap-2 active:scale-[0.99] cursor-pointer">
                            <i class="fas fa-archive text-[10px]"></i> حفظ كمسودة
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </main>
@endsection

@push('scripts')
<script>
    // 🌟 سكربت التمدد التلقائي الذكي لخانات الاستشارة القانونية
    document.addEventListener('DOMContentLoaded', function() {
        const textarea = document.getElementById('consultation-consultation-description' ? 'consultation-description' : '');
        if (textarea) {
            // ملء النص القديم في حال كان تعديل مسودة وتحديد الارتفاع البدائي
            textarea.value = `{!! old('description', $draft->description ?? '') !!}`;
            adjustTextareaHeight(textarea);

            // مراقبة الكتابة لتوسيع الحقل حياً
            textarea.addEventListener('input', function() {
                adjustTextareaHeight(this);
            });
        }
    });

    function adjustTextareaHeight(el) {
        el.style.height = 'auto'; // إعادة التعيين لحساب المساحة بدقة
        el.style.height = el.scrollHeight + 'px'; // مطابقة الارتفاع الفعلي للأسطر
    }

    // التحكم بظهور حقل تصنيف "أخرى"
    function toggleCustomTypeField() {
        const selectField = document.getElementById('consultation-type');
        const customFieldContainer = document.getElementById('custom-type-container');
        const customInput = document.getElementById('custom-type-input');

        if (selectField.value === 'أخرى') {
            customFieldContainer.classList.remove('hidden');
            customInput.setAttribute('required', 'required');
        } else {
            customFieldContainer.classList.add('hidden');
            customInput.removeAttribute('required');
            customInput.value = ''; 
        }
    }

    function setAction(actionValue) {
        document.getElementById('form-action').value = actionValue;
    }

    let selectedFiles = [];

    function addFileToCollection(input) {
        if (!input.files || input.files.length === 0) return;

        const file = input.files[0];
        const isDuplicate = selectedFiles.some(f => f.name === file.name && f.size === file.size);
        if (isDuplicate) {
            alert("تنبيه: هذا المستند مضاف ومرفق مسبقاً!");
            input.value = '';
            return;
        }

        if (selectedFiles.length >= 3) {
            alert("لقد وصلت للحد الأقصى المسموح به وهو 3 مستندات فقط يخص الاستشارة!");
            input.value = '';
            return;
        }

        selectedFiles.push(file);
        input.value = ''; 
        renderFilesLayout();
    }

    function removeFileFromCollection(index) {
        selectedFiles.splice(index, 1);
        renderFilesLayout();
    }

    function renderFilesLayout() {
        const container = document.getElementById('embedded-files-container');
        const counterLabel = document.getElementById('files-counter');
        const uploadIcon = document.getElementById('upload-icon');
        const uploadText = document.getElementById('upload-text');
        const uploadSubtext = document.getElementById('upload-subtext');
        const uploadClickableArea = document.getElementById('upload-clickable-area');

        container.innerHTML = '';

        if (selectedFiles.length > 0) {
            counterLabel.innerText = `${selectedFiles.length} / 3 ملفات`;
            counterLabel.className = "text-[10px] font-bold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/40 px-2.5 py-1 rounded-lg border border-blue-100 dark:border-blue-900";
            
            uploadIcon.className = "fas fa-plus-circle text-blue-500 text-sm mb-1 group-hover:text-wadimakkah-dark transition";
            uploadText.innerText = "اضغط هنا لإضافة مستند إضافي للمعاملة";
            uploadSubtext.classList.add('hidden');
        } else {
            counterLabel.innerText = "0 / 3 ملفات";
            counterLabel.className = "text-[10px] font-bold text-gray-400 dark:text-gray-500 bg-gray-50 dark:bg-gray-700 px-2.5 py-1 rounded-lg border dark:border-gray-600";
            
            uploadIcon.className = "fas fa-cloud-upload-alt text-gray-400 dark:text-gray-500 text-xl mb-1.5 group-hover:text-wadimakkah-light transition";
            uploadText.innerText = "اضغط هنا لإرفاق المذكرات أو المستندات ذات الصلة";
            uploadSubtext.classList.remove('hidden');
        }

        if (selectedFiles.length >= 3) {
            uploadClickableArea.classList.add('hidden');
        } else {
            uploadClickableArea.classList.remove('hidden');
        }

        selectedFiles.forEach((file, index) => {
            const sizeInMB = (file.size / (1024 * 1024)).toFixed(2);
            const card = document.createElement('div');
            card.className = "w-full bg-white dark:bg-gray-800 p-3 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm flex items-center justify-between gap-3 animate-fadeIn";
            card.innerHTML = `
                <div class="flex items-center gap-2.5 overflow-hidden">
                    <div class="w-8 h-8 rounded-xl bg-blue-50 dark:bg-blue-950/50 text-wadimakkah-dark dark:text-wadimakkah-light flex items-center justify-center text-sm flex-shrink-0 border border-blue-100 dark:border-blue-900">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="overflow-hidden text-right">
                        <p class="text-xs font-bold text-gray-700 dark:text-gray-200 truncate">${file.name}</p>
                        <p class="text-[10px] text-gray-400 font-mono mt-0.5">${sizeInMB} MB</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <span class="text-[9px] text-emerald-600 dark:text-emerald-400 font-bold bg-emerald-50 dark:bg-emerald-950/30 px-1.5 py-0.5 rounded border border-emerald-100 dark:border-emerald-900">
                        جاهز للرفع
                    </span>
                    <button type="button" onclick="removeFileFromCollection(${index})" class="w-7 h-7 rounded-lg hover:bg-red-50 dark:hover:bg-red-950/40 text-gray-400 hover:text-red-500 flex items-center justify-center text-xs transition">
                        <i class="fas fa-trash-alt text-xs"></i>
                    </button>
                </div>
            `;
            container.appendChild(card);
        });
    }

    function prepareFilesForUpload(event) {
        const container = document.getElementById('hidden-inputs-container');
        container.innerHTML = ''; 

        if (selectedFiles.length === 0) return true;

        const dataTransfer = new DataTransfer();
        selectedFiles.forEach(file => dataTransfer.items.add(file));

        const hiddenFileInput = document.createElement('input');
        hiddenFileInput.type = 'file';
        hiddenFileInput.name = 'attachments[]';
        hiddenFileInput.multiple = true;
        hiddenFileInput.files = dataTransfer.files;
        hiddenFileInput.className = 'hidden';

        container.appendChild(hiddenFileInput);
        return true;
    }
</script>
@endpush
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Storage; 
use Illuminate\Support\Facades\Schema; 

class ConsultationsController extends Controller
{
    /**
     * 📊 لوحة تحكم الموظف القانوني (role_id = 2)
     */
    public function employeeDashboard()
    {
        $userId = auth()->id(); 

        // 🌟 جلب الاستشارات المسندة للموظف الحالي بحالتها النشطة (قيد المراجعة أو بحاجة لتعديل)
        $myConsultations = DB::table('user_consultations')
            ->where('assigned_to', $userId)
            ->whereIn('status', ['قيد الدراسة', 'قيد المراجعة', 'بحاجة لتعديل'])
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = [
            'total_cases'         => Schema::hasTable('user_cases') ? DB::table('user_cases')->count() : 0,
            'total_contracts'     => Schema::hasTable('user_contracts') ? DB::table('user_contracts')->count() : 0,
            'total_consultations' => $myConsultations->count(), 
        ];

        return view('Consultations.legalEmployeePage.InterfacesEmployee', compact('stats', 'myConsultations'));
    }

    /**
     * 🏢 لوحة تحكم المستخدم / الموظف الداخلي (role_id = 3)
     */
    public function internalDashboard()
    {
        $currentUserId = auth()->id();

        $stats = [
            'total_cases'         => Schema::hasTable('user_cases') ? DB::table('user_cases')->where('user_id', $currentUserId)->count() : 0,
            'total_contracts'     => Schema::hasTable('user_contracts') ? DB::table('user_contracts')->where('user_id', $currentUserId)->count() : 0,
            'total_consultations' => Schema::hasTable('user_consultations') 
                ? DB::table('user_consultations')
                    ->where('user_id', $currentUserId)
                    ->where('status', '!=', 'مسودة') 
                    ->count() 
                : 0,
        ];

        return view('Consultations.userPage.InterfacesUser', compact('stats'));
    }

    /**
     * 👑 لوحة تحكم المدير القانوني (role_id = 1) وجدول المتابعة
     */
    public function managerDashboard()
    {
        $stats = [
            'total_cases'         => Schema::hasTable('user_cases') ? DB::table('user_cases')->count() : 0,
            'total_contracts'     => Schema::hasTable('user_contracts') ? DB::table('user_contracts')->count() : 0,
            'total_consultations' => Schema::hasTable('user_consultations') ? DB::table('user_consultations')->where('status', '!=', 'مسودة')->count() : 0,
        ];

        $allAssignedTasks = DB::table('user_consultations')
            ->leftJoin('users', 'user_consultations.assigned_to', '=', 'users.user_id')
            ->select('user_consultations.*', 'users.name as employee_name')
            ->where('user_consultations.status', '!=', 'مسودة')
            ->orderBy('user_consultations.updated_at', 'desc')
            ->get();

        return view('Consultations.legalManagerPage.InterfacesManager', compact('stats', 'allAssignedTasks'));
    }

    /**
     * شاشة عرض وإسناد المهام من قِبل المدير
     */
    public function managerTasks()
    {
        // 1. جلب الاستشارات الواردة التي حالتها "قيد المراجعة" أو "قيد الإسناد" لدعم سيناريو الإسناد
        $incomingConsultations = DB::table('user_consultations')
            ->whereIn('status', ['قيد المراجعة', 'قيد الإسناد'])
            ->orderBy('created_at', 'desc')
            ->get();

        // 2. جلب الموظفين القانونيين (المستشارين) المتاحين للإسناد
        $legalEmployees = DB::table('users')
            ->where('role_id', 2)
            ->get();

        return view('Consultations.legalManagerPage.ManagerTasks', compact('incomingConsultations', 'legalEmployees'));
    }

    /**
     * 🔄 إسناد الاستشارة القانونية من المدير إلى موظف قانوني محدد وتغيير الحالة إلى "قيد المراجعة"
     */
    public function storeTask(Request $request, $id)
    {
        // 🌟 في حال كانت المهمة جديدة ومبتدأة مباشرة من المدير القانوني (id = 0)
        if ($id == 0 || $id == '0') {
            $request->validate([
                'title'       => 'required|string|max:255',
                'type'        => 'required|string',
                'description' => 'required|string',
                'assigned_to' => 'required|integer',
            ]);

            $savedFiles = [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path         = $file->store('consultations/attachments', 'public');
                    $savedFiles[] = [
                        'name' => $file->getClientOriginalName(),
                        'path' => $path
                    ];
                }
            }

            DB::table('user_consultations')->insert([
                'title'       => $request->title,
                'type'        => $request->type,
                'description' => $request->description,
                'status'      => 'قيد المراجعة', // تذهب للموظف مباشرة
                'assigned_to' => $request->assigned_to,
                'attachment'  => !empty($savedFiles) ? json_encode($savedFiles, JSON_UNESCAPED_UNICODE) : null,
                'user_id'     => auth()->id(), // المدير هو المنشئ
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            return back()->with('success', 'تم إنشاء التكليف القانوني الداخلي وإحالته للمستشار القانوني مباشرة بنجاح!');
        }

        // السيناريو العادي لإسناد الطلبات الواردة
        $request->validate([
            'assigned_to' => 'required|integer',
        ]);

        DB::table('user_consultations')
            ->where('consultation_id', $id)
            ->update([
                'assigned_to' => $request->assigned_to,
                'status'      => 'قيد المراجعة', // تحديث المسمى الموحد هنا بالملي
                'updated_at'  => now(),
            ]);

        return back()->with('success', 'تم إسناد طلب الاستشارة للمستشار القانوني لبدء الدراسة بنجاح!');
    }

    /**
     * 👑 جلب قائمة بكل الاستشارات الواردة للمدير القانوني (جدول المتابعة والاعتماد)
     */
    public function managerIncomingConsultations()
    {
        $consultations = DB::table('user_consultations')
            ->leftJoin('users', 'user_consultations.user_id', '=', 'users.user_id')
            ->select('user_consultations.*', 'users.name as user_name')
            ->whereIn('user_consultations.status', ['قيد المراجعة', 'بانتظار الاعتماد', 'قيد الإسناد', 'قيد الاعتماد'])
            ->orderBy('user_consultations.status', 'desc') 
            ->orderBy('user_consultations.created_at', 'desc')
            ->get();

        foreach ($consultations as $item) {
            // تصحيح التوقيت ليعتمد توقيت الرياض
            $item->created_at = \Carbon\Carbon::parse($item->created_at)->timezone('Asia/Riyadh')->format('Y/m/d H:i');
            
            // حماية المسميات حياً عند العرض
            if ($item->status === 'بانتظار الاعتماد') {
                $item->status = 'بانتظار الاعتماد';
            }
        }

        return view('Consultations.legalManagerPage.ResponseManager', compact('consultations'));
    }

    /**
     * 🛠️ قرار المدير النهائي: اعتماد الرد ونشره للمخدم أو الرفض النهائي للمعاملة بالكامل
     */
    public function approveConsultation(Request $request, $id)
    {
        if ($request->input('manager_action') === 'approve') {
            DB::table('user_consultations')
                ->where('consultation_id', $id)
                ->update([
                    'status'     => 'تم الرد', 
                    'updated_at' => now()
                ]);
                
            return redirect()->route('manager.consultations.incoming')->with('success', 'تم اعتماد ونشر الرد القانوني وإتاحته للموظف الداخلي بنجاح!');
        } else {
            DB::table('user_consultations')
                ->where('consultation_id', $id)
                ->update([
                    'status'           => 'مرفوض', 
                    'rejection_reason' => $request->input('manager_notes'), 
                    'updated_at'       => now()
                ]);
                
            return redirect()->route('manager.consultations.incoming')->with('success', 'تم رفض طلب الاستشارة وإغلاق المعاملة مع إشعار المستخدم الداخلي بالرفض.');
        }
    }

    /**
     * السجل القانوني التاريخي (أرشيف الاعتمادات المحمي للمدير)
     */
    public function managerRecords()
    {
        $allRequests = DB::table('user_consultations')
            ->whereIn('status', ['تم الرد', 'معتمد', 'مرفوض'])
            ->orderBy('updated_at', 'desc')
            ->get();

        foreach ($allRequests as $request) {
            $request->type_category = 'استشارة'; 
            $request->created_at    = \Carbon\Carbon::parse($request->created_at)->timezone('Asia/Riyadh');
        }

        return view('Consultations.legalManagerPage.LegalRecord', compact('allRequests'));
    }

    /**
     * دالة استقبال وحفظ الاستشارة القانونية من الموظف الداخلي (المسودات أو الإرسال المباشر)
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'         => 'required|string|max:255',
            'type'          => 'required|string',
            'description'   => 'required|string',
            'attachments.*' => 'nullable|file|max:10240', 
        ]);

        $status    = ($request->action === 'draft') ? 'مسودة' : 'قيد الإسناد'; // تحديث المسمى هنا بالملي
        $finalType = ($request->type === 'أخرى') ? $request->custom_type : $request->type;

        $savedFiles = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path         = $file->store('consultations/attachments', 'public');
                $savedFiles[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path
                ];
            }
        }

        DB::table('user_consultations')->insert([
            'title'       => $request->title,
            'type'        => $finalType,
            'description' => $request->description,
            'status'      => $status,
            'attachment'  => !empty($savedFiles) ? json_encode($savedFiles, JSON_UNESCAPED_UNICODE) : null,
            'user_id'     => auth()->id(), 
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        return redirect()->route('internal.orders.records')->with('success', 'تم حفظ الطلب القانوني بنجاح!');
    }

    public function edit($id)
    {
        $draft = DB::table('user_consultations')
            ->where('consultation_id', $id)
            ->where('user_id', auth()->id())
            ->where('status', 'مسودة')
            ->first();

        if (!$draft) {
            return redirect()->route('internal.orders.records')->with('error', 'المسودة المطلوبة غير موجودة.');
        }

        return view('Consultations.userPage.RequestConsultation', compact('draft'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'type'        => 'required|string',
            'description' => 'required|string',
        ]);

        $status    = ($request->action === 'draft') ? 'مسودة' : 'قيد الإسناد';
        $finalType = ($request->type === 'أخرى') ? $request->custom_type : $request->type;

        DB::table('user_consultations')
            ->where('consultation_id', $id)
            ->update([
                'title'       => $request->title,
                'type'        => $finalType,
                'description' => $request->description,
                'status'      => $status,
                'updated_at'  => now(),
            ]);

        return redirect()->route('internal.orders.records')->with('success', 'تم تحديث البيانات بنجاح!');
    }

    public function destroy($id)
    {
        DB::table('user_consultations')
            ->where('consultation_id', $id)
            ->where('user_id', auth()->id())
            ->where('status', 'مسودة')
            ->delete();

        return back()->with('success', 'تم حذف المسودة المحددة بنجاح.');
    }

    /**
     * سجل المعاملات التاريخية الخاص بالموظف القانوني (Role 2)
     */
    public function employeeRecord()
    {
        $userId = auth()->id();

        $myReplies = DB::table('user_consultations')
            ->leftJoin('users', 'user_consultations.user_id', '=', 'users.user_id')
            ->select('user_consultations.*', 'users.name as user_name')
            ->where('user_consultations.assigned_to', $userId)
            ->whereNotNull('user_consultations.reply') 
            ->orderBy('user_consultations.updated_at', 'desc')
            ->get();

        foreach ($myReplies as $reply) {
            $reply->type_category = 'استشارة';
        }

        return view('Consultations.legalEmployeePage.EmployeeRecord', compact('myReplies'));
    }

    /**
     * 📥 عرض الاستشارات المعلقة المسندة للموظف القانوني ليرد عليها
     */
    public function employeeIncomingConsultations()
    {
        $userId = auth()->id();

        $consultations = DB::table('user_consultations')
            ->where('assigned_to', $userId)
            ->whereIn('status', ['قيد الدراسة', 'قيد المراجعة', 'بحاجة لتعديل'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('Consultations.legalEmployeePage.ResponseConsultation', compact('consultations'));
    }

    /**
     * 🌟 عرض صفحة صياغة الرد القانوني المخصصة للموظف
     */
    public function showResponsePage($id)
    {
        $consultation = DB::table('user_consultations')->where('consultation_id', $id)->first(); 

        if (!$consultation) {
            abort(404, 'الاستشارة المطلوبة غير موجودة.');
        }

        return view('Consultations.legalEmployeePage.ResponseConsultation', compact('consultation'));
    }

    /**
     * 🌟 استقبال الرد المكتوب من الموظف القانوني وتخزين الملفات المتعددة (حتى 3 مستندات)
     * 🌟 تم إصلاح وحل المشكلة وتوجيه الملفات لحقل reply_attachment بالملي لتقرأها شاشة المدير والملفات المؤرشفة
     */
    public function submitResponse(Request $request, $id)
    {
        $request->validate([
            'legal_response'           => 'required|string',
            'employee_attachments.*'   => 'nullable|file|max:10240',
        ]);

        $updateData = [
            'reply'      => $request->legal_response,
            'status'     => 'بانتظار الاعتماد', // يتم قراءتها كـ "قيد الاعتماد" في الواجهة حياً
            'updated_at' => now(),
        ];

        // 📎 معالجة وتخزين مصفوفة الملفات المرفوعة من الموظف القانوني وحفظها كـ JSON بداخل حقل reply_attachment
        if ($request->hasFile('employee_attachments')) {
            $savedReplyFiles = [];
            foreach ($request->file('employee_attachments') as $file) {
                $path = $file->store('consultations/replies_attachments', 'public');
                $savedReplyFiles[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path
                ];
            }
            
            // 🌟 الحل الهندسي المعتمد لحل مشكلة عدم الظهور عند المدير 🌟
            $updateData['reply_attachment'] = json_encode($savedReplyFiles, JSON_UNESCAPED_UNICODE);
        }

        DB::table('user_consultations')
            ->where('consultation_id', $id)
            ->where('assigned_to', auth()->id()) //  تم تعديل السطر وإصلاح دالة التوثيق هنا بنجاح
            ->update($updateData);

        return redirect()->route('legal.consultations.index')->with('success', 'تم صياغة المذكرة القانونية ورفعها للمدير القانوني بانتظار الاعتماد النهائي.');
    }

    /**
     * سجل المعاملات التاريخية الخاص بالمستخدم الداخلي (Role 3)
     */
    public function internalRecords()
    {
        $allRequests = DB::table('user_consultations')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($allRequests as $request) {
            $request->type_category = 'استشارة'; 
            $request->created_at    = \Carbon\Carbon::parse($request->created_at)->timezone('Asia/Riyadh');
        }

        return view('Consultations.userPage.OrderRecordsUser', compact('allRequests'));
    }

    /**
     * 🛠️ الدالة الخاصة بصفحة المهام الإدارية القديمة المحفوظة كتعليق:
     */
    public function employeeTasks()
    {
        $userId = auth()->id();

        $consultations = DB::table('user_consultations')
            ->where('assigned_to', $userId)
            ->whereIn('status', ['قيد الدراسة', 'قيد المراجعة', 'بحاجة لتعديل'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('Consultations.legalEmployeePage.EmployeeTasks', compact('consultations'));
    }
}
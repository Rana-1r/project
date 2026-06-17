<?php 

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// هذا المايقريشن يضيف عمود جديد "employee_attachment" إلى جدول "user_consultations" لتخزين مرفقات الموظف القانونية

return new class extends Migration
{
    // هذا المايقريشن يضيف عمود جديد "employee_attachment" إلى جدول "user_consultations" لتخزين مرفقات الموظف القانونية
    public function up(): void
    {
        Schema::table('user_consultations', function (Blueprint $table) {
            $table->longText('employee_attachment')->nullable();
        });
    }
    // في حالة التراجع عن هذا المايقريشن، سيتم حذف عمود "employee_attachment" من جدول "user_consultations"
    public function down(): void
    {
        Schema::table('user_consultations', function (Blueprint $table) {
            $table->dropColumn('employee_attachment');
        });
    }
};
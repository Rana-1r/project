<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
// هذا المايقريشن ينشئ جدول "user_tasks" لتخزين المهام المكلفة للموظفين القانونيين، مع الحقول اللازمة لتفاصيل المهمة وحالتها وربطها بالمستخدمين
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('user_tasks', function (Blueprint $table) {
        $table->id('task_id'); // المعرف الرئيسي للمهمة
        $table->string('title'); // عنوان التكليف
        $table->string('type'); // نوع المعاملة
        $table->unsignedBigInteger('assigned_to'); // رقم الموظف القانوني المكلف
        $table->string('status')->default('جاري العمل'); // حالة المهمة
        $table->timestamps(); // حقول created_at و updated_at

        // ربط العلاقة مع جدول المستخدمين الأساسي لضمان سلامة البيانات
        $table->foreign('assigned_to')->references('user_id')->on('users')->onDelete('cascade');
    });
}
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
// هذا الميجريشن لإضافة حقل "assigned_to" إلى جدول "user_consultations" لتحديد الموظف القانوني المسؤول عن الاستشارة
//لتحديد الموظف القانوني المسؤول عن الاستشارة
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    
    public function up(): void
    {
    Schema::table('user_consultations', function (Blueprint $table) {
        // إضافة حقل الموظف القانوني ويكون nullable لأنه في البداية ما يكون مسند لأحد
        $table->unsignedBigInteger('assigned_to')->nullable()->after('user_id');
        
        // إذا حاب تسوي لها الربط كـ Foreign Key مع جدول المستخدمين
        $table->foreign('assigned_to')->references('user_id')->on('users')->onDelete('set null');
    });
    }

public function down(): void
{
    Schema::table('user_consultations', function (Blueprint $table) {
        $table->dropForeign(['assigned_to']);
        $table->dropColumn('assigned_to');
    });
}
};

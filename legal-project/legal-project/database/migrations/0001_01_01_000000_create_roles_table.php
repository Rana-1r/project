<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
// هذا هو ملف لإنشاء جدول الأدوار (roles) في قاعدة البيانات
return new class extends Migration {
    // هذا هو ملف لإنشاء جدول الأدوار (roles) في قاعدة البيانات
    public function up(): void {
        Schema::create('roles', function (Blueprint $table) {
            $table->id('role_id'); 
            $table->string('role_name');
            $table->timestamps();
        });
    }
    // هذا هو الجزء الخاص بإلغاء إنشاء جدول الأدوار في حالة التراجع عن الملف
    public function down(): void {
        Schema::dropIfExists('roles');
    }
};
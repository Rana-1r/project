<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// هذا المايقريشن ينشئ جدول "user_consultations" لتخزين استشارات المستخدمين القانونية، مع الحقول اللازمة لتفاصيل الاستشارة وحالتها وربطها بالمستخدمين
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_consultations', function (Blueprint $table) {
            $table->id('consultation_id'); 
            $table->string('title');       
            $table->string('type');        
            $table->text('description');   
            $table->string('status');      
            $table->text('reply')->nullable(); 
            
            // 🌟 الحقل الموحد لاستقبال مرفقات الموظف القانوني
            $table->text('reply_attachment')->nullable(); 
            
            // الحقول الإضافية وحقل الرفع الخاص بالمستخدم الداخلي الأصلي
            $table->unsignedBigInteger('user_id'); 
            $table->text('attachment')->nullable(); 
            $table->timestamps();         
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_consultations');
    }
};
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // 👈 استدعاء مكتبة العلاقات

class UserConsultation extends Model
{
    use HasFactory;

    // تحديد اسم الجدول بدقة في قاعدة البيانات
    protected $table = 'user_consultations';

    // تحديد المفتاح الأساسي المخصص
    protected $primaryKey = 'consultation_id';

    // الحقول المسموح بحفظها وتعديلها تلقائياً
    protected $fillable = [
        'title',
        'type',
        'description',
        'status',
        'reply',
        'user_id',
        'attachment',
    ];

    /**
     * العلاقة: الاستشارة تنتمي إلى مستخدم (عميل أو مقدم الطلب)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // 👈 استدعاء مكتبة العلاقات

class UserTask extends Model
{
    use HasFactory;

    // تحديد اسم الجدول بدقة في قاعدة البيانات
    protected $table = 'user_tasks';

    // تحديد المفتاح الأساسي المخصص
    protected $primaryKey = 'task_id';

    // الحقول المسموح بحفظها وتعديلها تلقائياً
    protected $fillable = [
        'title',
        'type',
        'assigned_to',
        'status',
    ];

    /**
     * العلاقة: المهمة تنتمي أو مكلفة إلى موظف قانوني محدد
     * (لاحظي أن الربط هنا يتم عبر حقل assigned_to المخصص في المايجريشن)
     */
    public function assignedEmployee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to', 'user_id');
    }

    /**
 * العلاقة: المستخدم يمكنه تقديم العديد من الاستشارات القانونية
 */
public function consultations(): \Illuminate\Database\Eloquent\Relations\HasMany
{
    return $this->hasMany(UserConsultation::class, 'user_id', 'user_id');
}

/**
 * العلاقة: الموظف القانوني يمكن أن تُسند إليه العديد من المهام القانونية
 */
public function tasks(): \Illuminate\Database\Eloquent\Relations\HasMany
{
    return $this->hasMany(UserTask::class, 'assigned_to', 'user_id');
}

}
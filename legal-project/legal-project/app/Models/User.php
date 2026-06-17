<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // 👈 استدعاء مكتبة العلاقات

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'user_id'; 

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'photo',
        'role_id',
        'department', // 👈 أضفت لك هذا الحقل لأنه موجود في المايجريشن ولم يكن مضافاً هنا
        'is_active'   // 👈 وأضفت هذا الحقل أيضاً لتتمكن من تعديله مستقبلاً في لوحة التحكم
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * العلاقة: المستخدم ينتمي إلى دور وظيفي معين
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }
}
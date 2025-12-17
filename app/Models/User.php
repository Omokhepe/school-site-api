<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'user_id',
        'gender',
        'password',
        'role',
        'class_id',
        'must_change_password',
        'email',
        'phone_number',
        'address',
        'date_of_birth',
        'state_of_origin',
        'image',
    ];

    public function class(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
      return $this->belongsTo(SchoolClass::class,'class_id');
    }

    public function studentProfile()
    {
        return $this->hasOne(StudentProfile::class, 'user_id', 'user_id');
    }
    public function staffProfile()
    {
        return $this->hasOne(StaffProfile::class, 'user_id', 'user_id');
    }
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
//            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'role_id',
        'name',
        'email',
        'phone',
        'address',
        'password',
    ];

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
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function hasRole(string $roleName): bool
    {
        return $this->role && $this->role->name === $roleName;
    }

    public function isStaff(): bool
    {
        return $this->hasRole('staf_klinik');
    }

    public function isPetOwner(): bool
    {
        return $this->hasRole('pemilik_hewan');
    }

    public function pets()
    {
        return $this->hasMany(Pet::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function handledAppointments()
    {
        return $this->hasMany(Appointment::class, 'handled_by');
    }

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class, 'vet_id');
    }

    public function restocks()
    {
        return $this->hasMany(MedicineRestock::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function receivedPayments()
    {
        return $this->hasMany(Payment::class, 'received_by');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PhpParser\ErrorHandler\Collecting;
use PhpParser\Node\Expr\Array_;

class Expert extends Model
{
    use HasFactory;

    protected $gaurded = [];

    protected $hidden = [
        'created_at',
        'updated_at',
        'id',
        'user_id'
    ];

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function chats()
    {
        return $this->hasMany(Chat::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function consultations()
    {
        return $this->hasMany(Consultation::class);
    }



    public function workdays()
    {
        return $this->hasMany(WorkDay::class);
    }

    protected function scopeUser($query, $user_id) { // get expert's user
        $query->whereHas(
            'user',
            fn($query) => $query->where('id', $user_id)
        );
    }

    protected function scopeFilter($query, $filters)
    {
        //TODO: search for user by name or type of consultation they offer
        //     $query->when($filters['search'] ?? false, fn($query, $search) =>
        //         $query->where(fn($query) =>
        //             $query
        //                 ->whereHas('user', fn($query) =>
        //                     $query
        //                         ->where('full_name_en', 'like', '%' . $search . '%')
        //                         ->orWhere('full_name_ar', 'like', '%' . $search . '%')
        //                 )
        //                 ->orWhereExists(fn($query) =>
        //                     $query
        //                         ->from('consultations')
        //                         ->where('type_en', 'like', '%' . $search . '%')
        //                         ->orwhere('type_ar', 'like', '%' . $search . '%')
        //                         )
        // );

        $query->
            when(
                $filters['consulttype'] ?? false,
                fn($query, $consulttype) =>
                $query->
                    whereHas(
                        'consultations', fn($query) =>
                        $query->where('id', $consulttype)
                    )
            );
    }

}
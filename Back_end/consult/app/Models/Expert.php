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
        return $consultations =  $this->hasMany(Consultation::class)->with('consult_type');
    }



    public function workdays()
    {
        return $this->hasMany(WorkDay::class);
    }

    protected function scopeFilter($query, $filters)
    {
        $query->when($filters['search'] ?? false, fn($query, $search) =>
            $query->where(fn($query) =>
                $query
                    ->whereHas('user', fn($query) =>
                        $query
                            ->where('full_name_en', 'like', '%' . $search . '%')
                            ->orWhere('full_name_ar', 'like', '%' . $search . '%')
                    )
                    ->orWhereExists(fn($query) =>
                        $query
                            ->from('consult_types')
                            ->where('type_en', 'like', '%' . $search . '%')
                            ->orwhere('type_ar', 'like', '%' . $search . '%')
                            ->andWhereHas('consultations', fn($query) =>
                                $query
                                    ->whereColumn('consultations.consult_type_id', 'consult_types.id')
                                    ->andWhereCulomn('consultations.expert_id', 'experts.id')
                            )
                    )
            )
        );

        $query->
            when($filters['consulttype'] ?? false, fn($query, $consulttype) =>
            $query->
                whereHas('consultations', fn($query) =>
                $query->where('consult_type_id', $consulttype)
            )
        );
    }
}

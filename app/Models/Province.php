<?php

/*
 * This file is part of the IndoRegion package.
 *
 * (c) Azis Hapidin <azishapidin.com | azishapidin@gmail.com>
 *
 */

namespace App\Models;

// use AzisHapidin\IndoRegion\Traits\ProvinceTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Province Model.
 */
class Province extends Model
{
    // use ProvinceTrait;
    use HasFactory;
    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'provinces';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $fillable = [
        'name',
        'id'
    ];

    /**
     * Province has many regencies.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function regencies()
    {
        return $this->hasMany(Regency::class);
    }


}
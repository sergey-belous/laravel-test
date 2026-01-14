<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'unit',
        'is_required',
        'is_filterable',
        'options',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_filterable' => 'boolean',
        'options' => 'array',
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_attribute')
            ->withPivot(['is_required', 'display_order'])
            ->withTimestamps();
    }

    public function productValues()
    {
        return $this->hasMany(ProductAttributeValue::class);
    }
}


<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class TranslationKey extends Model
{
    use HasFactory;
    protected $fillable = ['namespace', 'key', 'description'];

    public function values()
    {
        return $this->hasMany(TranslationValue::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'translation_key_tag');
    }
}

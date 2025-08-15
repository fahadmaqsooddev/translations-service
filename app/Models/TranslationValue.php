<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class TranslationValue extends Model
{
    use HasFactory;
    protected $fillable = ['translation_key_id', 'locale_id', 'value'];

    public function translationKey()
    {
        return $this->belongsTo(TranslationKey::class, 'translation_key_id');
    }

    public function locale()
    {
        return $this->belongsTo(Locale::class);
    }
}

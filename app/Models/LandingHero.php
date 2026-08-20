<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingHero extends Model
{
    protected $fillable = [
        'badge_text',
        'title_line_1', 'title_line_1_highlight',
        'title_line_2', 'title_line_2_highlight',
        'title_line_3', 'title_line_3_highlight',
        'subtitle',
        'cta_primary_text', 'cta_primary_url',
        'cta_secondary_text', 'cta_secondary_url',
        'trust_count', 'trust_text',
    ];

    /**
     * Hero adalah singleton (cuma 1 baris data). Ambil (atau buat default kalau belum ada).
     */
    public static function singleton(): self
    {
        return static::firstOrCreate(['id' => 1]);
    }
}

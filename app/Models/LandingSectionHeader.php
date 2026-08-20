<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingSectionHeader extends Model
{
    protected $fillable = [
        'section_key',
        'badge',
        'title_normal',
        'title_gradient',
        'subtitle',
        'button_primary_text', 'button_primary_url',
        'button_secondary_text', 'button_secondary_url',
    ];

    /**
     * Daftar section_key yang tersedia + label tampilnya di admin.
     */
    public const SECTIONS = [
        'dashboard' => 'Dashboard Preview',
        'solution'  => 'Solusi (Bento Grid)',
        'contact'   => 'CTA / Contact',
    ];

    public static function forKey(string $key): self
    {
        return static::firstOrCreate(
            ['section_key' => $key],
            [
                'badge'          => ucfirst($key),
                'title_normal'   => 'Judul Section',
                'title_gradient' => 'Highlight',
                'subtitle'       => '',
            ]
        );
    }
}

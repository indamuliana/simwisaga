<?php

namespace App\Modules\AnalitikSpmb\Models;

use Illuminate\Database\Eloquent\Model;

class SpmbIgnoredDuplicate extends Model
{
    protected $fillable = [
        'school_a',
        'school_b',
        'pair_hash',
    ];

    public static function hashPair(string $a, string $b): string
    {
        $sorted = [strtolower(trim($a)), strtolower(trim($b))];
        sort($sorted);

        return hash('sha256', implode('|||', $sorted));
    }

    public static function ignorePair(string $a, string $b): self
    {
        $hash = self::hashPair($a, $b);

        return self::firstOrCreate(
            ['pair_hash' => $hash],
            [
                'school_a' => trim($a),
                'school_b' => trim($b),
            ]
        );
    }
}

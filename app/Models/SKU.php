<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Zone;
use App\Models\Rack;

class SKU extends Model
{
    use HasFactory;

    protected $table = 'skus';

    protected $fillable = [
        'image',
        'sku_code',
        'zone_id',
        'rack_id',
    ];

    public function zone(): BelongsTo {
        return $this->belongsTo(Zone::class);
    }

    public function rack(): BelongsTo {
        return $this->belongsTo(Rack::class);
    }
}

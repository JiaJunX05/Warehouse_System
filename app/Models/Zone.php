<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Storack;
use App\Models\Rack;
use App\Models\SKU;

class Zone extends Model
{
    use HasFactory;

    protected $table = 'zones';

    protected $fillable = [
        'zone_name',
    ];

    public function racks(): HasManyThrough {
        return $this->hasManyThrough(Rack::class, Storack::class, 'zone_id', 'rack_id');
    }

    public function storacks(): HasMany {
        return $this->hasMany(Storack::class, 'zone_id');
    }

    public function skus(): HasMany {
        return $this->hasMany(SKU::class);
    }
}

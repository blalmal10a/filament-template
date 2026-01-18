<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Image extends Model
{
    use HasFactory;
    use HasUuids;

    protected $appends = ['url'];
    protected function url(): Attribute
    {
        return Attribute::make(
            get: fn() => env('APP_URL') . '/storage/' . $this->full_path,
        );
    }
}

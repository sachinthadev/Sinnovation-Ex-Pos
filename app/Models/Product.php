<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'image',
        'barcode',
        'part_number',
        'store_location',
        'price',
        'tax',
        'quantity',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean'
    ];

    public function getImageUrl(): string
    {
        if (! $this->image) {
            return '/img/img-placeholder.jpg';
        }

        $path = ltrim($this->image, '/');
        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('public_uploads');

        if ($disk->exists($path)) {
            return '/uploads/' . $path;
        }

        $basename = pathinfo($path, PATHINFO_BASENAME);
        if ($disk->exists($basename)) {
            return '/uploads/' . $basename;
        }

        return '/uploads/' . $basename;
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FolderResponse extends Model
{
    use HasFactory;

    protected $fillable = ['folder_id', 'response'];

    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }
}

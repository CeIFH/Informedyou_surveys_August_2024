<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function surveys()
    {
        return $this->hasMany(Survey::class);
    }

    // New method to save folder response
    public function saveResponse($response)
    {
        $this->responses()->create(['response' => $response]);
    }

    public function responses()
    {
        return $this->hasMany(FolderResponse::class);
    }
}
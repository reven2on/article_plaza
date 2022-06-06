<?php

namespace App\Models;

use App\Models\Article;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserView extends Model
{
    use HasFactory;

    /**
     * Attributes -----------------------------.
     */
    protected $fillable = ['ipaddress'];
    protected $dates = [
        'created_at',
        'updated_at',
    ];
    /**
     * Relations -----------------------------.
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}

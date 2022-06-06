<?php

namespace App\Models;

use App\Models\Rating;
use App\Models\Category;
use App\Models\UserView;
use App\Services\ArticleService;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Article extends Model
{
    use HasFactory;

    /**
     * Attributes -----------------------------.
     */
    protected $fillable = ['title','body','calculated_rate'];
    protected $dates = [
        'created_at',
        'updated_at',
    ];

   
    /**
     * Accessors & Mutators -----------------------------.
     */
    /*protected function score(): Attribute
    {
        return Attribute::make(
            get: function () {
                if($this->ratings_sum_rate > 0) {
                    $articleService = new ArticleService();
                    return $articleService->calcArticleRate($this);
                }else{
                    return 0;
                }
                
            },
        );
    }*/

    /**
     * Scopes -----------------------------.
     */
    public function scopeSearch($query, $request)
    {
        $searchTerm = $request->input('searchTerm');
        if (!empty($searchTerm)) {
            return $query
                ->where('title', 'like', '%' . $searchTerm . '%')
                ->orWhere('body', 'like', '%' . $searchTerm . '%');
        }
    }

    public function scopeFilter($query, $request)
    {

        $filter = $request->input('filter');
        if (!empty($filter)) {
            if (Arr::exists($filter, 'categories')) {
                $categories=$filter['categories'];
                $query->whereRelation('categories', 'title', $categories);
            }
            if (Arr::exists($filter, 'date')) {
                $dateRange=$filter['date'];
                $query->whereBetween('created_at', [$dateRange[0], $dateRange[1]]);
            }
        }

        return $query;
    }

    public function scopeSort($query, $request)
    {
        $sort = $request->input('sort');
        if (!empty($sort)) {
            if (Arr::exists($sort, 'popularity')) {
                if ($sort['popularity'] === 'view') {
                    return $query->withCount('userViews')->orderBy('user_views_count', 'desc');
                }
                if ($sort['popularity'] === 'rating') {
                    return $query->orderBy('calculated_rate', 'desc');
                }
            }
            if (Arr::exists($sort, 'trending')) {
                $dateRange=$sort['trending'];
                return $query->withCount(['userViews' => function (Builder $query) use ($dateRange) {
                    $query->whereBetween('created_at', [$dateRange[0], $dateRange[1]]);
                }]);
            }
        }
    }

    /**
     * Relations -----------------------------.
     */
    public function userViews(): HasMany
    {
        return $this->hasMany(UserView::class);
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

  
}

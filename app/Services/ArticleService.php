<?php 
namespace App\Services;

use App\Models\Rating;
use App\Models\Article;
use App\Models\Category;
use App\Events\ArticleRated;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Builder;
use App\Exceptions\TooMuchRatingsException;
use App\Exceptions\RatingAlreadyExistException;

class ArticleService
{
    public function createArticle($request): Article
    {
        $validated = $request->validated();
        $article = Article::create($validated);
        $categories = Category::whereIn('title', array(array_values($validated['categories'])))->pluck('id');
        $article->categories()->attach($categories);
        return $article;
    }

    public function rateArticle($article,$request): Rating
    {
        $numberOfRatings = Rating::where('ipaddress', $request->ip())->whereDate('created_at', Carbon::today())->count();
        if($numberOfRatings >= config('article.daily_limit')){
            throw new TooMuchRatingsException(__('messages.reach_daily_rating_limit'));
        }
        $rate = $article->ratings()->firstOrCreate(
            ['ipaddress' => $request->ip()],
            ['rate' => $request->input('rate')]
        );
        if ($rate->wasRecentlyCreated) {
            event(new ArticleRated($article));
            return $rate;
        } else {
            throw new RatingAlreadyExistException(__('messages.article_rated_before'));
        }
    }

    public function calcArticleRate($article)
    {
        $init_arr= [ 1=> 0, 2=> 0, 3=>0, 4=>0, 5=>0];
        $ratings = $article->ratings->pluck('rate')->toArray();
        $ratings =array_count_values($ratings);
        for($i=1;$i<=5;$i++) {
            if(isset($ratings[$i])) { 
                $init_arr[$i]=$ratings[$i];
            }
        }
        $ratings=$init_arr;   
        
        ksort($ratings);
        
        $confidenceZ = 1.65;
        $fakeRatings = array_map(function($item) { return ++$item; },$ratings);
        $n = array_sum($fakeRatings);
        $average = array_sum(array_map(function($item, $i) use ($n) { return (($i+1)*$item)/$n; },$fakeRatings, array_keys($fakeRatings)));
        $x = array_sum(array_map(function($item, $i) use ($n) { return (pow(($i+1),2)*$item)/$n; },$fakeRatings, array_keys($fakeRatings)));
        $standardDeviation = sqrt($x - pow($average,2)) / ($n+1);

       

        return round($average - $confidenceZ * $standardDeviation,2);
    }


}


?>
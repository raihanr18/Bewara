<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function __invoke(Request $request){
        $currentDateTime = Carbon::now();

        $featuredPosts = Cache::remember('featuredPosts', now()->addDay(), function () use ($currentDateTime) {
            return Post::published()
                ->where('published_at', '<=', $currentDateTime) // Hanya tambahkan kondisi ini sekali
                ->featured()
                ->with('categories')
                ->latest('published_at')
                ->take(3)
                ->get();
        });

        $latestPosts = Cache::remember('latestPosts', now()->addDay(), function () use ($currentDateTime) {
            return Post::published()
                ->where('published_at', '<=', $currentDateTime) // Hanya tambahkan kondisi ini sekali
                ->with('categories')
                ->latest('published_at')
                ->take(9)
                ->get();
        });

        return view('home', [
            'featuredPosts' => $featuredPosts,
            'latestPosts' => $latestPosts
        ]);
    }
}
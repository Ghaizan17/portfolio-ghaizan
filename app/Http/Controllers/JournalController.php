<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Profile;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class JournalController extends Controller
{
    public function index(Request $request): View
    {
        $categories = PostCategory::query()
            ->ordered()
            ->withCount(['posts' => fn ($query) => $query->published()])
            ->get();

        $activeCategory = null;

        if ($request->filled('category')) {
            $activeCategory = $categories->firstWhere('slug', $request->string('category')->toString());
        }

        $featuredPost = $activeCategory
            ? null
            : Post::query()->published()->ordered()->with('category')->first();

        $posts = Post::query()
            ->published()
            ->ordered()
            ->with(['category', 'author'])
            ->when($activeCategory, fn ($query) => $query->whereBelongsTo($activeCategory, 'category'))
            ->when($featuredPost, fn ($query) => $query->whereKeyNot($featuredPost->getKey()))
            ->paginate(config('digital-dimension.pagination.journal'))
            ->withQueryString();

        return view('pages.journal.index', [
            'profile' => Profile::current(),
            'posts' => $posts,
            'categories' => $categories,
            'activeCategory' => $activeCategory,
            'featuredPost' => $featuredPost,
        ]);
    }

    public function show(string $slug): View
    {
        $post = Post::query()
            ->published()
            ->with(['category', 'author', 'tags'])
            ->where('slug', $slug)
            ->firstOrFail();

        $relatedPosts = Post::query()
            ->published()
            ->ordered()
            ->with('category')
            ->whereKeyNot($post->getKey())
            ->when($post->post_category_id, fn ($query) => $query->where('post_category_id', $post->post_category_id))
            ->limit(3)
            ->get();

        if ($relatedPosts->isEmpty()) {
            $relatedPosts = Post::query()
                ->published()
                ->ordered()
                ->with('category')
                ->whereKeyNot($post->getKey())
                ->limit(3)
                ->get();
        }

        return view('pages.journal.show', [
            'profile' => Profile::current(),
            'post' => $post,
            'relatedPosts' => $relatedPosts,
        ]);
    }
}

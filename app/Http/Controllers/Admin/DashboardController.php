<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Requests\StoreSourceRequest;
use App\Http\Requests\UpdateSourceRequest;
use App\Models\Article;
use App\Models\Category;
use App\Models\Source;
use App\Services\AIClassifierService;
use App\Services\NewsCrawlerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    /**
     * Show admin dashboard
     */
    public function index(): View
    {
        $stats = [
            'total_articles' => Article::count(),
            'published_articles' => Article::published()->count(),
            'total_categories' => Category::count(),
            'total_sources' => Source::count(),
            'today_articles' => Article::whereDate('created_at', today())->count(),
            'total_views' => Article::sum('views_count'),
        ];

        $recentArticles = Article::with(['category', 'source'])
            ->latest()
            ->take(10)
            ->get();

        $topArticles = Article::published()
            ->with(['category'])
            ->orderBy('views_count', 'desc')
            ->take(10)
            ->get();

        $categoryStats = Category::withCount(['articles', 'publishedArticles'])
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'recentArticles',
            'topArticles',
            'categoryStats'
        ));
    }

    /**
     * Trigger manual crawl
     */
    public function crawl(NewsCrawlerService $crawler): RedirectResponse
    {
        try {
            $count = $crawler->crawlAll();

            return redirect()->back()->with('success', "Đã crawl {$count} bài viết mới!");
        } catch (\Exception $e) {
            Log::error('Crawl error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Lỗi khi crawl: ' . $e->getMessage());
        }
    }

    /**
     * Articles management
     */
    public function articles(): View
    {
        $articles = Article::with(['category', 'source'])
            ->latest()
            ->paginate(20);

        return view('admin.articles.index', compact('articles'));
    }

    /**
     * Edit article
     */
    public function editArticle(Article $article): View
    {
        $categories = Category::active()->ordered()->get();
        return view('admin.articles.edit', compact('article', 'categories'));
    }

    /**
     * Update article
     */
    public function updateArticle(Request $request, Article $article): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'category_id' => 'required|exists:categories,id',
            'content' => 'required',
            'summary' => 'nullable',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        $article->update($validated);

        return redirect()->route('admin.articles')->with('success', 'Đã cập nhật bài viết!');
    }

    /**
     * Delete article
     */
    public function deleteArticle(Article $article): RedirectResponse
    {
        $article->delete();
        return redirect()->back()->with('success', 'Đã xóa bài viết!');
    }

    /**
     * Categories management
     */
    public function categories(): View
    {
        $categories = Category::withCount('articles')->ordered()->get();
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Sources management
     */
    public function sources(): View
    {
        $sources = Source::withCount('articles')->get();
        return view('admin.sources.index', compact('sources'));
    }

    /**
     * Test AI Classification page
     */
    public function testAI(): View
    {
        $categories = Category::active()->ordered()->get();
        return view('admin.test-ai', compact('categories'));
    }

    /**
     * Process AI Classification test
     */
    public function processAI(Request $request, AIClassifierService $aiService): JsonResponse
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string|min:50',
            ]);

            // Gọi AI Service để phân loại
            $result = $aiService->classifyArticle($validated['title'], $validated['content']);

            // Lấy tên danh mục nếu có
            $categoryName = null;
            if ($result['category_id']) {
                $category = Category::find($result['category_id']);
                $categoryName = $category?->name;
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'category_id' => $result['category_id'],
                    'category_name' => $categoryName ?? 'Không xác định',
                    'confidence_score' => round($result['confidence_score'] * 100, 2) . '%',
                    'summary' => $result['summary'] ?? 'Không có tóm tắt',
                    'tags' => $result['tags'] ?? [],
                    'metadata' => $result['metadata'] ?? [],
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('AI Test Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi xử lý AI: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Create category page
     */
    public function createCategory(): View
    {
        return view('admin.categories.create');
    }

    /**
     * Store new category
     */
    public function storeCategory(StoreCategoryRequest $request): RedirectResponse
    {
        Category::create($request->validated());

        return redirect()->route('admin.categories')->with('success', 'Đã thêm danh mục mới!');
    }

    /**
     * Edit category page
     */
    public function editCategory(Category $category): View
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update category
     */
    public function updateCategory(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $category->update($request->validated());

        return redirect()->route('admin.categories')->with('success', 'Đã cập nhật danh mục!');
    }

    /**
     * Delete category
     */
    public function deleteCategory(Category $category): RedirectResponse
    {
        $category->delete();
        return redirect()->back()->with('success', 'Đã xóa danh mục!');
    }

    /**
     * Create source page
     */
    public function createSource(): View
    {
        return view('admin.sources.create');
    }

    /**
     * Store new source
     */
    public function storeSource(StoreSourceRequest $request): RedirectResponse
    {
        Source::create($request->validated());

        return redirect()->route('admin.sources')->with('success', 'Đã thêm nguồn tin mới!');
    }

    /**
     * Edit source page
     */
    public function editSource(Source $source): View
    {
        return view('admin.sources.edit', compact('source'));
    }

    /**
     * Update source
     */
    public function updateSource(Request $request, Source $source): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:sources,name,' . $source->id,
            'url' => 'required|url|unique:sources,url,' . $source->id,
            'rss_url' => 'nullable|url',
            'logo' => 'nullable|url',
            'crawl_frequency' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $source->update($validated);

        return redirect()->route('admin.sources')->with('success', 'Đã cập nhật nguồn tin!');
    }

    /**
     * Delete source
     */
    public function deleteSource(Source $source): RedirectResponse
    {
        $source->delete();
        return redirect()->back()->with('success', 'Đã xóa nguồn tin!');
    }

    /**
     * Create article page
     */
    public function createArticle(): View
    {
        $categories = Category::active()->ordered()->get();
        $sources = Source::where('is_active', true)->get();
        return view('admin.articles.create', compact('categories', 'sources'));
    }

    /**
     * Classify article using AI
     */
    public function classifyArticle(Request $request, AIClassifierService $aiService): JsonResponse
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string|min:20',
            ]);

            // Use AI Service to classify
            $result = $aiService->classifyArticle($validated['title'], $validated['content']);

            // Get category name if found
            $categoryName = null;
            $categoryColor = null;
            if ($result['category_id']) {
                $category = Category::find($result['category_id']);
                $categoryName = $category?->name;
                $categoryColor = $category?->color ?? '#3b82f6';
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'category_id' => $result['category_id'],
                    'category_name' => $categoryName ?? 'Chưa xác định',
                    'category_color' => $categoryColor,
                    'confidence_score' => round($result['confidence_score'] * 100, 2),
                    'summary' => $result['summary'] ?? '',
                    'tags' => $result['tags'] ?? [],
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Article Classification Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi phân loại: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Store new article
     */
    public function storeArticle(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'source_id' => 'nullable|exists:sources,id',
            'content' => 'required|string|min:50',
            'summary' => 'nullable|string',
            'image_url' => 'nullable|url',
            'author' => 'nullable|string|max:255',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        $validated['published_at'] = now();
        $validated['original_url'] = $request->input('original_url', route('home'));

        Article::create($validated);

        return redirect()->route('admin.articles')->with('success', 'Đã thêm bài viết mới!');
    }
}

<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\Article;
use App\Services\ArticleService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Controller for managing articles.
 */
class ArticleController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Article::query();

            if ($request->has('keyword')) {
                $query->where('title', 'like', '%' . $request->keyword . '%');
            }

            if ($request->has('category')) {
                $query->where('category', $request->category);
            }

            if ($request->has('source')) {
                $query->where('source', $request->source);
            }
            // Return the paginated query result, with 10 items per page
            return ResponseHelper::success($query->paginate(10), 'Articles fetched successfully', 200);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return ResponseHelper::error('Error occured while fetching article', 500);
        }
    }

    public function show($id)
    {
        $view_article = ArticleService::view($id);

        if (!$view_article[0]) {
            return ResponseHelper::error($view_article[1], $view_article[2]);
        }
        return ResponseHelper::success($view_article[1], 'Article fetched successfully', 200);
    }
}

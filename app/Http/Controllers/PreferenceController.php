<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\PreferenceRequest;
use App\Models\Preference;
use App\Models\Article;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PreferenceController extends Controller
{
    /**
     * Store or update user preferences.
     */
    public function store(PreferenceRequest $request)
    {
        try {
            // update or create preference record for the authenticated user
            $preference = Preference::updateOrCreate(
                ['user_id' => Auth::id()],
                [
                    'preferred_sources' => $request->preferred_sources ?? [],
                    'preferred_categories' => $request->preferred_categories ?? [],
                    'preferred_authors' => $request->preferred_authors ?? [],
                ]
            );
            return ResponseHelper::success($preference, "preference stored successfully");
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return ResponseHelper::error();
        }
    }

    /**
     * Retrieve the authenticated user's preferences.
     */
    public function show()
    {
        try {
            $preference = Auth::user()->preference;

            return ResponseHelper::success($preference, "preference successfully retrieved", 200);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return ResponseHelper::error();
        }
    }


    /**
     * Fetch a personalized news feed based on user preferences.
     */
    public function personalizedFeed()
    {
        try {
            $userPreference = Auth::user()->preference;

            if (!$userPreference) {
                return ResponseHelper::error('No preferences found for the user.', 404);
            }

            // Query the articles based on user preferences
            $articles = Article::query()
                ->when($userPreference->preferred_sources, function ($query, $sources) {
                    $query->whereIn('source', $sources);
                })
                ->when($userPreference->preferred_categories, function ($query, $categories) {
                    $query->whereIn('category', $categories);
                })
                ->when($userPreference->preferred_authors, function ($query, $authors) {
                    $query->whereIn('author', $authors);
                })
                ->paginate(10);

            return ResponseHelper::success($articles, 'personalized feeds successfully fetched');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return ResponseHelper::error();
        }
    }
}

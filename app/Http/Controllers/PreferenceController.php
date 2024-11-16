<?php

namespace App\Http\Controllers;

use App\Models\Preference;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PreferenceController extends Controller
{
    /**
     * Store or update user preferences.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'preferred_sources' => 'nullable|array',
            'preferred_categories' => 'nullable|array',
            'preferred_authors' => 'nullable|array',
        ]);

        // Retrieve or create preference record for the authenticated user
        $preference = Preference::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'preferred_sources' => $validatedData['preferred_sources'] ?? [],
                'preferred_categories' => $validatedData['preferred_categories'] ?? [],
                'preferred_authors' => $validatedData['preferred_authors'] ?? [],
            ]
        );

        return response()->json(['message' => 'Preferences saved successfully', 'preference' => $preference], 200);
    }

    /**
     * Retrieve the authenticated user's preferences.
     */
    public function show()
    {
        $preference = Auth::user()->preference;

        return response()->json(['preference' => $preference], 200);
    }


    /**
     * Fetch a personalized news feed based on user preferences.
     */
    public function personalizedFeed()
    {
        $userPreference = Auth::user()->preference;

        if (!$userPreference) {
            return response()->json(['message' => 'No preferences found for the user.'], 404);
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

        return response()->json($articles);
    }
}

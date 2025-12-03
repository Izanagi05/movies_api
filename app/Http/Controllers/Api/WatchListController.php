<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Watchlists;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;


class WatchListController extends Controller
{
    public function index($userId)
    // {
    //     try {
    //         // $userId = $request->query('user_id');
    //         $watchlist = DB::table('watchlists')
    //             ->where('user_id', $userId)
    //             ->get();
    //             // dd($userId);
    //         return response()->json($watchlist);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'message' => 'Failed to fetch watchlist',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }
    {
        // $movieIds = Watchlists::where('user_id', $userId)
        //     ->pluck('movie_api_id')
        //     ->toArray();

        // if (empty($movieIds)) {
        //     return response()->json([]);
        // }

        $tmdbKey = 'dd04310045c6e1d96bd35eaa2dc8e64e';
        // $tmdbKey = env('TMDB_API_KEY');

        $watchlistItems = Watchlists::where('user_id', $userId)->get();
        $movies = [];

        foreach ($watchlistItems as $item) {
            $id = $item->movie_api_id;

            $response = Http::get("https://api.themoviedb.org/3/movie/$id", [
                'api_key' => $tmdbKey,
                'language' => 'en-US'
            ]);

            if ($response->successful()) {
                $tmdb = $response->json();

                $movies[] = [
                    "watchlist_id" => $item->id, // movie ID
                    "id" => $tmdb["id"], // movie ID
                    "title" => $tmdb["title"],
                    "posterPath" => $tmdb["poster_path"],
                    // "rating" => $tmdb["vote_average"],

                    "rating"    => $item->rating !== null
                        ? (float) $item->rating
                        : 0,
                    "addedAt" => $item->created_at->toISOString(),
                ];
            }
        }

        return response()->json($movies);
    }
    public function add(Request $request)
    {
        try {
            $data = $request->validate([
                'user_id' => 'required|integer',
                'movie_api_id' => 'required|string',
                'status' => 'sometimes|in:want_to_watch,watching,watched',
                'rating' => 'nullable|integer|min:1|max:5'
            ]);

            $exists = DB::table('watchlists')
                ->where('user_id', $data['user_id'])
                ->where('movie_api_id', $data['movie_api_id'])
                ->first();

            if ($exists) {
                return response()->json(['message' => 'Already in watchlist'], 400);
            }

            $id = DB::table('watchlists')->insertGetId($data + ['created_at' => now(), 'updated_at' => now()]);

            return response()->json(['message' => 'Added to watchlist', 'id' => $id]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to add to watchlist',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // public function update(Request $request, $id)
    // {
    //     try {
    //         $data = $request->validate([
    //             'status' => 'sometimes|in:want_to_watch,watching,watched',
    //             'rating' => 'nullable|integer|min:1|max:5'
    //         ]);

    //         $updated = DB::table('watchlists')
    //             ->where('id', $id)
    //             ->update($data + ['updated_at' => now()]);

    //         if ($updated) {
    //             return response()->json(['message' => 'Updated watchlist']);
    //         } else {
    //             return response()->json(['message' => 'Not found or nothing to update'], 404);
    //         }
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'message' => 'Failed to update watchlist',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }
    public function update(Request $request, $id)
    {
        try {
            $data = $request->validate([
                'user_id' => 'required|integer',
                'status' => 'sometimes|in:want_to_watch,watching,watched',
                'rating' => 'nullable|numeric|min:1|max:10'
            ]);

            // Update record
            // $userIdAuth= auth()->user();
            $item = DB::table('watchlists')
                ->where('user_id', $data['user_id'])
                ->where('movie_api_id', $id) //movie id
                ->first();
            $updated = DB::table('watchlists')
                ->where('id', $item->id)
                ->update($data + ['updated_at' => now()]);

            if (!$updated) {
                return response()->json(['message' => 'Not found or nothing to update'], 404);
            }
            $item = DB::table('watchlists')->where('id', $item->id)->first();

            if (!$item) {
                return response()->json(['message' => 'Record missing after update'], 404);
            }
            $tmdbKey = 'dd04310045c6e1d96bd35eaa2dc8e64e';
            $tmdbResponse = Http::get("https://api.themoviedb.org/3/movie/{$item->movie_api_id}", [
                'api_key' => $tmdbKey,
                'language' => 'en-US'
            ]);

            if (!$tmdbResponse->successful()) {
                return response()->json(['message' => 'Updated but failed to fetch TMDB data'], 500);
            }

            $tmdb = $tmdbResponse->json();
            $formatted = [
                'id' => $tmdb['id'],
                'title' => $tmdb['title'],
                'posterPath' => $tmdb['poster_path'],
                // 'rating' => $tmdb['vote_average'],
                'rating' => $data['rating'],
                'addedAt' => $item->created_at,
            ];

            return response()->json($formatted);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update watchlist',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function delete($id)
    {
        try {
            $deleted = DB::table('watchlists')->where('id', $id)->delete();

            if ($deleted) {
                return response()->json(['message' => 'Removed from watchlist']);
            } else {
                return response()->json(['message' => 'Not found'], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete watchlist',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function toggle(Request $request)
    {
        try {
            $data = $request->validate([
                'user_id' => 'required|integer',
                'movie_api_id' => 'required|string',
            ]);

            $watch = DB::table('watchlists')
                ->where('user_id', $data['user_id'])
                ->where('movie_api_id', $data['movie_api_id'])
                ->first();

            if ($watch) {
                DB::table('watchlists')->where('id', $watch->id)->delete();
                return response()->json(['status' => 'removed']);
            } else {
                DB::table('watchlists')->insert([
                    'user_id' => $data['user_id'],
                    'movie_api_id' => $data['movie_api_id'],
                    'status' => 'want_to_watch',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                return response()->json(['status' => 'added']);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to toggle watchlist',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

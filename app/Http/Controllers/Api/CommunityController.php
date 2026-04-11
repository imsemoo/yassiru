<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CommunityPost;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommunityController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = CommunityPost::approved()
            ->with('user:id,name');

        if ($request->category) {
            $query->where('category', $request->category);
        }

        $posts = $query->latest()->paginate(15);

        return response()->json($posts);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:5000',
            'category' => 'required|in:advice,experience,question,tip',
        ]);

        $post = CommunityPost::create([
            'user_id' => $request->user()->id,
            'title' => strip_tags($request->title),
            'content' => strip_tags($request->content),
            'category' => $request->category,
        ]);

        return response()->json([
            'message' => 'تم نشر المنشور بنجاح. سيظهر بعد مراجعة الإدارة',
            'post' => $post,
        ], 201);
    }
}

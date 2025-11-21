<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Todo;
use Illuminate\Http\Request;
use Throwable;

class TodoController extends Controller
{
    public function all(Request $request)
    {
        // dd(auth()->user()->hasRole('Admin'));
        // dd(auth()->user()->can('get-all-todos'));
        // dd(auth()->user());
        try {
            $todos = Todo::where('user_id', $request->user()->id)->get();

            return response()->json([
                'success' => true,
                'message' => 'All todos fetched successfully',
                'data' => $todos,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'data' => null,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function get(Request $request, $id)
    {
        try {
            $todo = Todo::where('id', $id)->where('user_id', $request->user()->id)->first();

            if (!$todo) {
                return response()->json(['message' => 'Todo not found or unauthorized'], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Todo with id ' . $id . ' fetched successfully',
                'data' => $todo,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'data' => null,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function create(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'is_completed' => 'sometimes|boolean',
        ]);

        try {
            $todo = Todo::create([
                'user_id' => $request->user()->id,
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'is_completed' => $validated['is_completed'] ?? false,
            ], 201);

            return response()->json([
                'success' => true,
                'message' => 'Todo created successfully',
                'data' => $todo,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'data' => null,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $todo = Todo::where('id', $id)->where('user_id', $request->user()->id)->first();

        if (!$todo) {
            return response()->json(['message' => 'Todo not found or unauthorized'], 404);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'is_completed' => 'sometimes|boolean',
        ]);

        try {
            $todo->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Todo updated successfully',
                'data' => $todo,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'data' => null,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function delete(Request $request, $id)
    {
        $todo = Todo::where('id', $id)->where('user_id', $request->user()->id)->first();

        if (!$todo) {
            return response()->json(['message' => 'Todo not found or unauthorized'], 404);
        }

        try {
            $todo->delete();
            return response()->json([
                'success' => true,
                'message' => 'Todo deleted successfully'
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\ThreadRequest;
use App\Models\Thread;
use App\Models\ThreadCategory;
use App\Models\User;
use Illuminate\Http\Request;

class ThreadController extends Controller
{
    public function index(Request $request)
    {
        $topContributors = User::withCount('comments')->orderBy('comments_count', 'desc')->take(5)->get();

        $threads = Thread::with(['user', 'categories'])
            ->latest()
            ->paginate(10);

        $categories = ThreadCategory::all();

        return view('threads.index', compact('threads', 'categories', 'topContributors'));
    }

    public function create()
    {
        $categories = ThreadCategory::all()->pluck('name', 'id');

        return view('threads.create', compact('categories'));
    }

    public function store(ThreadRequest $request)
    {
        $thread = Thread::create($request->all());

        $thread->categories()->attach($request->category_ids);

        dd($request->all());

        return redirect()->route('comunity.show', $thread->slug);
    }

    public function show(Thread $thread)
    {
        $thread->load('user', 'categories');

        return view('threads.show', compact('thread'));
    }

    public function edit(Thread $thread)
    {
        return view('threads.edit', compact('thread'));
    }

    public function update(ThreadRequest $request, Thread $thread)
    {
        $thread->update($request->validated());

        $thread->categories()->sync($request->category_ids);

        return redirect()->route('threads.show', $thread);
    }

    public function destroy(Thread $thread)
    {
        $thread->delete();

        return redirect()->route('threads.index');
    }

    public function uploadImage(Request $request, Thread $thread)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $file = $request->file('image');

        $path = $file->store('uploads/threads', 'public');

        return response()->json([
            'success' => true,
            'url' => asset('storage/' . $path),
        ]);
    }
}

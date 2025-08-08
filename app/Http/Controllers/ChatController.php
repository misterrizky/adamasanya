<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cmgmyr\Messenger\Models\Thread;

class ChatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($threadId)
    {
        // Pastikan thread ada dan pengguna memiliki akses
        $thread = Thread::findOrFail($threadId);
        if (!auth()->user()->hasRole(['Super Admin', 'Owner']) && !$thread->hasParticipant(auth()->id())) {
            abort(403, 'Unauthorized');
        }

        // Kembalikan view dengan threadId
        return view('livewire.drawers.chat', ['threadId' => $threadId]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

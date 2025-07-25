<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WaitingList;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WaitingListController extends Controller
{
    public function index(Request $request)
    {
        // Optional filters
        $query = WaitingList::query();
        if ($request->filled('source')) {
            $query->where('signup_source', $request->source);
        }
        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('created_at', [$request->from, $request->to]);
        }

        $list = $query->orderBy('created_at', 'desc')
                      ->paginate(15);

        return response()->json($list);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:waiting_lists,email',
            'signup_source' => 'nullable|string|max:100',
        ]);

        $entry = WaitingList::create($data);

        return response()->json($entry, 201);
    }

    public function show($id)
    {
        $entry = WaitingList::findOrFail($id);
        return response()->json($entry);
    }

    public function update(Request $request, $id)
    {
        $entry = WaitingList::findOrFail($id);

        $data = $request->validate([
            'name'          => 'sometimes|required|string|max:255',
            'email'         => [
                                  'sometimes',
                                  'required',
                                  'email',
                                  Rule::unique('waiting_lists')->ignore($entry->id),
                               ],
            'signup_source' => 'sometimes|nullable|string|max:100',
        ]);

        $entry->update($data);

        return response()->json($entry);
    }

    public function destroy($id)
    {
        WaitingList::destroy($id);
        return response()->json(null, 204);
    }
}

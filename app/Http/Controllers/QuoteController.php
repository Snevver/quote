<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReorderQuotesRequest;
use App\Http\Requests\StoreQuoteRequest;
use App\Http\Requests\UpdateQuoteRequest;
use App\Http\Resources\QuoteFolderResource;
use App\Http\Resources\QuoteResource;
use App\Models\Quote;
use App\Models\QuoteFolder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;

class QuoteController
{
    use AuthorizesRequests;

    public function index()
    {
        return response()->json([
            'quotes' => QuoteResource::collection(Quote::orderBy('position')->get()),
            'folders' => QuoteFolderResource::collection(QuoteFolder::with('children')->orderBy('position')->get()),
            'auth' => auth()->check(),
        ]);
    }

    public function store(StoreQuoteRequest $request)
    {
        $this->authorize('manage', Quote::class);

        Quote::create($request->validated());

        return response()->json(['success' => true]);
    }

    public function update(UpdateQuoteRequest $request, Quote $quote)
    {
        $this->authorize('manage', $quote);

        $quote->update($request->validated());

        return response()->json(['success' => true, 'quote' => new QuoteResource($quote)]);
    }

    public function destroy(Quote $quote)
    {
        $this->authorize('manage', $quote);

        $quote->delete();

        return response()->json(['success' => true]);
    }

    public function reorder(ReorderQuotesRequest $request)
    {
        $this->authorize('manage', Quote::class);

        $data = $request->validated();
        $folderId = $data['folder_id'] ?? null;

        DB::transaction(function () use ($data, $folderId) {
            foreach ($data['order'] as $index => $id) {
                Quote::where('id', $id)->update(['position' => $index, 'folder_id' => $folderId]);
            }
        });

        return response()->json(['success' => true]);
    }
}

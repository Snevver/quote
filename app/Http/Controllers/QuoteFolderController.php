<?php

namespace App\Http\Controllers;

use App\Http\Requests\QuoteFolderRequest;
use App\Http\Requests\ReorderQuoteFoldersRequest;
use App\Http\Resources\QuoteFolderResource;
use App\Models\Quote;
use App\Models\QuoteFolder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;

class QuoteFolderController
{
    use AuthorizesRequests;

    public function store(QuoteFolderRequest $request)
    {
        $this->authorize('manage', QuoteFolder::class);

        return response()->json([
            'success' => true,
            'folder' => new QuoteFolderResource(QuoteFolder::create($request->validated())),
        ]);
    }

    public function update(QuoteFolderRequest $request, QuoteFolder $quoteFolder)
    {
        $this->authorize('manage', $quoteFolder);

        $quoteFolder->update($request->validated());

        return response()->json([
            'success' => true,
            'folder' => new QuoteFolderResource($quoteFolder),
        ]);
    }

    public function destroy(QuoteFolder $quoteFolder)
    {
        $this->authorize('manage', $quoteFolder);

        $ids = $this->collectDescendantIds($quoteFolder);

        DB::transaction(function () use ($ids) {
            Quote::whereIn('folder_id', $ids)->delete();
            QuoteFolder::whereIn('id', $ids)->delete();
        });

        return response()->json(['success' => true]);
    }

    public function reorder(ReorderQuoteFoldersRequest $request)
    {
        $this->authorize('manage', QuoteFolder::class);

        $data = $request->validated();
        $parentId = $data['parent_id'] ?? null;

        DB::transaction(function () use ($data, $parentId) {
            foreach ($data['order'] as $index => $id) {
                QuoteFolder::where('id', $id)->update(['position' => $index, 'parent_id' => $parentId]);
            }
        });

        return response()->json(['success' => true]);
    }

    private function collectDescendantIds(QuoteFolder $folder): array
    {
        $ids = [$folder->id];
        foreach ($folder->children as $child) {
            $ids = array_merge($ids, $this->collectDescendantIds($child));
        }

        return $ids;
    }
}

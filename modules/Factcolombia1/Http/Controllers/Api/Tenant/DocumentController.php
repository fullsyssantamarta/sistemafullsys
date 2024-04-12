<?php

namespace Modules\Factcolombia1\Http\Controllers\Api\Tenant;

use Illuminate\Http\Request;
use Modules\Factcolombia1\Http\Controllers\Controller;
use Modules\Factcolombia1\Http\Controllers\Tenant\DocumentController as WebDocumentController;
use Modules\Factcolombia1\Http\Requests\Tenant\DocumentRequest;
use Modules\Factcolombia1\Models\Tenant\Document;
use App\Http\Resources\Tenant\ItemCollection;
use App\Models\Tenant\Item;

class DocumentController extends Controller
{
    public function tables()
    {
        return (new WebDocumentController)->tables();
    }

    public function store(DocumentRequest $request)
    {
        // dd($request->all());
        return (new WebDocumentController)->store($request);
    }

    public function searchItems(Request $request)
    {
        $records = Item::query()
            ->when($request->has('name'), function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->name . '%')
                    ->orWhere('description', 'like', '%' . $request->name . '%')
                    ->orwhere('internal_id', 'like', '%' .$request->name . '%');
            });

        return new ItemCollection($records->paginate(config('tenant.items_per_page')));
    }
}
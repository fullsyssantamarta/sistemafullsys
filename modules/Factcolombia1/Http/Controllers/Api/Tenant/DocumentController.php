<?php

namespace Modules\Factcolombia1\Http\Controllers\Api\Tenant;

use Illuminate\Http\Request;
use Modules\Factcolombia1\Http\Controllers\Controller;
use Modules\Factcolombia1\Http\Controllers\Tenant\DocumentController as WebDocumentController;
use Modules\Factcolombia1\Http\Resources\Tenant\DocumentCollection;
use Modules\Factcolombia1\Http\Requests\Tenant\DocumentRequest;
use Modules\Factcolombia1\Http\Resources\Tenant\PersonCollection;
use Modules\Factcolombia1\Models\Tenant\Document;
use App\Http\Resources\Tenant\ItemCollection;
use App\Models\Tenant\Person;
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

    public function searchDocuments(Request $request)
    {
        $records = Document::query()
            ->when($request->has('serie'), function ($query) use ($request) {
                $query->where('prefix', 'like', '%' . $request->serie . '%');
            })
            ->when($request->has('number'), function ($query) use ($request) {
                $query->where('number', 'like', '%' . $request->number . '%');
            });

        return new DocumentCollection($records->paginate(config('tenant.items_per_page')));
    }

    public function searchCustomers(Request $request)
    {
        $records = Person::query()
            ->when($request->has('name'), function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->name . '%');
            })
            ->when($request->has('number'), function ($query) use ($request) {
                $query->where('number', 'like', '%' . $request->number . '%');
            });

        return new PersonCollection($records->paginate(config('tenant.items_per_page')));
    }
}
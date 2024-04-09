<?php

namespace Modules\Factcolombia1\Http\Controllers\Api\Tenant;

use Modules\Factcolombia1\Http\Controllers\Controller;
use Modules\Factcolombia1\Http\Controllers\Tenant\DocumentController as WebDocumentController;
use Modules\Factcolombia1\Http\Requests\Tenant\DocumentRequest;
use Modules\Factcolombia1\Models\Tenant\Document;
use Illuminate\Http\Request;

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
}
<?php

namespace Modules\Accounting\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Accounting\Models\JournalPrefix;

class JournalPrefixController extends Controller
{
    public function index()
    {
        return JournalPrefix::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'prefix' => 'required|string|unique:journal_prefixes,prefix',
            'description' => 'required|string',
            'modifiable' => 'required|boolean',
        ]);

        return JournalPrefix::create($request->all());
    }

    public function show($id)
    {
        return JournalPrefix::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $prefix = JournalPrefix::findOrFail($id);
        if (!$prefix->modifiable) {
            return response()->json(['message' => 'Este prefijo no es modificable'], 403);
        }

        $prefix->update($request->all());
        return $prefix;
    }

    /**
     * Elimina un prefijo de asiento contable.
     *
     * @param int $id Identificador del prefijo a eliminar.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $prefix = JournalPrefix::findOrFail($id);
        if (!$prefix->modifiable) {
            return response()->json(['message' => 'Este prefijo no se puede eliminar'], 403);
        }

        $prefix->delete();
        return response()->json(['message' => 'Eliminado correctamente']);
    }
}

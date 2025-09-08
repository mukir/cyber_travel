<?php

namespace App\Http\Controllers;

use App\Models\DocumentType;
use Illuminate\Http\Request;

class AdminDocumentTypeController extends Controller
{
    public function index()
    {
        $types = DocumentType::orderBy('name')->get();
        return view('admin.document_types.index', compact('types'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'key' => ['required','string','max:50','regex:/^[a-z0-9_\-]+$/','unique:document_types,key'],
            'name' => ['required','string','max:100'],
            'description' => ['nullable','string','max:500'],
            'required' => ['nullable','boolean'],
            'active' => ['nullable','boolean'],
        ]);

        $data['required'] = (bool)($data['required'] ?? true);
        $data['active'] = (bool)($data['active'] ?? true);

        DocumentType::create($data);

        return back()->with('success', 'Document type added.');
    }

    public function update(Request $request, DocumentType $document_type)
    {
        $data = $request->validate([
            'name' => ['required','string','max:100'],
            'description' => ['nullable','string','max:500'],
            'required' => ['nullable','boolean'],
            'active' => ['nullable','boolean'],
        ]);
        $data['required'] = (bool)($data['required'] ?? false);
        $data['active'] = (bool)($data['active'] ?? false);
        $document_type->update($data);
        return back()->with('success', 'Document type updated.');
    }

    public function destroy(DocumentType $document_type)
    {
        $document_type->delete();
        return back()->with('success', 'Document type deleted.');
    }
}


<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentRequirement;
use App\Models\PermitType;
use Illuminate\Http\Request;

class RequirementController extends Controller
{
    public function index(Request $request)
    {
        $requirements = DocumentRequirement::with('permitType')->orderBy('document_name')->paginate(10);
        $permitTypes = PermitType::orderBy('name')->get();
        $editing = $request->filled('edit') ? DocumentRequirement::findOrFail($request->query('edit')) : null;

        return view('admin.requirements.index', compact('requirements', 'permitTypes', 'editing'));
    }

    public function store(Request $request)
    {
        DocumentRequirement::create($this->validated($request) + [
            'is_required' => $request->boolean('is_required'),
            'is_active'   => true,
        ]);

        return back()->with('success', 'Persyaratan dokumen berhasil ditambahkan.');
    }

    public function update(Request $request, DocumentRequirement $requirement)
    {
        $requirement->update($this->validated($request, $requirement) + [
            'is_required' => $request->boolean('is_required'),
            'is_active'   => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Persyaratan dokumen berhasil diperbarui.');
    }

    public function destroy(DocumentRequirement $requirement)
    {
        $requirement->delete();

        return back()->with('success', 'Persyaratan dokumen berhasil dihapus.');
    }

    private function validated(Request $request, ?DocumentRequirement $requirement = null): array
    {
        return $request->validate([
            'application_type' => ['required', 'in:PERMIT,AGREEMENT'],
            'permit_type_id'   => ['nullable', 'required_if:application_type,PERMIT', 'exists:permit_types,id'],
            'document_name'    => ['required', 'string', 'max:255'],
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\DocumentFolder;
use Illuminate\Http\Request;

/**
 * Manajemen folder manual Document Management (Legal/Admin).
 * Mendukung sub-folder tak terbatas (self-referencing parent_id).
 */
class DocumentFolderController extends Controller
{
    public function index()
    {
        $this->authorizeManage();

        // Seluruh folder (di-mapping jadi tree di view).
        $folders = DocumentFolder::with('children.creator')->orderBy('name')->get();
        $roots = $folders->whereNull('parent_id')->values();

        return view('documents.folders', ['roots' => $roots]);
    }

    public function store(Request $request)
    {
        $this->authorizeManage();

        $data = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'exists:document_folders,id'],
        ], [
            'name.required' => 'Nama folder wajib diisi.',
        ]);

        $exists = DocumentFolder::where('name', $data['name'])
            ->where('parent_id', $data['parent_id'] ?? null)
            ->exists();

        if ($exists) {
            return back()->withErrors(['name' => 'Folder dengan nama tersebut sudah ada di lokasi yang sama.']);
        }

        DocumentFolder::create($data + ['created_by' => auth()->id()]);

        return back()->with('success', 'Folder berhasil dibuat.');
    }

    public function update(Request $request, DocumentFolder $folder)
    {
        $this->authorizeManage();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ], [
            'name.required' => 'Nama folder wajib diisi.',
        ]);

        $exists = DocumentFolder::where('name', $data['name'])
            ->where('parent_id', $folder->parent_id)
            ->where('id', '!=', $folder->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['name' => 'Folder dengan nama tersebut sudah ada di lokasi yang sama.']);
        }

        $folder->update($data);

        return back()->with('success', 'Folder berhasil diubah namanya.');
    }

    public function destroy(DocumentFolder $folder)
    {
        $this->authorizeManage();

        $folder->delete(); // sub-folder tidak ikut terhapus — otomatis naik jadi folder root

        return back()->with('success', 'Folder berhasil dihapus. Sub-folder (jika ada) naik menjadi folder utama.');
    }

    private function authorizeManage(): void
    {
        abort_unless(auth()->user()->canReview(), 403,
            'Hanya Legal/Admin yang dapat mengelola folder Document Management.');
    }
}

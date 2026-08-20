<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PermitType;
use Illuminate\Http\Request;

class PermitTypeController extends Controller
{
    public function index(Request $request)
    {
        $permitTypes = PermitType::withCount('requirements')->orderBy('name')->paginate(10);
        $editing = $request->filled('edit') ? PermitType::findOrFail($request->query('edit')) : null;

        return view('admin.permit-types.index', compact('permitTypes', 'editing'));
    }

    public function store(Request $request)
    {
        PermitType::create($request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'category'    => ['required', 'in:USAHA,BANGUNAN,LINGKUNGAN,PRODUK,OPERASIONAL'],
            'description' => ['nullable', 'string'],
            'is_active'   => ['boolean'],
        ]) + ['is_active' => $request->boolean('is_active')]);

        return back()->with('success', 'Jenis izin berhasil ditambahkan.');
    }

    public function update(Request $request, PermitType $permitType)
    {
        $permitType->update($request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'category'    => ['required', 'in:USAHA,BANGUNAN,LINGKUNGAN,PRODUK,OPERASIONAL'],
            'description' => ['nullable', 'string'],
        ]) + ['is_active' => $request->boolean('is_active')]);

        return back()->with('success', 'Jenis izin berhasil diperbarui.');
    }

    public function destroy(PermitType $permitType)
    {
        $permitType->delete();

        return back()->with('success', 'Jenis izin berhasil dihapus.');
    }
}

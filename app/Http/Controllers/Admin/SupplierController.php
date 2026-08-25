<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $suppliers = Supplier::withCount('applications')
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = $request->string('q');
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('npwp', 'like', "%{$q}%")
                    ->orWhere('contact_person', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            })
            ->orderBy('name')->paginate(10)->withQueryString();

        return view('admin.suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('admin.suppliers.form', ['supplier' => new Supplier()]);
    }

    public function store(Request $request)
    {
        Supplier::create($this->validated($request) + $this->booleans($request));

        return redirect()->route('admin.suppliers.index')->with('success', 'Supplier berhasil ditambahkan.');
    }

    public function edit(Supplier $supplier)
    {
        return view('admin.suppliers.form', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $supplier->update($this->validated($request) + $this->booleans($request));

        return redirect()->route('admin.suppliers.index')->with('success', 'Supplier berhasil diperbarui.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return back()->with('success', 'Supplier berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name'                => ['required', 'string', 'max:255'],
            'npwp'                => ['nullable', 'string', 'max:50'],
            'address'             => ['nullable', 'string'],
            'phone'               => ['nullable', 'string', 'max:50'],
            'email'               => ['nullable', 'email', 'max:255'],
            'contact_person'      => ['nullable', 'string', 'max:255'],
            'assessment_score'    => ['nullable', 'numeric', 'min:0', 'max:100'],
            'assessment_date'     => ['nullable', 'date'],
            'risk_level'          => ['nullable', 'in:LOW,MEDIUM,HIGH'],
            'notes'               => ['nullable', 'string'],
        ]);
    }

    private function booleans(Request $request): array
    {
        return [
            'is_registered'        => $request->boolean('is_registered'),
            'assessment_available' => $request->boolean('assessment_available'),
            'data_complete'        => $request->boolean('data_complete'),
            'documents_complete'   => $request->boolean('documents_complete'),
        ];
    }
}

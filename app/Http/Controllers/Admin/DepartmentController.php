<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $departments = Department::withCount('users')->orderBy('name')->paginate(10);
        $editing = $request->filled('edit') ? Department::findOrFail($request->query('edit')) : null;

        return view('admin.departments.index', compact('departments', 'editing'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:departments,name'],
            'code' => ['nullable', 'string', 'max:50'],
        ]);

        Department::create($data);

        return back()->with('success', 'Divisi berhasil ditambahkan.');
    }

    public function update(Request $request, Department $department)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:departments,name,' . $department->id],
            'code' => ['nullable', 'string', 'max:50'],
        ]);

        $department->update($data);

        return back()->with('success', 'Divisi berhasil diperbarui.');
    }

    public function destroy(Department $department)
    {
        $department->delete();

        return back()->with('success', 'Divisi berhasil dihapus.');
    }
}

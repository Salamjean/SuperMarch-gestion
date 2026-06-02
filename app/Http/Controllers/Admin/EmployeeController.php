<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = User::where('role', 'employee')->latest()->get();
        return view('admin.employees.index', compact('employees'));
    }

    public function create()
    {
        return view('admin.employees.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', 'unique:users,email'],
            'phone'      => ['nullable', 'string', 'max:20'],
            'position'   => ['nullable', 'string', 'max:100'],
            'department' => ['nullable', 'string', 'max:100'],
            'hire_date'  => ['nullable', 'date'],
            'address'    => ['nullable', 'string', 'max:255'],
            'gender'     => ['nullable', 'in:male,female,other'],
            'password'   => ['required', 'confirmed', Password::min(8)],
        ]);

        User::create([
            'name'       => $validated['name'],
            'email'      => $validated['email'],
            'phone'      => $validated['phone'] ?? null,
            'position'   => $validated['position'] ?? null,
            'department' => $validated['department'] ?? null,
            'hire_date'  => $validated['hire_date'] ?? null,
            'address'    => $validated['address'] ?? null,
            'gender'     => $validated['gender'] ?? null,
            'password'   => Hash::make($validated['password']),
            'role'       => 'employee',
        ]);

        return redirect()->route('admin.employees.index')->with('success', 'Employé créé avec succès.');
    }

    public function show(User $employee)
    {
        return view('admin.employees.show', compact('employee'));
    }

    public function edit(User $employee)
    {
        return view('admin.employees.edit', compact('employee'));
    }

    public function update(Request $request, User $employee)
    {
        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', 'unique:users,email,' . $employee->id],
            'phone'      => ['nullable', 'string', 'max:20'],
            'position'   => ['nullable', 'string', 'max:100'],
            'department' => ['nullable', 'string', 'max:100'],
            'hire_date'  => ['nullable', 'date'],
            'address'    => ['nullable', 'string', 'max:255'],
            'gender'     => ['nullable', 'in:male,female,other'],
            'password'   => ['nullable', 'confirmed', Password::min(8)],
        ]);

        $data = [
            'name'       => $validated['name'],
            'email'      => $validated['email'],
            'phone'      => $validated['phone'] ?? null,
            'position'   => $validated['position'] ?? null,
            'department' => $validated['department'] ?? null,
            'hire_date'  => $validated['hire_date'] ?? null,
            'address'    => $validated['address'] ?? null,
            'gender'     => $validated['gender'] ?? null,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($validated['password']);
        }

        $employee->update($data);

        return redirect()->route('admin.employees.index')->with('success', 'Employé mis à jour avec succès.');
    }

    public function destroy(User $employee)
    {
        $employee->delete();
        return redirect()->route('admin.employees.index')->with('success', 'Employé bloqué avec succès.');
    }

    public function blocked()
    {
        $employees = User::onlyTrashed()->where('role', 'employee')->latest()->get();
        return view('admin.employees.index', compact('employees'))->with('isBlocked', true);
    }

    public function unblock($id)
    {
        $employee = User::onlyTrashed()->findOrFail($id);
        $employee->restore();
        return redirect()->route('admin.employees.index')->with('success', 'Employé débloqué avec succès.');
    }
}

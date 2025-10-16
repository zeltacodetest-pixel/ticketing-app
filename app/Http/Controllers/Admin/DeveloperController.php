<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class DeveloperController extends Controller
{
    // Show all developers
    public function index()
    {
        // Fetch all users that have the Developer role
        $developers = User::role('Developer')->get();
        return view('dashboards.cruddevelopers', compact('developers'));
    }

    // Store new developer
   public function store(Request $request)
{
    // dd('store method hit', $request->all()); // keep for debugging

    $validated = $request->validate([
        'first_name' => 'required|string|max:255',
        'last_name'  => 'required|string|max:255', // ✅ add this
        'email'      => 'required|email|unique:users,email',
        'password'   => 'required|string|min:6',
    ]);

    $developer = User::create([
        'first_name' => $validated['first_name'],
        'last_name'  => $validated['last_name'],  // ✅ store it
        'email'      => $validated['email'],
        'password'   => bcrypt($validated['password']),
    ]);

    $developer->assignRole('Developer');

    return response()->json(['message' => 'Developer created successfully!']);
}


    // Update developer
   // Update developer
public function update(Request $request, User $developer)
{
    $validated = $request->validate([
        'first_name' => 'required|string|max:255',
        'last_name' => 'nullable|string|max:255',
        'email' => ['required', 'email', Rule::unique('users')->ignore($developer->id)],
        'password' => 'nullable|string|min:6',
    ]);

    $developer->update([
        'first_name' => $validated['first_name'],
        'last_name' => $validated['last_name'] ?? null,
        'email' => $validated['email'],
        'password' => !empty($validated['password'])
            ? bcrypt($validated['password'])
            : $developer->password,
    ]);

    if (!$developer->hasRole('Developer')) {
        $developer->assignRole('Developer');
    }

    if ($request->expectsJson()) {
        return response()->json(['message' => 'Developer updated successfully.']);
    }

    return redirect()->route('admin.developers.index')->with('success', 'Developer updated successfully!');
}

// Delete developer
public function destroy(Request $request, User $developer)
{
    $developer->delete();

    if ($request->expectsJson()) {
        return response()->json(['message' => 'Developer deleted successfully.']);
    }

    return redirect()->route('admin.developers.index')->with('success', 'Developer deleted successfully!');
}

}

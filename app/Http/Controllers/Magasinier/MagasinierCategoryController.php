<?php

namespace App\Http\Controllers\Magasinier;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MagasinierCategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('creator')->latest()->get();
        return view('magasinier.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('magasinier.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:categories,name'],
            'description' => ['nullable', 'string', 'max:500'],
            'color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        Category::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'color' => $validated['color'] ?? '#004d99',
            'is_active' => $request->boolean('is_active', true),
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('magasinier.categories.index')
            ->with('success', 'Categorie ajoutee avec succes.');
    }

    public function edit(Category $category)
    {
        return view('magasinier.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:categories,name,' . $category->id],
            'description' => ['nullable', 'string', 'max:500'],
            'color' => ['nullable', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $category->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'color' => $validated['color'] ?? $category->color,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('magasinier.categories.index')
            ->with('success', 'Categorie mise a jour avec succes.');
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->route('magasinier.categories.index')
            ->with('success', 'Categorie supprimee avec succes.');
    }
}

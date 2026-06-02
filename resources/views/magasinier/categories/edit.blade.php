@extends('magasinier.layouts.app')

@section('title', 'Modifier categorie')
@section('page_title', 'Modifier categorie')

@section('content')
    <div class="card" style="max-width:780px; padding:18px;">
        <form method="POST" action="{{ route('magasinier.categories.update', $category) }}">
            @csrf
            @method('PUT')

            <div style="margin-bottom:14px;">
                <label for="name" style="display:block; margin-bottom:6px; font-weight:700;">Nom</label>
                <input id="name" name="name" value="{{ old('name', $category->name) }}" required
                    style="width:100%; padding:10px; border:1px solid var(--border); border-radius:8px;">
                @error('name')
                    <small style="color:#dc2626;">{{ $message }}</small>
                @enderror
            </div>

            <div style="margin-bottom:14px;">
                <label for="description" style="display:block; margin-bottom:6px; font-weight:700;">Description</label>
                <textarea id="description" name="description" rows="4"
                    style="width:100%; padding:10px; border:1px solid var(--border); border-radius:8px;">{{ old('description', $category->description) }}</textarea>
            </div>

            <div style="margin-bottom:14px;">
                <label for="color" style="display:block; margin-bottom:6px; font-weight:700;">Couleur</label>
                <input id="color" type="color" name="color"
                    value="{{ old('color', $category->color ?? '#004d99') }}">
            </div>

            <label style="display:flex; align-items:center; gap:8px; margin-bottom:16px;">
                <input type="checkbox" name="is_active" value="1"
                    {{ old('is_active', $category->is_active) ? 'checked' : '' }}> Active
            </label>

            <div style="display:flex; gap:10px;">
                <a href="{{ route('magasinier.categories.index') }}" class="mg-nav-link"
                    style="border:1px solid var(--border);">Annuler</a>
                <button class="mg-logout-btn" type="submit"><i class="fa-solid fa-floppy-disk"></i> Mettre a jour</button>
            </div>
        </form>
    </div>
@endsection

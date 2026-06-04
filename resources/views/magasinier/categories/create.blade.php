@extends('magasinier.layouts.app')

@section('title', 'Nouvelle catégorie')
@section('page_title', 'Nouvelle catégorie')

@section('content')
    <div class="card" style="max-width:800px; padding:18px; margin: 0 auto;">
        <div style="display:flex; align-items:center; gap:12px; margin-bottom:20px; border-bottom:1px solid var(--border); padding-bottom:16px;">
            <div style="background:var(--primary); color:white; border-radius:10px; width:45px; height:45px; display:flex; align-items:center; justify-content:center; font-size:20px;">
                <i class="fa-solid fa-tags"></i>
            </div>
            <div>
                <h2 style="font-size:18px; margin:0; font-weight:800; color:var(--primary);">Nouvelle catégorie</h2>
                <p style="font-size:12px; color:var(--muted); margin:0;">Ajouter une nouvelle catégorie de produits au catalogue</p>
            </div>
        </div>

        <form method="POST" action="{{ route('magasinier.categories.store') }}">
            @csrf

            <div style="margin-bottom:14px;">
                <label for="name" style="display:block; margin-bottom:6px; font-weight:700; color:var(--text);">Nom de la catégorie <span style="color:var(--danger);">*</span></label>
                <input id="name" name="name" value="{{ old('name') }}" required placeholder="Ex: Boissons, Épicerie..."
                    style="width:100%; height:40px; padding:10px; border:1px solid var(--border); border-radius:8px; outline:none; font-size:14px; font-family:inherit;">
                @error('name')
                    <small style="color:var(--danger); display:block; margin-top:4px;">{{ $message }}</small>
                @enderror
            </div>

            <div style="margin-bottom:14px;">
                <label for="description" style="display:block; margin-bottom:6px; font-weight:700; color:var(--text);">Description</label>
                <textarea id="description" name="description" rows="4" placeholder="Description de la catégorie..."
                    style="width:100%; padding:10px; border:1px solid var(--border); border-radius:8px; outline:none; font-size:14px; font-family:inherit;">{{ old('description') }}</textarea>
            </div>

            <div style="margin-bottom:14px; display:flex; align-items:center; gap:24px; flex-wrap:wrap;">
                <div>
                    <label for="color" style="display:block; margin-bottom:6px; font-weight:700; color:var(--text);">Couleur</label>
                    <input id="color" type="color" name="color" value="{{ old('color', '#004d99') }}" 
                        style="width:65px; height:38px; padding:2px; border:1px solid var(--border); border-radius:8px; cursor:pointer; background:none;">
                </div>
                <div style="margin-top:20px;">
                    <label style="display:flex; align-items:center; gap:8px; font-weight:700; color:var(--text); cursor:pointer;">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                            style="width:18px; height:18px; accent-color:var(--primary); cursor:pointer;"> Catégorie active
                    </label>
                </div>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px; border-top:1px solid var(--border); padding-top:16px; margin-top:20px;">
                <a href="{{ route('magasinier.categories.index') }}" class="mg-logout-btn"
                    style="text-decoration:none; background:white; color:var(--text); border:1px solid var(--border);">
                    Annuler
                </a>
                <button class="mg-logout-btn" type="submit">
                    <i class="fa-solid fa-floppy-disk" style="margin-right:6px;"></i> Enregistrer
                </button>
            </div>
        </form>
    </div>
@endsection

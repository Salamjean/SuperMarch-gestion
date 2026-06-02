@extends('magasinier.layouts.app')

@section('title', 'Nouveau produit')
@section('page_title', 'Nouveau produit')

@section('content')
    <div class="card" style="max-width:860px; padding:18px;">
        <form method="POST" action="{{ route('magasinier.products.store') }}" enctype="multipart/form-data">
            @csrf

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                <div>
                    <label for="name" style="display:block; margin-bottom:6px; font-weight:700;">Nom complet <span class="required" style="color:var(--danger);">*</span></label>
                    <input id="name" name="name" value="{{ old('name') }}" required
                        style="width:100%; padding:10px; border:1px solid var(--border); border-radius:8px;">
                    @error('name')
                        <small style="color:#dc2626;">{{ $message }}</small>
                    @enderror
                </div>

                <div>
                    <label for="barcode_value" style="display:block; margin-bottom:6px; font-weight:700;">Code-barres (Optionnel - généré auto si vide)</label>
                    <input id="barcode_value" name="barcode_value" value="{{ old('barcode_value') }}"
                        style="width:100%; padding:10px; border:1px solid var(--border); border-radius:8px;" placeholder="Ex: 8412345678901">
                    @error('barcode_value')
                        <small style="color:#dc2626;">{{ $message }}</small>
                    @enderror
                </div>

                <div>
                    <label for="category_name" style="display:block; margin-bottom:6px; font-weight:700;">Categorie</label>
                    <select id="category_name" name="category_name" required
                        style="width:100%; padding:10px; border:1px solid var(--border); border-radius:8px;">
                        <option value="">Selectionner</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->name }}"
                                {{ old('category_name') === $category->name ? 'selected' : '' }}>{{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_name')
                        <small style="color:#dc2626;">{{ $message }}</small>
                    @enderror
                </div>

                <div>
                    <label for="price" style="display:block; margin-bottom:6px; font-weight:700;">Prix</label>
                    <input id="price" type="number" min="0" name="price" value="{{ old('price', 0) }}"
                        required style="width:100%; padding:10px; border:1px solid var(--border); border-radius:8px;">
                </div>

                <div>
                    <label for="stock" style="display:block; margin-bottom:6px; font-weight:700;">Stock</label>
                    <input id="stock" type="number" min="0" name="stock" value="{{ old('stock', 0) }}"
                        required style="width:100%; padding:10px; border:1px solid var(--border); border-radius:8px;">
                </div>

                <div>
                    <label for="stock_threshold" style="display:block; margin-bottom:6px; font-weight:700;">Seuil</label>
                    <input id="stock_threshold" type="number" min="0" name="stock_threshold"
                        value="{{ old('stock_threshold', 5) }}" required
                        style="width:100%; padding:10px; border:1px solid var(--border); border-radius:8px;">
                </div>

                <div>
                    <label for="supplier_id" style="display:block; margin-bottom:6px; font-weight:700;">Fournisseur</label>
                    <select id="supplier_id" name="supplier_id"
                        style="width:100%; padding:10px; border:1px solid var(--border); border-radius:8px;">
                        <option value="">Aucun</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}"
                                {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="margin-top:12px;">
                <label for="image" style="display:block; margin-bottom:6px; font-weight:700;">Image</label>
                <input id="image" type="file" name="image" accept="image/*">
            </div>

            <div style="margin-top:12px;">
                <label for="description" style="display:block; margin-bottom:6px; font-weight:700;">Description</label>
                <textarea id="description" name="description" rows="4"
                    style="width:100%; padding:10px; border:1px solid var(--border); border-radius:8px;">{{ old('description') }}</textarea>
            </div>

            <div style="display:flex; gap:10px; margin-top:16px;">
                <a href="{{ route('magasinier.products.index') }}" class="mg-nav-link"
                    style="border:1px solid var(--border);">Annuler</a>
                <button class="mg-logout-btn" type="submit"><i class="fa-solid fa-floppy-disk"></i> Enregistrer</button>
            </div>
        </form>
    </div>
@endsection

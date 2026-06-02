@extends('magasinier.layouts.app')

@section('title', 'Modifier le produit')
@section('page_title', 'Modifier le produit')

@section('content')
    <div class="card" style="max-width:860px; padding:18px; margin: 0 auto;">
        <div style="display:flex; align-items:center; gap:10px; margin-bottom:18px;">
            <div style="background:var(--primary); color:white; border-radius:8px; width:40px; height:40px; display:flex; align-items:center; justify-content:center; font-size:18px;">
                <i class="fa-solid fa-pen-to-square"></i>
            </div>
            <div>
                <h2 style="font-size:18px; margin:0; font-weight:800; color:var(--primary);">Modifier le produit</h2>
                <p style="font-size:12px; color:var(--muted); margin:0;">Code de référence actuel : <strong>{{ $product->reference }}</strong></p>
            </div>
        </div>

        <form method="POST" action="{{ route('magasinier.products.update', $product) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                <div>
                    <label for="name" style="display:block; margin-bottom:6px; font-weight:700;">Nom complet <span class="required" style="color:var(--danger);">*</span></label>
                    <input id="name" name="name" value="{{ old('name', $product->name) }}" required
                        style="width:100%; padding:10px; border:1px solid var(--border); border-radius:8px;">
                    @error('name')
                        <small style="color:#dc2626;">{{ $message }}</small>
                    @enderror
                </div>

                <div>
                    <label for="barcode_display" style="display:block; margin-bottom:6px; font-weight:700;">Code-barres / Référence (Non modifiable)</label>
                    <input id="barcode_display" readonly value="{{ $product->reference }}"
                        style="width:100%; padding:10px; border:1px solid var(--border); border-radius:8px; background:#f1f5f9; color:#64748b; font-weight:700;">
                </div>

                <div>
                    <label for="category_name" style="display:block; margin-bottom:6px; font-weight:700;">Categorie <span class="required" style="color:var(--danger);">*</span></label>
                    <select id="category_name" name="category_name" required
                        style="width:100%; padding:10px; border:1px solid var(--border); border-radius:8px;">
                        <option value="">Selectionner</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->name }}"
                                {{ old('category_name', $product->category_name) === $category->name ? 'selected' : '' }}>{{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_name')
                        <small style="color:#dc2626;">{{ $message }}</small>
                    @enderror
                </div>

                <div>
                    <label for="price" style="display:block; margin-bottom:6px; font-weight:700;">Prix (FCFA) <span class="required" style="color:var(--danger);">*</span></label>
                    <input id="price" type="number" min="0" name="price" value="{{ old('price', $product->price) }}"
                        required style="width:100%; padding:10px; border:1px solid var(--border); border-radius:8px;">
                </div>

                <div>
                    <label for="stock" style="display:block; margin-bottom:6px; font-weight:700;">Stock <span class="required" style="color:var(--danger);">*</span></label>
                    <input id="stock" type="number" min="0" name="stock" value="{{ old('stock', $product->stock) }}"
                        required style="width:100%; padding:10px; border:1px solid var(--border); border-radius:8px;">
                </div>

                <div>
                    <label for="stock_threshold" style="display:block; margin-bottom:6px; font-weight:700;">Seuil d'alerte <span class="required" style="color:var(--danger);">*</span></label>
                    <input id="stock_threshold" type="number" min="0" name="stock_threshold"
                        value="{{ old('stock_threshold', $product->stock_threshold) }}" required
                        style="width:100%; padding:10px; border:1px solid var(--border); border-radius:8px;">
                </div>

                <div>
                    <label for="supplier_id" style="display:block; margin-bottom:6px; font-weight:700;">Fournisseur</label>
                    <select id="supplier_id" name="supplier_id"
                        style="width:100%; padding:10px; border:1px solid var(--border); border-radius:8px;">
                        <option value="">Aucun</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}"
                                {{ old('supplier_id', $product->supplier_id) == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="margin-top:12px; display:flex; gap:16px; align-items:center;">
                @if ($product->image)
                    <div style="width:70px; height:70px; border-radius:8px; border:1px solid var(--border); overflow:hidden;">
                        <img src="{{ asset('storage/' . $product->image) }}" style="width:100%; height:100%; object-fit:cover;">
                    </div>
                @endif
                <div style="flex:1;">
                    <label for="image" style="display:block; margin-bottom:6px; font-weight:700;">Remplacer l'image</label>
                    <input id="image" type="file" name="image" accept="image/*">
                </div>
            </div>

            <div style="margin-top:12px;">
                <label for="description" style="display:block; margin-bottom:6px; font-weight:700;">Description</label>
                <textarea id="description" name="description" rows="4"
                    style="width:100%; padding:10px; border:1px solid var(--border); border-radius:8px;">{{ old('description', $product->description) }}</textarea>
            </div>

            <div style="display:flex; gap:10px; margin-top:16px;">
                <a href="{{ route('magasinier.products.index') }}" class="mg-logout-btn"
                    style="text-decoration:none; background:white; color:var(--text); border:1px solid var(--border);">Annuler</a>
                <button class="mg-logout-btn" type="submit"><i class="fa-solid fa-floppy-disk"></i> Enregistrer les modifications</button>
            </div>
        </form>
    </div>
@endsection

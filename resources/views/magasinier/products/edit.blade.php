@extends('magasinier.layouts.app')

@section('title', 'Modifier le produit')
@section('page_title', 'Modifier le produit')

@section('content')
    <div class="card" style="max-width:860px; padding:18px; margin: 0 auto;">
        <div style="display:flex; align-items:center; gap:12px; margin-bottom:20px; border-bottom:1px solid var(--border); padding-bottom:16px;">
            <div style="background:var(--primary); color:white; border-radius:10px; width:45px; height:45px; display:flex; align-items:center; justify-content:center; font-size:20px;">
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

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom: 20px;">
                <div>
                    <label for="name" style="display:block; margin-bottom:6px; font-weight:700; color:var(--text); font-size:13.5px;">Nom complet <span style="color:var(--danger);">*</span></label>
                    <input id="name" name="name" value="{{ old('name', $product->name) }}" required
                        style="width:100%; height:40px; padding:10px; border:1px solid var(--border); border-radius:8px; outline:none; font-family:inherit; font-size:14px;" placeholder="Nom du produit">
                    @error('name')
                        <small style="color:var(--danger); display:block; margin-top:4px;">{{ $message }}</small>
                    @enderror
                </div>

                <div>
                    <label for="barcode_display" style="display:block; margin-bottom:6px; font-weight:700; color:var(--text); font-size:13.5px;">Code-barres / Référence (Non modifiable)</label>
                    <input id="barcode_display" readonly value="{{ $product->reference }}"
                        style="width:100%; height:40px; padding:10px; border:1px solid var(--border); border-radius:8px; background:#f1f5f9; color:#64748b; font-weight:700; outline:none; font-family:inherit; font-size:14px;">
                </div>

                <div>
                    <label for="category_name" style="display:block; margin-bottom:6px; font-weight:700; color:var(--text); font-size:13.5px;">Catégorie <span style="color:var(--danger);">*</span></label>
                    <select id="category_name" name="category_name" required
                        style="width:100%; height:40px; padding:0 10px; border:1px solid var(--border); border-radius:8px; outline:none; background:white; cursor:pointer; font-family:inherit; font-size:14px;">
                        <option value="">Sélectionner</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->name }}"
                                {{ old('category_name', $product->category_name) === $category->name ? 'selected' : '' }}>{{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_name')
                        <small style="color:var(--danger); display:block; margin-top:4px;">{{ $message }}</small>
                    @enderror
                </div>

                <div>
                    <label for="price" style="display:block; margin-bottom:6px; font-weight:700; color:var(--text); font-size:13.5px;">Prix (FCFA) <span style="color:var(--danger);">*</span></label>
                    <input id="price" type="number" min="0" name="price" value="{{ old('price', $product->price) }}" required 
                        style="width:100%; height:40px; padding:10px; border:1px solid var(--border); border-radius:8px; outline:none; font-family:inherit; font-size:14px;">
                    @error('price')
                        <small style="color:var(--danger); display:block; margin-top:4px;">{{ $message }}</small>
                    @enderror
                </div>

                <div>
                    <label for="stock" style="display:block; margin-bottom:6px; font-weight:700; color:var(--text); font-size:13.5px;">Stock <span style="color:var(--danger);">*</span></label>
                    <input id="stock" type="number" min="0" name="stock" value="{{ old('stock', $product->stock) }}" required 
                        style="width:100%; height:40px; padding:10px; border:1px solid var(--border); border-radius:8px; outline:none; font-family:inherit; font-size:14px;">
                    @error('stock')
                        <small style="color:var(--danger); display:block; margin-top:4px;">{{ $message }}</small>
                    @enderror
                </div>

                <div>
                    <label for="stock_threshold" style="display:block; margin-bottom:6px; font-weight:700; color:var(--text); font-size:13.5px;">Seuil d'alerte <span style="color:var(--danger);">*</span></label>
                    <input id="stock_threshold" type="number" min="0" name="stock_threshold" value="{{ old('stock_threshold', $product->stock_threshold) }}" required 
                        style="width:100%; height:40px; padding:10px; border:1px solid var(--border); border-radius:8px; outline:none; font-family:inherit; font-size:14px;">
                    @error('stock_threshold')
                        <small style="color:var(--danger); display:block; margin-top:4px;">{{ $message }}</small>
                    @enderror
                </div>

                <div style="grid-column: 1 / -1;">
                    <label for="supplier_id" style="display:block; margin-bottom:6px; font-weight:700; color:var(--text); font-size:13.5px;">Fournisseur</label>
                    <select id="supplier_id" name="supplier_id"
                        style="width:100%; height:40px; padding:0 10px; border:1px solid var(--border); border-radius:8px; outline:none; background:white; cursor:pointer; font-family:inherit; font-size:14px;">
                        <option value="">Aucun fournisseur</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}"
                                {{ old('supplier_id', $product->supplier_id) == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="margin-bottom:20px; display:flex; gap:16px; align-items:center; background:#f8fafc; padding:16px; border-radius:10px; border:1px solid var(--border);">
                @if ($product->image)
                    <div style="width:70px; height:70px; border-radius:8px; border:1px solid var(--border); overflow:hidden; background:white; display:flex; align-items:center; justify-content:center;">
                        <img src="{{ asset('storage/' . $product->image) }}" style="width:100%; height:100%; object-fit:cover;">
                    </div>
                @endif
                <div style="flex:1;">
                    <label for="image" style="display:block; margin-bottom:6px; font-weight:700; color:var(--text); font-size:13.5px;">Remplacer l'image du produit</label>
                    <input id="image" type="file" name="image" accept="image/*" style="font-size:13.5px;">
                </div>
            </div>

            <div style="margin-bottom:20px;">
                <label for="description" style="display:block; margin-bottom:6px; font-weight:700; color:var(--text); font-size:13.5px;">Description</label>
                <textarea id="description" name="description" rows="4" placeholder="Détails du produit..."
                    style="width:100%; padding:10px; border:1px solid var(--border); border-radius:8px; outline:none; font-family:inherit; font-size:14px;">{{ old('description', $product->description) }}</textarea>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px; border-top:1px solid var(--border); padding-top:16px;">
                <a href="{{ route('magasinier.products.index') }}" class="mg-logout-btn"
                    style="text-decoration:none; background:white; color:var(--text); border:1px solid var(--border);">
                    Annuler
                </a>
                <button class="mg-logout-btn" type="submit">
                    <i class="fa-solid fa-floppy-disk" style="margin-right:6px;"></i> Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
@endsection

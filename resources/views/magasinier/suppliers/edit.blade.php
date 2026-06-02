@extends('magasinier.layouts.app')

@section('title', 'Modifier le fournisseur')
@section('page_title', 'Modifier le fournisseur')

@section('content')
    <div class="card" style="max-width:860px; padding:18px; margin: 0 auto;">
        <div style="display:flex; align-items:center; gap:10px; margin-bottom:18px;">
            <div style="background:var(--primary); color:white; border-radius:8px; width:40px; height:40px; display:flex; align-items:center; justify-content:center; font-size:18px;">
                <i class="fa-solid fa-pen-to-square"></i>
            </div>
            <div>
                <h2 style="font-size:18px; margin:0; font-weight:800; color:var(--primary);">Modifier le fournisseur</h2>
                <p style="font-size:12px; color:var(--muted); margin:0;">Mettez à jour les coordonnées de <strong>{{ $supplier->name }}</strong></p>
            </div>
        </div>

        <form method="POST" action="{{ route('magasinier.suppliers.update', $supplier) }}">
            @csrf
            @method('PUT')

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                <div>
                    <label for="name" style="display:block; margin-bottom:6px; font-weight:700;">Nom du fournisseur <span class="required" style="color:var(--danger);">*</span></label>
                    <input id="name" name="name" value="{{ old('name', $supplier->name) }}" required
                        style="width:100%; padding:10px; border:1px solid var(--border); border-radius:8px;" placeholder="Ex: Grossiste Alimentaire CI">
                    @error('name')
                        <small style="color:#dc2626;">{{ $message }}</small>
                    @enderror
                </div>

                <div>
                    <label for="contact_person" style="display:block; margin-bottom:6px; font-weight:700;">Personne de contact</label>
                    <input id="contact_person" name="contact_person" value="{{ old('contact_person', $supplier->contact_person) }}"
                        style="width:100%; padding:10px; border:1px solid var(--border); border-radius:8px;" placeholder="Ex: Marcel Kassi">
                </div>

                <div>
                    <label for="phone" style="display:block; margin-bottom:6px; font-weight:700;">Téléphone</label>
                    <input id="phone" name="phone" value="{{ old('phone', $supplier->phone) }}"
                        style="width:100%; padding:10px; border:1px solid var(--border); border-radius:8px;" placeholder="Ex: +225 07 00 00 00">
                </div>

                <div>
                    <label for="email" style="display:block; margin-bottom:6px; font-weight:700;">Adresse E-mail</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $supplier->email) }}"
                        style="width:100%; padding:10px; border:1px solid var(--border); border-radius:8px;" placeholder="fournisseur@email.com">
                    @error('email')
                        <small style="color:#dc2626;">{{ $message }}</small>
                    @enderror
                </div>

                <div>
                    <label for="city" style="display:block; margin-bottom:6px; font-weight:700;">Ville</label>
                    <input id="city" name="city" value="{{ old('city', $supplier->city) }}"
                        style="width:100%; padding:10px; border:1px solid var(--border); border-radius:8px;" placeholder="Ex: Abidjan">
                </div>

                <div>
                    <label for="website" style="display:block; margin-bottom:6px; font-weight:700;">Site Internet (URL)</label>
                    <input id="website" name="website" value="{{ old('website', $supplier->website) }}"
                        style="width:100%; padding:10px; border:1px solid var(--border); border-radius:8px;" placeholder="Ex: https://grossiste.com">
                </div>

                <div style="grid-column: 1 / -1;">
                    <label for="address" style="display:block; margin-bottom:6px; font-weight:700;">Adresse</label>
                    <input id="address" name="address" value="{{ old('address', $supplier->address) }}"
                        style="width:100%; padding:10px; border:1px solid var(--border); border-radius:8px;" placeholder="Ex: Zone Industrielle Yopougon, Abidjan">
                </div>
            </div>

            <div style="margin-top:12px;">
                <label for="notes" style="display:block; margin-bottom:6px; font-weight:700;">Notes & Observations</label>
                <textarea id="notes" name="notes" rows="3"
                    style="width:100%; padding:10px; border:1px solid var(--border); border-radius:8px;" placeholder="Informations complémentaires, jours de livraison habituels...">{{ old('notes', $supplier->notes) }}</textarea>
            </div>

            <div style="margin-top:12px; display:flex; align-items:center; gap:8px;">
                <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $supplier->is_active) ? 'checked' : '' }} style="width:18px; height:18px; accent-color:var(--primary);">
                <label for="is_active" style="font-weight:700; cursor:pointer;">Fournisseur actif</label>
            </div>

            <div style="display:flex; gap:10px; margin-top:16px;">
                <a href="{{ route('magasinier.suppliers.index') }}" class="mg-logout-btn"
                    style="text-decoration:none; background:white; color:var(--text); border:1px solid var(--border);">Annuler</a>
                <button class="mg-logout-btn" type="submit"><i class="fa-solid fa-floppy-disk"></i> Enregistrer les modifications</button>
            </div>
        </form>
    </div>
@endsection

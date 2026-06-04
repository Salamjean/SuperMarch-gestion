@extends('admin.layouts.app')

@section('title', 'Nouveau client')
@section('page-title', 'Nouveau client')

@section('content')

    <div class="create-wrap"
        style="max-width: 800px; margin: 0 auto; background: var(--card); border: 1px solid var(--border); border-radius: 12px; padding: 30px;">
        <div class="create-header" style="display: flex; align-items: center; gap: 15px; margin-bottom: 25px;">
            <div class="create-header-icon"
                style="background: #059669; width:45px; height:45px; border-radius:10px; display:flex; align-items:center; justify-content:center; color:white; font-size:18px;">
                <i class="fa-solid fa-user-plus"></i>
            </div>
            <div>
                <h2 class="create-header-title" style="margin:0; font-size:18px; font-weight:800; color: #1e293b;">Nouveau
                    client</h2>
                <p class="create-header-sub" style="margin:0; font-size:12px; color: var(--text-muted);">Enregistrez une
                    nouvelle fiche client dans la base de données</p>
            </div>
        </div>

        <form id="customer-form" method="POST" action="{{ route('admin.customers.store') }}">
            @csrf

            <div class="form-section">
                <div class="form-grid-2" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label
                            style="font-size:12px; font-weight:700; color:var(--text); display:block; margin-bottom:6px;">Nom
                            complet <span style="color:var(--danger)">*</span></label>
                        <input type="text" name="name" class="form-control"
                            style="width:100%; padding:10px; border-radius:8px; border:1px solid var(--border); outline:none;"
                            value="{{ old('name') }}" placeholder="Ex: Jean Dupont" required>
                        @error('name')
                            <p style="color:var(--danger); font-size:11px; margin-top:4px;">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label
                            style="font-size:12px; font-weight:700; color:var(--text); display:block; margin-bottom:6px;">Téléphone</label>
                        <input type="text" name="phone" class="form-control"
                            style="width:100%; padding:10px; border-radius:8px; border:1px solid var(--border); outline:none;"
                            value="{{ old('phone') }}" placeholder="+225 07 ...">
                        @error('phone')
                            <p style="color:var(--danger); font-size:11px; margin-top:4px;">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="form-grid-2"
                    style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 15px;">
                    <div class="form-group">
                        <label
                            style="font-size:12px; font-weight:700; color:var(--text); display:block; margin-bottom:6px;">Adresse
                            email</label>
                        <input type="email" name="email" class="form-control"
                            style="width:100%; padding:10px; border-radius:8px; border:1px solid var(--border); outline:none;"
                            value="{{ old('email') }}" placeholder="client@exemple.com">
                        @error('email')
                            <p style="color:var(--danger); font-size:11px; margin-top:4px;">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label
                            style="font-size:12px; font-weight:700; color:var(--text); display:block; margin-bottom:6px;">Adresse
                            physique</label>
                        <input type="text" name="address" class="form-control"
                            style="width:100%; padding:10px; border-radius:8px; border:1px solid var(--border); outline:none;"
                            value="{{ old('address') }}" placeholder="Ex: Abidjan, Plateau">
                        @error('address')
                            <p style="color:var(--danger); font-size:11px; margin-top:4px;">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <hr style="border: 0; border-top: 1px solid var(--border); margin: 25px 0;">

                <div
                    style="font-size: 13.5px; font-weight: 800; color: #004d99; margin-bottom: 15px; display:flex; align-items:center; gap:8px;">
                    <i class="fa-solid fa-hand-holding-dollar"></i> Initialisation du solde de crédit (Optionnel)
                </div>

                <div class="form-group">
                    <label
                        style="font-size:12px; font-weight:700; color:var(--text); display:block; margin-bottom:6px;">Encours
                        de Dette de départ (FCFA)</label>
                    <input type="number" name="debt_balance" class="form-control"
                        style="width:100%; padding:10px; border-radius:8px; border:1px solid var(--border); outline:none;"
                        value="{{ old('debt_balance', 0.0) }}" min="0" step="0.01">
                </div>
            </div>

            <div
                style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 30px; border-top: 1px solid var(--border); padding-top: 20px;">
                <a href="{{ route('admin.customers.index') }}" class="btn btn-gray"
                    style="text-decoration:none;">Annuler</a>
                <button type="submit" class="btn btn-yellow">Créer la fiche client</button>
            </div>
        </form>
    </div>

@endsection

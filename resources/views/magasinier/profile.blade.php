@extends('magasinier.layouts.app')

@section('title', 'Mon Profil')
@section('page_title', 'Mon Profil')

@section('content')
    <div class="card" style="max-width:860px; padding:24px; margin: 0 auto;">
        <div style="display:flex; align-items:center; gap:12px; margin-bottom:20px; border-bottom:1px solid var(--border); padding-bottom:16px;">
            <div style="background:var(--primary); color:white; border-radius:10px; width:45px; height:45px; display:flex; align-items:center; justify-content:center; font-size:20px;">
                <i class="fa-solid fa-user-gear"></i>
            </div>
            <div>
                <h2 style="font-size:20px; margin:0; font-weight:800; color:var(--primary);">Gestion du profil</h2>
                <p style="font-size:13px; color:var(--muted); margin:0;">Mettez à jour vos informations personnelles et votre mot de passe</p>
            </div>
        </div>

        @if (session('success'))
            <div style="margin-bottom:16px; padding:12px; border:1px solid #86efac; background:#f0fdf4; color:#166534; border-radius:8px; font-weight: 500;">
                <i class="fa-solid fa-circle-check" style="margin-right:6px;"></i> {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('magasinier.profile.update') }}">
            @csrf

            <!-- Section Infos de Connexion (Lecture seule) -->
            <div style="background: #f8fafc; border: 1px solid var(--border); border-radius: 10px; padding: 16px; margin-bottom: 20px; display: flex; flex-wrap: wrap; gap: 20px; align-items: center;">
                <div style="flex: 1; min-width: 200px;">
                    <span style="display: block; font-size: 11px; text-transform: uppercase; color: var(--muted); font-weight: 700; letter-spacing: 0.5px;">Code d'identification</span>
                    <strong style="font-size: 18px; color: var(--primary); font-family: monospace;">{{ $user->login_code ?? '—' }}</strong>
                    <span style="display: block; font-size: 11px; color: var(--muted); margin-top: 2px;">(Utilisez ce code pour vous connecter)</span>
                </div>
                <div style="flex: 1; min-width: 200px;">
                    <span style="display: block; font-size: 11px; text-transform: uppercase; color: var(--muted); font-weight: 700; letter-spacing: 0.5px;">Rôle</span>
                    <span class="badge" style="background: #e0f2fe; color: #0369a1; padding: 4px 10px; border-radius: 999px; font-size: 12px; font-weight: 700; display: inline-block; margin-top: 4px;">
                        <i class="fa-solid fa-warehouse" style="margin-right: 4px;"></i> MAGASINIER
                    </span>
                </div>
            </div>

            <!-- Grille des informations personnelles -->
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom: 20px;">
                <div>
                    <label for="name" style="display:block; margin-bottom:6px; font-weight:700; color: var(--text);">Nom complet <span style="color:var(--danger);">*</span></label>
                    <input id="name" name="name" value="{{ old('name', $user->name) }}" required
                        style="width:100%; padding:10px; border:1px solid var(--border); border-radius:8px; font-family: inherit; font-size: 14px;" placeholder="Ex: Jean Dupont">
                    @error('name')
                        <small style="color:var(--danger); display:block; margin-top:4px;">{{ $message }}</small>
                    @enderror
                </div>

                <div>
                    <label for="email" style="display:block; margin-bottom:6px; font-weight:700; color: var(--text);">Adresse E-mail <span style="color:var(--danger);">*</span></label>
                    <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required
                        style="width:100%; padding:10px; border:1px solid var(--border); border-radius:8px; font-family: inherit; font-size: 14px;" placeholder="adresse@email.com">
                    @error('email')
                        <small style="color:var(--danger); display:block; margin-top:4px;">{{ $message }}</small>
                    @enderror
                </div>

                <div>
                    <label for="phone" style="display:block; margin-bottom:6px; font-weight:700; color: var(--text);">Téléphone</label>
                    <input id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                        style="width:100%; padding:10px; border:1px solid var(--border); border-radius:8px; font-family: inherit; font-size: 14px;" placeholder="Ex: +225 07 00 00 00">
                    @error('phone')
                        <small style="color:var(--danger); display:block; margin-top:4px;">{{ $message }}</small>
                    @enderror
                </div>

                <div>
                    <label for="gender" style="display:block; margin-bottom:6px; font-weight:700; color: var(--text);">Genre</label>
                    <select id="gender" name="gender" style="width:100%; padding:10px; border:1px solid var(--border); border-radius:8px; font-family: inherit; font-size: 14px; background: white;">
                        <option value="">Sélectionner...</option>
                        <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Masculin</option>
                        <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Féminin</option>
                        <option value="other" {{ old('gender', $user->gender) == 'other' ? 'selected' : '' }}>Autre</option>
                    </select>
                    @error('gender')
                        <small style="color:var(--danger); display:block; margin-top:4px;">{{ $message }}</small>
                    @enderror
                </div>

                <div style="grid-column: 1 / -1;">
                    <label for="address" style="display:block; margin-bottom:6px; font-weight:700; color: var(--text);">Adresse</label>
                    <input id="address" name="address" value="{{ old('address', $user->address) }}"
                        style="width:100%; padding:10px; border:1px solid var(--border); border-radius:8px; font-family: inherit; font-size: 14px;" placeholder="Ex: Rue des Jardins, Abidjan">
                    @error('address')
                        <small style="color:var(--danger); display:block; margin-top:4px;">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <!-- Section Modification Mot de Passe -->
            <div style="border-top: 1px solid var(--border); padding-top: 16px; margin-bottom: 20px;">
                <h3 style="font-size: 15px; margin: 0 0 12px; font-weight: 700; color: var(--primary);">
                    <i class="fa-solid fa-lock" style="margin-right: 6px;"></i> Changer de mot de passe
                </h3>
                <p style="font-size:12px; color:var(--muted); margin:0 0 14px 0;">Laissez vide si vous ne souhaitez pas modifier votre mot de passe.</p>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                    <div>
                        <label for="password" style="display:block; margin-bottom:6px; font-weight:700; color: var(--text);">Nouveau mot de passe</label>
                        <input id="password" type="password" name="password"
                            style="width:100%; padding:10px; border:1px solid var(--border); border-radius:8px; font-family: inherit; font-size: 14px;" placeholder="Minimum 6 caractères">
                        @error('password')
                            <small style="color:var(--danger); display:block; margin-top:4px;">{{ $message }}</small>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" style="display:block; margin-bottom:6px; font-weight:700; color: var(--text);">Confirmer le nouveau mot de passe</label>
                        <input id="password_confirmation" type="password" name="password_confirmation"
                            style="width:100%; padding:10px; border:1px solid var(--border); border-radius:8px; font-family: inherit; font-size: 14px;" placeholder="Répéter le mot de passe">
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div style="display:flex; justify-content: flex-end; gap:10px; border-top: 1px solid var(--border); padding-top: 16px;">
                <a href="{{ route('magasinier.dashboard') }}" class="mg-logout-btn"
                    style="text-decoration:none; background:white; color:var(--text); border:1px solid var(--border);">
                    Annuler
                </a>
                <button class="mg-logout-btn" type="submit">
                    <i class="fa-solid fa-floppy-disk" style="margin-right:6px;"></i> Mettre à jour le profil
                </button>
            </div>
        </form>
    </div>
@endsection

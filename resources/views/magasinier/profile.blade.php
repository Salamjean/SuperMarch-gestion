@extends('magasinier.layouts.app')

@section('title', 'Mon Profil')
@section('page_title', 'Mon Profil')

@section('content')
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border); padding-bottom: 20px; margin-bottom: 25px;">
        <div>
            <h2 style="font-size: 24px; font-weight: 800; color: var(--primary); display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-user-gear"></i> Mon Profil Utilisateur
            </h2>
            <p style="color: var(--muted); font-size: 14px; margin-top: 4px;">Gérez vos informations personnelles et mettez à jour votre mot de passe.</p>
        </div>
    </div>

    <!-- Main Profile Content Grid -->
    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px; align-items: start;">
        <!-- Left Column: Avatar & Basic Employee Info -->
        <div style="background: #fff; border: 1px solid var(--border); border-radius: 20px; padding: 30px; text-align: center; display: flex; flex-direction: column; align-items: center; gap: 20px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);">
            <div style="width: 110px; height: 110px; background: rgba(0, 77, 153, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 4px solid #fff; box-shadow: 0 10px 15px -3px rgba(0, 77, 153, 0.15);">
                <i class="fa-solid fa-user" style="font-size: 48px; color: var(--primary);"></i>
            </div>

            <div>
                <h3 id="profile-display-name" style="font-size: 18px; font-weight: 800; color: var(--text); margin-bottom: 4px;">
                    {{ $user->name }}
                </h3>
                <div style="font-size: 13px; font-weight: 700; color: var(--primary); text-transform: uppercase; letter-spacing: 0.05em;">
                    {{ $user->position ?? 'Magasinier' }}
                </div>
                <div style="font-size: 12px; color: var(--muted); margin-top: 4px;">Département:
                    {{ $user->department ?? 'Stock & Logistique' }}
                </div>
            </div>

            <!-- Meta Badges / Dates -->
            <div style="width: 100%; border-top: 1px solid #f1f5f9; padding-top: 20px; display: flex; flex-direction: column; gap: 12px; text-align: left;">
                <div style="display: flex; align-items: center; gap: 10px; font-size: 13px;">
                    <i class="fa-solid fa-calendar-day" style="color: var(--muted); width: 16px;"></i>
                    <span style="color: var(--muted);">Embauché le :</span>
                    <span style="font-weight: 700; color: var(--text);">
                        {{ $user->hire_date ? \Carbon\Carbon::parse($user->hire_date)->format('d F Y') : 'N/A' }}
                    </span>
                </div>
                <div style="display: flex; align-items: center; gap: 10px; font-size: 13px;">
                    <i class="fa-solid fa-id-card" style="color: var(--muted); width: 16px;"></i>
                    <span style="color: var(--muted);">Rôle d'accès :</span>
                    <span style="background: #e0f2fe; color: #0369a1; padding: 2px 8px; border-radius: 6px; font-weight: bold; font-size: 11px;">
                        {{ $user->role ?? 'Magasinier' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Right Column: Settings Form -->
        <div style="background: #fff; border: 1px solid var(--border); border-radius: 20px; padding: 30px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);">
            @if (session('success'))
                <div style="margin-bottom: 20px; padding: 12px 18px; border: 1px solid #86efac; background: #f0fdf4; color: #166534; border-radius: 12px; font-weight: 600; font-size: 13.5px; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('magasinier.profile.update') }}">
                @csrf
                <h3 style="font-size: 16px; font-weight: 800; color: var(--text); margin-bottom: 20px; display: flex; align-items: center; gap: 8px; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">
                    <i class="fa-solid fa-user-pen" style="color: var(--primary);"></i> Informations du Compte
                </h3>

                <!-- Personnal Information Fields -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px;">
                    <div style="grid-column: 1 / -1; background: #eff6ff; padding: 12px 18px; border-radius: 10px; border: 1px dashed #3b82f6; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);">
                        <span style="font-size: 13.5px; font-weight: 700; color: #1e40af; display: flex; align-items: center; gap: 6px;"><i class="fa-solid fa-id-badge" style="color: #3b82f6;"></i> Votre Code ID de Connexion :</span>
                        <span style="font-size: 16px; font-weight: 800; color: #1e3a8a; letter-spacing: 0.5px;">{{ $user->login_code }}</span>
                    </div>
                    <div>
                        <label for="name" style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--muted); margin-bottom: 8px;">Nom Complet</label>
                        <input type="text" id="name" name="name" required value="{{ old('name', $user->name) }}"
                            style="width: 100%; height: 40px; background: #fff; border: 1px solid var(--border); border-radius: 10px; padding: 10px 14px; color: var(--text); font-size: 13.5px; font-weight: 500; outline: none; transition: border-color 0.2s; font-family: inherit;">
                        @error('name')
                            <small style="color: var(--danger); display: block; margin-top: 4px;">{{ $message }}</small>
                        @enderror
                    </div>
                    <div>
                        <label for="email" style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--muted); margin-bottom: 8px;">Adresse Email</label>
                        <input type="email" id="email" name="email" required value="{{ old('email', $user->email) }}"
                            style="width: 100%; height: 40px; background: #fff; border: 1px solid var(--border); border-radius: 10px; padding: 10px 14px; color: var(--text); font-size: 13.5px; font-weight: 500; outline: none; transition: border-color 0.2s; font-family: inherit;">
                        @error('email')
                            <small style="color: var(--danger); display: block; margin-top: 4px;">{{ $message }}</small>
                        @enderror
                    </div>
                    <div>
                        <label for="phone" style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--muted); margin-bottom: 8px;">Numéro de Téléphone</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="Ex: +225 07 00 00 00"
                            style="width: 100%; height: 40px; background: #fff; border: 1px solid var(--border); border-radius: 10px; padding: 10px 14px; color: var(--text); font-size: 13.5px; font-weight: 500; outline: none; transition: border-color 0.2s; font-family: inherit;">
                        @error('phone')
                            <small style="color: var(--danger); display: block; margin-top: 4px;">{{ $message }}</small>
                        @enderror
                    </div>
                    <div>
                        <label for="gender" style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--muted); margin-bottom: 8px;">Genre (Sexe)</label>
                        <select id="gender" name="gender"
                            style="width: 100%; height: 40px; background: #fff; border: 1px solid var(--border); border-radius: 10px; padding: 0 14px; color: var(--text); font-size: 13.5px; font-weight: 500; outline: none; cursor: pointer; font-family: inherit;">
                            <option value="">Sélectionner</option>
                            <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Homme / Masculin</option>
                            <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Femme / Féminin</option>
                            <option value="other" {{ old('gender', $user->gender) == 'other' ? 'selected' : '' }}>Autre</option>
                        </select>
                        @error('gender')
                            <small style="color: var(--danger); display: block; margin-top: 4px;">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div style="margin-bottom: 25px;">
                    <label for="address" style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--muted); margin-bottom: 8px;">Adresse Domicile</label>
                    <input type="text" id="address" name="address" value="{{ old('address', $user->address) }}" placeholder="Ex: Cocody Riviera Palmeraie, Abidjan"
                        style="width: 100%; height: 40px; background: #fff; border: 1px solid var(--border); border-radius: 10px; padding: 10px 14px; color: var(--text); font-size: 13.5px; font-weight: 500; outline: none; transition: border-color 0.2s; font-family: inherit;">
                    @error('address')
                        <small style="color: var(--danger); display: block; margin-top: 4px;">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Password Update Fields -->
                <h3 style="font-size: 16px; font-weight: 800; color: var(--text); margin-bottom: 20px; display: flex; align-items: center; gap: 8px; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px; margin-top: 30px;">
                    <i class="fa-solid fa-lock" style="color: var(--danger);"></i> Modifier le mot de passe (Optionnel)
                </h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
                    <div>
                        <label for="password" style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--muted); margin-bottom: 8px;">Nouveau Mot de Passe</label>
                        <input type="password" id="password" name="password" placeholder="Min. 6 caractères"
                            style="width: 100%; height: 40px; background: #fff; border: 1px solid var(--border); border-radius: 10px; padding: 10px 14px; color: var(--text); font-size: 13.5px; font-weight: 500; outline: none; transition: border-color 0.2s; font-family: inherit;">
                        @error('password')
                            <small style="color: var(--danger); display: block; margin-top: 4px;">{{ $message }}</small>
                        @enderror
                    </div>
                    <div>
                        <label for="password_confirmation" style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--muted); margin-bottom: 8px;">Confirmer Nouveau Mot de Passe</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Répéter le mot de passe"
                            style="width: 100%; height: 40px; background: #fff; border: 1px solid var(--border); border-radius: 10px; padding: 10px 14px; color: var(--text); font-size: 13.5px; font-weight: 500; outline: none; transition: border-color 0.2s; font-family: inherit;">
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                    <a href="{{ route('magasinier.dashboard') }}" class="mg-logout-btn"
                        style="text-decoration:none; background:white; color:var(--text); border:1px solid var(--border);">
                        Annuler
                    </a>
                    <button class="mg-logout-btn" type="submit">
                        <i class="fa-solid fa-floppy-disk" style="margin-right:6px;"></i> Enregistrer les Modifications
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

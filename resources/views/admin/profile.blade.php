@extends('admin.layouts.app')

@section('title', 'Mon Profil')
@section('page-title', 'Mon Profil')

@section('content')

    <div class="create-wrap">

        {{-- En-tête --}}
        <div class="create-header">
            <div class="create-header-icon">
                <i class="fa-solid fa-user-gear"></i>
            </div>
            <div>
                <h2 class="create-header-title">Gestion du profil</h2>
                <p class="create-header-sub">Mettez à jour vos informations personnelles et votre mot de passe d'administrateur</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.profile.update') }}" autocomplete="off">
            @csrf

            {{-- Section 1 : Informations de Connexion (Lecture seule) --}}
            <div class="form-section" style="background: #f8fafc; border-style: dashed;">
                <div style="display: flex; flex-wrap: wrap; gap: 20px; align-items: center;">
                    <div style="flex: 1; min-width: 200px;">
                        <span style="display: block; font-size: 11px; text-transform: uppercase; color: var(--text-muted); font-weight: 700; letter-spacing: 0.5px;">Code d'identification</span>
                        <strong style="font-size: 18px; color: var(--blue); font-family: monospace;">{{ $user->login_code ?? '—' }}</strong>
                        <span style="display: block; font-size: 11px; color: var(--text-muted); margin-top: 2px;">(Utilisez ce code pour vous connecter)</span>
                    </div>
                    <div style="flex: 1; min-width: 200px;">
                        <span style="display: block; font-size: 11px; text-transform: uppercase; color: var(--text-muted); font-weight: 700; letter-spacing: 0.5px;">Rôle</span>
                        <span class="badge" style="background: var(--blue-light); color: var(--blue); padding: 4px 10px; border-radius: 999px; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; margin-top: 4px;">
                            <i class="fa-solid fa-shield-halved"></i> ADMINISTRATEUR
                        </span>
                    </div>
                </div>
            </div>

            {{-- Section 2 : Identité & Coordonnées --}}
            <div class="form-section">
                <div class="form-section-title">
                    <i class="fa-solid fa-id-card"></i> Identité & Coordonnées
                </div>
                
                <div class="form-grid-2">
                    <div class="form-group">
                        <label for="name">Nom complet <span class="required">*</span></label>
                        <div class="input-wrap">
                            <i class="fa-solid fa-user"></i>
                            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required placeholder="Kassi Marcel">
                        </div>
                        @error('name')
                            <p class="error-msg">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email">Adresse e-mail</label>
                        <div class="input-wrap">
                            <i class="fa-solid fa-envelope"></i>
                            <input type="email" id="email" name="email" value="{{ $user->email }}" readonly style="background: #f1f5f9; cursor: not-allowed; color: var(--text-muted);" title="L'adresse e-mail ne peut pas être modifiée.">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="phone">Téléphone</label>
                        <div class="input-wrap">
                            <i class="fa-solid fa-phone"></i>
                            <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="+225 01 02 03 04 05">
                        </div>
                        @error('phone')
                            <p class="error-msg">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="gender">Genre</label>
                        <div class="input-wrap">
                            <i class="fa-solid fa-venus-mars"></i>
                            <select id="gender" name="gender">
                                <option value="">— Sélectionner —</option>
                                <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Homme</option>
                                <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Femme</option>
                            </select>
                        </div>
                        @error('gender')
                            <p class="error-msg">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label for="address">Adresse</label>
                        <div class="input-wrap">
                            <i class="fa-solid fa-location-dot"></i>
                            <input type="text" id="address" name="address" value="{{ old('address', $user->address) }}" placeholder="Aboisso, Maféré">
                        </div>
                        @error('address')
                            <p class="error-msg">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Section 3 : Sécurité --}}
            <div class="form-section">
                <div class="form-section-title">
                    <i class="fa-solid fa-lock"></i> Changer de mot de passe (Optionnel)
                </div>
                <p style="font-size:12px; color:var(--text-muted); margin: -10px 0 14px 0;">Laissez vide si vous ne souhaitez pas modifier votre mot de passe actuel.</p>
                
                <div class="form-grid-2">
                    <div class="form-group">
                        <label for="password">Nouveau mot de passe</label>
                        <div class="input-wrap">
                            <i class="fa-solid fa-lock"></i>
                            <input type="password" id="password" name="password" placeholder="Minimum 6 caractères">
                        </div>
                        @error('password')
                            <p class="error-msg">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Confirmer le mot de passe</label>
                        <div class="input-wrap">
                            <i class="fa-solid fa-lock"></i>
                            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Répéter le mot de passe">
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('admin.dashboard') }}" class="btn-cancel">
                    <i class="fa-solid fa-xmark"></i> Annuler
                </a>
                <button type="submit" class="btn btn-yellow">
                    <i class="fa-solid fa-floppy-disk"></i> Enregistrer les modifications
                </button>
            </div>

        </form>
    </div>

    @push('styles')
        <style>
            .create-wrap {
                width: 70%;
                margin: 0 auto;
            }

            .create-header {
                display: flex;
                align-items: center;
                gap: 16px;
                margin-bottom: 24px;
            }

            .create-header-icon {
                width: 52px;
                height: 52px;
                border-radius: 14px;
                background: var(--blue);
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 22px;
                color: #fff;
                flex-shrink: 0;
                box-shadow: 0 4px 16px rgba(0, 77, 153, .25);
            }

            .create-header-title {
                font-size: 18px;
                font-weight: 800;
                color: var(--blue);
                margin: 0 0 3px;
            }

            .create-header-sub {
                font-size: 13px;
                color: var(--text-muted);
                margin: 0;
            }

            .form-section {
                background: #fff;
                border: 1px solid var(--border);
                border-radius: 14px;
                padding: 22px 24px 18px;
                margin-bottom: 16px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, .04);
            }

            .form-section-title {
                font-size: 12px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: .07em;
                color: var(--blue);
                margin-bottom: 18px;
                display: flex;
                align-items: center;
                gap: 7px;
                padding-bottom: 12px;
                border-bottom: 1.5px solid var(--blue-light);
            }

            .form-grid-2 {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 14px 20px;
            }

            select.input-wrap-select,
            .input-wrap select {
                padding-left: 38px;
                appearance: none;
                cursor: pointer;
            }

            .required {
                color: #e53e3e;
            }

            .form-actions {
                display: flex;
                justify-content: flex-end;
                gap: 12px;
                margin-top: 8px;
            }

            .btn-cancel {
                background: #fff;
                border: 1.5px solid #d0dce8;
                color: #5a7a99;
                font-family: inherit;
                font-size: 14px;
                font-weight: 600;
                padding: 11px 22px;
                border-radius: 10px;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                gap: 7px;
                transition: background .2s, border-color .2s;
                cursor: pointer;
            }

            .btn-cancel:hover {
                background: #f5f9ff;
                border-color: #a0b8cc;
            }

            @media (max-width: 600px) {
                .form-grid-2 {
                    grid-template-columns: 1fr;
                }

                .form-grid-2 .form-group[style*="grid-column"] {
                    grid-column: auto;
                }

                .create-wrap {
                    width: 100%;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                @if (session('error'))
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: "{{ session('error') }}",
                        confirmButtonColor: '#004d99'
                    });
                @endif

                const form = document.querySelector('form[action="{{ route('admin.profile.update') }}"]');
                
                form.addEventListener('submit', function(e) {
                    e.preventDefault(); // Stop normal form submission
                    
                    Swal.fire({
                        title: 'Confirmation requise',
                        text: 'Veuillez saisir votre mot de passe actuel pour valider les modifications :',
                        input: 'password',
                        inputAttributes: {
                            autocapitalize: 'off',
                            autocorrect: 'off',
                            required: 'required',
                            placeholder: 'Votre mot de passe actuel'
                        },
                        showCancelButton: true,
                        confirmButtonText: 'Confirmer',
                        cancelButtonText: 'Annuler',
                        confirmButtonColor: '#004d99',
                        cancelButtonColor: '#64748b',
                        inputValidator: (value) => {
                            if (!value) {
                                return 'Vous devez saisir votre mot de passe actuel !'
                            }
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Append current password to form as hidden input
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'current_password';
                            input.value = result.value;
                            form.appendChild(input);
                            
                            // Submit the form
                            form.submit();
                        }
                    });
                });
            });
        </script>
    @endpush

@endsection

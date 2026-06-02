@extends('admin.layouts.app')

@section('title', 'Modifier l\'employé')
@section('page-title', 'Modifier l\'employé')

@section('content')

    <div class="create-wrap">

        {{-- En-tête --}}
        <div class="create-header">
            <div class="create-header-icon" style="background: #f59e0b;">
                <i class="fa-solid fa-user-pen"></i>
            </div>
            <div>
                <h2 class="create-header-title">Modifier le compte de {{ $employee->name }}</h2>
                <p class="create-header-sub">Mettez à jour les informations de l'employé ci-dessous</p>
            </div>
        </div>


        {{-- Indicateur de progression --}}
        <div class="steps-indicator">
            <div class="step-item active" id="indicator-1">
                <div class="step-number">1</div>
                <div class="step-label">Informations</div>
            </div>
            <div class="step-line" id="step-line-1"></div>
            <div class="step-item" id="indicator-2">
                <div class="step-number">2</div>
                <div class="step-label">Accès Compte</div>
            </div>
        </div>

        <form id="employee-form" method="POST" action="{{ route('admin.employees.update', $employee) }}" autocomplete="off">
            @csrf
            @method('PUT')

            {{-- ── ÉTAPE 1 : Informations Personnelles & Professionnelles ── --}}
            <div id="step-1" class="form-step">
                {{-- ── Section 1 : Identité ── --}}
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fa-solid fa-id-card"></i> Identité
                    </div>
                    <div class="form-grid-2">

                        <div class="form-group">
                            <label for="name">Nom complet <span class="required">*</span></label>
                            <div class="input-wrap">
                                <i class="fa-solid fa-user"></i>
                                <input type="text" id="name" name="name" value="{{ old('name', $employee->name) }}"
                                    placeholder="Kassi Marcel" autofocus>
                            </div>
                            @error('name')
                                <p class="error-msg">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="gender">Genre</label>
                            <div class="input-wrap">
                                <i class="fa-solid fa-venus-mars"></i>
                                <select id="gender" name="gender">
                                    <option value="">— Sélectionner —</option>
                                    <option value="male" {{ old('gender', $employee->gender) == 'male' ? 'selected' : '' }}>Homme</option>
                                    <option value="female" {{ old('gender', $employee->gender) == 'female' ? 'selected' : '' }}>Femme</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="phone">Téléphone</label>
                            <div class="input-wrap">
                                <i class="fa-solid fa-phone"></i>
                                <input type="text" id="phone" name="phone" value="{{ old('phone', $employee->phone) }}"
                                    placeholder="+225 01 02 03 04 05">
                            </div>
                            @error('phone')
                                <p class="error-msg">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="address">Adresse</label>
                            <div class="input-wrap">
                                <i class="fa-solid fa-location-dot"></i>
                                <input type="text" id="address" name="address" value="{{ old('address', $employee->address) }}"
                                    placeholder="Aboisso, Maféré">
                            </div>
                            @error('address')
                                <p class="error-msg">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>
                </div>

                {{-- ── Section 2 : Poste --}}
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fa-solid fa-briefcase"></i> Poste & département
                    </div>
                    <div class="form-grid-2">

                        <div class="form-group">
                            <label for="position">Poste / Fonction</label>
                            <div class="input-wrap">
                                <i class="fa-solid fa-tag"></i>
                                <select id="position" name="position" onchange="updateDepartment(this.value)">
                                    <option value="">— Sélectionner —</option>
                                    <option value="magasinier" {{ old('position', $employee->position) == 'magasinier' ? 'selected' : '' }}>Magasinier</option>
                                    <option value="caissier" {{ old('position', $employee->position) == 'caissier' ? 'selected' : '' }}>Caissier</option>
                                    <option value="livreur" {{ old('position', $employee->position) == 'livreur' ? 'selected' : '' }}>Livreur</option>
                                </select>
                            </div>
                            @error('position')
                                <p class="error-msg">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="department">Département</label>
                            <div class="input-wrap">
                                <i class="fa-solid fa-building"></i>
                                <input type="text" id="department" name="department" value="{{ old('department', $employee->department) }}"
                                    placeholder="Caisse, Réception, Stocks…" readonly>
                            </div>
                            @error('department')
                                <p class="error-msg">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="hire_date">Date d'embauche</label>
                            <div class="input-wrap">
                                <i class="fa-solid fa-calendar-days"></i>
                                <input type="text" id="hire_date" name="hire_date" value="{{ old('hire_date', $employee->hire_date) }}" 
                                    placeholder="Sélectionner une date" class="datepicker">
                            </div>
                            @error('hire_date')
                                <p class="error-msg">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('admin.employees.index') }}" class="btn btn-cancel">
                        <i class="fa-solid fa-xmark"></i> Annuler
                    </a>
                    <button type="button" class="btn btn-yellow" onclick="nextStep()">
                        Suivant <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </div>
            </div>

            {{-- ── ÉTAPE 2 : Accès & Sécurité ── --}}
            <div id="step-2" class="form-step" style="display: none;">
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fa-solid fa-shield-halved"></i> Accès & mot de passe
                    </div>
                    <div class="form-grid-2">

                        <div class="form-group" style="grid-column: 1 / -1; background: #f0f7ff; padding: 12px; border-radius: 8px; border: 1px dashed #004d99; margin-bottom: 12px; display: flex; align-items: center; justify-content: space-between;">
                            <div>
                                <span style="font-size: 13px; font-weight: 600; color: #004d99;">Code ID (Identifiant de connexion) :</span>
                                <span style="font-size: 15px; font-weight: 700; color: #1a2840; margin-left: 8px;">{{ $employee->login_code }}</span>
                            </div>
                            <span class="role-badge employee" style="font-size: 11px; background: #e0f2fe; color: #0369a1; border-color: #bae6fd;"><i class="fa-solid fa-id-badge"></i> Généré automatiquement</span>
                        </div>

                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label for="email">Adresse e-mail <span class="required">*</span></label>
                            <div class="input-wrap">
                                <i class="fa-solid fa-envelope"></i>
                                <input type="email" id="email" name="email" value="{{ old('email', $employee->email) }}"
                                    placeholder="employe@supermarche.com">
                            </div>
                            @error('email')
                                <p class="error-msg">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password">Nouveau mot de passe</label>
                            <div class="input-wrap">
                                <i class="fa-solid fa-lock"></i>
                                <input type="password" id="password" name="password" placeholder="Laissez vide pour conserver">
                            </div>
                            @error('password')
                                <p class="error-msg">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation">Confirmer le mot de passe</label>
                            <div class="input-wrap">
                                <i class="fa-solid fa-lock"></i>
                                <input type="password" id="password_confirmation" name="password_confirmation"
                                    placeholder="Répétez le mot de passe">
                            </div>
                        </div>

                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-cancel" onclick="prevStep()">
                        <i class="fa-solid fa-arrow-left"></i> Précédent
                    </button>
                    <button type="submit" class="btn btn-yellow">
                        <i class="fa-solid fa-floppy-disk"></i> Enregistrer les modifications
                    </button>
                </div>
            </div>


        </form>
    </div>

    @push('styles')
        <style>
            .create-wrap {
                width: 70%;
                margin: 0 auto;
            }

            /* Indicateur de étapes */
            .steps-indicator {
                display: flex;
                align-items: center;
                justify-content: center;
                margin-bottom: 30px;
                padding: 0 20px;
            }

            .step-item {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 8px;
                position: relative;
                z-index: 2;
            }

            .step-number {
                width: 36px;
                height: 36px;
                border-radius: 50%;
                background: #fff;
                border: 2px solid #d0dce8;
                color: #7a94aa;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 700;
                font-size: 14px;
                transition: all 0.3s ease;
            }

            .step-label {
                font-size: 12px;
                font-weight: 600;
                color: #7a94aa;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                transition: all 0.3s ease;
            }

            .step-item.active .step-number {
                background: #004d99;
                border-color: #004d99;
                color: #fff;
                box-shadow: 0 4px 10px rgba(0, 77, 153, 0.3);
            }

            .step-item.active .step-label {
                color: #004d99;
            }

            .step-item.completed .step-number {
                background: #28a745;
                border-color: #28a745;
                color: #fff;
            }

            .step-line {
                flex: 1;
                height: 2px;
                background: #d0dce8;
                margin: 0 15px;
                margin-top: -22px;
                position: relative;
                z-index: 1;
                max-width: 100px;
            }

            .step-line.active {
                background: #004d99;
            }

            /* Flatpickr Custom Styling */
            .flatpickr-calendar {
                border-radius: 12px !important;
                box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
                border: 1px solid #e2eaf3 !important;
                font-family: 'Inter', sans-serif !important;
            }
            .flatpickr-day.selected {
                background: #004d99 !important;
                border-color: #004d99 !important;
            }
            .flatpickr-day:hover {
                background: #f0f7ff !important;
            }
            .flatpickr-current-month {
                font-weight: 700 !important;
            }

            /* Header */
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
                background: #004d99;
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
                color: #004d99;
                margin: 0 0 3px;
            }

            .create-header-sub {
                font-size: 13px;
                color: #7a94aa;
                margin: 0;
            }

            /* Sections */
            .form-section {
                background: #fff;
                border: 1px solid #e2eaf3;
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
                color: #004d99;
                margin-bottom: 18px;
                display: flex;
                align-items: center;
                gap: 7px;
                padding-bottom: 12px;
                border-bottom: 1.5px solid #e8f1fb;
            }

            .form-grid-2 {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 14px 20px;
            }

            /* Select styling */
            select.input-wrap-select,
            .input-wrap select {
                padding-left: 38px;
                appearance: none;
                cursor: pointer;
            }

            /* Required star */
            .required {
                color: #e53e3e;
            }

            /* Actions */
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

    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/fr.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Initialisation de Flatpickr
                flatpickr("#hire_date", {
                    locale: "fr",
                    dateFormat: "Y-m-d",
                    altInput: true,
                    altFormat: "d F Y",
                    allowInput: true,
                    disableMobile: "true"
                });

                @if($errors->has('email') || $errors->has('password'))
                    nextStep();
                @endif
            });

            function updateDepartment(position) {
                const deptInput = document.getElementById('department');
                const mapping = {
                    'magasinier': 'Stocks',
                    'caissier': 'Caisse',
                    'livreur': 'Livraison'
                };
                
                if (mapping[position]) {
                    deptInput.value = mapping[position];
                }
            }

            function nextStep() {
                document.getElementById('step-1').style.display = 'none';
                document.getElementById('step-2').style.display = 'block';

                document.getElementById('indicator-1').classList.remove('active');
                document.getElementById('indicator-1').classList.add('completed');
                document.getElementById('step-line-1').classList.add('active');
                document.getElementById('indicator-2').classList.add('active');
                
                // On remonte en haut du formulaire
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }

            function prevStep() {
                document.getElementById('step-2').style.display = 'none';
                document.getElementById('step-1').style.display = 'block';

                document.getElementById('indicator-2').classList.remove('active');
                document.getElementById('step-line-1').classList.remove('active');
                document.getElementById('indicator-1').classList.remove('completed');
                document.getElementById('indicator-1').classList.add('active');

                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        </script>
    @endpush

@endsection

@extends('admin.layouts.app')

@section('title', 'Nouveau fournisseur')
@section('page-title', 'Nouveau fournisseur')

@section('content')

    <div class="create-wrap">

        {{-- Barre de progression --}}
        <div class="stepper-modern">
            <div class="step active" id="step1-indicator">
                <div class="step-icon"><i class="fa-solid fa-building"></i></div>
                <div class="step-label">Identité</div>
            </div>
            <div class="step-line"></div>
            <div class="step" id="step2-indicator">
                <div class="step-icon"><i class="fa-solid fa-location-dot"></i></div>
                <div class="step-label">Coordonnées</div>
            </div>
        </div>

        <div class="create-header-simple">
            <h2 id="step-title">Informations du fournisseur</h2>
            <p id="step-subtitle">Étape 1 sur 2 : Identité et contact</p>
        </div>

        <form method="POST" action="{{ route('admin.suppliers.store') }}" id="supplier-form" autocomplete="off">
            @csrf

            {{-- Étape 1 : Identité --}}
            <div class="form-step active" id="step1">
                <div class="form-card">
                    <div class="form-group">
                        <label for="name">Nom de l'entreprise <span class="required">*</span></label>
                        <div class="input-modern-wrap">
                            <i class="fa-solid fa-building"></i>
                            <input type="text" id="name" name="name" value="{{ old('name') }}"
                                placeholder="Ex: Global Logistics SARL" autofocus>
                        </div>
                        @error('name')
                            <p class="error-msg-modern"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="contact_person">Personne de contact</label>
                            <div class="input-modern-wrap">
                                <i class="fa-solid fa-user-tie"></i>
                                <input type="text" id="contact_person" name="contact_person"
                                    value="{{ old('contact_person') }}" placeholder="Nom du responsable">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="phone">Numéro de téléphone</label>
                            <div class="input-modern-wrap">
                                <i class="fa-solid fa-phone"></i>
                                <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
                                    placeholder="+225 01 02 03 04 05">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">Adresse email professionnelle</label>
                        <div class="input-modern-wrap">
                            <i class="fa-solid fa-envelope"></i>
                            <input type="email" id="email" name="email" value="{{ old('email') }}"
                                placeholder="contact@entreprise.com">
                        </div>
                    </div>
                </div>

                <div class="form-actions-modern">
                    <a href="{{ route('admin.suppliers.index') }}" class="btn-modern btn-secondary">
                        Annuler
                    </a>
                    <button type="button" class="btn-modern btn-primary" onclick="nextStep()">
                        Continuer <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </div>
            </div>

            {{-- Étape 2 : Localisation & Notes --}}
            <div class="form-step" id="step2">
                <div class="form-card">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="city">Ville</label>
                            <div class="input-modern-wrap">
                                <i class="fa-solid fa-city"></i>
                                <input type="text" id="city" name="city" value="{{ old('city') }}"
                                    placeholder="Abidjan, San-Pedro...">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="website">Site internet</label>
                            <div class="input-modern-wrap">
                                <i class="fa-solid fa-globe"></i>
                                <input type="url" id="website" name="website" value="{{ old('website') }}"
                                    placeholder="https://www.exemple.com">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="address">Adresse géographique</label>
                        <div class="input-modern-wrap">
                            <i class="fa-solid fa-map-location-dot"></i>
                            <input type="text" id="address" name="address" value="{{ old('address') }}"
                                placeholder="Quartier, Rue, Porte...">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="notes">Notes & Observations (facultatif)</label>
                        <div class="input-modern-wrap textarea-wrap">
                            <i class="fa-solid fa-file-pen"></i>
                            <textarea id="notes" name="notes" rows="4" placeholder="Conditions de paiement, délais habituels, etc.">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="form-actions-modern">
                    <button type="button" class="btn-modern btn-secondary" onclick="prevStep()">
                        <i class="fa-solid fa-arrow-left"></i> Précédent
                    </button>
                    <button type="submit" class="btn-modern btn-primary">
                        <i class="fa-solid fa-truck"></i> Enregistrer le fournisseur
                    </button>
                </div>
            </div>
        </form>
    </div>

    @push('styles')
        <style>
            .create-wrap {
                max-width: 750px;
                margin: 0 auto;
                animation: fadeInUp 0.5s ease-out;
            }

            @keyframes fadeInUp {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }

            /* Stepper */
            .stepper-modern {
                display: flex;
                align-items: center;
                justify-content: center;
                margin-bottom: 40px;
                gap: 15px;
            }

            .step {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 8px;
                opacity: 0.4;
                transition: all 0.3s;
            }

            .step.active {
                opacity: 1;
            }

            .step-icon {
                width: 45px;
                height: 45px;
                background: #fff;
                border: 2px solid #e2eaf3;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 18px;
                color: #7a94aa;
                transition: all 0.3s;
            }

            .step.active .step-icon {
                background: #004d99;
                color: #fff;
                border-color: #004d99;
                box-shadow: 0 5px 15px rgba(0, 77, 153, 0.2);
            }

            .step-label {
                font-size: 12px;
                font-weight: 700;
                color: #4a5568;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .step-line {
                width: 60px;
                height: 2px;
                background: #e2eaf3;
                margin-top: -20px;
            }

            /* Header */
            .create-header-simple {
                text-align: center;
                margin-bottom: 30px;
            }

            .create-header-simple h2 {
                font-size: 22px;
                font-weight: 800;
                color: #1a2840;
                margin: 0 0 5px;
            }

            .create-header-simple p {
                font-size: 14px;
                color: #7a94aa;
            }

            /* Form Step */
            .form-step {
                display: none;
            }

            .form-step.active {
                display: block;
                animation: fadeInRight 0.4s ease-out;
            }

            @keyframes fadeInRight {
                from { opacity: 0; transform: translateX(10px); }
                to { opacity: 1; transform: translateX(0); }
            }

            .form-card {
                background: #fff;
                border-radius: 24px;
                padding: 35px;
                border: 1px solid #eef2f6;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            }

            .form-group {
                margin-bottom: 24px;
            }

            .form-group label {
                display: block;
                font-size: 13.5px;
                font-weight: 700;
                color: #4a5568;
                margin-bottom: 10px;
            }

            .input-modern-wrap {
                position: relative;
                display: flex;
                align-items: center;
                background: #ffffff;
                border: 1.5px solid #e2eaf3;
                border-radius: 14px;
                padding: 0 18px;
                transition: all 0.2s;
            }

            .input-modern-wrap:hover {
                border-color: #cbd5e1;
            }

            .input-modern-wrap i {
                font-size: 16px;
                color: #94a3b8;
                margin-right: 14px;
            }

            .input-modern-wrap input,
            .input-modern-wrap textarea {
                flex: 1;
                border: none;
                background: transparent;
                padding: 15px 0;
                font-size: 14.5px;
                font-weight: 500;
                color: #1a2840;
                outline: none;
            }

            .input-modern-wrap:focus-within {
                border-color: #004d99;
                box-shadow: 0 0 0 4px rgba(0, 77, 153, 0.08);
                transform: translateY(-1px);
            }

            .textarea-wrap {
                align-items: flex-start;
            }

            .textarea-wrap i {
                margin-top: 18px;
            }

            .form-row {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 24px;
            }

            /* Error */
            .error-msg-modern {
                font-size: 12px;
                color: #e11d48;
                font-weight: 600;
                margin-top: 8px;
                display: flex;
                align-items: center;
                gap: 5px;
            }

            /* Actions */
            .form-actions-modern {
                display: flex;
                justify-content: space-between;
                margin-top: 32px;
                gap: 16px;
            }

            .btn-modern {
                flex: 1;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 10px;
                padding: 16px;
                border-radius: 18px;
                font-size: 15px;
                font-weight: 700;
                text-decoration: none;
                transition: all 0.3s;
                cursor: pointer;
                border: none;
            }

            .btn-primary {
                background: #ffc300;
                color: #004d99;
                box-shadow: 0 6px 20px rgba(255, 195, 0, 0.3);
            }

            .btn-primary:hover {
                background: #eebb00;
                transform: translateY(-3px);
            }

            .btn-secondary {
                background: #fff;
                color: #64748b;
                border: 2px solid #e2e8f0;
            }

            .btn-secondary:hover {
                background: #f8fafc;
                border-color: #cbd5e1;
            }

            .required { color: #e11d48; }

            @media (max-width: 600px) {
                .form-row { grid-template-columns: 1fr; }
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            function nextStep() {
                const name = document.getElementById('name').value;
                if (!name) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oups...',
                        text: 'Le nom de l\'entreprise est obligatoire !',
                        confirmButtonColor: '#004d99'
                    });
                    return;
                }

                document.getElementById('step1').classList.remove('active');
                document.getElementById('step2').classList.add('active');
                
                document.getElementById('step1-indicator').classList.remove('active');
                document.getElementById('step2-indicator').classList.add('active');
                
                document.getElementById('step-title').textContent = 'Localisation & Coordonnées';
                document.getElementById('step-subtitle').textContent = 'Étape 2 sur 2 : Où se situe le fournisseur ?';
                
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }

            function prevStep() {
                document.getElementById('step2').classList.remove('active');
                document.getElementById('step1').classList.add('active');
                
                document.getElementById('step2-indicator').classList.remove('active');
                document.getElementById('step1-indicator').classList.add('active');
                
                document.getElementById('step-title').textContent = 'Informations du fournisseur';
                document.getElementById('step-subtitle').textContent = 'Étape 1 sur 2 : Identité et contact';
                
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        </script>
    @endpush

@endsection

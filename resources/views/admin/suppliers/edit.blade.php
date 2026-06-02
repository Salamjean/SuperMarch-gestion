@extends('admin.layouts.app')

@section('title', 'Modifier le fournisseur')
@section('page-title', 'Modifier le fournisseur')

@section('content')

    <div class="create-wrap">

        <div class="create-header">
            <div class="create-header-icon" style="background:#004d99;">
                <i class="fa-solid fa-pen-to-square"></i>
            </div>
            <div>
                <h2 class="create-header-title">Modifier : {{ $supplier->name }}</h2>
                <p class="create-header-sub">Mettez à jour les informations du fournisseur</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.suppliers.update', $supplier) }}" autocomplete="off">
            @csrf @method('PUT')

            {{-- Section 1 : Identité --}}
            <div class="form-section">
                <div class="form-section-title">
                    <i class="fa-solid fa-building"></i> Identité
                </div>
                <div class="form-grid-2">

                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label for="name">Nom du fournisseur <span class="required">*</span></label>
                        <div class="input-wrap">
                            <i class="fa-solid fa-building"></i>
                            <input type="text" id="name" name="name" value="{{ old('name', $supplier->name) }}"
                                autofocus>
                        </div>
                        @error('name')
                            <p class="error-msg">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="contact_person">Personne de contact</label>
                        <div class="input-wrap">
                            <i class="fa-solid fa-user"></i>
                            <input type="text" id="contact_person" name="contact_person"
                                value="{{ old('contact_person', $supplier->contact_person) }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="phone">Téléphone</label>
                        <div class="input-wrap">
                            <i class="fa-solid fa-phone"></i>
                            <input type="text" id="phone" name="phone"
                                value="{{ old('phone', $supplier->phone) }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">Email professionnel</label>
                        <div class="input-wrap">
                            <i class="fa-solid fa-envelope"></i>
                            <input type="email" id="email" name="email"
                                value="{{ old('email', $supplier->email) }}">
                        </div>
                        @error('email')
                            <p class="error-msg">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
            </div>

            {{-- Section 2 : Localisation --}}
            <div class="form-section">
                <div class="form-section-title">
                    <i class="fa-solid fa-location-dot"></i> Localisation
                </div>
                <div class="form-grid-2">

                    <div class="form-group">
                        <label for="address">Adresse</label>
                        <div class="input-wrap">
                            <i class="fa-solid fa-map-marker-alt"></i>
                            <input type="text" id="address" name="address"
                                value="{{ old('address', $supplier->address) }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="city">Ville</label>
                        <div class="input-wrap">
                            <i class="fa-solid fa-city"></i>
                            <input type="text" id="city" name="city" value="{{ old('city', $supplier->city) }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="website">Site web</label>
                        <div class="input-wrap">
                            <i class="fa-solid fa-globe"></i>
                            <input type="url" id="website" name="website"
                                value="{{ old('website', $supplier->website) }}" placeholder="https://…">
                        </div>
                        @error('website')
                            <p class="error-msg">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Statut</label>
                        <label class="toggle-wrap">
                            <input type="checkbox" name="is_active" value="1"
                                {{ old('is_active', $supplier->is_active) ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                            <span class="toggle-label">Fournisseur actif</span>
                        </label>
                    </div>

                </div>
            </div>

            {{-- Section 3 : Notes --}}
            <div class="form-section">
                <div class="form-section-title">
                    <i class="fa-solid fa-file-lines"></i> Notes internes
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <div class="input-wrap input-wrap-textarea">
                        <i class="fa-solid fa-align-left"></i>
                        <textarea name="notes" rows="3">{{ old('notes', $supplier->notes) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('admin.suppliers.index') }}" class="btn btn-cancel">
                    <i class="fa-solid fa-xmark"></i> Annuler
                </a>
                <button type="submit" class="btn btn-yellow">
                    <i class="fa-solid fa-check"></i> Mettre à jour
                </button>
            </div>
        </form>
    </div>

    @push('styles')
        <style>
            .create-wrap {
                max-width: 780px;
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
            }

            .btn-cancel:hover {
                background: #f5f9ff;
            }

            .toggle-wrap {
                display: flex;
                align-items: center;
                gap: 10px;
                cursor: pointer;
                margin-top: 6px;
            }

            .toggle-wrap input {
                display: none;
            }

            .toggle-slider {
                width: 42px;
                height: 24px;
                background: #d0dce8;
                border-radius: 999px;
                position: relative;
                transition: background .2s;
                flex-shrink: 0;
            }

            .toggle-slider::after {
                content: '';
                position: absolute;
                width: 18px;
                height: 18px;
                background: #fff;
                border-radius: 50%;
                top: 3px;
                left: 3px;
                transition: transform .2s;
                box-shadow: 0 1px 4px rgba(0, 0, 0, .2);
            }

            .toggle-wrap input:checked+.toggle-slider {
                background: #004d99;
            }

            .toggle-wrap input:checked+.toggle-slider::after {
                transform: translateX(18px);
            }

            .toggle-label {
                font-size: 13.5px;
                color: #4a6580;
                font-weight: 500;
            }

            .input-wrap-textarea {
                align-items: flex-start;
                padding-top: 10px;
            }

            .input-wrap-textarea i {
                margin-top: 2px;
            }

            .input-wrap textarea {
                flex: 1;
                border: none;
                outline: none;
                font-family: inherit;
                font-size: 14px;
                color: #1a2e44;
                background: transparent;
                resize: vertical;
                padding: 0;
            }
        </style>
    @endpush

@endsection

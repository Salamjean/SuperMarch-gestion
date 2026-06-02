@extends('admin.layouts.app')

@section('title', 'Nouvelle categorie')
@section('page-title', 'Nouvelle categorie')

@section('content')
    <div class="cat-create-shell">
        <section class="cat-create-hero">
            <div class="cat-hero-left">
                <p class="cat-kicker">ADMIN / CATEGORIES</p>
                <h2>Creer une categorie avec une identite visuelle claire</h2>
                <p>Donnez un nom, une couleur et un statut pour organiser les produits proprement dans toute l'application.
                </p>
            </div>
            <div class="cat-hero-preview" id="heroPreview">
                <span class="cat-chip" id="previewChip">
                    <i class="fa-solid fa-tag"></i>
                    <span id="previewName">Nouvelle categorie</span>
                </span>
                <small id="previewState">Statut: Actif</small>
            </div>
        </section>

        <form method="POST" action="{{ route('admin.categories.store') }}" class="cat-form-grid" autocomplete="off">
            @csrf

            <article class="cat-form-panel">
                <header class="cat-panel-head">
                    <h3><i class="fa-solid fa-pen-ruler"></i> Informations principales</h3>
                </header>

                <div class="cat-field">
                    <label for="name">Nom de la categorie <span>*</span></label>
                    <div class="cat-input-wrap {{ $errors->has('name') ? 'is-invalid' : '' }}">
                        <i class="fa-solid fa-layer-group"></i>
                        <input type="text" id="name" name="name" value="{{ old('name') }}"
                            placeholder="Ex: Boissons fraiches" autofocus required maxlength="100">
                    </div>
                    @error('name')
                        <p class="cat-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                    @enderror
                </div>

                <div class="cat-field">
                    <label for="description">Description</label>
                    <div class="cat-input-wrap cat-textarea-wrap {{ $errors->has('description') ? 'is-invalid' : '' }}">
                        <i class="fa-solid fa-align-left"></i>
                        <textarea id="description" name="description" rows="5" maxlength="500"
                            placeholder="Description de la categorie (facultatif)">{{ old('description') }}</textarea>
                    </div>
                    @error('description')
                        <p class="cat-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                    @enderror
                </div>
            </article>

            <article class="cat-form-panel">
                <header class="cat-panel-head">
                    <h3><i class="fa-solid fa-palette"></i> Identite & publication</h3>
                </header>

                <div class="cat-field">
                    <label for="color">Couleur de la categorie</label>
                    <div class="cat-color-wrap {{ $errors->has('color') ? 'is-invalid' : '' }}">
                        <input type="color" id="color" name="color" value="{{ old('color', '#004d99') }}">
                        <div>
                            <p class="cat-color-code" id="colorCode">{{ strtoupper(old('color', '#004d99')) }}</p>
                            <small>Utilisee dans les badges et listes</small>
                        </div>
                    </div>
                    @error('color')
                        <p class="cat-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                    @enderror
                </div>

                <div class="cat-field">
                    <label>Statut</label>
                    <label class="cat-toggle">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" id="is_active" name="is_active" value="1"
                            {{ old('is_active', true) ? 'checked' : '' }}>
                        <span class="cat-toggle-ui"></span>
                        <span class="cat-toggle-label" id="toggleLabel">Categorie active</span>
                    </label>
                </div>

                <div class="cat-actions">
                    <a href="{{ route('admin.categories.index') }}" class="cat-btn cat-btn-muted">
                        <i class="fa-solid fa-arrow-left"></i> Annuler
                    </a>
                    <button type="submit" class="cat-btn cat-btn-primary">
                        <i class="fa-solid fa-floppy-disk"></i> Creer la categorie
                    </button>
                </div>
            </article>
        </form>
    </div>
@endsection

@push('styles')
    <style>
        .cat-create-shell {
            display: grid;
            gap: 20px;
            max-width: 1100px;
            margin: 0 auto;
        }

        .cat-create-hero {
            background: radial-gradient(circle at 20% 20%, #0066cc 0, #004d99 40%, #003d7a 100%);
            border-radius: 22px;
            padding: 24px;
            color: #fff;
            display: grid;
            grid-template-columns: 1.4fr 1fr;
            gap: 16px;
            box-shadow: 0 16px 30px rgba(0, 77, 153, .22);
        }

        .cat-kicker {
            font-size: 11px;
            letter-spacing: .12em;
            font-weight: 700;
            opacity: .85;
            margin: 0 0 8px;
        }

        .cat-hero-left h2 {
            margin: 0;
            font-size: 27px;
            line-height: 1.15;
        }

        .cat-hero-left p {
            margin: 10px 0 0;
            opacity: .9;
            font-size: 14px;
            max-width: 52ch;
        }

        .cat-hero-preview {
            background: rgba(255, 255, 255, .12);
            border: 1px solid rgba(255, 255, 255, .2);
            border-radius: 14px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            gap: 10px;
            padding: 18px;
        }

        .cat-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 999px;
            background: #004d99;
            color: #fff;
            font-weight: 700;
            border: 2px solid rgba(255, 255, 255, .25);
        }

        .cat-form-grid {
            display: grid;
            grid-template-columns: 1.2fr .8fr;
            gap: 18px;
        }

        .cat-form-panel {
            background: #fff;
            border: 1px solid #e2eaf3;
            border-radius: 18px;
            padding: 22px;
        }

        .cat-panel-head h3 {
            margin: 0 0 16px;
            font-size: 16px;
            color: #1a2840;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .cat-field {
            margin-bottom: 16px;
        }

        .cat-field label {
            display: block;
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: 700;
            color: #425466;
        }

        .cat-field label span {
            color: #e11d48;
        }

        .cat-input-wrap {
            border: 1.5px solid #dfe8f3;
            background: #fff;
            border-radius: 12px;
            padding: 0 14px;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: .2s;
        }

        .cat-input-wrap i {
            color: #7a94aa;
            font-size: 14px;
        }

        .cat-input-wrap input,
        .cat-input-wrap textarea {
            width: 100%;
            border: none;
            outline: none;
            background: transparent;
            padding: 13px 0;
            font-size: 14px;
            color: #1a2840;
            font-family: inherit;
        }

        .cat-input-wrap:focus-within {
            border-color: #004d99;
            box-shadow: 0 0 0 4px rgba(0, 77, 153, .1);
        }

        .cat-input-wrap.is-invalid,
        .cat-color-wrap.is-invalid {
            border-color: #e11d48;
            box-shadow: 0 0 0 3px rgba(225, 29, 72, .08);
        }

        .cat-textarea-wrap {
            align-items: flex-start;
        }

        .cat-textarea-wrap i {
            margin-top: 14px;
        }

        .cat-color-wrap {
            display: flex;
            align-items: center;
            gap: 12px;
            border: 1px solid #e2eaf3;
            background: #f8fbff;
            border-radius: 12px;
            padding: 10px;
        }

        .cat-color-wrap input[type="color"] {
            width: 48px;
            height: 48px;
            border: none;
            padding: 0;
            background: transparent;
            cursor: pointer;
        }

        .cat-color-code {
            margin: 0;
            font-weight: 800;
            letter-spacing: .06em;
            color: #1a2840;
            font-size: 13px;
        }

        .cat-color-wrap small {
            color: #7a94aa;
        }

        .cat-toggle {
            display: flex;
            align-items: center;
            gap: 10px;
            user-select: none;
            cursor: pointer;
        }

        .cat-toggle input {
            display: none;
        }

        .cat-toggle-ui {
            width: 46px;
            height: 26px;
            border-radius: 999px;
            background: #cbd5e1;
            position: relative;
            transition: .2s;
        }

        .cat-toggle-ui::after {
            content: '';
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #fff;
            position: absolute;
            top: 3px;
            left: 3px;
            transition: .2s;
            box-shadow: 0 2px 5px rgba(0, 0, 0, .2);
        }

        .cat-toggle input:checked+.cat-toggle-ui {
            background: #059669;
        }

        .cat-toggle input:checked+.cat-toggle-ui::after {
            transform: translateX(20px);
        }

        .cat-toggle-label {
            font-size: 13px;
            font-weight: 600;
            color: #1f2937;
        }

        .cat-actions {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin-top: 20px;
        }

        .cat-btn {
            border: none;
            border-radius: 11px;
            padding: 11px 14px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .cat-btn-primary {
            background: #ffc300;
            color: #003d7a;
            border: 1px solid #e6b000;
        }

        .cat-btn-primary:hover {
            background: #e6b000;
        }

        .cat-btn-muted {
            background: #fff;
            color: #004d99;
            border: 1px solid #d6e1ef;
        }

        .cat-btn-muted:hover {
            background: #f5f9ff;
        }

        .cat-error {
            margin: 6px 0 0;
            color: #e11d48;
            font-size: 12px;
            font-weight: 700;
            display: flex;
            gap: 5px;
            align-items: center;
        }

        @media (max-width: 980px) {

            .cat-create-hero,
            .cat-form-grid {
                grid-template-columns: 1fr;
            }

            .cat-actions {
                flex-direction: column;
            }

            .cat-btn {
                justify-content: center;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        (function() {
            const nameInput = document.getElementById('name');
            const colorInput = document.getElementById('color');
            const activeInput = document.getElementById('is_active');
            const previewName = document.getElementById('previewName');
            const previewChip = document.getElementById('previewChip');
            const previewState = document.getElementById('previewState');
            const colorCode = document.getElementById('colorCode');
            const toggleLabel = document.getElementById('toggleLabel');

            const syncPreview = () => {
                const label = (nameInput.value || 'Nouvelle categorie').trim();
                const color = colorInput.value || '#004d99';
                previewName.textContent = label;
                previewChip.style.background = color;
                colorCode.textContent = color.toUpperCase();
                previewState.textContent = activeInput.checked ? 'Statut: Actif' : 'Statut: Inactif';
                toggleLabel.textContent = activeInput.checked ? 'Categorie active' : 'Categorie inactive';
            };

            nameInput.addEventListener('input', syncPreview);
            colorInput.addEventListener('input', syncPreview);
            activeInput.addEventListener('change', syncPreview);
            syncPreview();
        })();
    </script>
@endpush

@extends('admin.layouts.app')

@section('title', 'Paramètres Boutique')
@section('page-title', 'Configuration')

@section('content')

    <!-- En-tête Moderne -->
    <div style="margin-bottom: 25px;">
        <h2 class="list-title" style="font-size: 24px; font-weight: 800; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-store" style="color: #6366f1;"></i> Configuration de la Boutique
        </h2>
        <p class="list-sub" style="font-size: 13.5px; color: var(--text-muted); margin: 4px 0 0 0;">
            Gérez l'identité de votre commerce et personnalisez l'en-tête et le pied de page de vos factures et tickets.
        </p>
    </div>

    @if(session('success'))
        <div style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; padding: 16px; border-radius: 12px; margin-bottom: 25px; font-size: 14.5px; font-weight: 600; display: flex; align-items: center; gap: 10px; box-shadow: 0 4px 6px -1px rgba(6, 95, 70, 0.05);">
            <i class="fa-solid fa-circle-check" style="font-size: 18px; color: #059669;"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="settings-layout">
        <!-- Colonne Gauche : Formulaire de Saisie -->
        <div class="settings-card">
            <div class="card-header-custom">
                <i class="fa-solid fa-pen-to-square"></i> Identité & Coordonnées
            </div>
            
            <form method="POST" action="{{ route('admin.settings.update') }}" autocomplete="off" style="display: flex; flex-direction: column; gap: 20px; padding: 25px;">
                @csrf

                <div class="input-field-group">
                    <label for="store_name">Nom du Supermarché <span class="req-star">*</span></label>
                    <div class="custom-input-wrap">
                        <i class="fa-solid fa-shop"></i>
                        <input type="text" id="store_name" name="store_name" value="{{ old('store_name', $settings->store_name) }}" required placeholder="Ex: SUPERMARCHÉ PRO" oninput="updateLivePreview()">
                    </div>
                    @error('store_name')
                        <p class="field-error-msg"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                    @enderror
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="input-field-group">
                        <label for="phone">Numéro de Téléphone <span class="req-star">*</span></label>
                        <div class="custom-input-wrap">
                            <i class="fa-solid fa-phone"></i>
                            <input type="text" id="phone" name="phone" value="{{ old('phone', $settings->phone) }}" required placeholder="Ex: +225 07 00 00 00 00" oninput="updateLivePreview()">
                        </div>
                        @error('phone')
                            <p class="field-error-msg"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>

                    <div class="input-field-group">
                        <label for="email">Adresse E-mail (Facultatif)</label>
                        <div class="custom-input-wrap">
                            <i class="fa-solid fa-envelope"></i>
                            <input type="email" id="email" name="email" value="{{ old('email', $settings->email) }}" placeholder="Ex: contact@boutique.com" oninput="updateLivePreview()">
                        </div>
                        @error('email')
                            <p class="field-error-msg"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="input-field-group">
                    <label for="address">Adresse Physique <span class="req-star">*</span></label>
                    <div class="custom-input-wrap">
                        <i class="fa-solid fa-location-dot"></i>
                        <input type="text" id="address" name="address" value="{{ old('address', $settings->address) }}" required placeholder="Ex: Abidjan, Cocody Riviera Palmeraie" oninput="updateLivePreview()">
                    </div>
                    @error('address')
                        <p class="field-error-msg"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                    @enderror
                </div>

                <div class="input-field-group">
                    <label for="invoice_format">Format de reçu / facture par défaut <span class="req-star">*</span></label>
                    <div class="custom-input-wrap">
                        <i class="fa-solid fa-file-invoice"></i>
                        <select id="invoice_format" name="invoice_format" required onchange="handleFormatChange(this.value)" style="width: 100%; border: none; outline: none; padding: 12px 0; font-size: 14px; color: #1e293b; background: transparent; font-family: inherit; cursor: pointer;">
                            <option value="ticket" {{ old('invoice_format', $settings->invoice_format) === 'ticket' ? 'selected' : '' }}>Format Ticket (80mm)</option>
                            <option value="a4" {{ old('invoice_format', $settings->invoice_format) === 'a4' ? 'selected' : '' }}>Format Facture (A4)</option>
                        </select>
                    </div>
                    <span class="field-helper-text">Détermine la disposition par défaut à l'écran et à l'impression (Caisse et Administration).</span>
                    @error('invoice_format')
                        <p class="field-error-msg"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                    @enderror
                </div>

                <div class="input-field-group">
                    <label for="invoice_footer">Pied de page des Factures / Tickets</label>
                    <div class="custom-input-wrap textarea-wrap">
                        <i class="fa-solid fa-message" style="margin-top: 12px;"></i>
                        <textarea id="invoice_footer" name="invoice_footer" rows="4" placeholder="Ex: Merci pour votre confiance ! Les marchandises vendues ne sont ni reprises ni échangées." oninput="updateLivePreview()">{{ old('invoice_footer', $settings->invoice_footer) }}</textarea>
                    </div>
                    <span class="field-helper-text">Ce message légal apparaîtra en bas des factures PDF et des tickets de caisse.</span>
                    @error('invoice_footer')
                        <p class="field-error-msg"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                    @enderror
                </div>

                <div style="margin-top: 10px; border-top: 1px solid #f1f5f9; padding-top: 20px;">
                    <button type="submit" class="save-settings-btn">
                        <i class="fa-solid fa-floppy-disk"></i> Enregistrer les Paramètres
                    </button>
                </div>
            </form>
        </div>

        <!-- Colonne Droite : Prévisualisation Directe -->
        <div class="preview-panel">
            <div class="preview-tabs">
                <button type="button" class="preview-tab-btn active" id="btn-tab-ticket" onclick="switchPreviewTab('ticket')">
                    <i class="fa-solid fa-receipt"></i> Format Ticket (Caisse)
                </button>
                <button type="button" class="preview-tab-btn" id="btn-tab-facture" onclick="switchPreviewTab('facture')">
                    <i class="fa-solid fa-file-invoice"></i> Format Facture (A4)
                </button>
            </div>
 
            <!-- Contenu 1 : Ticket de Caisse -->
            <div id="preview-ticket-container" class="preview-content-box active">
                <div class="thermal-receipt">
                    <div class="receipt-header">
                        <h4 id="preview-ticket-title" class="receipt-store-title">SUPERMARCHÉ PRO</h4>
                        <p id="preview-ticket-coords" class="receipt-store-coords">
                            Abidjan, Cocody Riviera Palmeraie<br>
                            Tel: +225 07 00 00 00 00<br>
                            Email: contact@boutique.com
                        </p>
                        <div class="dotted-divider"></div>
                        <p class="receipt-meta">
                            REF: #SAL-2026-0001<br>
                            Date: 04/06/2026 10:24<br>
                            Caissier: Caissier Demo
                        </p>
                        <div class="dotted-divider"></div>
                    </div>
 
                    <table class="receipt-table">
                        <thead>
                            <tr>
                                <th>Article</th>
                                <th style="text-align: center;">Qté</th>
                                <th style="text-align: right;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Huile Dinor 1.5L</td>
                                <td style="text-align: center;">2</td>
                                <td style="text-align: right;">3 600 FCFA</td>
                            </tr>
                            <tr>
                                <td>Sucre Roux 1kg</td>
                                <td style="text-align: center;">3</td>
                                <td style="text-align: right;">2 850 FCFA</td>
                            </tr>
                        </tbody>
                    </table>
 
                    <div class="dotted-divider" style="margin-top: 15px;"></div>
                     
                    <div class="receipt-totals">
                        <div style="display: flex; justify-content: space-between; font-weight: bold; font-size: 13px; margin-bottom: 5px;">
                            <span>TOTAL</span>
                            <span>6 450 FCFA</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 11px;">
                            <span>Paiement:</span>
                            <span>Espèces</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 11px;">
                            <span>Reçu:</span>
                            <span>7 000 FCFA</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 11px;">
                            <span>Rendu:</span>
                            <span>550 FCFA</span>
                        </div>
                    </div>
 
                    <div class="dotted-divider"></div>
 
                    <div class="receipt-footer">
                        <div style="display: flex; justify-content: center; margin-bottom: 12px;">
                            <i class="fa-solid fa-qrcode" style="font-size: 65px; color: #000;"></i>
                        </div>
                        <p id="preview-ticket-footer">Merci pour votre confiance ! Les marchandises vendues ne sont ni reprises ni échangées.</p>
                        <p style="font-size: 9px; color: #94a3b8; margin-top: 10px;">Système de caisse intelligent</p>
                    </div>
                </div>
            </div>
 
            <!-- Contenu 2 : Facture A4 -->
            <div id="preview-facture-container" class="preview-content-box">
                <div class="a4-invoice-mock">
                    <div class="invoice-mock-header">
                        <div>
                            <h3 id="preview-facture-title" style="margin: 0; color: #004d99; font-size: 20px; font-weight: 800; text-transform: uppercase; letter-spacing: -0.5px;">SUPERMARCHÉ PRO</h3>
                            <p id="preview-facture-coords" style="margin: 5px 0 0 0; font-size: 11px; color: #64748b; line-height: 1.5;">
                                Abidjan, Cocody Riviera Palmeraie<br>
                                Tel: +225 07 00 00 00 00 / Email: contact@boutique.com
                            </p>
                        </div>
                        <div style="text-align: right;">
                            <h4 style="margin: 0; color: #004d99; font-size: 18px; font-weight: 800;">FACTURE</h4>
                            <p style="margin: 5px 0 0 0; font-size: 11px; color: #64748b; line-height: 1.4;">
                                Réf: <strong>#SAL-2026-0001</strong><br>Date: 04/06/2026 10:24
                            </p>
                        </div>
                    </div>
 
                    <div style="border-bottom: 2px solid #f1f5f9; margin: 20px 0;"></div>
 
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; font-size: 11px; margin-bottom: 20px; line-height: 1.4;">
                        <div>
                            <span style="color: #94a3b8; text-transform: uppercase; font-size: 9px; font-weight: bold; display: block; margin-bottom: 4px; letter-spacing: 0.5px;">Facturé à :</span>
                            <strong>Koffi Kouamé</strong><br>
                            Tel: +225 01 02 03 04
                        </div>
                        <div style="text-align: right;">
                            <span style="color: #94a3b8; text-transform: uppercase; font-size: 9px; font-weight: bold; display: block; margin-bottom: 4px; letter-spacing: 0.5px;">Opérateur :</span>
                            <strong>Caissier Demo</strong><br>
                            <span style="color: #64748b;">Mode de règlement : <strong style="color: #059669;">Espèces</strong></span><br>
                            <span style="color: #64748b;">Statut : <span style="font-weight: bold; color: #059669;">Validée / Payée</span></span>
                        </div>
                    </div>
 
                    <table class="invoice-mock-table">
                        <thead>
                            <tr>
                                <th>Désignation de l'article</th>
                                <th style="text-align: center; width: 80px;">P.U.</th>
                                <th style="text-align: center; width: 60px;">Qté</th>
                                <th style="text-align: right; width: 100px;">Montant Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Riz Parfumé 25kg</strong></td>
                                <td style="text-align: center;">18 500</td>
                                <td style="text-align: center;">1</td>
                                <td style="text-align: right; font-weight: 700;">18 500 FCFA</td>
                            </tr>
                            <tr>
                                <td><strong>Carton de Pâtes Spaghettis</strong></td>
                                <td style="text-align: center;">6 200</td>
                                <td style="text-align: center;">2</td>
                                <td style="text-align: right; font-weight: 700;">12 400 FCFA</td>
                            </tr>
                        </tbody>
                    </table>
 
                    <div style="display: flex; justify-content: flex-end; margin-top: 20px; font-size: 12px;">
                        <div style="width: 250px;">
                            <div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #f1f5f9;">
                                <span>Total Global:</span>
                                <strong>30 900 FCFA</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #f1f5f9; color: #64748b;">
                                <span>Montant Encaissé:</span>
                                <span>35 000 FCFA</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #f1f5f9; color: #64748b;">
                                <span>Monnaie Rendue:</span>
                                <span>4 100 FCFA</span>
                            </div>
                        </div>
                    </div>
 
                    <div style="border-top: 1px dashed #e2e8f0; margin-top: 30px; padding-top: 15px; text-align: center;">
                        <p id="preview-facture-footer" style="margin: 0; font-size: 11px; color: #64748b; font-style: italic;">Merci pour votre confiance ! Les marchandises vendues ne sont ni reprises ni échangées.</p>
                        <p style="margin: 8px 0 0 0; font-size: 9px; color: #94a3b8;">Supermarché Pro - Solution de Facturation Intégrée</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            /* Layout Grid */
            .settings-layout {
                display: grid;
                grid-template-columns: 1.2fr 1.1fr;
                gap: 25px;
                max-width: 1200px;
                margin: 0 auto;
                align-items: start;
            }

            .settings-card {
                background: white;
                border: 1px solid var(--border, #e2e8f0);
                border-radius: 16px;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.02);
                overflow: hidden;
            }

            .card-header-custom {
                background: #f8fafc;
                border-bottom: 1px solid #e2e8f0;
                padding: 18px 25px;
                font-size: 15px;
                font-weight: 700;
                color: #1e293b;
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .card-header-custom i {
                color: #6366f1;
            }

            /* Custom Inputs */
            .input-field-group {
                display: flex;
                flex-direction: column;
                gap: 7px;
            }

            .input-field-group label {
                font-size: 13px;
                font-weight: 700;
                color: #475569;
            }

            .req-star {
                color: #e11d48;
            }

            .custom-input-wrap {
                border: 1.5px solid #e2e8f0;
                border-radius: 10px;
                padding: 0 14px;
                display: flex;
                align-items: center;
                gap: 10px;
                background: #fff;
                transition: all 0.2s ease-in-out;
            }

            .custom-input-wrap:focus-within {
                border-color: #6366f1;
                box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
            }

            .custom-input-wrap i {
                color: #94a3b8;
                font-size: 14px;
                width: 16px;
                text-align: center;
            }

            .custom-input-wrap input,
            .custom-input-wrap textarea {
                width: 100%;
                border: none;
                outline: none;
                padding: 12px 0;
                font-size: 14px;
                color: #1e293b;
                background: transparent;
                font-family: inherit;
            }

            .textarea-wrap {
                align-items: flex-start;
                padding: 4px 14px;
            }

            .textarea-wrap textarea {
                resize: none;
            }

            .field-helper-text {
                font-size: 11.5px;
                color: #64748b;
                margin-top: 1px;
            }

            .field-error-msg {
                font-size: 12px;
                color: #e11d48;
                font-weight: 600;
                margin: 4px 0 0 0;
                display: flex;
                align-items: center;
                gap: 5px;
            }

            .save-settings-btn {
                background: #6366f1;
                color: white;
                border: none;
                padding: 13px 22px;
                border-radius: 10px;
                font-size: 14px;
                font-weight: 700;
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                transition: all 0.2s;
                box-shadow: 0 4px 10px rgba(99, 102, 241, 0.2);
            }

            .save-settings-btn:hover {
                background: #4f46e5;
                transform: translateY(-1px);
                box-shadow: 0 6px 12px rgba(99, 102, 241, 0.3);
            }

            /* Preview Panel */
            .preview-panel {
                background: white;
                border: 1px solid var(--border, #e2e8f0);
                border-radius: 16px;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
                overflow: hidden;
                display: flex;
                flex-direction: column;
            }

            .preview-tabs {
                display: flex;
                background: #f8fafc;
                border-bottom: 1px solid #e2e8f0;
                padding: 8px 15px;
                gap: 8px;
            }

            .preview-tab-btn {
                background: transparent;
                border: none;
                padding: 8px 14px;
                font-size: 13px;
                font-weight: 700;
                color: #64748b;
                border-radius: 8px;
                cursor: pointer;
                display: flex;
                align-items: center;
                gap: 6px;
                transition: all 0.2s;
            }

            .preview-tab-btn:hover {
                color: #1e293b;
                background: #f1f5f9;
            }

            .preview-tab-btn.active {
                background: white;
                color: #6366f1;
                box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            }

            .preview-content-box {
                padding: 30px;
                background: #f1f5f9;
                display: none;
                justify-content: center;
                align-items: start;
                min-height: 480px;
            }

            .preview-content-box.active {
                display: flex;
            }

            /* Ticket Caisse Styling */
            .thermal-receipt {
                background: white;
                width: 100%;
                max-width: 290px;
                padding: 20px;
                box-shadow: 0 8px 16px rgba(0,0,0,0.04);
                border-radius: 2px;
                font-family: 'Courier New', Courier, monospace;
                color: #000;
                border-top: 4px solid #6366f1;
                position: relative;
            }

            .receipt-store-title {
                margin: 0;
                font-size: 16px;
                font-weight: bold;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .receipt-store-coords {
                margin: 5px 0 0 0;
                font-size: 11px;
                line-height: 1.4;
                color: #334155;
            }

            .dotted-divider {
                border-top: 1px dotted #000;
                margin: 12px 0;
            }

            .receipt-meta {
                margin: 0;
                font-size: 10.5px;
                line-height: 1.4;
                color: #475569;
            }

            .receipt-table {
                width: 100%;
                font-size: 11px;
                border-collapse: collapse;
                margin-top: 10px;
            }

            .receipt-table th {
                border-bottom: 1px solid #000;
                padding-bottom: 4px;
                font-weight: bold;
                text-align: left;
            }

            .receipt-table td {
                padding: 6px 0;
            }

            .receipt-totals {
                font-size: 11.5px;
            }

            .receipt-footer {
                text-align: center;
                font-size: 10px;
                margin-top: 15px;
                color: #334155;
                line-height: 1.4;
            }

            /* A4 Facture Mock */
            .a4-invoice-mock {
                background: white;
                width: 100%;
                padding: 25px;
                box-shadow: 0 8px 16px rgba(0,0,0,0.04);
                border-radius: 8px;
                font-family: inherit;
                border-top: 4px solid #1e3a8a;
            }

            .invoice-mock-header {
                display: flex;
                justify-content: space-between;
                align-items: start;
            }

            .invoice-mock-table {
                width: 100%;
                border-collapse: collapse;
                font-size: 9.5px;
                margin-top: 10px;
            }

            .invoice-mock-table th {
                background: #f8fafc;
                border-bottom: 1px solid #cbd5e1;
                padding: 6px;
                font-weight: 800;
                color: #475569;
                text-align: left;
            }

            .invoice-mock-table td {
                padding: 8px 6px;
                border-bottom: 1px solid #f1f5f9;
            }

            @media (max-width: 1024px) {
                .settings-layout {
                    grid-template-columns: 1fr;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            // Live Preview Sync
            function updateLivePreview() {
                const storeNameVal = document.getElementById('store_name').value.trim() || 'SUPERMARCHÉ PRO';
                const phoneVal = document.getElementById('phone').value.trim() || '+225 07 00 00 00 00';
                const emailVal = document.getElementById('email').value.trim();
                const addressVal = document.getElementById('address').value.trim() || 'Abidjan, Cocody Riviera Palmeraie';
                const footerVal = document.getElementById('invoice_footer').value.trim() || 'Merci pour votre confiance !';

                // Format Coords (HTML)
                let ticketCoords = addressVal + '<br>Tel: ' + phoneVal;
                let A4Coords = addressVal + '<br>Tel: ' + phoneVal;
                
                if (emailVal) {
                    ticketCoords += '<br>Email: ' + emailVal;
                    A4Coords += ' / Email: ' + emailVal;
                }

                // Update Ticket Preview
                document.getElementById('preview-ticket-title').textContent = storeNameVal;
                document.getElementById('preview-ticket-coords').innerHTML = ticketCoords;
                document.getElementById('preview-ticket-footer').textContent = footerVal;

                // Update A4 Invoice Preview
                document.getElementById('preview-facture-title').textContent = storeNameVal;
                document.getElementById('preview-facture-coords').innerHTML = A4Coords;
                document.getElementById('preview-facture-footer').textContent = footerVal;
            }

            // Tab Switching
            function switchPreviewTab(tab) {
                const tabs = document.querySelectorAll('.preview-tab-btn');
                const contentBoxes = document.querySelectorAll('.preview-content-box');
 
                tabs.forEach(btn => btn.classList.remove('active'));
                contentBoxes.forEach(box => box.classList.remove('active'));
 
                if (tab === 'ticket') {
                    document.getElementById('btn-tab-ticket').classList.add('active');
                    document.getElementById('preview-ticket-container').classList.add('active');
                } else {
                    document.getElementById('btn-tab-facture').classList.add('active');
                    document.getElementById('preview-facture-container').classList.add('active');
                }
            }

            function handleFormatChange(val) {
                if (val === 'ticket') {
                    switchPreviewTab('ticket');
                } else {
                    switchPreviewTab('facture');
                }
            }
 
            // Init values on page load
            document.addEventListener('DOMContentLoaded', function() {
                updateLivePreview();
                const activeFormat = document.getElementById('invoice_format').value;
                switchPreviewTab(activeFormat === 'ticket' ? 'ticket' : 'facture');
            });
        </script>
    @endpush

@endsection

@extends('admin.layouts.app')

@section('title', 'Détails de la Vente #' . $sale->reference)
@section('page-title', 'Détails de la Vente')

@section('content')

    <div class="list-header no-print">
        <div>
            <h2 class="list-title"><i class="fa-solid fa-receipt"></i> Vente #{{ $sale->reference }}</h2>
            <p class="list-sub">Enregistrée le {{ $sale->created_at->format('d/m/Y à H:i:s') }}</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('admin.sales.index') }}" class="btn btn-gray">
                <i class="fa-solid fa-arrow-left"></i> Retour à la liste
            </a>
            <button onclick="window.print()" class="btn btn-yellow">
                <i class="fa-solid fa-print"></i> Imprimer la Facture
            </button>
        </div>
    </div>

    <div class="invoice-container">
        <!-- Partie imprimable ou d'affichage standard -->
        <div class="invoice-card">
            <!-- Header de l'entreprise -->
            <div class="invoice-header">
                <div>
                    <h1 class="logo-title">SUPERMARCHÉ PRO</h1>
                    <p class="company-detail">
                        Abidjan, Cocody Riviera Palmeraie<br>
                        Tel: +225 07 00 00 00 00 / Email: contact@supermarchepro.com
                    </p>
                </div>
                <div style="text-align: right;">
                    <h2 style="margin: 0; color: #004d99; font-size: 20px; font-weight: 800;">FACTURE</h2>
                    <p style="margin: 5px 0 0 0; font-size: 13px; color: var(--text-muted);">
                        Réf : <strong>#{{ $sale->reference }}</strong><br>
                        Date : {{ $sale->created_at->format('d/m/Y H:i') }}
                    </p>
                </div>
            </div>

            <div class="invoice-divider"></div>

            <!-- Infos Client et Caissier -->
            <div class="invoice-meta-grid">
                <div>
                    <h3 class="meta-title">Client :</h3>
                    @if ($sale->customer)
                        <p class="meta-value"><strong>{{ $sale->customer->name }}</strong></p>
                        @if ($sale->customer->phone)
                            <p class="meta-detail">Tél : {{ $sale->customer->phone }}</p>
                        @endif
                        @if ($sale->customer->email)
                            <p class="meta-detail">Email : {{ $sale->customer->email }}</p>
                        @endif
                        @if ($sale->customer->address)
                            <p class="meta-detail">Adresse : {{ $sale->customer->address }}</p>
                        @endif
                    @else
                        <p class="meta-value"><em>Client de Passage (Anonyme)</em></p>
                    @endif
                </div>
                <div style="text-align: right;">
                    <h3 class="meta-title">Opérateur :</h3>
                    <p class="meta-value"><strong>{{ $sale->user ? $sale->user->name : 'Inconnu' }}</strong></p>
                    <p class="meta-detail">Rôle : Caissier / POS</p>
                    <p class="meta-detail" style="margin-top: 10px;">
                        Mode de paiement :
                        @if ($sale->payment_method === 'cash')
                            <strong style="color: #059669;">Espèces</strong>
                        @elseif ($sale->payment_method === 'card')
                            <strong style="color: #2563eb;">Carte / Mobile Money</strong>
                        @elseif ($sale->payment_method === 'credit')
                            <strong style="color: #d97706;">Vente à Crédit</strong>
                        @else
                            <strong>{{ ucfirst($sale->payment_method) }}</strong>
                        @endif
                    </p>
                    <p class="meta-detail">
                        Statut :
                        @if ($sale->status === 'completed')
                            <span style="font-weight: bold; color: #059669;">Validée / Payée</span>
                        @else
                            <span style="font-weight: bold; color: #dc2626;">Retournée / Annulée</span>
                        @endif
                    </p>
                </div>
            </div>

            <!-- Tableau d'articles -->
            <div class="table-responsive">
                <table class="invoice-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Désignation de l'article</th>
                            <th style="text-align: center; width: 100px;">Prix unitaire</th>
                            <th style="text-align: center; width: 80px;">Quantité</th>
                            <th style="text-align: right; width: 120px;">Montant Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sale->items as $idx => $item)
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td>
                                    <strong>{{ $item->product ? $item->product->name : 'Produit supprimé' }}</strong>
                                    <br><small style="color: var(--text-muted)">Code-barres:
                                        {{ $item->product ? $item->product->barcode : 'N/A' }}</small>
                                </td>
                                <td style="text-align: center;">{{ number_format($item->unit_price, 0, ',', ' ') }} FCFA</td>
                                <td style="text-align: center;">{{ $item->quantity }}</td>
                                <td style="text-align: right; font-weight: 700;">
                                    {{ number_format($item->subtotal, 0, ',', ' ') }} FCFA
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Totaux -->
            <div class="invoice-totals-wrapper">
                <div class="invoice-signature">
                    <p style="margin-bottom: 50px;">Cachet & Signature</p>
                    <div style="border-bottom: 1px dotted #000; width: 150px; margin: auto;"></div>
                </div>
                <div class="invoice-totals">
                    <div class="total-row">
                        <span>Total Global:</span>
                        <strong>{{ number_format($sale->total_amount, 0, ',', ' ') }} FCFA</strong>
                    </div>
                    <div class="total-row font-normal">
                        <span>Montant Encaissé:</span>
                        <span>{{ number_format($sale->amount_received, 0, ',', ' ') }} FCFA</span>
                    </div>
                    <div class="total-row font-normal">
                        <span>Monnaie Rendue:</span>
                        <span>{{ number_format($sale->change_amount, 0, ',', ' ') }} FCFA</span>
                    </div>

                    @if ($sale->payment_method === 'credit')
                        <div class="total-row"
                            style="color: #dc2626; border-top: 1px double #dc2626; margin-top: 5px; padding-top: 5px;">
                            <span>Dette / Crédit Client:</span>
                            <strong>{{ number_format(max(0, $sale->total_amount - $sale->amount_received), 0, ',', ' ') }}
                                FCFA</strong>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Footer de la facture -->
            <div class="invoice-footer">
                <p>Merci pour votre confiance ! Les marchandises vendues ne sont ni reprises ni échangées, sauf accord de la
                    direction.</p>
                <p style="font-size: 10px; color: var(--text-muted); margin-top: 10px;">Supermarché Pro - Solution de
                    Facturation Intégrée</p>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .invoice-container {
                display: flex;
                justify-content: center;
                margin-top: 20px;
            }

            .invoice-card {
                background: var(--card);
                border: 1px solid var(--border);
                border-radius: 12px;
                padding: 40px;
                width: 100%;
                max-width: 800px;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            }

            .invoice-header {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
            }

            .logo-title {
                margin: 0 0 5px 0;
                font-size: 24px;
                font-weight: 800;
                color: #004d99;
                letter-spacing: -0.5px;
            }

            .company-detail {
                margin: 0;
                font-size: 12px;
                color: var(--text-muted);
                line-height: 1.6;
            }

            .invoice-divider {
                border-bottom: 2px solid #f1f5f9;
                margin: 25px 0;
            }

            .invoice-meta-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 20px;
                margin-bottom: 30px;
            }

            .meta-title {
                margin: 0 0 8px 0;
                font-size: 11px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                color: var(--text-muted);
            }

            .meta-value {
                margin: 0 0 5px 0;
                font-size: 15px;
                color: var(--text);
            }

            .meta-detail {
                margin: 0 0 3px 0;
                font-size: 12px;
                color: var(--text-muted);
            }

            .invoice-table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 30px;
            }

            .invoice-table th {
                background: #f8fafc;
                border-bottom: 2px solid #e2e8f0;
                padding: 12px 10px;
                font-size: 11px;
                font-weight: 700;
                text-transform: uppercase;
                color: #475569;
                text-align: left;
            }

            .invoice-table td {
                border-bottom: 1px solid #f1f5f9;
                padding: 12px 10px;
                font-size: 13px;
                color: var(--text);
                text-align: left;
            }

            .invoice-totals-wrapper {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                margin-top: 20px;
            }

            .invoice-signature {
                text-align: center;
                width: 200px;
                font-size: 12px;
                color: var(--text-muted);
            }

            .invoice-totals {
                width: 300px;
            }

            .total-row {
                display: flex;
                justify-content: space-between;
                padding: 8px 0;
                font-size: 13px;
                color: var(--text);
                border-bottom: 1px solid #f1f5f9;
            }

            .total-row strong {
                font-size: 16px;
                color: #004d99;
            }

            .total-row.font-normal {
                color: var(--text-muted);
            }

            .invoice-footer {
                margin-top: 40px;
                text-align: center;
                border-top: 1px dashed #e2e8f0;
                padding-top: 20px;
                font-size: 11px;
                color: var(--text-muted);
                line-height: 1.5;
            }

            @media print {
                body {
                    background: #fff !important;
                    color: #000 !important;
                }

                .no-print {
                    display: none !important;
                }

                .invoice-card {
                    border: none !important;
                    padding: 0 !important;
                    box-shadow: none !important;
                }

                .sidebar,
                .navbar,
                .main-header {
                    display: none !important;
                }

                .content-wrapper {
                    margin-left: 0 !important;
                    padding: 0 !important;
                }
            }

            @media (max-width: 600px) {
                .invoice-meta-grid {
                    grid-template-columns: 1fr;
                }
                .invoice-meta-grid > div:last-child {
                    text-align: left !important;
                }
                .invoice-header {
                    flex-direction: column;
                    gap: 15px;
                }
                .invoice-header > div:last-child {
                    text-align: left !important;
                }
                .invoice-totals-wrapper {
                    flex-direction: column-reverse;
                    gap: 25px;
                    align-items: center;
                }
                .invoice-totals {
                    width: 100%;
                }
                .invoice-card {
                    padding: 20px !important;
                }
            }
        </style>
    @endpush

@endsection
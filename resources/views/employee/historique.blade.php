<!-- History View -->
<main class="pos-center" id="view-history" style="display: none; background: #fff; flex-direction: column;">
    <div style="margin-bottom: 25px; display: flex; justify-content: space-between; align-items: flex-end;">
        <div>
            <h2 style="font-size: 22px; font-weight: 800; color: var(--primary);">Historique des Ventes</h2>
            <p style="color: var(--text-muted); font-size: 14px;">Consultez vos transactions récentes.</p>
        </div>
        <div class="search-wrap" style="max-width: 300px; width: 100%;">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" class="search-input" id="history-search-input"
                oninput="filterSalesHistory()" placeholder="Rechercher une référence...">
        </div>
    </div>

    <div
        style="overflow-x: auto; background: #fff; border-radius: 15px; border: 1px solid var(--border); flex: 1;">
        <table style="width: 100%; border-collapse: collapse; text-align: center;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 1px solid var(--border);">
                    <th
                        style="padding: 15px 20px; font-size: 13px; color: var(--text-muted); text-transform: uppercase; text-align: center;">
                        Référence</th>
                    <th
                        style="padding: 15px 20px; font-size: 13px; color: var(--text-muted); text-transform: uppercase; text-align: center;">
                        Date</th>
                    <th
                        style="padding: 15px 20px; font-size: 13px; color: var(--text-muted); text-transform: uppercase; text-align: center;">
                        Articles</th>
                    <th
                        style="padding: 15px 20px; font-size: 13px; color: var(--text-muted); text-transform: uppercase; text-align: center;">
                        Total</th>
                    <th
                        style="padding: 15px 20px; font-size: 13px; color: var(--text-muted); text-transform: uppercase; text-align: center;">
                        Statut</th>
                    <th
                        style="padding: 15px 20px; font-size: 13px; color: var(--text-muted); text-transform: uppercase; text-align: center;">
                        Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sales as $sale)
                    <tr class="sale-row" data-ref="{{ strtolower($sale->reference) }}"
                        style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 15px 20px; font-weight: 700; text-align: center;">#{{ $sale->reference }}</td>
                        <td style="padding: 15px 20px; text-align: center;">{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                        <td style="padding: 15px 20px; text-align: center;">
                            <div style="font-size: 12px; color: var(--text-muted);">
                                {{ $sale->items->count() }} produit(s)
                            </div>
                        </td>
                        <td style="padding: 15px 20px; font-weight: 700; color: var(--primary); text-align: center;">
                            {{ number_format($sale->total_amount, 0, ',', ' ') }} FCFA
                        </td>
                        <td style="padding: 15px 20px; text-align: center;">
                            @if($sale->status === 'returned')
                                <span style="display: inline-block; padding: 4px 10px; font-size: 12px; font-weight: 600; color: #be123c; background: #fff1f2; border: 1px solid #fecdd3; border-radius: 20px;">
                                    <i class="fa-solid fa-rotate-left"></i> Retournée
                                </span>
                            @else
                                <span style="display: inline-block; padding: 4px 10px; font-size: 12px; font-weight: 600; color: #047857; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 20px;">
                                    <i class="fa-solid fa-circle-check"></i> Complétée
                                </span>
                            @endif
                        </td>
                        <td style="padding: 15px 20px; text-align: center;">
                            <button class="qty-btn" style="width: auto; padding: 5px 10px; font-size: 11px; margin: 0 auto;"
                                onclick="viewSaleDetails({{ json_encode($sale) }})">
                                <i class="fa-solid fa-eye"></i> Détails
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</main>

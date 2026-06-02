<!-- Stock View (Table) -->
<main class="pos-center" id="view-stock" style="display: none; background: #fff; flex-direction: column;">
    <div style="margin-bottom: 25px; display: flex; justify-content: space-between; align-items: flex-end;">
        <div>
            <h2 style="font-size: 22px; font-weight: 800; color: var(--primary);">Inventaire du Stock</h2>
            <p style="color: var(--text-muted); font-size: 14px;">Consultez les niveaux de stock en temps réel.</p>
        </div>
        <div class="search-wrap" style="max-width: 300px; width: 100%;">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" class="search-input" id="stock-search-input" oninput="filterStockTable()"
                placeholder="Rechercher dans le stock...">
        </div>
    </div>

    <div class="category-filter" style="margin-bottom: 20px;">
        <button class="cat-badge-stock active" onclick="filterStockCategory('all', this)"
            style="padding: 8px 15px; border-radius: 10px; border: 1px solid var(--border); background: #fff; cursor: pointer; font-weight: 600; font-size: 13px;">Tous</button>
        @foreach ($categories as $cat)
            <button class="cat-badge-stock" onclick="filterStockCategory('{{ $cat->name }}', this)"
                style="padding: 8px 15px; border-radius: 10px; border: 1px solid var(--border); background: #fff; cursor: pointer; font-weight: 600; font-size: 13px; margin-left: 8px;">{{ $cat->name }}</button>
        @endforeach

        <button onclick="showCriticalStock(this)"
            style="padding: 8px 15px; border-radius: 10px; border: 1px solid var(--danger); background: #fff; color: var(--danger); cursor: pointer; font-weight: 700; font-size: 13px; margin-left: auto; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-triangle-exclamation"></i> Seuils Critiques
        </button>
    </div>

    <div style="overflow-x: auto; background: #fff; border-radius: 15px; border: 1px solid var(--border); flex: 1;">
        <table id="stock-table" style="width: 100%; border-collapse: collapse; text-align: center;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 1px solid var(--border);">
                    <th style="padding: 15px 20px; font-size: 13px; color: var(--text-muted); text-transform: uppercase;">Aperçu</th>
                    <th style="padding: 15px 20px; font-size: 13px; color: var(--text-muted); text-transform: uppercase;">Produit</th>
                    <th style="padding: 15px 20px; font-size: 13px; color: var(--text-muted); text-transform: uppercase;">Catégorie</th>
                    <th style="padding: 15px 20px; font-size: 13px; color: var(--text-muted); text-transform: uppercase;">Prix Unitaire</th>
                    <th style="padding: 15px 20px; font-size: 13px; color: var(--text-muted); text-transform: uppercase;">Stock Actuel</th>
                    <th style="padding: 15px 20px; font-size: 13px; color: var(--text-muted); text-transform: uppercase; text-align: center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($allProducts as $product)
                    <tr class="stock-row" data-name="{{ strtolower($product->name) }}"
                        data-category="{{ $product->category_name }}"
                        data-is-low="{{ $product->stock <= $product->stock_threshold ? 'true' : 'false' }}"
                        style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 10px 20px;">
                            <div style="width: 45px; height: 45px; border-radius: 8px; overflow: hidden; background: #f1f5f9; display: inline-flex; align-items: center; justify-content: center;">
                                @if ($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                    <i class="fa-solid fa-image" style="color: #cbd5e1;"></i>
                                @endif
                            </div>
                        </td>
                        <td style="padding: 15px 20px; font-size: 14px; font-weight: 700;">{{ $product->name }}</td>
                        <td style="padding: 15px 20px; font-size: 14px;">{{ $product->category_name }}</td>
                        <td style="padding: 15px 20px; font-size: 14px; font-weight: 700;">{{ number_format($product->price, 0, ',', ' ') }} FCFA</td>
                        <td style="padding: 15px 20px;">
                            @if ($product->stock <= $product->stock_threshold)
                                <span style="background: #fff1f2; color: #e11d48; padding: 4px 10px; border-radius: 6px; font-size: 13px; font-weight: 700;">
                                    {{ $product->stock }} (Bas)
                                </span>
                            @else
                                <span style="background: #e8f9f0; color: #059669; padding: 4px 10px; border-radius: 6px; font-size: 13px; font-weight: 700;">
                                    {{ $product->stock }}
                                </span>
                            @endif
                        </td>
                        <td style="padding: 10px 20px; text-align: center;" id="action-cell-{{ $product->id }}">
                            @if(in_array($product->id, $pendingRestockRequestIds))
                                <span style="background: #eff6ff; color: #1d4ed8; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 5px;">
                                    <i class="fa-solid fa-clock"></i> En attente
                                </span>
                            @else
                                <button onclick="demandRestock({{ $product->id }}, this)" 
                                    style="background: var(--primary); color: #fff; border: none; padding: 8px 16px; border-radius: 8px; font-size: 12px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s ease; box-shadow: 0 2px 4px rgba(0, 77, 153, 0.15);"
                                    onmouseover="this.style.background='var(--primary-light)'; this.style.transform='translateY(-1px)';" 
                                    onmouseout="this.style.background='var(--primary)'; this.style.transform='none';">
                                    <i class="fa-solid fa-paper-plane"></i> Demander
                                </button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</main>

<script>
function demandRestock(productId, button) {
    const originalContent = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Envoi...';

    fetch("{{ route('employee.stock.request') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({ product_id: productId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Demande envoyée !',
                text: data.message,
                confirmButtonColor: 'var(--primary)',
                timer: 2000,
                timerProgressBar: true
            });
            const cell = document.getElementById('action-cell-' + productId);
            if (cell) {
                cell.innerHTML = `
                    <span style="background: #eff6ff; color: #1d4ed8; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 5px; animation: pulse 2s infinite;">
                        <i class="fa-solid fa-clock"></i> En attente
                    </span>
                `;
            }
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'Attention',
                text: data.message,
                confirmButtonColor: 'var(--primary)'
            });
            button.disabled = false;
            button.innerHTML = originalContent;
        }
    })
    .catch(error => {
        console.error("Erreur:", error);
        Swal.fire({
            icon: 'error',
            title: 'Erreur',
            text: 'Une erreur est survenue lors de la demande.',
            confirmButtonColor: 'var(--primary)'
        });
        button.disabled = false;
        button.innerHTML = originalContent;
    });
}
</script>

<style>
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.6; }
}
</style>

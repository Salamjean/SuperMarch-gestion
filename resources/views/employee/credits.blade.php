<!-- Credits View -->
<main class="pos-center" id="view-credits" style="display: none; background: #fff; flex-direction: column;">
    <div style="margin-bottom: 25px; display: flex; justify-content: space-between; align-items: flex-end;">
        <div>
            <h2 style="font-size: 22px; font-weight: 800; color: var(--primary);">Suivi des Crédits Clients</h2>
            <p style="color: var(--text-muted); font-size: 14px;">Consultez les dettes clients et encaissez les règlements.</p>
        </div>
        <div class="search-wrap" style="max-width: 300px; width: 100%;">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" class="search-input" id="credit-search-input" oninput="filterCreditTable()"
                placeholder="Rechercher un client ou téléphone...">
        </div>
    </div>

    <div style="overflow-x: auto; background: #fff; border-radius: 15px; border: 1px solid var(--border); flex: 1;">
        <table id="credits-table" style="width: 100%; border-collapse: collapse; text-align: center;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 1px solid var(--border);">
                    <th style="padding: 15px 20px; font-size: 13px; color: var(--text-muted); text-transform: uppercase;">ID</th>
                    <th style="padding: 15px 20px; font-size: 13px; color: var(--text-muted); text-transform: uppercase; text-align: left;">Nom du Client</th>
                    <th style="padding: 15px 20px; font-size: 13px; color: var(--text-muted); text-transform: uppercase;">Téléphone</th>
                    <th style="padding: 15px 20px; font-size: 13px; color: var(--text-muted); text-transform: uppercase;">Statut Crédit</th>
                    <th style="padding: 15px 20px; font-size: 13px; color: var(--text-muted); text-transform: uppercase; text-align: right;">Dette Active</th>
                    <th style="padding: 15px 20px; font-size: 13px; color: var(--text-muted); text-transform: uppercase; text-align: center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $anyDebtor = false;
                @endphp
                @foreach ($customers as $cust)
                    @if ($cust->debt_balance > 0)
                        @php $anyDebtor = true; @endphp
                        <tr class="credit-row" data-name="{{ strtolower($cust->name) }}" data-phone="{{ strtolower($cust->phone ?? '') }}" style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 15px 20px; font-size: 14px; color: var(--text-muted);">#{{ $cust->id }}</td>
                            <td style="padding: 15px 20px; font-size: 14px; font-weight: 700; text-align: left;">{{ $cust->name }}</td>
                            <td style="padding: 15px 20px; font-size: 14px;">{{ $cust->phone ?? '—' }}</td>
                            <td style="padding: 15px 20px;">
                                @if ($cust->is_credit_blocked)
                                    <span style="background: #fff1f2; color: #e11d48; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="fa-solid fa-lock"></i> Bloqué
                                    </span>
                                @else
                                    <span style="background: #e8f9f0; color: #059669; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="fa-solid fa-circle-check"></i> Autorisé
                                    </span>
                                @endif
                            </td>
                            <td style="padding: 15px 20px; text-align: right; font-weight: 800; font-size: 14.5px;" id="debt-display-{{ $cust->id }}">
                                <span style="color: #e11d48;">{{ number_format($cust->debt_balance, 0, ',', ' ') }} FCFA</span>
                            </td>
                            <td style="padding: 10px 20px; text-align: center;" id="credit-action-{{ $cust->id }}">
                                <button onclick="payCustomerDebtModal({{ $cust->id }}, '{{ addslashes($cust->name) }}', {{ $cust->debt_balance }})"
                                    style="background: #059669; color: #fff; border: none; padding: 8px 16px; border-radius: 8px; font-size: 12px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s ease; box-shadow: 0 2px 4px rgba(5, 150, 105, 0.15);"
                                    onmouseover="this.style.background='#10b981'; this.style.transform='translateY(-1px)';" 
                                    onmouseout="this.style.background='#059669'; this.style.transform='none';">
                                    <i class="fa-solid fa-hand-holding-dollar"></i> Encaisser
                                </button>
                            </td>
                        </tr>
                    @endif
                @endforeach
                @if (!$anyDebtor)
                    <tr>
                        <td colspan="6" style="padding: 40px 20px; color: var(--text-muted); text-align: center; font-style: italic;">
                            Aucun client n'a de crédit en cours actuellement.
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</main>

<script>
function filterCreditTable() {
    const query = document.getElementById('credit-search-input').value.toLowerCase();
    const rows = document.querySelectorAll('.credit-row');

    rows.forEach(row => {
        const name = row.dataset.name;
        const phone = row.dataset.phone;

        const matchName = name.includes(query);
        const matchPhone = phone.includes(query);

        row.style.display = (matchName || matchPhone) ? '' : 'none';
    });
}

function payCustomerDebtModal(id, name, maxDebt) {
    Swal.fire({
        title: 'Règlement de crédit',
        html: `Saisir le montant du remboursement apporté par <b>${name}</b> :<br><span style="font-size:12.5px;color:var(--text-muted);">Encours maximal : ${parseFloat(maxDebt).toLocaleString()} FCFA</span>`,
        input: 'number',
        inputPlaceholder: 'Montant versé en FCFA...',
        showCancelButton: true,
        confirmButtonText: 'Valider le paiement',
        cancelButtonText: 'Annuler',
        confirmButtonColor: '#059669',
        inputValidator: (value) => {
            if (!value || value <= 0) {
                return 'Le montant doit être positif et non nul !'
            }
            if (parseFloat(value) > parseFloat(maxDebt)) {
                return `Le montant ne peut pas dépasser la dette actuelle (${parseFloat(maxDebt).toLocaleString()} FCFA)`
            }
        }
    }).then(async (result) => {
        if (result.isConfirmed && result.value) {
            const amount = parseFloat(result.value);
            
            // Détecter l'environnement
            const isElectron = typeof window.electronAPI !== 'undefined';
            
            // Détection de connexion
            let isCurrentlyOnline = navigator.onLine;
            if (typeof checkActualConnection === 'function') {
                isCurrentlyOnline = await checkActualConnection();
            }
            
            let localSuccess = false;
            let currentUserId = {{ auth()->id() ?? 'null' }};
            let activeSessionId = null;
            
            if (isElectron) {
                try {
                    // Trouver la session de caisse active locale
                    const activeSession = await window.electronAPI.invoke('sqlite-get-active-session', currentUserId);
                    if (activeSession) activeSessionId = activeSession.id;
                    
                    const localResult = await window.electronAPI.invoke('sqlite-pay-debt', {
                        customerId: id,
                        userId: currentUserId,
                        cashSessionId: activeSessionId,
                        amount: amount
                    });
                    
                    if (localResult.success) {
                        localSuccess = true;
                        console.log("Règlement de dette enregistré localement dans SQLite :", localResult);
                    } else {
                        throw new Error(localResult.message);
                    }
                } catch (err) {
                    console.error("Erreur remboursement SQLite :", err);
                    Swal.fire('Erreur Locale !', 'Impossible de valider le remboursement localement dans SQLite : ' + err.message, 'error');
                    return;
                }
            }
            
            if (isCurrentlyOnline) {
                const routeUrl = "{{ route('employee.customers.pay-debt', ':id') }}".replace(':id', id);
                
                fetch(routeUrl, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ amount: amount })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (isElectron) {
                            // Déclencher immédiatement la synchronisation
                            window.electronAPI.invoke('sqlite-push', {
                                baseUrl: window.location.origin,
                                sessionCookie: document.cookie
                            }).catch(() => {});
                        }
                        Swal.fire({
                            icon: 'success',
                            title: 'Enregistré !',
                            text: data.message,
                            confirmButtonColor: 'var(--primary)'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Échec',
                            text: data.message,
                            confirmButtonColor: 'var(--primary)'
                        });
                    }
                })
                .catch(error => {
                    console.error("Erreur serveur payDebt:", error);
                    if (isElectron && localSuccess) {
                        Swal.fire('Mode Hors-ligne', 'Règlement enregistré localement. Il sera envoyé au serveur plus tard.', 'success').then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: 'Une erreur est survenue lors de l\'enregistrement du remboursement.',
                            confirmButtonColor: 'var(--primary)'
                        });
                    }
                });
            } else {
                if (isElectron && localSuccess) {
                    Swal.fire('Mode Hors-ligne', 'Règlement enregistré localement. Les données seront synchronisées au retour de la connexion.', 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Hors-ligne', 'Connexion internet requise pour enregistrer le remboursement.', 'error');
                }
            }
        }
    });
}
</script>

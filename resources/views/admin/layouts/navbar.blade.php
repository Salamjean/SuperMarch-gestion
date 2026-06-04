<header class="navbar">

    <!-- Bouton toggle sidebar (mobile) -->
    <button class="navbar-toggle" id="sidebarToggle" aria-label="Ouvrir le menu">
        <i class="fa-solid fa-bars"></i>
    </button>

    <!-- Titre de la page courante -->
    <div class="navbar-title">@yield('page-title', 'Tableau de bord')</div>

    <!-- Actions à droite -->
    <div class="navbar-actions" style="display: flex; align-items: center; gap: 16px;">

        <!-- Notifications Restock -->
        <div class="notification-dropdown-wrapper" style="position: relative; display: inline-block;">
            <button class="notification-btn" id="notifBtn" onclick="toggleNotifDropdown(event)"
                style="position: relative; background: #f3f7fc; border: none; width: 38px; height: 38px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--blue); cursor: pointer; transition: all 0.2s ease;">
                <i class="fa-solid fa-bell" style="font-size: 18px;"></i>
                @if ($restockRequests->count() > 0)
                    <span class="notif-badge"
                        style="position: absolute; top: -2px; right: -2px; background: #dc2626; color: #fff; font-size: 10px; font-weight: 800; min-width: 18px; height: 18px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 2px solid #fff; padding: 0 4px; animation: bounce 1s infinite alternate;">
                        {{ $restockRequests->count() }}
                    </span>
                @endif
            </button>

            <!-- Dropdown Menu -->
            <div class="notif-dropdown" id="notifDropdown"
                style="display: none; position: absolute; top: 48px; right: 0; width: 320px; background: #fff; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); border: 1px solid var(--border); z-index: 1000; overflow: hidden; transform-origin: top right; transition: all 0.3s ease;">
                <div
                    style="padding: 14px 16px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; background: #fafbfc;">
                    <span style="font-weight: 700; font-size: 14px; color: var(--blue);">Réapprovisionnements</span>
                    <span style="font-size: 11px; color: var(--text-muted); font-weight: 600;"
                        id="notifCountText">{{ $restockRequests->count() }} active(s)</span>
                </div>
                <div class="notif-list" style="max-height: 280px; overflow-y: auto;" id="notifListContainer">
                    @if ($restockRequests->count() > 0)
                        @foreach ($restockRequests as $req)
                            <div class="notif-item" id="notif-item-{{ $req->id }}"
                                style="padding: 12px 16px; border-bottom: 1px solid #f1f5f9; display: flex; flex-direction: column; gap: 6px; transition: background 0.2s;"
                                onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='#fff'">
                                <div
                                    style="display: flex; justify-content: space-between; align-items: flex-start; gap: 8px;">
                                    <span
                                        style="font-size: 13px; font-weight: 700; color: var(--text);">{{ $req->product->name }}</span>
                                    <button onclick="resolveRequest({{ $req->id }}, this)"
                                        style="background: #e8f9f0; color: #059669; border: none; padding: 4px 8px; border-radius: 6px; font-size: 11px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 4px; transition: all 0.2s;"
                                        onmouseover="this.style.background='#d1fae5'"
                                        onmouseout="this.style.background='#e8f9f0'">
                                        <i class="fa-solid fa-check"></i> Traité
                                    </button>
                                </div>
                                <div
                                    style="display: flex; justify-content: space-between; font-size: 11px; color: var(--text-muted);">
                                    <span>Par: {{ $req->user?->name ?? 'Inconnu' }}</span>
                                    <span>{{ $req->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div
                            style="padding: 30px 16px; text-align: center; color: var(--text-muted); display: flex; flex-direction: column; align-items: center; gap: 8px;">
                            <i class="fa-regular fa-bell-slash" style="font-size: 24px; color: #cbd5e1;"></i>
                            <span style="font-size: 12px;">Aucune demande active</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Badge admin -->
        <span class="navbar-badge">
            <i class="fa-solid fa-shield-halved"></i> Admin
        </span>

        <!-- Nom utilisateur -->
        <a href="{{ route('admin.profile.show') }}" class="navbar-username" style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 6px; transition: opacity 0.2s;" onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
            <i class="fa-solid fa-circle-user" style="font-size:18px; color:#004d99"></i>
            {{ auth()->user()->name }}
        </a>

    </div>
</header>

<script>
    function toggleNotifDropdown(event) {
        if (event) event.stopPropagation();
        const dropdown = document.getElementById('notifDropdown');
        const btn = document.getElementById('notifBtn');
        if (dropdown.style.display === 'none' || !dropdown.style.display) {
            dropdown.style.display = 'block';
            btn.style.background = 'var(--blue-light)';
        } else {
            dropdown.style.display = 'none';
            btn.style.background = '#f3f7fc';
        }
    }

    // Fermer le dropdown si on clique à l'extérieur
    window.addEventListener('click', function(e) {
        const dropdown = document.getElementById('notifDropdown');
        const btn = document.getElementById('notifBtn');
        if (dropdown && dropdown.style.display === 'block') {
            if (!dropdown.contains(e.target) && !btn.contains(e.target)) {
                dropdown.style.display = 'none';
                if (btn) btn.style.background = '#f3f7fc';
            }
        }
    });

    function resolveRequest(requestId, button) {
        Swal.fire({
            title: 'Valider le réapprovisionnement',
            text: 'Saisir la quantité à ajouter au stock pour ce produit :',
            input: 'number',
            customClass: {
                input: 'swal-custom-qty-input'
            },
            inputAttributes: {
                min: 1,
                step: 1
            },
            showCancelButton: true,
            confirmButtonText: 'Ajouter au Stock',
            cancelButtonText: 'Annuler',
            confirmButtonColor: '#059669',
            cancelButtonColor: '#64748b',
            inputValidator: (value) => {
                if (!value || value <= 0) {
                    return 'Veuillez entrer une quantité positive valide !'
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const qty = result.value;
                const originalContent = button.innerHTML;
                button.disabled = true;
                button.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

                fetch(`/admin/stock/request/${requestId}/resolve`, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({
                            quantity: qty
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Stock mis à jour !',
                                text: data.message,
                                confirmButtonColor: 'var(--blue)',
                                timer: 2000,
                                timerProgressBar: true
                            });

                            const item = document.getElementById('notif-item-' + requestId);
                            if (item) {
                                item.style.opacity = '0';
                                item.style.transform = 'scale(0.95)';
                                item.style.transition = 'all 0.3s ease';
                                setTimeout(() => {
                                    item.remove();
                                    const activeItems = document.querySelectorAll(
                                        '#notifListContainer .notif-item');
                                    const count = activeItems.length;

                                    const countText = document.getElementById('notifCountText');
                                    if (countText) countText.textContent = count + ' active(s)';

                                    const badge = document.querySelector('.notif-badge');
                                    if (badge) {
                                        if (count > 0) {
                                            badge.textContent = count;
                                        } else {
                                            badge.remove();
                                        }
                                    }

                                    if (count === 0) {
                                        const container = document.getElementById(
                                            'notifListContainer');
                                        if (container) {
                                            container.innerHTML = `
                                            <div style="padding: 30px 16px; text-align: center; color: var(--text-muted); display: flex; flex-direction: column; align-items: center; gap: 8px;">
                                                <i class="fa-regular fa-bell-slash" style="font-size: 24px; color: #cbd5e1;"></i>
                                                <span style="font-size: 12px;">Aucune demande active</span>
                                            </div>
                                        `;
                                        }
                                    }
                                }, 300);
                            }
                        } else {
                            button.disabled = false;
                            button.innerHTML = originalContent;
                            Swal.fire({
                                icon: 'error',
                                title: 'Erreur',
                                text: data.message,
                                confirmButtonColor: 'var(--blue)'
                            });
                        }
                    })
                    .catch(error => {
                        console.error("Erreur:", error);
                        button.disabled = false;
                        button.innerHTML = originalContent;
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: 'Une erreur est survenue lors de la résolution.',
                            confirmButtonColor: 'var(--blue)'
                        });
                    });
    }
</script>

<style>
    @keyframes bounce {
        0% {
            transform: translateY(0);
        }

        100% {
            transform: translateY(-4px);
        }
    }

    .swal-custom-qty-input {
        max-width: 250px !important;
        margin: 15px auto !important;
        box-sizing: border-box !important;
    }
</style>

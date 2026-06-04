@extends('admin.layouts.app')

@section('title', 'Suivi des Sessions de Caisse')
@section('page-title', 'Sessions de Caisse')

@section('content')

    <div class="list-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <div>
            <h2 class="list-title"
                style="font-size: 22px; font-weight: 800; color: #1e293b; margin: 0; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-vault" style="color: #6366f1;"></i> Sessions de Caisse
            </h2>
            <p class="list-sub" style="font-size: 13px; color: var(--text-muted); margin: 3px 0 0 0;">Contrôlez les
                encaissements, auditez les tiroirs et identifiez les écarts physiques</p>
        </div>
    </div>

    <!-- Rapid Stats Sessions -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 25px;">
        <div
            style="background: white; border: 1px solid var(--border); border-radius: 12px; padding: 20px; display: flex; align-items: center; gap: 15px;">
            <div
                style="background: #eef2ff; color: #6366f1; width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-cash-register"></i>
            </div>
            <div>
                <span
                    style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; display: block;">Sessions
                    Actives</span>
                <span
                    style="font-size: 20px; font-weight: 800; color: #1e293b;">{{ \App\Models\CashSession::where('status', 'open')->count() }}
                    Ouverte(s)</span>
            </div>
        </div>

        <div
            style="background: white; border: 1px solid var(--border); border-radius: 12px; padding: 20px; display: flex; align-items: center; gap: 15px;">
            <div
                style="background: #f0fdf4; color: #16a34a; width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-lock"></i>
            </div>
            <div>
                <span
                    style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; display: block;">Sessions
                    Clôturées</span>
                <span
                    style="font-size: 20px; font-weight: 800; color: #16a34a;">{{ \App\Models\CashSession::where('status', 'closed')->count() }}
                    Fermée(s)</span>
            </div>
        </div>

        <div
            style="background: white; border: 1px solid var(--border); border-radius: 12px; padding: 20px; display: flex; align-items: center; gap: 15px;">
            <div
                style="background: #fff8e6; color: #d97706; width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-scale-unbalanced-triformed"></i>
            </div>
            <div>
                <span
                    style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; display: block;">Anomalies
                    (Écarts)</span>
                @php $globalDiff = \App\Models\CashSession::whereNotNull('closed_at')->sum('difference'); @endphp
                <span style="font-size: 20px; font-weight: 800; color: {{ $globalDiff >= 0 ? '#16a34a' : '#dc2626' }};">
                    {{ number_format($globalDiff, 0, ',', ' ') }} FCFA
                </span>
            </div>
        </div>
    </div>

    <!-- Filtres Modernisés -->
    <div class="card"
        style="margin-bottom: 25px; border-radius: 12px; border: 1px solid var(--border); overflow: hidden; background: white; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
        <div class="card-body" style="padding: 20px;">
            <form method="GET" action="{{ route('admin.cash-sessions.index') }}">
                <div
                    style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; align-items: flex-end;">
                    <div class="form-group" style="margin: 0;">
                        <label
                            style="font-size: 11px; font-weight: 700; color: #475569; display: block; margin-bottom: 6px; text-transform: uppercase;">Caissier</label>
                        <select name="user_id" class="form-control"
                            style="font-size: 13px; height: 40px; border-radius: 8px; border: 1px solid var(--border); width: 100%; outline: none;">
                            <option value="">Tous les caissiers</option>
                            @foreach ($cashiers as $cashier)
                                <option value="{{ $cashier->id }}"
                                    {{ request('user_id') == $cashier->id ? 'selected' : '' }}>{{ $cashier->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group" style="margin: 0;">
                        <label
                            style="font-size: 11px; font-weight: 700; color: #475569; display: block; margin-bottom: 6px; text-transform: uppercase;">Statut
                            de session</label>
                        <select name="status" class="form-control"
                            style="font-size: 13px; height: 40px; border-radius: 8px; border: 1px solid var(--border); width: 100%; outline: none;">
                            <option value="">Tous les statuts</option>
                            <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Ouvertes (Actives)
                            </option>
                            <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Clôturées</option>
                        </select>
                    </div>

                    <div class="form-group" style="margin: 0;">
                        <label
                            style="font-size: 11px; font-weight: 700; color: #475569; display: block; margin-bottom: 6px; text-transform: uppercase;">Ouvert
                            le</label>
                        <input type="date" name="date" class="form-control"
                            style="font-size: 13px; height: 40px; border-radius: 8px; border: 1px solid var(--border); width: 100%; outline: none;"
                            value="{{ request('date') }}">
                    </div>

                    <div style="display: flex; gap: 8px;">
                        <button type="submit" class="btn btn-yellow"
                            style="height: 40px; border-radius: 8px; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 6px; flex: 1; background: #1e293b; border-color: #1e293b; color: white;">
                            Filtrer
                        </button>
                        <a href="{{ route('admin.cash-sessions.index') }}" class="btn btn-gray"
                            style="height: 40px; width: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; background: #e2e8f0; border: none; color: #475569;"
                            title="Réinitialiser">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table Modernisée -->
    <div class="card"
        style="border-radius: 12px; border: 1px solid var(--border); overflow: hidden; background: white; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
        <div class="card-body" style="padding:0;">
            @if ($sessions->isEmpty())
                <div class="empty-state" style="text-align: center; padding: 50px 20px; color: var(--text-muted);">
                    <div
                        style="width: 60px; height: 60px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                        <i class="fa-solid fa-cash-register" style="font-size: 24px; color: #94a3b8;"></i>
                    </div>
                    <p style="font-weight: 600; font-size: 15px; margin: 0; color: #334155;">Aucune session trouvée</p>
                    <p style="font-size: 13px; margin: 5px 0 0 0;">Aucune session de caisse n'a été ouverte aux dates
                        demandées.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="data-table" style="width: 100%; border-collapse: collapse; text-align: left;">
                        <thead>
                            <tr style="background: #f8fafc; border-bottom: 1px solid var(--border);">
                                <th
                                    style="padding: 14px 20px; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase;">
                                    Caissier</th>
                                <th
                                    style="padding: 14px 20px; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase;">
                                    Période de Validation</th>
                                <th
                                    style="padding: 14px 20px; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase; text-align: right;">
                                    Fond Début</th>
                                <th
                                    style="padding: 14px 20px; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase; text-align: right;">
                                    Attendu Théorique</th>
                                <th
                                    style="padding: 14px 20px; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase; text-align: right;">
                                    Physique Déclaré</th>
                                <th
                                    style="padding: 14px 20px; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase; text-align: center;">
                                    Écart (Audit)</th>
                                <th
                                    style="padding: 14px 20px; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase; text-align: center; width: 130px;">
                                    Statut</th>
                                <th
                                    style="padding: 14px 20px; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase; text-align: center; width: 80px;">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sessions as $sess)
                                <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.2s;"
                                    onmouseover="this.style.backgroundColor='#f8fafc'"
                                    onmouseout="this.style.backgroundColor='transparent'">
                                    <td style="padding: 14px 20px;">
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <div
                                                style="width: 28px; height: 28px; background: #f3e8ff; color: #a855f7; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 11px;">
                                                {{ strtoupper(substr($sess->user?->name ?? '?', 0, 1)) }}
                                            </div>
                                            <span
                                                style="font-size: 13.5px; font-weight: 700; color: #334155;">{{ $sess->user ? $sess->user->name : 'Inconnu' }}</span>
                                        </div>
                                    </td>
                                    <td style="padding: 14px 20px; font-size: 12.5px; color: var(--text-muted);">
                                        <div style="font-weight: 500; color: #475569;">Ouvert:
                                            {{ $sess->opened_at ? $sess->opened_at->format('d/m/Y H:i') : '—' }}</div>
                                        <div style="font-size:11px;">Fermé:
                                            {{ $sess->closed_at ? $sess->closed_at->format('d/m/Y H:i') : 'En cours...' }}
                                        </div>
                                    </td>
                                    <td
                                        style="padding: 14px 20px; text-align: right; font-weight: 600; font-size: 13.5px; color: #334155;">
                                        {{ number_format($sess->opening_balance, 0, ',', ' ') }} FCFA
                                    </td>
                                    <td
                                        style="padding: 14px 20px; text-align: right; font-weight: 600; font-size: 13.5px; color: #1e3a8a;">
                                        {{ $sess->expected_closing_balance ? number_format($sess->expected_closing_balance, 0, ',', ' ') . ' FCFA' : '—' }}
                                    </td>
                                    <td
                                        style="padding: 14px 20px; text-align: right; font-weight: 600; font-size: 13.5px; color: #0f172a;">
                                        {{ $sess->actual_closing_balance ? number_format($sess->actual_closing_balance, 0, ',', ' ') . ' FCFA' : '—' }}
                                    </td>
                                    <td style="padding: 14px 20px; text-align: center; font-weight: 800; font-size: 13px;">
                                        @if ($sess->status === 'open')
                                            <span style="color: #94a3b8; font-weight: 500;">Non clôturée</span>
                                        @else
                                            @if ($sess->difference == 0)
                                                <span
                                                    style="color: #16a34a; background: #f0fdf4; padding: 2px 6px; border-radius: 4px; border: 1px solid #bbf7d0;"><i
                                                        class="fa-solid fa-circle-check"></i> Parfait</span>
                                            @elseif ($sess->difference > 0)
                                                <span
                                                    style="color: #2563eb; background: #eff6ff; padding: 2px 6px; border-radius: 4px; border: 1px solid #bfdbfe;">+{{ number_format($sess->difference, 0, ',', ' ') }}
                                                    FCFA</span>
                                            @else
                                                <span
                                                    style="color: #dc2626; background: #fee2e2; padding: 2px 6px; border-radius: 4px; border: 1px solid #fecdd3;">{{ number_format($sess->difference, 0, ',', ' ') }}
                                                    FCFA</span>
                                            @endif
                                        @endif
                                    </td>
                                    <td style="padding: 14px 20px; text-align: center;">
                                        @if ($sess->status === 'open')
                                            <span class="badge"
                                                style="background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; padding: 3px 8px; border-radius: 6px; font-weight: 700; font-size:11px;">
                                                <i class="fa-solid fa-play" style="font-size: 9px; margin-right: 3px;"></i>
                                                Ouverte
                                            </span>
                                        @else
                                            <span class="badge"
                                                style="background: #f1f5f9; color: #475569; border: 1px solid var(--border); padding: 3px 8px; border-radius: 6px; font-weight: 700; font-size:11px;">
                                                <i class="fa-solid fa-lock" style="font-size: 9px; margin-right: 3px;"></i>
                                                Clôturée
                                            </span>
                                        @endif
                                    </td>
                                    <td style="padding: 14px 20px; text-align: center;">
                                        <a href="{{ route('admin.cash-sessions.show', $sess->id) }}" class="btn-icon"
                                            style="background: #f1f5f9; color: #1e293b; width: 32px; height: 32px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; text-decoration: none;"
                                            title="Rapport d'audit de caisse">
                                            <i class="fa-solid fa-magnifying-glass-chart"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div style="padding: 15px; border-top: 1px solid var(--border); display: flex; justify-content: flex-end;">
                    {{ $sessions->links() }}
                </div>
            @endif
        </div>
    </div>

@endsection

<!-- Statistics View -->
<main class="pos-center" id="view-stats" style="display: none; background: #f8fafc; flex-direction: column; overflow-y: auto; gap: 25px;">
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border); padding-bottom: 20px;">
        <div>
            <h2 style="font-size: 24px; font-weight: 800; color: var(--primary); display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-chart-line"></i> Statistiques & Performances
            </h2>
            <p style="color: var(--text-muted); font-size: 14px; margin-top: 4px;">Suivez vos ventes et votre productivité en temps réel.</p>
        </div>
        <div style="background: #e0f2fe; color: #0369a1; padding: 8px 16px; border-radius: 12px; font-weight: 700; font-size: 13.5px; display: flex; align-items: center; gap: 8px; border: 1px solid #bae6fd;">
            <i class="fa-solid fa-user-check"></i> Caissier : {{ auth()->user()->name }}
        </div>
    </div>

    <!-- Section: Aujourd'hui -->
    <div>
        <h3 style="font-size: 16px; font-weight: 800; color: var(--text); margin-bottom: 15px; display: flex; align-items: center; gap: 8px;">
            <span style="width: 8px; height: 8px; background: var(--accent); border-radius: 50%;"></span> Activité d'Aujourd'hui
        </h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px;">
            <!-- Card 1: Chiffre d'Affaires -->
            <div class="stats-card" style="background: #fff; border: 1px solid var(--border); border-radius: 16px; padding: 20px; display: flex; align-items: center; gap: 16px; transition: all 0.3s; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);"
                 onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 12px 20px -8px rgba(0,0,0,0.08)'; this.style.borderColor='var(--primary)';"
                 onmouseout="this.style.transform='none'; this.style.boxShadow='0 4px 6px -1px rgba(0,0,0,0.02)'; this.style.borderColor='var(--border)';">
                <div style="width: 50px; height: 50px; background: #ecfdf5; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--accent); font-size: 20px;">
                    <i class="fa-solid fa-wallet"></i>
                </div>
                <div>
                    <div style="font-size: 13px; color: var(--text-muted); font-weight: 500;">Chiffre d'Affaires</div>
                    <div style="font-size: 18px; font-weight: 800; color: var(--text); margin-top: 2px;">{{ number_format($todayRevenue, 0, ',', ' ') }} FCFA</div>
                </div>
            </div>

            <!-- Card 2: Transactions -->
            <div class="stats-card" style="background: #fff; border: 1px solid var(--border); border-radius: 16px; padding: 20px; display: flex; align-items: center; gap: 16px; transition: all 0.3s; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);"
                 onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 12px 20px -8px rgba(0,0,0,0.08)'; this.style.borderColor='var(--primary)';"
                 onmouseout="this.style.transform='none'; this.style.boxShadow='0 4px 6px -1px rgba(0,0,0,0.02)'; this.style.borderColor='var(--border)';">
                <div style="width: 50px; height: 50px; background: #f0fdf4; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #16a34a; font-size: 20px;">
                    <i class="fa-solid fa-receipt"></i>
                </div>
                <div>
                    <div style="font-size: 13px; color: var(--text-muted); font-weight: 500;">Transactions (Ventes)</div>
                    <div style="font-size: 18px; font-weight: 800; color: var(--text); margin-top: 2px;">{{ $todayCount }}</div>
                </div>
            </div>

            <!-- Card 3: Panier Moyen -->
            <div class="stats-card" style="background: #fff; border: 1px solid var(--border); border-radius: 16px; padding: 20px; display: flex; align-items: center; gap: 16px; transition: all 0.3s; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);"
                 onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 12px 20px -8px rgba(0,0,0,0.08)'; this.style.borderColor='var(--primary)';"
                 onmouseout="this.style.transform='none'; this.style.boxShadow='0 4px 6px -1px rgba(0,0,0,0.02)'; this.style.borderColor='var(--border)';">
                <div style="width: 50px; height: 50px; background: #fffbeb; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #d97706; font-size: 20px;">
                    <i class="fa-solid fa-cart-shopping"></i>
                </div>
                <div>
                    <div style="font-size: 13px; color: var(--text-muted); font-weight: 500;">Panier Moyen</div>
                    <div style="font-size: 18px; font-weight: 800; color: var(--text); margin-top: 2px;">{{ number_format($todayAverage, 0, ',', ' ') }} FCFA</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section: Performances Globales -->
    <div>
        <h3 style="font-size: 16px; font-weight: 800; color: var(--text); margin-bottom: 15px; display: flex; align-items: center; gap: 8px;">
            <span style="width: 8px; height: 8px; background: var(--primary); border-radius: 50%;"></span> Statistiques Cumulées (Globales)
        </h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px;">
            <!-- Card 1: Chiffre d'Affaires Cumulé -->
            <div class="stats-card" style="background: #fff; border: 1px solid var(--border); border-radius: 16px; padding: 20px; display: flex; align-items: center; gap: 16px; transition: all 0.3s; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);"
                 onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 12px 20px -8px rgba(0,0,0,0.08)'; this.style.borderColor='var(--primary)';"
                 onmouseout="this.style.transform='none'; this.style.boxShadow='0 4px 6px -1px rgba(0,0,0,0.02)'; this.style.borderColor='var(--border)';">
                <div style="width: 50px; height: 50px; background: #eff6ff; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 20px;">
                    <i class="fa-solid fa-chart-pie"></i>
                </div>
                <div>
                    <div style="font-size: 13px; color: var(--text-muted); font-weight: 500;">C.A. Cumulé</div>
                    <div style="font-size: 18px; font-weight: 800; color: var(--text); margin-top: 2px;">{{ number_format($totalRevenue, 0, ',', ' ') }} FCFA</div>
                </div>
            </div>

            <!-- Card 2: Transactions Totales -->
            <div class="stats-card" style="background: #fff; border: 1px solid var(--border); border-radius: 16px; padding: 20px; display: flex; align-items: center; gap: 16px; transition: all 0.3s; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);"
                 onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 12px 20px -8px rgba(0,0,0,0.08)'; this.style.borderColor='var(--primary)';"
                 onmouseout="this.style.transform='none'; this.style.boxShadow='0 4px 6px -1px rgba(0,0,0,0.02)'; this.style.borderColor='var(--border)';">
                <div style="width: 50px; height: 50px; background: #faf5ff; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #9333ea; font-size: 20px;">
                    <i class="fa-solid fa-bag-shopping"></i>
                </div>
                <div>
                    <div style="font-size: 13px; color: var(--text-muted); font-weight: 500;">Total Ventes</div>
                    <div style="font-size: 18px; font-weight: 800; color: var(--text); margin-top: 2px;">{{ $totalCount }}</div>
                </div>
            </div>

            <!-- Card 3: Panier Moyen Global -->
            <div class="stats-card" style="background: #fff; border: 1px solid var(--border); border-radius: 16px; padding: 20px; display: flex; align-items: center; gap: 16px; transition: all 0.3s; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);"
                 onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 12px 20px -8px rgba(0,0,0,0.08)'; this.style.borderColor='var(--primary)';"
                 onmouseout="this.style.transform='none'; this.style.boxShadow='0 4px 6px -1px rgba(0,0,0,0.02)'; this.style.borderColor='var(--border)';">
                <div style="width: 50px; height: 50px; background: #fdf2f8; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #db2777; font-size: 20px;">
                    <i class="fa-solid fa-calculator"></i>
                </div>
                <div>
                    <div style="font-size: 13px; color: var(--text-muted); font-weight: 500;">Panier Moyen Global</div>
                    <div style="font-size: 18px; font-weight: 800; color: var(--text); margin-top: 2px;">{{ number_format($totalAverage, 0, ',', ' ') }} FCFA</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section: Graphique & Top Produits -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 25px; align-items: start; margin-bottom: 10px;">
        <!-- Column Left: Activité Hebdomadaire (Chart CSS) -->
        <div style="background: #fff; border: 1px solid var(--border); border-radius: 18px; padding: 25px; display: flex; flex-direction: column; gap: 20px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);">
            <div>
                <h3 style="font-size: 16px; font-weight: 800; color: var(--text);">Activité des 7 Derniers Jours</h3>
                <p style="color: var(--text-muted); font-size: 13px; margin-top: 2px;">Visualisez vos encaissements quotidiens sur la semaine.</p>
            </div>
            
            @php
                $maxWeeklyRevenue = collect($weeklySales)->max('revenue') ?: 1;
            @endphp

            <!-- Chart Container -->
            <div style="display: flex; justify-content: space-between; align-items: flex-end; height: 220px; padding: 10px 10px 0 10px; border-bottom: 2px solid var(--border); position: relative; gap: 15px; margin-top: 15px;">
                @foreach ($weeklySales as $day)
                    @php
                        $heightPercent = ($day['revenue'] / $maxWeeklyRevenue) * 100;
                        if ($day['revenue'] > 0 && $heightPercent < 5) {
                            $heightPercent = 5;
                        }
                    @endphp
                    <div style="flex: 1; display: flex; flex-direction: column; align-items: center; height: 100%; justify-content: flex-end; position: relative;">
                        <!-- Tooltip / Label -->
                        <div class="chart-tooltip" style="position: absolute; bottom: calc({{ $heightPercent }}% + 8px); background: var(--text); color: #fff; padding: 5px 8px; border-radius: 6px; font-size: 11px; font-weight: 700; white-space: nowrap; opacity: 0; pointer-events: none; transition: opacity 0.2s, transform 0.2s; transform: translateY(5px); box-shadow: 0 4px 10px rgba(0,0,0,0.15); z-index: 10;">
                            {{ number_format($day['revenue'], 0, ',', ' ') }} FCFA
                        </div>
                        <!-- Bar -->
                        <div class="chart-bar" style="width: 100%; max-width: 45px; height: {{ $heightPercent }}%; background: linear-gradient(180deg, var(--primary-light) 0%, var(--primary) 100%); border-radius: 8px 8px 0 0; cursor: pointer; transition: all 0.3s ease; position: relative; box-shadow: 0 4px 10px rgba(0, 77, 153, 0.15);"
                             onmouseenter="this.previousElementSibling.style.opacity = 1; this.previousElementSibling.style.transform = 'translateY(0)';"
                             onmouseleave="this.previousElementSibling.style.opacity = 0; this.previousElementSibling.style.transform = 'translateY(5px)';"
                             onmouseover="this.style.filter = 'brightness(1.15)'; this.style.transform = 'scaleY(1.02)';"
                             onmouseout="this.style.filter = 'none'; this.style.transform = 'scaleY(1)';">
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- X Axis Labels -->
            <div style="display: flex; justify-content: space-between; padding: 0 10px; gap: 15px;">
                @foreach ($weeklySales as $day)
                    <div style="flex: 1; text-align: center;">
                        <div style="font-size: 13px; font-weight: 700; color: var(--text);">{{ $day['day'] }}</div>
                        <div style="font-size: 11px; color: var(--text-muted); margin-top: 1px;">{{ $day['date'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Column Right: Top 5 Products -->
        <div style="background: #fff; border: 1px solid var(--border); border-radius: 18px; padding: 25px; display: flex; flex-direction: column; gap: 20px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); height: 100%;">
            <div>
                <h3 style="font-size: 16px; font-weight: 800; color: var(--text);">Vos Top Produits</h3>
                <p style="color: var(--text-muted); font-size: 13px; margin-top: 2px;">Les 5 produits les plus vendus par vos soins.</p>
            </div>

            @if ($topProducts->isEmpty())
                <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 12px; padding: 40px 0; color: var(--text-muted); text-align: center;">
                    <i class="fa-solid fa-cubes" style="font-size: 32px; opacity: 0.3;"></i>
                    <p style="font-size: 13px;">Aucune vente enregistrée pour le moment.</p>
                </div>
            @else
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    @foreach ($topProducts as $index => $item)
                        @if ($item->product)
                            <div style="display: flex; align-items: center; justify-content: space-between; padding: 12px; border-radius: 12px; border: 1px solid #f1f5f9; background: #fafafa; transition: all 0.2s;"
                                 onmouseover="this.style.background = '#f1f5f9'; this.style.borderColor = 'var(--border)';"
                                 onmouseout="this.style.background = '#fafafa'; this.style.borderColor = '#f1f5f9';">
                                <div style="display: flex; align-items: center; gap: 12px; flex: 1; min-width: 0;">
                                    <!-- Rank Badge -->
                                    <div style="width: 28px; height: 28px; border-radius: 50%; background: {{ $index == 0 ? '#ffc300' : ($index == 1 ? '#cbd5e1' : ($index == 2 ? '#cd7f32' : '#f1f5f9')) }}; color: {{ $index < 3 ? '#1e293b' : 'var(--text-muted)' }}; font-weight: 800; font-size: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: {{ $index < 3 ? '0 2px 5px rgba(0,0,0,0.1)' : 'none' }};">
                                        {{ $index + 1 }}
                                    </div>
                                    <!-- Product info -->
                                    <div style="min-width: 0; flex: 1;">
                                        <h4 style="font-size: 13.5px; font-weight: 700; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; line-height: 1.2;" title="{{ $item->product->name }}">{{ $item->product->name }}</h4>
                                        <div style="font-size: 11px; color: var(--text-muted); margin-top: 1px;">{{ number_format($item->product->price, 0, ',', ' ') }} FCFA</div>
                                    </div>
                                </div>
                                <!-- Qty sold -->
                                <div style="text-align: right; flex-shrink: 0; margin-left: 10px;">
                                    <span style="background: #e8f9f0; color: #059669; padding: 4px 10px; border-radius: 20px; font-size: 12.5px; font-weight: 800; display: inline-block;">
                                        {{ $item->total_qty }} u.
                                    </span>
                                    <div style="font-size: 11px; color: var(--text-muted); font-weight: 600; margin-top: 2px;">{{ number_format($item->total_subtotal, 0, ',', ' ') }} FCFA</div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</main>

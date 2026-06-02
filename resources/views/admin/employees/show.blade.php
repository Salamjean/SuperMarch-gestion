@extends('admin.layouts.app')

@section('title', 'Détails de l\'employé')
@section('page-title', 'Détails de l\'employé')

@section('content')

    <div class="profile-container">
        
        <div class="profile-sidebar">
            <div class="profile-card">
                <div class="profile-avatar">
                    {{ strtoupper(substr($employee->name, 0, 1)) }}
                </div>
                <h2 class="profile-name">{{ $employee->name }}</h2>
                <div class="profile-role">{{ $employee->position ?? 'Employé' }}</div>
                <div class="profile-badge">{{ $employee->department ?? 'Général' }}</div>
                
                <div class="profile-actions-vertical">
                    <a href="{{ route('admin.employees.edit', $employee) }}" class="btn-p btn-p-yellow">
                        <i class="fa-solid fa-user-pen"></i> Modifier le profil
                    </a>
                    <a href="{{ route('admin.employees.index') }}" class="btn-p btn-p-outline">
                        <i class="fa-solid fa-arrow-left"></i> Retour à la liste
                    </a>
                </div>
            </div>
        </div>

        <div class="profile-main">
            {{-- Informations personnelles --}}
            <div class="info-section">
                <div class="info-section-header">
                    <i class="fa-solid fa-id-card"></i> Informations personnelles
                </div>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Nom complet</div>
                        <div class="info-value">{{ $employee->name }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Genre</div>
                        <div class="info-value">{{ $employee->gender == 'male' ? 'Homme' : ($employee->gender == 'female' ? 'Femme' : '—') }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Téléphone</div>
                        <div class="info-value">{{ $employee->phone ?? '—' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Adresse</div>
                        <div class="info-value">{{ $employee->address ?? '—' }}</div>
                    </div>
                </div>
            </div>

            {{-- Informations professionnelles --}}
            <div class="info-section">
                <div class="info-section-header">
                    <i class="fa-solid fa-briefcase"></i> Détails professionnels
                </div>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Poste</div>
                        <div class="info-value">{{ $employee->position ?? '—' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Département</div>
                        <div class="info-value">{{ $employee->department ?? '—' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Date d'embauche</div>
                        <div class="info-value">{{ $employee->hire_date ? \Carbon\Carbon::parse($employee->hire_date)->format('d F Y') : '—' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Date d'inscription</div>
                        <div class="info-value">{{ $employee->created_at->format('d F Y à H:i') }}</div>
                    </div>
                </div>
            </div>

            {{-- Informations de compte --}}
            <div class="info-section">
                <div class="info-section-header">
                    <i class="fa-solid fa-shield-halved"></i> Sécurité du compte
                </div>
                <div class="info-grid">
                    <div class="info-item" style="grid-column: 1 / -1; background: #eff6ff; padding: 12px 16px; border-radius: 10px; border: 1px dashed #bfdbfe; margin-bottom: 5px; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <div class="info-label" style="color: #004d99; font-weight: 700;">Code ID (Identifiant de Connexion)</div>
                            <div class="info-value" style="font-size: 16px; color: #1e3a8a; letter-spacing: 0.5px;">{{ $employee->login_code }}</div>
                        </div>
                        <i class="fa-solid fa-id-badge" style="font-size: 24px; color: #3b82f6; opacity: 0.7;"></i>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Adresse e-mail</div>
                        <div class="info-value">{{ $employee->email }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Rôle système</div>
                        <div class="info-value" style="text-transform: capitalize;">{{ $employee->role }}</div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @push('styles')
        <style>
            .profile-container {
                display: grid;
                grid-template-columns: 300px 1fr;
                gap: 24px;
                align-items: start;
            }

            /* Sidebar Card */
            .profile-card {
                background: #fff;
                border-radius: 20px;
                padding: 32px 24px;
                text-align: center;
                border: 1px solid #e2eaf3;
                box-shadow: 0 4px 12px rgba(0,0,0,0.04);
            }

            .profile-avatar {
                width: 100px;
                height: 100px;
                background: linear-gradient(135deg, #004d99 0%, #003366 100%);
                color: #fff;
                font-size: 42px;
                font-weight: 800;
                border-radius: 30px;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 20px;
                box-shadow: 0 8px 16px rgba(0, 77, 153, 0.2);
            }

            .profile-name {
                font-size: 20px;
                font-weight: 800;
                color: #1a2840;
                margin-bottom: 4px;
            }

            .profile-role {
                font-size: 14px;
                font-weight: 600;
                color: #004d99;
                margin-bottom: 12px;
            }

            .profile-badge {
                display: inline-block;
                padding: 4px 12px;
                background: #eef4ff;
                color: #004d99;
                font-size: 11px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 1px;
                border-radius: 999px;
                margin-bottom: 24px;
            }

            .profile-actions-vertical {
                display: flex;
                flex-direction: column;
                gap: 10px;
            }

            .btn-p {
                padding: 12px;
                border-radius: 12px;
                font-size: 14px;
                font-weight: 700;
                text-decoration: none;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                transition: all 0.2s;
            }

            .btn-p-yellow {
                background: #ffc300;
                color: #004d99;
            }
            .btn-p-yellow:hover {
                background: #e6b000;
                transform: translateY(-2px);
            }

            .btn-p-outline {
                background: #fff;
                border: 1.5px solid #d0dce8;
                color: #5a7a99;
            }
            .btn-p-outline:hover {
                background: #f8fbff;
                border-color: #a0b8cc;
            }

            /* Main Content Info */
            .info-section {
                background: #fff;
                border-radius: 20px;
                padding: 24px;
                border: 1px solid #e2eaf3;
                margin-bottom: 24px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.04);
            }

            .info-section-header {
                font-size: 13px;
                font-weight: 800;
                text-transform: uppercase;
                letter-spacing: 1px;
                color: #004d99;
                display: flex;
                align-items: center;
                gap: 10px;
                padding-bottom: 16px;
                border-bottom: 1.5px solid #f0f4f8;
                margin-bottom: 20px;
            }

            .info-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 20px;
            }

            .info-label {
                font-size: 12px;
                font-weight: 600;
                color: #7a94aa;
                margin-bottom: 4px;
            }

            .info-value {
                font-size: 15px;
                font-weight: 700;
                color: #1a2840;
            }

            @media (max-width: 850px) {
                .profile-container {
                    grid-template-columns: 1fr;
                }
            }
        </style>
    @endpush

@endsection

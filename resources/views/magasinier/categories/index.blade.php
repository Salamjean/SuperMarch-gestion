@extends('magasinier.layouts.app')

@section('title', 'Categories magasinier')
@section('page_title', 'Categories')

@section('content')
    <div class="card" style="padding:18px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
            <h2 style="font-size:18px;"><i class="fa-solid fa-tags"></i> Categories</h2>
            <a href="{{ route('magasinier.categories.create') }}" class="mg-logout-btn" style="text-decoration:none;">
                <i class="fa-solid fa-plus"></i> Nouvelle categorie
            </a>
        </div>

        @if (session('success'))
            <div
                style="margin-bottom:10px; padding:10px; border:1px solid #86efac; background:#f0fdf4; color:#166534; border-radius:8px;">
                {{ session('success') }}
            </div>
        @endif

        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr>
                    <th style="text-align:center; padding:10px; border-bottom:1px solid var(--border);">Nom</th>
                    <th style="text-align:center; padding:10px; border-bottom:1px solid var(--border);">Description</th>
                    <th style="text-align:center; padding:10px; border-bottom:1px solid var(--border);">Ajoute par</th>
                    <th style="text-align:center; padding:10px; border-bottom:1px solid var(--border);">Date</th>
                    <th style="text-align:center; padding:10px; border-bottom:1px solid var(--border);">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categories as $category)
                    <tr>
                        <td style="text-align:center; padding:10px; border-bottom:1px solid var(--border);">
                            {{ $category->name }}</td>
                        <td style="text-align:center; padding:10px; border-bottom:1px solid var(--border);">
                            {{ $category->description ?? '—' }}
                        </td>
                        <td style="text-align:center; padding:10px; border-bottom:1px solid var(--border);">
                            {{ $category->creator->name ?? 'Inconnu' }}</td>
                        <td style="text-align:center; padding:10px; border-bottom:1px solid var(--border);">
                            {{ $category->created_at->format('d/m/Y H:i') }}</td>
                        <td style="text-align:center; padding:10px; border-bottom:1px solid var(--border);">
                            <div style="display:flex; gap:8px; align-items:center; justify-content:center;">
                                <a href="{{ route('magasinier.categories.edit', $category) }}"
                                    style="text-decoration:none; color:#004d99; font-weight:700;">
                                    <i class="fa-solid fa-pen-to-square"></i> 
                                </a>

                                <form method="POST" action="{{ route('magasinier.categories.destroy', $category) }}"
                                    onsubmit="return confirm('Supprimer cette categorie ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        style="border:none; background:none; color:#e11d48; cursor:pointer; font-weight:700;">
                                        <i class="fa-solid fa-trash"></i> 
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align:center; padding:14px;">Aucune categorie.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection

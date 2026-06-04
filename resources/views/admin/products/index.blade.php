@extends('admin.layouts.app')

@section('title', 'Produits')
@section('page-title', 'Gestion des produits')

@section('content')

    <div class="list-header">
        <div>
            <h2 class="list-title"><i class="fa-solid fa-box"></i> Produits</h2>
            <p class="list-sub">{{ $products->count() }} produit(s) en inventaire</p>
        </div>
        <a href="{{ route('admin.products.create') }}" class="btn btn-yellow">
            <i class="fa-solid fa-plus"></i> Nouveau produit
        </a>
    </div>

    @if(isset($selectedCategory) && $selectedCategory)
        <div style="background: #e0f2fe; border: 1px solid #bae6fd; color: #0369a1; padding: 12px 20px; border-radius: 12px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; font-size: 14px;">
            <span><i class="fa-solid fa-filter" style="margin-right: 6px;"></i> Filtré par la catégorie : <strong>{{ $selectedCategory }}</strong></span>
            <a href="{{ route('admin.products.index') }}" style="color: #0369a1; text-decoration: none; font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 4px; border: 1px solid rgba(3, 105, 161, 0.2); padding: 4px 10px; border-radius: 8px; background: rgba(3, 105, 161, 0.05);">
                <i class="fa-solid fa-xmark"></i> Réinitialiser
            </a>
        </div>
    @endif

    <div class="card">
        <div class="card-body" style="padding:0;">
            @if ($products->isEmpty())
                <div class="empty-state">
                    <i class="fa-solid fa-box-open"></i>
                    <p>Aucun produit enregistré pour l'instant.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="width:48px; text-align:center;">#</th>
                                <th style="text-align:center;">Image</th>
                                <th style="text-align:center;">Référence</th>
                                <th style="text-align:center;">Produit</th>
                                <th style="text-align:center;">Catégorie</th>
                                <th style="text-align:center;">Fournisseur</th>
                                <th style="text-align:center;">Ajouté par</th>
                                <th style="text-align:center;">Prix</th>
                                <th style="text-align:center;">Stock</th>
                                <th style="text-align:center;">QR Code</th>
                                <th style="width:110px; text-align:center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $i => $product)
                                <tr>
                                    <td class="td-id" style="text-align:center;">{{ $i + 1 }}</td>
                                    <td>
                                        <div style="display:flex; justify-content:center;">
                                            @if ($product->image)
                                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                                                    style="width:45px; height:45px; border-radius:10px; object-fit:cover; border:1px solid #e2eaf3;">
                                            @else
                                                <div style="width:45px; height:45px; border-radius:10px; background: linear-gradient(135deg, #004d99, #1a6bbf); display:flex; align-items:center; justify-content:center; color:white; font-weight:800; font-size:18px; border:1px solid #e2eaf3; flex-shrink:0;">
                                                    {{ strtoupper(mb_substr($product->name, 0, 1)) }}
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td style="text-align:center;">
                                        <span class="badge badge-gray"
                                            style="font-family: monospace; background:#f1f5f9; color:#475569;">{{ $product->reference }}</span>
                                    </td>
                                    <td style="text-align:center;">
                                        <div class="td-name">{{ $product->name }}</div>
                                    </td>
                                    <td style="text-align:center;">
                                        <span class="badge badge-gray"
                                            style="background:#eef4ff; color:#004d99; border:1px solid rgba(0,77,153,0.1);">{{ $product->category_name }}</span>
                                    </td>
                                    <td style="text-align:center;" class="td-muted">
                                        {{ $product->supplier->name ?? 'Aucun' }}
                                    </td>
                                    <td style="text-align:center;" class="td-muted">
                                        {{ $product->creator ? $product->creator->name : 'Non trace (ancien enregistrement)' }}
                                    </td>
                                    <td style="text-align:center; font-weight:700; color:#1a2840;">
                                        {{ number_format($product->price, 0, ',', ' ') }} FCFA
                                    </td>
                                    <td style="text-align:center;">
                                        @if ($product->stock <= ($product->stock_threshold ?? 5))
                                            <span style="color:#e11d48; font-weight:700;"
                                                title="Seuil: {{ $product->stock_threshold }}">
                                                <i class="fa-solid fa-triangle-exclamation"></i> {{ $product->stock }}
                                            </span>
                                        @else
                                            <span style="color:#059669; font-weight:700;">{{ $product->stock }}</span>
                                        @endif
                                    </td>
                                    <td style="text-align:center;">
                                        @if ($product->reference)
                                            <div class="qr-thumbnail"
                                                onclick="viewQR('{{ $product->reference }}', '{{ addslashes($product->name) }}', this)">
                                                @if (class_exists('SimpleSoftwareIO\QrCode\Facades\QrCode'))
                                                    {!! QrCode::size(100)->generate($product->reference) !!}
                                                @else
                                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ $product->reference }}"
                                                        alt="QR">
                                                @endif
                                            </div>
                                        @else
                                            <span class="td-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="td-actions" style="justify-content:center;">
                                        <a href="{{ route('admin.products.edit', $product) }}" class="btn-icon"
                                            title="Modifier">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    @push('styles')
        <style>
            .list-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 20px;
            }

            .list-title {
                font-size: 17px;
                font-weight: 800;
                color: #004d99;
                margin: 0 0 3px;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .list-sub {
                font-size: 12.5px;
                color: #7a94aa;
                margin: 0;
            }

            .data-table {
                width: 100%;
                border-collapse: collapse;
                font-size: 13.5px;
            }

            .data-table thead tr {
                background: #f5f9ff;
                border-bottom: 1.5px solid #e2eaf3;
            }

            .data-table th {
                padding: 11px 16px;
                font-size: 11px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: .05em;
                color: #7a94aa;
            }

            .data-table tbody tr {
                border-bottom: 1px solid #f0f4f8;
                transition: background .15s;
            }

            .data-table tbody tr:hover {
                background: #f8fbff;
            }

            .data-table td {
                padding: 12px 16px;
                vertical-align: middle;
            }

            .td-id {
                color: #a0b5c8;
                font-size: 12px;
                font-weight: 600;
            }

            .td-name {
                font-weight: 600;
                color: #1a2e44;
            }

            .td-muted {
                color: #7a94aa;
            }

            .td-actions {
                display: flex;
                gap: 6px;
                align-items: center;
            }

            .badge {
                display: inline-flex;
                align-items: center;
                padding: 3px 10px;
                border-radius: 10px;
                font-size: 11px;
                font-weight: 700;
            }

            .badge-gray {
                background: #f0f4f8;
                color: #7a94aa;
            }

            .btn-icon {
                width: 30px;
                height: 30px;
                border-radius: 7px;
                border: 1px solid #e2eaf3;
                background: #fff;
                color: #004d99;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-size: 12px;
                cursor: pointer;
                text-decoration: none;
                transition: background .15s;
            }

            .btn-icon:hover {
                background: #eef4ff;
            }

            .empty-state {
                text-align: center;
                padding: 48px 20px;
                color: #a0b5c8;
                font-size: 14px;
            }

            .empty-state i {
                font-size: 36px;
                margin-bottom: 12px;
                display: block;
            }

            .qr-thumbnail {
                width: 45px;
                height: 45px;
                cursor: zoom-in;
                border: 1px solid #e2eaf3;
                padding: 3px;
                border-radius: 8px;
                background: #fff;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto;
                transition: transform 0.2s;
            }

            .qr-thumbnail:hover {
                transform: scale(1.1);
                border-color: #004d99;
            }

            .qr-thumbnail img,
            .qr-thumbnail svg {
                width: 100%;
                height: 100%;
                object-fit: contain;
            }

            @media print {
                body.printing-qr-only {
                    margin: 0 !important;
                    padding: 0 !important;
                    background: #fff !important;
                    min-height: 100vh !important;
                }
                body.printing-qr-only > *:not(#qr-print-area) {
                    display: none !important;
                }
                body.printing-qr-only #qr-print-area {
                    display: flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                    width: 100vw !important;
                    height: 100vh !important;
                    position: fixed !important;
                    top: 0 !important;
                    left: 0 !important;
                    margin: 0 !important;
                    padding: 0 !important;
                    background: #fff !important;
                    z-index: 9999999 !important;
                }
                body.printing-qr-only #qr-print-area svg,
                body.printing-qr-only #qr-print-area img {
                    width: 250px !important;
                    height: 250px !important;
                    max-width: 100% !important;
                    max-height: 100% !important;
                    display: block !important;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            function viewQR(code, name, element) {
                let qrHtml = '';
                if (element) {
                    const svgOrImg = element.querySelector('svg, img');
                    if (svgOrImg) {
                        qrHtml = svgOrImg.outerHTML;
                    }
                }
                
                if (!qrHtml) {
                    qrHtml = `<img src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${code}" style="width: 250px; height: 250px;" />`;
                }
                
                Swal.fire({
                    title: `QR Code : ${name}`,
                    text: `Référence : ${code}`,
                    html: `
                        <div style="display:flex; justify-content:center; margin-top:20px;" id="swal-qr-container">${qrHtml}</div>
                        <div style="display:flex; justify-content:center; gap:10px; margin-top:20px;">
                            <button id="swal-btn-download" class="btn btn-yellow" style="padding: 8px 16px; font-size: 13px;">
                                <i class="fa-solid fa-download"></i> Télécharger
                            </button>
                            <button id="swal-btn-print" class="btn btn-primary" style="padding: 8px 16px; font-size: 13px; background-color: #059669;">
                                <i class="fa-solid fa-print"></i> Imprimer
                            </button>
                        </div>
                    `,
                    confirmButtonColor: '#64748b',
                    confirmButtonText: 'Fermer',
                    didOpen: () => {
                        const downloadBtn = document.getElementById('swal-btn-download');
                        const printBtn = document.getElementById('swal-btn-print');
                        const container = document.getElementById('swal-qr-container');
                        
                        // Resize SVG in modal if present
                        const svgEl = container.querySelector('svg');
                        if (svgEl) {
                            svgEl.setAttribute('width', '250');
                            svgEl.setAttribute('height', '250');
                            svgEl.style.width = '250px';
                            svgEl.style.height = '250px';
                        }
                        
                        downloadBtn.addEventListener('click', () => {
                            const modalSvg = container.querySelector('svg');
                            if (modalSvg) {
                                const svgString = new XMLSerializer().serializeToString(modalSvg);
                                const svgBlob = new Blob([svgString], { type: 'image/svg+xml;charset=utf-8' });
                                const blobUrl = URL.createObjectURL(svgBlob);
                                const link = document.createElement('a');
                                link.href = blobUrl;
                                link.download = `QR_${name.replace(/[^a-z0-9]/gi, '_').toLowerCase()}.svg`;
                                document.body.appendChild(link);
                                link.click();
                                document.body.removeChild(link);
                            } else {
                                const modalImg = container.querySelector('img');
                                if (modalImg) {
                                    const imageUrl = modalImg.src;
                                    const loadingAlert = Swal.fire({
                                        title: 'Téléchargement...',
                                        didOpen: () => {
                                            Swal.showLoading();
                                        },
                                        allowOutsideClick: false,
                                        showConfirmButton: false
                                    });
                                    
                                    fetch(imageUrl)
                                        .then(response => response.blob())
                                        .then(blob => {
                                            const blobUrl = URL.createObjectURL(blob);
                                            const link = document.createElement('a');
                                            link.href = blobUrl;
                                            link.download = `QR_${name.replace(/[^a-z0-9]/gi, '_').toLowerCase()}.png`;
                                            document.body.appendChild(link);
                                            link.click();
                                            document.body.removeChild(link);
                                            loadingAlert.close();
                                        })
                                        .catch(err => {
                                            console.error(err);
                                            Swal.fire('Erreur', 'Impossible de télécharger le fichier', 'error');
                                        });
                                }
                            }
                        });
                        
                        printBtn.addEventListener('click', () => {
                            const modalSvg = container.querySelector('svg');
                            const modalImg = container.querySelector('img');
                            if (modalSvg) {
                                printQR(modalSvg.outerHTML, false);
                            } else if (modalImg) {
                                printQR(modalImg.src, true);
                            }
                        });
                    }
                });
            }

            function printQR(qrHtmlOrUrl, isImage = false) {
                let printArea = document.getElementById('qr-print-area');
                if (!printArea) {
                    printArea = document.createElement('div');
                    printArea.id = 'qr-print-area';
                    document.body.appendChild(printArea);
                }
                
                let qrContent = qrHtmlOrUrl;
                if (isImage) {
                    qrContent = '<img src="' + qrHtmlOrUrl + '" />';
                }
                
                printArea.innerHTML = qrContent;
                document.body.classList.add('printing-qr-only');
                
                setTimeout(() => {
                    window.print();
                    
                    const cleanup = () => {
                        document.body.classList.remove('printing-qr-only');
                        printArea.innerHTML = '';
                    };
                    
                    if ('onafterprint' in window) {
                        window.addEventListener('afterprint', cleanup, { once: true });
                    } else {
                        setTimeout(cleanup, 500);
                    }
                }, 50);
            }
        </script>
    @endpush

@endsection

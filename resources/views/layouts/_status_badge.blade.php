@php
    $statusConfig = [
        'draft' => ['label' => 'Draft', 'class' => 'badge-gray'],
        'pending' => ['label' => 'Menunggu Admin', 'class' => 'badge-warning'],
        'pending_sa' => ['label' => 'Menunggu Super Admin', 'class' => 'badge-info'],
        'approved' => ['label' => 'Disetujui', 'class' => 'badge-success'],
        'approved_revisi' => ['label' => 'Disetujui (Revisi)', 'class' => 'badge-success'],
        'ditunda' => ['label' => 'Ditunda', 'class' => 'badge-purple'],
        'ditolak' => ['label' => 'Ditolak', 'class' => 'badge-danger'],
    ];
    $cfg = $statusConfig[$status] ?? ['label' => $status, 'class' => 'badge-gray'];
@endphp

<span class="badge {{ $cfg['class'] }}">{{ $cfg['label'] }}</span>

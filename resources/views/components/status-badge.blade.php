{{--
    Komponen: status badge
    Usage: <x-status-badge :status="$model->status" />

    Kamu bisa extend $map sesuai model masing-masing
--}}
@props(['status'])

@php
$map = [
    // universal
    'draft'            => ['label' => 'Draft',            'class' => 'badge-gray'],
    'pending'          => ['label' => 'Menunggu',         'class' => 'badge-warning'],
    'pending_approval' => ['label' => 'Menunggu Approval','class' => 'badge-warning'],
    'pending_finance'  => ['label' => 'Menunggu Finance', 'class' => 'badge-warning'],
    'approved'         => ['label' => 'Disetujui',        'class' => 'badge-success'],
    'rejected'         => ['label' => 'Ditolak',          'class' => 'badge-danger'],
    'ditolak'          => ['label' => 'Ditolak',          'class' => 'badge-danger'],
    'in_progress'      => ['label' => 'Berjalan',         'class' => 'badge-info'],
    'in_transit'       => ['label' => 'Dalam Perjalanan', 'class' => 'badge-info'],
    'completed'        => ['label' => 'Selesai',          'class' => 'badge-success'],
    'received'         => ['label' => 'Diterima',         'class' => 'badge-success'],
    'verified'         => ['label' => 'Terverifikasi',    'class' => 'badge-success'],
    'submitted'        => ['label' => 'Terkirim',         'class' => 'badge-info'],
    'partial'          => ['label' => 'Sebagian',         'class' => 'badge-warning'],
    'confirmed'        => ['label' => 'Dikonfirmasi',     'class' => 'badge-info'],
    'cancelled'        => ['label' => 'Dibatalkan',       'class' => 'badge-danger'],
    // active / inactive
    '1'                => ['label' => 'Aktif',            'class' => 'badge-success'],
    '0'                => ['label' => 'Nonaktif',         'class' => 'badge-gray'],
];

$item  = $map[$status] ?? ['label' => ucfirst(str_replace('_',' ',$status)), 'class' => 'badge-gray'];
@endphp

<span class="badge {{ $item['class'] }}">{{ $item['label'] }}</span>

@php
    $map = [
        'draft'           => ['bg-gray-100', 'text-gray-600', 'Draft'],
        'pending'         => ['bg-orange-100', 'text-orange-700', 'Menunggu Admin'],
        'pending_sa'      => ['bg-blue-100', 'text-blue-700', 'Menunggu Super Admin'],
        'approved'        => ['bg-green-100', 'text-green-700', 'Disetujui'],
        'approved_revisi' => ['bg-teal-100', 'text-teal-700', 'Disetujui (Revisi)'],
        'ditolak'         => ['bg-red-100', 'text-red-700', 'Ditolak'],
        'ditunda'         => ['bg-purple-100', 'text-purple-700', 'Ditunda'],
    ];
    [$bg, $text, $label] = $map[$status] ?? ['bg-gray-100', 'text-gray-600', $status];
@endphp
<span class="px-2 py-0.5 rounded text-xs font-medium {{ $bg }} {{ $text }}">{{ $label }}</span>

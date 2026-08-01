# Backend (Warehouse Dashboard) — Placeholder

Folder ini disiapkan untuk tampilan web **warehouse management** yang akan
dipakai setelah user login (Admin, Admin Gudang, Super Admin, dsb).

Struktur yang disiapkan:

```
backend/
├── css/       -> stylesheet khusus dashboard
├── js/        -> script khusus dashboard (chart, tabel, form, dll)
├── img/       -> logo, icon, gambar dashboard
└── (nanti diisi file .html / diubah ke Blade view per halaman:
     dashboard.html, products.html, warehouses.html,
     purchase-orders.html, sales-orders.html, stock-transfers.html,
     stock-opname.html, reports.html, dst — mengikuti tabel migration)
```

Catatan penting:
- Folder `frontend/` = halaman publik (landing page), **tidak butuh login**.
- Folder `backend/` = halaman setelah login, **wajib dilindungi middleware `auth`**
  di Laravel (jangan taruh logic penting di sini kalau nanti full pakai Blade +
  route Laravel — folder public hanya untuk aset statis, bukan tempat proses data).

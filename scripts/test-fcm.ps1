# ============================================================
# Tes FCM lengkap (jalankan dari folder laravel_backend_warehouse):
#   .\scripts\test-fcm.ps1                 -> ke user ID 2 (admin@test.com)
#   .\scripts\test-fcm.ps1 -UserId 6 -Type po
#   .\scripts\test-fcm.ps1 -All
#
# HP harus sedang login sebagai user tujuan.
# ============================================================
param(
    [int]$UserId = 2,
    [string]$Type = "rab",
    [switch]$All
)

$root = Split-Path -Parent $PSScriptRoot
Set-Location $root

php artisan config:clear | Out-Null

Write-Host "=== 1. Cek kesiapan ==="
php artisan fcm:check $UserId
if ($LASTEXITCODE -ne 0) {
    Write-Host ""
    Write-Host "Perbaiki dulu masalah di atas, lalu jalankan ulang skrip ini."
    exit 1
}

Write-Host ""
Write-Host "=== 2. Tes kirim langsung ke HP ==="
php artisan fcm:test $UserId

Write-Host ""
Write-Host "=== 3. Tes jalur asli (NotificationService) ==="
if ($All) {
    php artisan notif:demo $UserId --all
}
else {
    php artisan notif:demo $UserId "--type=$Type"
}

Write-Host ""
Write-Host "=== Log FCM terakhir ==="
$log = Join-Path $root "storage\logs\laravel.log"
if (Test-Path $log) {
    $hits = Get-Content $log -Tail 80 | Select-String -Pattern "FCM|Push"
    if ($hits) { $hits | Select-Object -Last 10 } else { Write-Host "(tidak ada error FCM)" }
}

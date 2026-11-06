# ============================================================
# Setup FCM sekali jalan (jalankan dari folder laravel_backend_warehouse):
#   .\scripts\setup-fcm.ps1
#   .\scripts\setup-fcm.ps1 -KeyFile "C:\path\ucta-warehouse-firebase-adminsdk-xxxx.json"
#
# Yang dilakukan:
#   1. Memindahkan file kunci Firebase ke storage\app\firebase-credentials.json
#   2. Menambahkan FIREBASE_CREDENTIALS & FCM_ANDROID_CHANNEL ke .env (kalau belum ada)
#   3. Menambahkan file kunci ke .gitignore (kalau belum ada)
#   4. php artisan config:clear + php artisan fcm:check
# ============================================================
param(
    [string]$KeyFile = ""
)

$root = Split-Path -Parent $PSScriptRoot
Set-Location $root

$dest = Join-Path $root "storage\app\firebase-credentials.json"

# 1. File kunci
if (Test-Path $dest) {
    Write-Host "[OK] Kunci sudah ada di storage\app\firebase-credentials.json"
}
else {
    if ($KeyFile -eq "") {
        # cari otomatis di folder induk lalu Downloads
        $candidates = @((Split-Path -Parent $root), (Join-Path $env:USERPROFILE "Downloads"))
        foreach ($dir in $candidates) {
            $found = Get-ChildItem -Path $dir -Filter "*firebase-adminsdk*.json" -ErrorAction SilentlyContinue |
                Sort-Object LastWriteTime -Descending | Select-Object -First 1
            if ($found) { $KeyFile = $found.FullName; break }
        }
    }

    if (($KeyFile -ne "") -and (Test-Path $KeyFile)) {
        Move-Item -Path $KeyFile -Destination $dest
        Write-Host "[OK] Kunci dipindahkan dari $KeyFile"
    }
    else {
        Write-Host "[GAGAL] File kunci tidak ditemukan. Jalankan ulang dengan:"
        Write-Host '        .\scripts\setup-fcm.ps1 -KeyFile "C:\path\nama-file-adminsdk.json"'
        exit 1
    }
}

# 2. .env
$envFile = Join-Path $root ".env"
if (Test-Path $envFile) {
    $envText = Get-Content $envFile -Raw
    $lines = @(
        "FIREBASE_CREDENTIALS=storage/app/firebase-credentials.json",
        "FCM_ANDROID_CHANNEL=high_importance_channel"
    )
    foreach ($line in $lines) {
        $key = $line.Split("=")[0]
        if ($envText -notmatch ("(?m)^" + $key + "=")) {
            Add-Content -Path $envFile -Value ("`r`n" + $line)
            Write-Host "[OK] .env ditambah: $key"
        }
        else {
            Write-Host "[OK] .env sudah punya $key"
        }
    }
}
else {
    Write-Host "[!] File .env tidak ditemukan di $root"
}

# 3. .gitignore
$gitignore = Join-Path $root ".gitignore"
$rule = "/storage/app/firebase-credentials.json"
$already = $false
if (Test-Path $gitignore) {
    $already = Select-String -Path $gitignore -SimpleMatch -Pattern $rule -Quiet
}
if (-not $already) {
    Add-Content -Path $gitignore -Value ("`r`n" + $rule)
    Write-Host "[OK] .gitignore ditambah: $rule"
}
else {
    Write-Host "[OK] .gitignore sudah berisi aturan kunci"
}

# 4. Muat ulang config + cek
php artisan config:clear
php artisan fcm:check

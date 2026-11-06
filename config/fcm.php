<?php

/*
| File service account dari Firebase Console:
| Project settings → Service accounts → Generate new private key
| Simpan di: storage/app/firebase-credentials.json (JANGAN di-commit, JANGAN di public/)
|
| Urutan yang dibaca: FCM_CREDENTIALS → FIREBASE_CREDENTIALS → bawaan.
| Path relatif otomatis jadi path lengkap dari folder project.
*/

$fcmPath = env('FCM_CREDENTIALS') ?: env('FIREBASE_CREDENTIALS') ?: 'storage/app/firebase-credentials.json';

$isAbsolute = str_starts_with($fcmPath, '/') || preg_match('/^[A-Za-z]:[\\\\\/]/', $fcmPath);

return [
    'credentials' => $isAbsolute ? $fcmPath : base_path($fcmPath),

    // Kosongkan = ambil dari file service account
    'project_id' => env('FCM_PROJECT_ID'),

    // Harus SAMA dengan channel di Flutter & AndroidManifest
    'android_channel_id' => env('FCM_ANDROID_CHANNEL', 'high_importance_channel'),
];

{{--
    1) Tambahkan meta csrf-token di <head> landing/index.blade.php kalau belum ada:
       <meta name="csrf-token" content="{{ csrf_token() }}">

    2) Ganti tombol "Contact Sales" yang lama:
       <a href="#contact" class="btn-ghost-white btn-xl ripple">
           <i data-lucide="phone"></i>
           Contact Sales
       </a>

       jadi:
       <button type="button" class="btn-ghost-white btn-xl ripple" onclick="openContactModal()">
           <i data-lucide="phone"></i>
           Contact Sales
       </button>

    3) Taruh blok modal di bawah ini tepat sebelum </body>, sebelum <script src="{{ asset('frontend/js/script.js') }}">
--}}

<div id="contactModalBackdrop" style="display:none;position:fixed;inset:0;background:rgba(2,6,23,.6);z-index:999;align-items:center;justify-content:center;padding:16px;">
    <div style="background:#fff;border-radius:16px;width:100%;max-width:440px;max-height:90vh;overflow-y:auto;">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:18px 22px;border-bottom:1px solid #e2e8f0;">
            <h3 style="font-family:'Inter',sans-serif;font-weight:700;font-size:1.05rem;color:#0f172a;margin:0;">Hubungi Sales Kami</h3>
            <button type="button" onclick="closeContactModal()" aria-label="Tutup" style="background:none;border:none;font-size:1.25rem;line-height:1;cursor:pointer;color:#64748b;">&times;</button>
        </div>

        <form id="contactLeadForm" style="padding:20px 22px;display:flex;flex-direction:column;gap:14px;">
            {{-- Honeypot anti-spam, harus tetap kosong --}}
            <input type="text" name="website" tabindex="-1" autocomplete="off" style="position:absolute;left:-9999px;">

            <div>
                <label style="display:block;font-size:.8rem;font-weight:500;color:#374151;margin-bottom:4px;">Nama</label>
                <input type="text" name="name" required
                       style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:.9rem;outline:none;">
            </div>
            <div>
                <label style="display:block;font-size:.8rem;font-weight:500;color:#374151;margin-bottom:4px;">Email</label>
                <input type="email" name="email" required
                       style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:.9rem;outline:none;">
            </div>
            <div>
                <label style="display:block;font-size:.8rem;font-weight:500;color:#374151;margin-bottom:4px;">No. Telepon (opsional)</label>
                <input type="text" name="phone"
                       style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:.9rem;outline:none;">
            </div>
            <div>
                <label style="display:block;font-size:.8rem;font-weight:500;color:#374151;margin-bottom:4px;">Nama Perusahaan (opsional)</label>
                <input type="text" name="company"
                       style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:.9rem;outline:none;">
            </div>
            <div>
                <label style="display:block;font-size:.8rem;font-weight:500;color:#374151;margin-bottom:4px;">Pesan</label>
                <textarea name="message" rows="3" required
                          style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:.9rem;outline:none;resize:none;"
                          placeholder="Ceritakan kebutuhan gudang Anda..."></textarea>
            </div>

            <div id="contactFormMessage" style="display:none;padding:10px 12px;border-radius:8px;font-size:.85rem;"></div>

            <button type="submit" id="contactFormSubmitBtn" class="btn-primary" style="justify-content:center;">
                Kirim Pesan
            </button>
        </form>
    </div>
</div>

<script>
function openContactModal() {
    document.getElementById('contactModalBackdrop').style.display = 'flex';
}
function closeContactModal() {
    document.getElementById('contactModalBackdrop').style.display = 'none';
}
document.getElementById('contactModalBackdrop').addEventListener('click', function (e) {
    if (e.target === this) closeContactModal();
});

document.getElementById('contactLeadForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const form = e.target;
    const btn = document.getElementById('contactFormSubmitBtn');
    const msgBox = document.getElementById('contactFormMessage');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    btn.disabled = true;
    btn.textContent = 'Mengirim...';
    msgBox.style.display = 'none';

    fetch("{{ route('landing.contact.store') }}", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
        },
        body: new FormData(form),
    })
    .then(async (res) => {
        const data = await res.json().catch(() => ({}));
        if (!res.ok) throw data;
        return data;
    })
    .then((data) => {
        msgBox.style.display = 'block';
        msgBox.style.background = '#ecfdf5';
        msgBox.style.color = '#065f46';
        msgBox.style.border = '1px solid #6ee7b7';
        msgBox.textContent = data.message || 'Terima kasih! Pesan Anda sudah terkirim.';
        form.reset();
        setTimeout(closeContactModal, 2200);
    })
    .catch((err) => {
        msgBox.style.display = 'block';
        msgBox.style.background = '#fef2f2';
        msgBox.style.color = '#991b1b';
        msgBox.style.border = '1px solid #fca5a5';
        msgBox.textContent = err.message || 'Gagal mengirim pesan. Periksa kembali isian Anda.';
    })
    .finally(() => {
        btn.disabled = false;
        btn.textContent = 'Kirim Pesan';
    });
});
</script>

@php $s = $supplier ?? null; @endphp

<div class="admin-form-grid">
    <div>
        <label class="admin-label">Nama Supplier *</label>
        <input type="text" name="name" value="{{ old('name', $s?->name) }}" required
            class="admin-input @error('name') is-invalid @enderror">
        @error('name') <p class="admin-input-error">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="admin-label">Kode *</label>
        <input type="text" name="code" value="{{ old('code', $s?->code) }}" required
            class="admin-input @error('code') is-invalid @enderror">
        @error('code') <p class="admin-input-error">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="admin-label">Contact Person</label>
        <input type="text" name="contact_person" value="{{ old('contact_person', $s?->contact_person) }}"
            class="admin-input">
    </div>
    <div>
        <label class="admin-label">Telepon</label>
        <input type="text" name="phone" value="{{ old('phone', $s?->phone) }}" class="admin-input">
    </div>
    <div>
        <label class="admin-label">Email</label>
        <input type="email" name="email" value="{{ old('email', $s?->email) }}"
            class="admin-input @error('email') is-invalid @enderror">
        @error('email') <p class="admin-input-error">{{ $message }}</p> @enderror
    </div>
    <div>
        <label class="admin-label">Kota</label>
        <input type="text" name="city" value="{{ old('city', $s?->city) }}" class="admin-input">
    </div>
    <div class="span-2">
        <label class="admin-label">Alamat</label>
        <textarea name="address" rows="2" class="admin-textarea">{{ old('address', $s?->address) }}</textarea>
    </div>
    <div>
        <label class="admin-label">Provinsi</label>
        <input type="text" name="province" value="{{ old('province', $s?->province) }}" class="admin-input">
    </div>
    <div>
        <label class="admin-label">NPWP</label>
        <input type="text" name="npwp" value="{{ old('npwp', $s?->npwp) }}" class="admin-input">
    </div>
    <div>
        <label class="admin-label">Nama Bank</label>
        <input type="text" name="bank_name" value="{{ old('bank_name', $s?->bank_name) }}" class="admin-input">
    </div>
    <div>
        <label class="admin-label">No. Rekening</label>
        <input type="text" name="bank_account" value="{{ old('bank_account', $s?->bank_account) }}" class="admin-input">
    </div>
    <div class="span-2">
        <label class="admin-label">Nama Pemilik Rekening</label>
        <input type="text" name="bank_account_name" value="{{ old('bank_account_name', $s?->bank_account_name) }}"
            class="admin-input">
    </div>
    <div class="span-2">
        <label class="admin-label">Catatan</label>
        <textarea name="notes" rows="2" class="admin-textarea">{{ old('notes', $s?->notes) }}</textarea>
    </div>
    <div class="span-2">
        <label class="admin-checkbox-label">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $s?->is_active ?? true))>
            Supplier aktif
        </label>
    </div>
</div>

<div class="admin-form-actions">
    <button class="btn-primary ripple">Simpan</button>
    <a href="{{ route('admin.suppliers.index') }}" class="btn-ghost">Batal</a>
</div>
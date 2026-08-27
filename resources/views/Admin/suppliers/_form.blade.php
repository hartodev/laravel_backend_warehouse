@if ($errors->any())
<div class="admin-alert admin-alert-error">
    <div>
        @foreach ($errors->all() as $error)
        <div>{{ $error }}</div>
        @endforeach
    </div>
</div>
@endif

<div class="admin-form-grid" style="grid-template-columns:repeat(2,1fr);margin-bottom:20px;">
    <div>
        <label class="admin-label">Nama Supplier</label>
        <input type="text" name="name" value="{{ old('name', $supplier->name ?? '') }}" required class="admin-input">
    </div>
    <div>
        <label class="admin-label">Kode</label>
        <input type="text" name="code" value="{{ old('code', $supplier->code ?? '') }}" required class="admin-input">
    </div>
    <div>
        <label class="admin-label">Contact Person</label>
        <input type="text" name="contact_person" value="{{ old('contact_person', $supplier->contact_person ?? '') }}" class="admin-input">
    </div>
    <div>
        <label class="admin-label">Telepon</label>
        <input type="text" name="phone" value="{{ old('phone', $supplier->phone ?? '') }}" class="admin-input">
    </div>
    <div>
        <label class="admin-label">Email</label>
        <input type="email" name="email" value="{{ old('email', $supplier->email ?? '') }}" class="admin-input">
    </div>
    <div>
        <label class="admin-label">Status</label>
        <select name="is_active" class="admin-select">
            <option value="1" @selected(old('is_active', $supplier->is_active ?? true) == 1)>Aktif</option>
            <option value="0" @selected(old('is_active', $supplier->is_active ?? true) == 0)>Nonaktif</option>
        </select>
    </div>
    <div style="grid-column:span 2;">
        <label class="admin-label">Alamat</label>
        <textarea name="address" class="admin-textarea">{{ old('address', $supplier->address ?? '') }}</textarea>
    </div>
    <div>
        <label class="admin-label">Kota</label>
        <input type="text" name="city" value="{{ old('city', $supplier->city ?? '') }}" class="admin-input">
    </div>
    <div>
        <label class="admin-label">Provinsi</label>
        <input type="text" name="province" value="{{ old('province', $supplier->province ?? '') }}" class="admin-input">
    </div>
    <div>
        <label class="admin-label">NPWP</label>
        <input type="text" name="npwp" value="{{ old('npwp', $supplier->npwp ?? '') }}" class="admin-input">
    </div>
    <div></div>
    <div>
        <label class="admin-label">Nama Bank</label>
        <input type="text" name="bank_name" value="{{ old('bank_name', $supplier->bank_name ?? '') }}" class="admin-input">
    </div>
    <div>
        <label class="admin-label">No. Rekening</label>
        <input type="text" name="bank_account" value="{{ old('bank_account', $supplier->bank_account ?? '') }}" class="admin-input">
    </div>
    <div style="grid-column:span 2;">
        <label class="admin-label">Nama Pemilik Rekening</label>
        <input type="text" name="bank_account_name" value="{{ old('bank_account_name', $supplier->bank_account_name ?? '') }}" class="admin-input">
    </div>
    <div style="grid-column:span 2;">
        <label class="admin-label">Catatan</label>
        <textarea name="notes" class="admin-textarea">{{ old('notes', $supplier->notes ?? '') }}</textarea>
    </div>
</div>

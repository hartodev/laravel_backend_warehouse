{{--
    Reusable confirmation modal component.

    Usage example (delete):
    <x-confirm-modal
        id="delete-po-{{ $po->id }}"
        title="Hapus Purchase Order?"
        message="Data PO #{{ $po->code }} akan dihapus permanen dan tidak bisa dikembalikan."
        :action="route('purchase-orders.destroy', $po)"
        method="DELETE"
        confirm-text="Ya, Hapus"
        confirm-class="btn-danger"
    />

    Trigger button anywhere on the page:
    <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-modal-delete-po-{{ $po->id }}'))" class="btn btn-danger btn-sm">
        Hapus
    </button>

    Or simpler, use the built-in trigger slot:
    <x-confirm-modal id="delete-po-1" title="Hapus?" message="..." :action="..." method="DELETE">
        <x-slot:trigger>
            <button type="button" class="btn btn-danger btn-sm">Hapus</button>
        </x-slot:trigger>
    </x-confirm-modal>
--}}

@props([
    'id' => 'confirm-' . uniqid(),
    'title' => 'Konfirmasi',
    'message' => 'Apakah Anda yakin?',
    'action' => null,
    'method' => 'POST',
    'confirmText' => 'Ya, Lanjutkan',
    'cancelText' => 'Batal',
    'confirmClass' => 'btn-danger',
])

<div x-data="{ open: false }" x-on:open-modal-{{ $id }}.window="open = true"
    x-on:keydown.escape.window="open = false">
    {{-- Optional inline trigger --}}
    @isset($trigger)
        <span onclick="window.dispatchEvent(new CustomEvent('open-modal-{{ $id }}'))"
            class="inline-block cursor-pointer">
            {{ $trigger }}
        </span>
    @endisset

    <template x-teleport="body">
        <div x-show="open" x-cloak class="modal-backdrop" x-transition.opacity @click.self="open = false">
            <div class="modal-box" x-show="open" x-transition>
                <div class="card-body">
                    <h3 class="page-title text-lg mb-2">{{ $title }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $message }}</p>
                </div>

                <div class="card-footer flex items-center justify-end gap-2">
                    <button type="button" @click="open = false" class="btn btn-secondary btn-sm">
                        {{ $cancelText }}
                    </button>

                    @if ($action)
                        <form method="POST" action="{{ $action }}">
                            @csrf
                            @if (strtoupper($method) !== 'POST')
                                @method($method)
                            @endif
                            <button type="submit" class="btn {{ $confirmClass }} btn-sm">
                                {{ $confirmText }}
                            </button>
                        </form>
                    @else
                        <button type="button" @click="open = false; $dispatch('confirmed-{{ $id }}')"
                            class="btn {{ $confirmClass }} btn-sm">
                            {{ $confirmText }}
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </template>
</div>




{{--
    Reusable confirmation modal component.

    Usage example (delete):
    <x-confirm-modal
        id="delete-po-{{ $po->id }}"
        title="Hapus Purchase Order?"
        message="Data PO #{{ $po->code }} akan dihapus permanen dan tidak bisa dikembalikan."
        :action="route('purchase-orders.destroy', $po)"
        method="DELETE"
        confirm-text="Ya, Hapus"
        confirm-class="btn-danger"
    />

    Trigger button anywhere on the page:
    <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-modal-delete-po-{{ $po->id }}'))" class="btn btn-danger btn-sm">
        Hapus
    </button>

    Or simpler, use the built-in trigger slot:
    <x-confirm-modal id="delete-po-1" title="Hapus?" message="..." :action="..." method="DELETE">
        <x-slot:trigger>
            <button type="button" class="btn btn-danger btn-sm">Hapus</button>
        </x-slot:trigger>
    </x-confirm-modal>
--}}

@props([
    'id' => 'confirm-' . uniqid(),
    'title' => 'Konfirmasi',
    'message' => 'Apakah Anda yakin?',
    'action' => null,
    'method' => 'POST',
    'confirmText' => 'Ya, Lanjutkan',
    'cancelText' => 'Batal',
    'confirmClass' => 'btn-danger',
])

<div x-data="{ open: false }" x-on:open-modal-{{ $id }}.window="open = true"
    x-on:keydown.escape.window="open = false">
    {{-- Optional inline trigger --}}
    @isset($trigger)
        <span onclick="window.dispatchEvent(new CustomEvent('open-modal-{{ $id }}'))"
            class="inline-block cursor-pointer">
            {{ $trigger }}
        </span>
    @endisset

    <template x-teleport="body">
        <div x-show="open" x-cloak class="modal-backdrop" x-transition.opacity @click.self="open = false">
            <div class="modal-box" x-show="open" x-transition>
                <div class="card-body">
                    <h3 class="page-title text-lg mb-2">{{ $title }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $message }}</p>
                </div>

                <div class="card-footer flex items-center justify-end gap-2">
                    <button type="button" @click="open = false" class="btn btn-secondary btn-sm">
                        {{ $cancelText }}
                    </button>

                    @if ($action)
                        <form method="POST" action="{{ $action }}">
                            @csrf
                            @if (strtoupper($method) !== 'POST')
                                @method($method)
                            @endif
                            <button type="submit" class="btn {{ $confirmClass }} btn-sm">
                                {{ $confirmText }}
                            </button>
                        </form>
                    @else
                        <button type="button" @click="open = false; $dispatch('confirmed-{{ $id }}')"
                            class="btn {{ $confirmClass }} btn-sm">
                            {{ $confirmText }}
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </template>
</div>



{{--
    Reusable confirmation modal component.

    Usage example (delete):
    <x-confirm-modal
        id="delete-po-{{ $po->id }}"
        title="Hapus Purchase Order?"
        message="Data PO #{{ $po->code }} akan dihapus permanen dan tidak bisa dikembalikan."
        :action="route('purchase-orders.destroy', $po)"
        method="DELETE"
        confirm-text="Ya, Hapus"
        confirm-class="btn-danger"
    />

    Trigger button anywhere on the page:
    <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-modal-delete-po-{{ $po->id }}'))" class="btn btn-danger btn-sm">
        Hapus
    </button>

    Or simpler, use the built-in trigger slot:
    <x-confirm-modal id="delete-po-1" title="Hapus?" message="..." :action="..." method="DELETE">
        <x-slot:trigger>
            <button type="button" class="btn btn-danger btn-sm">Hapus</button>
        </x-slot:trigger>
    </x-confirm-modal>
--}}

@props([
    'id' => 'confirm-' . uniqid(),
    'title' => 'Konfirmasi',
    'message' => 'Apakah Anda yakin?',
    'action' => null,
    'method' => 'POST',
    'confirmText' => 'Ya, Lanjutkan',
    'cancelText' => 'Batal',
    'confirmClass' => 'btn-danger',
])

<div x-data="{ open: false }" x-on:open-modal-{{ $id }}.window="open = true"
    x-on:keydown.escape.window="open = false">
    {{-- Optional inline trigger --}}
    @isset($trigger)
        <span onclick="window.dispatchEvent(new CustomEvent('open-modal-{{ $id }}'))"
            class="inline-block cursor-pointer">
            {{ $trigger }}
        </span>
    @endisset

    <template x-teleport="body">
        <div x-show="open" x-cloak class="modal-backdrop" x-transition.opacity @click.self="open = false">
            <div class="modal-box" x-show="open" x-transition>
                <div class="card-body">
                    <h3 class="page-title text-lg mb-2">{{ $title }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $message }}</p>
                </div>

                <div class="card-footer flex items-center justify-end gap-2">
                    <button type="button" @click="open = false" class="btn btn-secondary btn-sm">
                        {{ $cancelText }}
                    </button>

                    @if ($action)
                        <form method="POST" action="{{ $action }}">
                            @csrf
                            @if (strtoupper($method) !== 'POST')
                                @method($method)
                            @endif
                            <button type="submit" class="btn {{ $confirmClass }} btn-sm">
                                {{ $confirmText }}
                            </button>
                        </form>
                    @else
                        <button type="button" @click="open = false; $dispatch('confirmed-{{ $id }}')"
                            class="btn {{ $confirmClass }} btn-sm">
                            {{ $confirmText }}
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </template>
</div>





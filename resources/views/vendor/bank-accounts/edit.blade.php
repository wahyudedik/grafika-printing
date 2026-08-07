@extends('layouts.vendor')

@section('title', 'Edit Detail Rekening')

@section('content')
<div x-data="bankForm()" class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <x-ui.breadcrumb :items="[['label' => 'Kelola Rekening Bank', 'url' => route('vendor.bank-accounts.index')], ['label' => 'Edit Detail Rekening']]" />

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Detail Rekening - {{ ucfirst($type) }}</h1>
            <p class="mt-1 text-sm text-gray-500">Perbarui informasi detail rekening</p>
        </div>
        <x.ui.button href="{{ route('vendor.bank-accounts.index') }}" variant="outline">
            <i class="fas fa-arrow-left mr-2"></i>Kembali
        </x.ui.button>
    </div>

    {{-- Form Card --}}
    <div class="bg-white rounded-xl border border-gray-200">
        <div class="px-5 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-edit mr-2 text-primary-600"></i>Form Edit Detail Rekening
            </h2>
        </div>
        <div class="p-5">
            <form action="{{ route('vendor.bank-accounts.update', $type) }}" method="POST">
                @csrf
                @method('PUT')

                @if ($type === 'primary' || $type === 'secondary')
                    {{-- Bank Account Fields --}}
                    <div class="space-y-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label for="bank_name" class="block text-sm font-medium text-gray-700 mb-1">
                                    Nama Bank <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="bank_name"
                                    name="bank_name"
                                    required
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('bank_name') border-red-500 @enderror"
                                >
                                    <option value="">Pilih Bank</option>
                                </select>
                                @error('bank_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="bank_code" class="block text-sm font-medium text-gray-700 mb-1">Kode Bank</label>
                                <input
                                    type="text"
                                    id="bank_code"
                                    name="bank_code"
                                    value="{{ old('bank_code', $vendor->{($type === 'primary' ? 'primary' : 'secondary') . '_bank_code'}) }}"
                                    readonly
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm bg-gray-50 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('bank_code') border-red-500 @enderror"
                                >
                                @error('bank_code')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label for="account_number" class="block text-sm font-medium text-gray-700 mb-1">
                                    Nomor Rekening <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="account_number"
                                    name="account_number"
                                    value="{{ old('account_number', $vendor->{($type === 'primary' ? 'primary' : 'secondary') . '_account_number'}) }}"
                                    placeholder="Masukkan nomor rekening"
                                    required
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('account_number') border-red-500 @enderror"
                                >
                                @error('account_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="account_name" class="block text-sm font-medium text-gray-700 mb-1">
                                    Nama Pemilik Rekening <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="account_name"
                                    name="account_name"
                                    value="{{ old('account_name', $vendor->{($type === 'primary' ? 'primary' : 'secondary') . '_account_name'}) }}"
                                    placeholder="Masukkan nama pemilik rekening"
                                    required
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('account_name') border-red-500 @enderror"
                                >
                                @error('account_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                @elseif($type === 'ewallet')
                    {{-- E-Wallet Fields --}}
                    <div class="space-y-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label for="ewallet_provider" class="block text-sm font-medium text-gray-700 mb-1">
                                    Provider E-Wallet <span class="text-red-500">*</span>
                                </label>
                                <select
                                    id="ewallet_provider"
                                    name="ewallet_provider"
                                    required
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('ewallet_provider') border-red-500 @enderror"
                                >
                                    <option value="">Pilih Provider</option>
                                </select>
                                @error('ewallet_provider')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="ewallet_number" class="block text-sm font-medium text-gray-700 mb-1">
                                    Nomor E-Wallet <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="ewallet_number"
                                    name="ewallet_number"
                                    value="{{ old('ewallet_number', $vendor->ewallet_number) }}"
                                    placeholder="Masukkan nomor e-wallet"
                                    required
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('ewallet_number') border-red-500 @enderror"
                                >
                                @error('ewallet_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label for="ewallet_name" class="block text-sm font-medium text-gray-700 mb-1">
                                    Nama Pemilik E-Wallet <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="ewallet_name"
                                    name="ewallet_name"
                                    value="{{ old('ewallet_name', $vendor->ewallet_name) }}"
                                    placeholder="Masukkan nama pemilik e-wallet"
                                    required
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('ewallet_name') border-red-500 @enderror"
                                >
                                @error('ewallet_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Bank Notes --}}
                <div class="mt-5">
                    <label for="bank_notes" class="block text-sm font-medium text-gray-700 mb-1">Catatan Rekening</label>
                    <textarea
                        id="bank_notes"
                        name="bank_notes"
                        rows="3"
                        placeholder="Masukkan catatan tambahan (opsional)"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('bank_notes') border-red-500 @enderror"
                    >{{ old('bank_notes', $vendor->bank_notes) }}</textarea>
                    @error('bank_notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Submit Buttons --}}
                <div class="mt-6 flex items-center gap-3">
                    <x.ui.button type="submit" variant="primary">
                        <i class="fas fa-save mr-2"></i>Update
                    </x.ui.button>
                    <x.ui.button href="{{ route('vendor.bank-accounts.index') }}" variant="outline">
                        <i class="fas fa-times mr-2"></i>Batal
                    </x.ui.button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function bankForm() {
        return {
            type: '{{ $type }}',
            init() {
                if (this.type === 'primary' || this.type === 'secondary') {
                    this.loadBanks();
                } else if (this.type === 'ewallet') {
                    this.loadEwalletProviders();
                }

                // Handle bank selection for bank code auto-fill
                this.$nextTick(() => {
                    const bankNameSelect = document.getElementById('bank_name');
                    const bankCodeInput = document.getElementById('bank_code');
                    if (bankNameSelect) {
                        bankNameSelect.addEventListener('change', function() {
                            const selectedOption = this.options[this.selectedIndex];
                            bankCodeInput.value = selectedOption.dataset.code || '';
                        });
                    }
                });
            },
            loadBanks() {
                fetch('/api/vendor/banks')
                    .then(response => response.json())
                    .then(banks => {
                        const bankNameSelect = document.getElementById('bank_name');
                        const bankCodeInput = document.getElementById('bank_code');
                        const currentBankName = '{{ $vendor->{($type === 'primary' ? 'primary' : 'secondary') . '_bank_name'} }}';
                        banks.forEach(bank => {
                            const option = document.createElement('option');
                            option.value = bank.name;
                            option.textContent = bank.name;
                            option.dataset.code = bank.code;
                            if (bank.name === currentBankName) {
                                option.selected = true;
                                bankCodeInput.value = bank.code;
                            }
                            bankNameSelect.appendChild(option);
                        });
                    })
                    .catch(error => console.error('Error loading banks:', error));
            },
            loadEwalletProviders() {
                fetch('/api/vendor/ewallet-providers')
                    .then(response => response.json())
                    .then(providers => {
                        const ewalletProviderSelect = document.getElementById('ewallet_provider');
                        const currentProvider = '{{ $vendor->ewallet_provider }}';
                        providers.forEach(provider => {
                            const option = document.createElement('option');
                            option.value = provider.name;
                            option.textContent = provider.name;
                            if (provider.name === currentProvider) {
                                option.selected = true;
                            }
                            ewalletProviderSelect.appendChild(option);
                        });
                    })
                    .catch(error => console.error('Error loading e-wallet providers:', error));
            }
        };
    }
</script>
@endsection

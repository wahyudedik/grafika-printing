@extends('layouts.vendor')

@section('title', 'Ajukan Penarikan Dana')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <div class="text-sm text-gray-500 font-medium">Vendor Panel</div>
        <h2 class="text-2xl font-bold text-gray-900">Ajukan Penarikan Dana</h2>
    </div>
    <a href="{{ route('vendor.withdrawal.index') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium text-sm">Kembali</a>
</div>

<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Form Card --}}
            <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Form Penarikan</h3>
                </div>
                <div class="p-5">
                    <form action="{{ route('vendor.withdrawal.store') }}" method="POST" id="withdrawalForm">
                        @csrf

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Penarikan (Rp)</label>
                            <input type="number" class="w-full rounded-lg border {{ $errors->has('amount') ? 'border-red-500' : 'border-gray-300' }} px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" name="amount" id="amount" value="{{ old('amount') }}" min="{{ $minWithdrawal }}" max="{{ $wallet->available_balance ?? 0 }}" required>
                            @error('amount')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            <p class="text-xs text-gray-500 mt-1">Minimum: Rp {{ number_format($minWithdrawal, 0, ',', '.') }}</p>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Metode Penarikan</label>
                            <select class="w-full rounded-lg border {{ $errors->has('method') ? 'border-red-500' : 'border-gray-300' }} px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" name="method" id="method" required>
                                <option value="">Pilih Metode</option>
                                <option value="bank_transfer" {{ old('method') === 'bank_transfer' ? 'selected' : '' }}>Transfer Bank</option>
                                <option value="e_wallet" {{ old('method') === 'e_wallet' ? 'selected' : '' }}>E-Wallet</option>
                                <option value="cash" {{ old('method') === 'cash' ? 'selected' : '' }}>Tunai</option>
                            </select>
                            @error('method')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="mb-4" id="bankNameGroup">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Bank</label>
                            <input type="text" class="w-full rounded-lg border {{ $errors->has('bank_name') ? 'border-red-500' : 'border-gray-300' }} px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" name="bank_name" value="{{ old('bank_name') }}" placeholder="Contoh: BCA, Mandiri, BRI">
                            @error('bank_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Rekening / Akun</label>
                            <input type="text" class="w-full rounded-lg border {{ $errors->has('account_number') ? 'border-red-500' : 'border-gray-300' }} px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" name="account_number" value="{{ old('account_number') }}" required>
                            @error('account_number')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pemilik Rekening</label>
                            <input type="text" class="w-full rounded-lg border {{ $errors->has('account_name') ? 'border-red-500' : 'border-gray-300' }} px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" name="account_name" value="{{ old('account_name') }}" required>
                            @error('account_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan (Opsional)</label>
                            <textarea class="w-full rounded-lg border {{ $errors->has('notes') ? 'border-red-500' : 'border-gray-300' }} px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500" name="notes" rows="3" placeholder="Tambahkan catatan jika diperlukan">{{ old('notes') }}</textarea>
                            @error('notes')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        {{-- Fee Summary --}}
                        <div class="bg-gray-50 rounded-lg p-4 mb-4">
                            <div class="flex justify-between mb-2 text-sm">
                                <span class="text-gray-600">Jumlah Penarikan</span>
                                <span id="displayAmount">Rp 0</span>
                            </div>
                            <div class="flex justify-between mb-2 text-sm">
                                <span class="text-gray-600">Biaya Admin</span>
                                <span id="displayFee">Rp 0</span>
                            </div>
                            <hr class="my-2 border-gray-200">
                            <div class="flex justify-between font-bold">
                                <span>Yang Diterima</span>
                                <span id="displayNet" class="text-green-600">Rp 0</span>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3">
                            <a href="{{ route('vendor.withdrawal.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium text-sm">Batal</a>
                            <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors font-medium text-sm">Ajukan Penarikan</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Info Sidebar --}}
            <div class="bg-white rounded-xl border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Informasi Saldo</h3>
                </div>
                <div class="p-5">
                    <div class="mb-4">
                        <div class="text-sm text-gray-500">Saldo Tersedia</div>
                        <div class="text-xl font-bold text-green-600">Rp {{ number_format($wallet->available_balance ?? 0, 0, ',', '.') }}</div>
                    </div>
                    <div class="mb-4">
                        <div class="text-sm text-gray-500">Total Pendapatan</div>
                        <div class="text-lg font-semibold">Rp {{ number_format($wallet->total_earned ?? 0, 0, ',', '.') }}</div>
                    </div>
                    <div class="mb-4">
                        <div class="text-sm text-gray-500">Total Ditarik</div>
                        <div class="text-lg font-semibold">Rp {{ number_format($wallet->total_withdrawn ?? 0, 0, ',', '.') }}</div>
                    </div>
                    <hr class="my-4 border-gray-200">
                    <div class="text-sm text-gray-500 space-y-1">
                        <p>• Penarikan akan diproses dalam 1-3 hari kerja</p>
                        <p>• Minimum penarikan: Rp {{ number_format($minWithdrawal, 0, ',', '.') }}</p>
                        <p>• Biaya admin akan dipotong dari jumlah penarikan</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const amountInput = document.getElementById('amount');
        const methodSelect = document.getElementById('method');
        const bankNameGroup = document.getElementById('bankNameGroup');
        const displayAmount = document.getElementById('displayAmount');
        const displayFee = document.getElementById('displayFee');
        const displayNet = document.getElementById('displayNet');

        function formatCurrency(amount) {
            return 'Rp ' + amount.toLocaleString('id-ID');
        }

        function calculateFee() {
            const amount = parseFloat(amountInput.value) || 0;
            const method = methodSelect.value;

            if (amount > 0 && method) {
                fetch('{{ route("vendor.withdrawal.calculate-fee") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ amount: amount, method: method })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayAmount.textContent = formatCurrency(data.amount);
                        displayFee.textContent = formatCurrency(data.fee);
                        displayNet.textContent = formatCurrency(data.net_amount);
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        }

        methodSelect.addEventListener('change', function() {
            bankNameGroup.style.display = this.value === 'bank_transfer' ? 'block' : 'none';
            calculateFee();
        });

        amountInput.addEventListener('input', calculateFee);

        if (methodSelect.value !== 'bank_transfer') {
            bankNameGroup.style.display = 'none';
        }
    });
</script>
@endpush
@endsection

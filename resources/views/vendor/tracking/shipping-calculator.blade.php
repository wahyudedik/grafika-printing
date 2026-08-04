@extends('layouts.vendor')

@section('content')
<div class="page-wrapper">
    <div class="container-xl">
        <div class="page-header d-print-none">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">Kalkulator Ongkir</h2>
                    <div class="page-pretitle">RajaOngkir Integration</div>
                </div>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            <div class="d-flex">
                <div>{{ session('success') }}</div>
            </div>
            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible" role="alert">
            <div class="d-flex">
                <div>{{ session('error') }}</div>
            </div>
            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        </div>
        @endif

        <div class="row">
            <!-- Calculator Form -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"/><path d="M12 7v5l3 3"/></svg>
                            Hitung Ongkos Kirim
                        </h3>
                    </div>
                    <div class="card-body">
                        <form id="shippingForm" onsubmit="return false;">
                            <div class="row g-3">
                                <!-- Origin City -->
                                <div class="col-md-6">
                                    <label class="form-label">Kota Asal <span class="text-danger">*</span></label>
                                    <select class="form-select" id="origin" name="origin" required>
                                        <option value="">Pilih Kota Asal</option>
                                        @foreach($cities as $city)
                                        <option value="{{ $city['city_id'] }}" {{ $vendor->city ?? '' === $city['city_name'] ? 'selected' : '' }}>
                                            {{ $city['city_name'] }}, {{ $city['province'] }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Destination City -->
                                <div class="col-md-6">
                                    <label class="form-label">Kota Tujuan <span class="text-danger">*</span></label>
                                    <select class="form-select" id="destination" name="destination" required>
                                        <option value="">Pilih Kota Tujuan</option>
                                        @foreach($cities as $city)
                                        <option value="{{ $city['city_id'] }}">
                                            {{ $city['city_name'] }}, {{ $city['province'] }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Weight -->
                                <div class="col-md-4">
                                    <label class="form-label">Berat (gram) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="weight" name="weight"
                                           min="1" max="30000" placeholder="Contoh: 1000" required>
                                    <div class="form-hint">Maksimal 30.000 gram (30 kg)</div>
                                </div>

                                <!-- Courier -->
                                <div class="col-md-4">
                                    <label class="form-label">Kurir <span class="text-danger">*</span></label>
                                    <select class="form-select" id="courier" name="courier" required>
                                        <option value="">Pilih Kurir</option>
                                        <option value="jne">JNE</option>
                                        <option value="tiki">TIKI</option>
                                        <option value="pos">POS Indonesia</option>
                                        <option value="jnt">J&T Express</option>
                                        <option value="lion">Lion Parcel</option>
                                        <option value="wahana">Wahana</option>
                                        <option value="sap">SAP</option>
                                        <option value="anteraja">AnterAja</option>
                                    </select>
                                </div>

                                <!-- Service Type -->
                                <div class="col-md-4">
                                    <label class="form-label">Layanan</label>
                                    <select class="form-select" id="service" name="service">
                                        <option value="">Semua Layanan</option>
                                    </select>
                                    <div class="form-hint">Pilih layanan spesifik (opsional)</div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="mt-4 d-flex gap-2">
                                <button type="button" class="btn btn-primary" id="calculateBtn" onclick="calculateShipping()">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"/><path d="M12 7v5l3 3"/></svg>
                                    <span id="btnText">Hitung Ongkir</span>
                                    <span id="btnSpinner" class="spinner-border spinner-border-sm d-none" role="status"></span>
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="resetForm()">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 14l-4.5 -4.5"/><path d="M5 14l7 -7"/><path d="M14 7l4.5 4.5"/><path d="M17 14l-7 -7"/></svg>
                                    Reset
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Results Section -->
                <div id="resultsSection" class="d-none">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l5 5l10 -10"/></svg>
                                Hasil Perhitungan
                            </h3>
                        </div>
                        <div class="card-body p-0">
                            <div id="resultsTable" class="table-responsive">
                                <table class="table table-vcenter card-table">
                                    <thead>
                                        <tr>
                                            <th>Kurir</th>
                                            <th>Layanan</th>
                                            <th>Estimasi</th>
                                            <th class="text-end">Biaya</th>
                                            <th class="w-1">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="resultsBody">
                                    </tbody>
                                </table>
                            </div>
                            <div id="noResults" class="empty d-none">
                                <p class="empty-title">Tidak ada hasil</p>
                                <p class="empty-subtitle text-secondary">Silakan masukkan data pengiriman dan klik "Hitung Ongkir".</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Error Alert -->
                <div id="errorAlert" class="alert alert-danger d-none" role="alert">
                    <div class="d-flex">
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 9v2m0 4v.01"/><path d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75"/></svg>
                        </div>
                        <div id="errorMessage">Terjadi kesalahan saat menghitung ongkir.</div>
                    </div>
                </div>
            </div>

            <!-- Sidebar: Info -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Informasi</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <h5 class="card-title">Cara Menggunakan</h5>
                            <ol class="ps-3 text-muted">
                                <li class="mb-2">Pilih <strong>kota asal</strong> (lokasi vendor)</li>
                                <li class="mb-2">Pilih <strong>kota tujuan</strong> (lokasi pelanggan)</li>
                                <li class="mb-2">Masukkan <strong>berat barang</strong> dalam gram</li>
                                <li class="mb-2">Pilih <strong>kurir</strong> yang diinginkan</li>
                                <li class="mb-2">Klik <strong>"Hitung Ongkir"</strong></li>
                            </ol>
                        </div>

                        <div class="mb-3">
                            <h5 class="card-title">Kurir Tersedia</h5>
                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge bg-blue-lt">JNE</span>
                                <span class="badge bg-blue-lt">TIKI</span>
                                <span class="badge bg-blue-lt">POS</span>
                                <span class="badge bg-blue-lt">J&T</span>
                                <span class="badge bg-blue-lt">Lion</span>
                                <span class="badge bg-blue-lt">Wahana</span>
                                <span class="badge bg-blue-lt">SAP</span>
                                <span class="badge bg-blue-lt">AnterAja</span>
                            </div>
                        </div>

                        <div>
                            <h5 class="card-title">Catatan</h5>
                            <ul class="list-unstyled text-muted">
                                <li class="mb-1">• Harga belum termasuk asuransi</li>
                                <li class="mb-1">• Estimasi waktu dapat berubah</li>
                                <li class="mb-1">• Berat minimum 1 gram</li>
                                <li>• Berat maksimum 30 kg</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let isCalculating = false;

    async function calculateShipping() {
        if (isCalculating) return;

        const origin = document.getElementById('origin').value;
        const destination = document.getElementById('destination').value;
        const weight = document.getElementById('weight').value;
        const courier = document.getElementById('courier').value;

        // Validation
        if (!origin || !destination || !weight || !courier) {
            showError('Mohon lengkapi semua field yang diperlukan (Kota Asal, Kota Tujuan, Berat, dan Kurir).');
            return;
        }

        if (origin === destination) {
            showError('Kota asal dan tujuan tidak boleh sama.');
            return;
        }

        if (parseInt(weight) < 1 || parseInt(weight) > 30000) {
            showError('Berat harus antara 1 hingga 30.000 gram.');
            return;
        }

        setLoading(true);
        hideError();
        hideResults();

        try {
            const response = await fetch('{{ route("vendor.shipping.calculate") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ origin, destination, weight, courier })
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Gagal menghitung ongkir. Status: ' + response.status);
            }

            if (data.success && data.data) {
                displayResults(data.data);
            } else {
                throw new Error(data.message || 'Tidak ada data ongkir ditemukan.');
            }
        } catch (error) {
            console.error('Shipping calculation error:', error);
            showError(error.message || 'Terjadi kesalahan saat menghitung ongkir. Silakan coba lagi.');
        } finally {
            setLoading(false);
        }
    }

    function setLoading(loading) {
        isCalculating = loading;
        const btn = document.getElementById('calculateBtn');
        const btnText = document.getElementById('btnText');
        const btnSpinner = document.getElementById('btnSpinner');

        if (loading) {
            btn.disabled = true;
            btnText.textContent = 'Menghitung...';
            btnSpinner.classList.remove('d-none');
        } else {
            btn.disabled = false;
            btnText.textContent = 'Hitung Ongkir';
            btnSpinner.classList.add('d-none');
        }
    }

    function showError(message) {
        const alert = document.getElementById('errorAlert');
        const errorMsg = document.getElementById('errorMessage');
        errorMsg.textContent = message;
        alert.classList.remove('d-none');
    }

    function hideError() {
        document.getElementById('errorAlert').classList.add('d-none');
    }

    function displayResults(results) {
        const section = document.getElementById('resultsSection');
        const tbody = document.getElementById('resultsBody');
        const noResults = document.getElementById('noResults');

        tbody.innerHTML = '';

        if (!results || results.length === 0) {
            section.classList.remove('d-none');
            noResults.classList.remove('d-none');
            return;
        }

        noResults.classList.add('d-none');
        section.classList.remove('d-none');

        results.forEach(item => {
            if (item.costs && item.costs.length > 0) {
                item.costs.forEach(cost => {
                    const etd = cost.etd || '-';
                    const description = cost.description || '-';
                    const price = cost.cost && cost.cost[0] ? cost.cost[0].value : 0;

                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>
                            <div class="fw-bold">{{ item.code ? item.code.toUpperCase() : '' }}</div>
                            <small class="text-muted">${item.name || ''}</small>
                        </td>
                        <td>
                            <div class="fw-bold">${description}</div>
                        </td>
                        <td>${etd}</td>
                        <td class="text-end fw-bold">Rp ${price.toLocaleString('id-ID')}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-ghost-primary" onclick="selectShipping('${item.code}', '${description}', ${price}, '${etd}')">
                                Pilih
                            </button>
                        </td>
                    `;
                    tbody.appendChild(row);
                });
            }
        });

        if (tbody.children.length === 0) {
            section.classList.remove('d-none');
            noResults.classList.remove('d-none');
        }
    }

    function hideResults() {
        document.getElementById('resultsSection').classList.add('d-none');
    }

    function selectShipping(courier, service, cost, etd) {
        // Store selected shipping data
        const data = { courier, service, cost, etd };
        localStorage.setItem('selectedShipping', JSON.stringify(data));

        // Show success message
        const successMsg = document.createElement('div');
        successMsg.className = 'alert alert-success alert-dismissible position-fixed';
        successMsg.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        successMsg.innerHTML = `
            <div class="d-flex">
                <div>Pengiriman dipilih: <strong>${courier.toUpperCase()} ${service}</strong> - Rp ${cost.toLocaleString('id-ID')}</div>
            </div>
            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        `;
        document.body.appendChild(successMsg);

        setTimeout(() => successMsg.remove(), 5000);
    }

    function resetForm() {
        document.getElementById('shippingForm').reset();
        hideResults();
        hideError();
        isCalculating = false;
    }
</script>
@endsection

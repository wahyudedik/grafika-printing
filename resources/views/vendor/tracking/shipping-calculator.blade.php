@extends('layouts.vendor')

@section('title', 'Kalkulator Ongkir')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">🚚 Kalkulator Ongkir</h3>
                        <div class="card-subtitle">Hitung biaya pengiriman dengan akurat menggunakan RajaOngkir API</div>
                    </div>
                    <div class="card-body">
                        <form id="shippingCalculatorForm">
                            @csrf
                            <div class="row">
                                <!-- Origin (Vendor Location) -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">📍 Kota Asal (Vendor)</label>
                                        <select class="form-select" id="origin" name="origin" required>
                                            <option value="">Pilih Kota Asal</option>
                                            @foreach ($cities as $city)
                                                <option value="{{ $city['city_id'] }}"
                                                    {{ $vendor->city_id == $city['city_id'] ? 'selected' : '' }}>
                                                    {{ $city['city_name'] }}, {{ $city['province'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Destination -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">🎯 Kota Tujuan</label>
                                        <select class="form-select" id="destination" name="destination" required>
                                            <option value="">Pilih Kota Tujuan</option>
                                            @foreach ($cities as $city)
                                                <option value="{{ $city['city_id'] }}">
                                                    {{ $city['city_name'] }}, {{ $city['province'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Weight -->
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">⚖️ Berat (gram)</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="weight" name="weight"
                                                min="1" max="30000" value="1000" required>
                                            <span class="input-group-text">gram</span>
                                        </div>
                                        <div class="form-text">Maksimal 30kg (30,000 gram)</div>
                                    </div>
                                </div>

                                <!-- Courier -->
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">🚛 Kurir</label>
                                        <select class="form-select" id="courier" name="courier" required>
                                            <option value="">Pilih Kurir</option>
                                            <option value="jne">JNE</option>
                                            <option value="tiki">TIKI</option>
                                            <option value="pos">POS Indonesia</option>
                                            <option value="jnt">J&T Express</option>
                                            <option value="sicepat">SiCepat</option>
                                            <option value="anteraja">AnterAja</option>
                                            <option value="lion">Lion Parcel</option>
                                            <option value="ninja">Ninja Xpress</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Service Type -->
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="form-label">📦 Layanan</label>
                                        <select class="form-select" id="service" name="service" required>
                                            <option value="">Pilih Layanan</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Address -->
                            <div class="mb-3">
                                <label class="form-label">🏠 Alamat Lengkap Tujuan</label>
                                <textarea class="form-control" id="address" name="address" rows="3"
                                    placeholder="Masukkan alamat lengkap tujuan pengiriman..."></textarea>
                            </div>

                            <!-- Calculate Button -->
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary btn-lg" id="calculateBtn">
                                    <i class="ti ti-calculator me-2"></i>
                                    Hitung Ongkir
                                </button>
                            </div>
                        </form>

                        <!-- Results -->
                        <div id="shippingResults" class="mt-4" style="display: none;">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">💰 Hasil Perhitungan Ongkir</h4>
                                </div>
                                <div class="card-body">
                                    <div id="resultsContent"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Manual Input Option -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h4 class="card-title">✏️ Input Manual</h4>
                                <div class="card-subtitle">Jika API tidak tersedia, input biaya secara manual</div>
                            </div>
                            <div class="card-body">
                                <form id="manualShippingForm">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Kurir</label>
                                                <input type="text" class="form-control" id="manualCourier"
                                                    placeholder="Contoh: JNE, TIKI, dll">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Biaya Ongkir (Rp)</label>
                                                <input type="number" class="form-control" id="manualCost" min="0"
                                                    placeholder="0">
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-outline-primary" id="useManualBtn">
                                        <i class="ti ti-edit me-2"></i>
                                        Gunakan Input Manual
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('shippingCalculatorForm');
            const resultsDiv = document.getElementById('shippingResults');
            const resultsContent = document.getElementById('resultsContent');
            const calculateBtn = document.getElementById('calculateBtn');
            const courierSelect = document.getElementById('courier');
            const serviceSelect = document.getElementById('service');
            const manualForm = document.getElementById('manualShippingForm');
            const useManualBtn = document.getElementById('useManualBtn');

            // Service types for each courier
            const serviceTypes = {
                'jne': ['REG', 'OKE', 'YES'],
                'tiki': ['REG', 'ECO', 'ONS'],
                'pos': ['REG', 'KILAT', 'EXPRESS'],
                'jnt': ['REG', 'EZ', 'COCO'],
                'sicepat': ['REG', 'BEST', 'EXPRESS'],
                'anteraja': ['REG', 'EXPRESS'],
                'lion': ['REG', 'EXPRESS'],
                'ninja': ['REG', 'EXPRESS']
            };

            // Update service options when courier changes
            courierSelect.addEventListener('change', function() {
                const courier = this.value;
                serviceSelect.innerHTML = '<option value="">Pilih Layanan</option>';

                if (serviceTypes[courier]) {
                    serviceTypes[courier].forEach(service => {
                        const option = document.createElement('option');
                        option.value = service;
                        option.textContent = service;
                        serviceSelect.appendChild(option);
                    });
                }
            });

            // Calculate shipping
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(form);
                const data = Object.fromEntries(formData);

                calculateBtn.innerHTML = '<i class="ti ti-loader-2 me-2"></i>Menghitung...';
                calculateBtn.disabled = true;

                fetch('{{ route('api.calculate-shipping') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content')
                        },
                        body: JSON.stringify(data)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            displayResults(data.data);
                        } else {
                            showError(data.message || 'Gagal menghitung ongkir');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showError('Terjadi kesalahan saat menghitung ongkir');
                    })
                    .finally(() => {
                        calculateBtn.innerHTML = '<i class="ti ti-calculator me-2"></i>Hitung Ongkir';
                        calculateBtn.disabled = false;
                    });
            });

            // Display results
            function displayResults(data) {
                const results = data.rajaongkir.results[0];
                const costs = results.costs;

                let html = `
            <div class="row">
                <div class="col-md-6">
                    <h5>🚛 ${results.name}</h5>
                    <p class="text-muted">Kurir: ${results.name}</p>
                </div>
                <div class="col-md-6">
                    <h5>📦 Layanan Tersedia</h5>
                </div>
            </div>
            <div class="row">
        `;

                costs.forEach(cost => {
                    const service = cost.service;
                    const costValue = cost.cost[0].value;
                    const etd = cost.cost[0].etd;

                    html += `
                <div class="col-md-6 mb-3">
                    <div class="card border">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title">${service}</h6>
                                    <small class="text-muted">Estimasi: ${etd}</small>
                                </div>
                                <div class="text-end">
                                    <h5 class="text-primary">Rp ${costValue.toLocaleString('id-ID')}</h5>
                                    <button class="btn btn-sm btn-outline-primary" onclick="selectShipping('${service}', ${costValue}, '${results.name}')">
                                        Pilih
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
                });

                html += '</div>';
                resultsContent.innerHTML = html;
                resultsDiv.style.display = 'block';
            }

            // Show error
            function showError(message) {
                resultsContent.innerHTML = `
            <div class="alert alert-danger">
                <i class="ti ti-alert-circle me-2"></i>
                ${message}
            </div>
        `;
                resultsDiv.style.display = 'block';
            }

            // Use manual input
            useManualBtn.addEventListener('click', function() {
                const courier = document.getElementById('manualCourier').value;
                const cost = document.getElementById('manualCost').value;

                if (!courier || !cost) {
                    alert('Mohon isi kurir dan biaya ongkir');
                    return;
                }

                resultsContent.innerHTML = `
            <div class="alert alert-info">
                <h5>📝 Input Manual</h5>
                <p><strong>Kurir:</strong> ${courier}</p>
                <p><strong>Biaya:</strong> Rp ${parseInt(cost).toLocaleString('id-ID')}</p>
                <button class="btn btn-primary" onclick="selectShipping('${courier}', ${cost}, '${courier}')">
                    Gunakan Data Ini
                </button>
            </div>
        `;
                resultsDiv.style.display = 'block';
            });
        });

        // Select shipping option
        function selectShipping(service, cost, courier) {
            // This function will be called when user selects a shipping option
            // You can implement the logic to save the selection
            alert(`Dipilih: ${service} - ${courier} - Rp ${cost.toLocaleString('id-ID')}`);

            // You can add logic here to save the selection to the transaction
            // For example, update a hidden form or make an AJAX call
        }
    </script>
@endsection

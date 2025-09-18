<!-- Enhanced Shipping Form Modal -->
<div class="modal fade" id="shippingModal" tabindex="-1" aria-labelledby="shippingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="shippingModalLabel">
                    <i class="ti ti-truck me-2"></i>
                    Kalkulator Ongkir
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- API Status Indicator -->
                <div class="alert alert-info d-flex align-items-center mb-3" id="apiStatus">
                    <i class="ti ti-info-circle me-2"></i>
                    <span>Menggunakan RajaOngkir API untuk perhitungan yang akurat</span>
                </div>

                <form id="enhancedShippingForm">
                    @csrf
                    <div class="row">
                        <!-- Origin -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="ti ti-map-pin me-1"></i>
                                    Kota Asal
                                </label>
                                <select class="form-select" id="origin" name="origin" required>
                                    <option value="">Pilih Kota Asal</option>
                                    <option value="151" selected>Jakarta Pusat, DKI Jakarta</option>
                                    <option value="152">Jakarta Utara, DKI Jakarta</option>
                                    <option value="153">Jakarta Selatan, DKI Jakarta</option>
                                    <option value="154">Jakarta Barat, DKI Jakarta</option>
                                    <option value="155">Jakarta Timur, DKI Jakarta</option>
                                    <option value="501">Bandung, Jawa Barat</option>
                                    <option value="502">Bogor, Jawa Barat</option>
                                    <option value="503">Depok, Jawa Barat</option>
                                    <option value="504">Tangerang, Banten</option>
                                    <option value="505">Bekasi, Jawa Barat</option>
                                    <option value="601">Surabaya, Jawa Timur</option>
                                    <option value="602">Malang, Jawa Timur</option>
                                    <option value="701">Yogyakarta, DI Yogyakarta</option>
                                    <option value="801">Semarang, Jawa Tengah</option>
                                    <option value="901">Denpasar, Bali</option>
                                    <option value="1001">Medan, Sumatera Utara</option>
                                    <option value="1101">Palembang, Sumatera Selatan</option>
                                    <option value="1201">Makassar, Sulawesi Selatan</option>
                                </select>
                            </div>
                        </div>

                        <!-- Destination -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="ti ti-map-pin me-1"></i>
                                    Kota Tujuan
                                </label>
                                <select class="form-select" id="destination" name="destination" required>
                                    <option value="">Pilih Kota Tujuan</option>
                                    <option value="151">Jakarta Pusat, DKI Jakarta</option>
                                    <option value="152">Jakarta Utara, DKI Jakarta</option>
                                    <option value="153">Jakarta Selatan, DKI Jakarta</option>
                                    <option value="154">Jakarta Barat, DKI Jakarta</option>
                                    <option value="155">Jakarta Timur, DKI Jakarta</option>
                                    <option value="501">Bandung, Jawa Barat</option>
                                    <option value="502">Bogor, Jawa Barat</option>
                                    <option value="503">Depok, Jawa Barat</option>
                                    <option value="504">Tangerang, Banten</option>
                                    <option value="505">Bekasi, Jawa Barat</option>
                                    <option value="601">Surabaya, Jawa Timur</option>
                                    <option value="602">Malang, Jawa Timur</option>
                                    <option value="701">Yogyakarta, DI Yogyakarta</option>
                                    <option value="801">Semarang, Jawa Tengah</option>
                                    <option value="901">Denpasar, Bali</option>
                                    <option value="1001">Medan, Sumatera Utara</option>
                                    <option value="1101">Palembang, Sumatera Selatan</option>
                                    <option value="1201">Makassar, Sulawesi Selatan</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Weight -->
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="ti ti-weight me-1"></i>
                                    Berat (gram)
                                </label>
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
                                <label class="form-label">
                                    <i class="ti ti-truck me-1"></i>
                                    Kurir
                                </label>
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
                                <label class="form-label">
                                    <i class="ti ti-package me-1"></i>
                                    Layanan
                                </label>
                                <select class="form-select" id="service" name="service" required>
                                    <option value="">Pilih Layanan</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Address -->
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="ti ti-home me-1"></i>
                            Alamat Lengkap Tujuan
                        </label>
                        <textarea class="form-control" id="address" name="address" rows="3"
                            placeholder="Masukkan alamat lengkap tujuan pengiriman..." required></textarea>
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
                    <div class="card border-primary">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">
                                <i class="ti ti-check-circle me-2"></i>
                                Hasil Perhitungan Ongkir
                            </h5>
                        </div>
                        <div class="card-body">
                            <div id="resultsContent"></div>
                        </div>
                    </div>
                </div>

                <!-- Manual Input Fallback -->
                <div class="card mt-3" id="manualInputCard" style="display: none;">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="ti ti-edit me-2"></i>
                            Input Manual
                        </h6>
                    </div>
                    <div class="card-body">
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
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-success" id="saveShippingBtn" style="display: none;">
                    <i class="ti ti-check me-2"></i>
                    Simpan & Kirim
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('enhancedShippingForm');
        const resultsDiv = document.getElementById('shippingResults');
        const resultsContent = document.getElementById('resultsContent');
        const calculateBtn = document.getElementById('calculateBtn');
        const courierSelect = document.getElementById('courier');
        const serviceSelect = document.getElementById('service');
        const manualInputCard = document.getElementById('manualInputCard');
        const useManualBtn = document.getElementById('useManualBtn');
        const saveShippingBtn = document.getElementById('saveShippingBtn');
        const apiStatus = document.getElementById('apiStatus');

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

            fetch('{{ route('api.shipping.calculate') }}', {
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
                        apiStatus.className = 'alert alert-success d-flex align-items-center mb-3';
                        apiStatus.innerHTML =
                            '<i class="ti ti-check-circle me-2"></i><span>RajaOngkir API berhasil diakses</span>';
                    } else {
                        if (data.fallback) {
                            showFallback(data.message);
                        } else {
                            showError(data.message || 'Gagal menghitung ongkir');
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showFallback('Terjadi kesalahan saat menghitung ongkir');
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
                    <h6>🚛 ${results.name}</h6>
                    <p class="text-muted">Kurir: ${results.name}</p>
                </div>
                <div class="col-md-6">
                    <h6>📦 Layanan Tersedia</h6>
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

        // Show fallback for manual input
        function showFallback(message) {
            apiStatus.className = 'alert alert-warning d-flex align-items-center mb-3';
            apiStatus.innerHTML = '<i class="ti ti-alert-triangle me-2"></i><span>' + message + '</span>';
            manualInputCard.style.display = 'block';
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
                <h6>📝 Input Manual</h6>
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
        // Update form fields
        document.getElementById('courier').value = courier.toLowerCase();
        document.getElementById('service').value = service;
        document.getElementById('weight').value = document.getElementById('weight').value;

        // Show save button
        document.getElementById('saveShippingBtn').style.display = 'inline-block';

        // Store selected data
        window.selectedShipping = {
            courier: courier,
            service: service,
            cost: cost
        };

        // Close modal or show success message
        alert(`Dipilih: ${service} - ${courier} - Rp ${cost.toLocaleString('id-ID')}`);
    }
</script>

<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold text-black">Daftar Penghuni</h3>
                    <a href="{{ route('penghuni.create') }}"
                        class="inline-flex items-center bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition text-sm font-semibold">
                        <span>Tambah Penghuni</span>
                    </a>
                </div>

                <div class="overflow-x-auto max-w-full">
                    <table id="penghuni-table"
                        class="w-full table-fixed border border-gray-200 text-sm rounded overflow-hidden">
                        <thead class="bg-blue-50 text-blue-800">
                            <tr>
                                <th class="px-4 py-2 border">Nama</th>
                                <th class="px-4 py-2 border">Email</th>
                                <th class="px-4 py-2 border">No. Telepon</th>
                                <th class="px-4 py-2 border">Lokasi Kost</th>
                                <th class="px-4 py-2 border">Status</th>
                                <th class="px-4 py-3 border">Tanggal Keluar</th>
                                <th class="px-4 py-2 border">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($penghunis as $penghuni)
                                <tr class="hover:bg-blue-50 transition">
                                    <td class="px-4 py-2 border">{{ $penghuni->name }}</td>
                                    <td class="px-4 py-2 border">{{ $penghuni->email }}</td>
                                    <td class="px-4 py-2 border">{{ $penghuni->phone }}</td>
                                    <td class="px-4 py-2 border">{{ $penghuni->lokasi_kost }}</td>
                                    <td class="px-4 py-2 border">
                                        @if (strtolower($penghuni->status) == 'aktif')
                                            <span
                                                class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">Aktif</span>
                                        @else
                                            <span
                                                class="px-2 py-1 text-xs font-semibold text-gray-500 bg-gray-200 rounded-full">Tidak
                                                Aktif</span>
                                        @endif
                                    </td>

                                    <!-- Tanggal Keluar -->
                                    <td class="px-4 py-3 border text-black">
                                        {{ $penghuni->penyewaanKost ? \Carbon\Carbon::parse($penghuni->penyewaanKost->tanggal_keluar)->format('d M Y') : '-' }}
                                    </td>

                                    <td class="px-4 py-2 border flex gap-3 justify-center items-center">
                                        <!-- Tombol Edit -->
                                        <a href="{{ route('penghuni.edit', $penghuni->id) }}"
                                            class="inline-flex items-center justify-center w-9 h-9 bg-blue-100 text-custom-blue rounded-md hover:bg-custom-blue hover:text-white transition"
                                            title="Edit">
                                            <x-heroicon-o-pencil class="w-5 h-5" />
                                        </a>
                                        <!-- Tombol Detail -->
                                        {{-- <button type="button"
                                            class="inline-flex items-center justify-center w-9 h-9 bg-yellow-100 text-yellow-600 rounded-md hover:bg-yellow-600 hover:text-white transition"
                                            data-bs-toggle="modal" data-bs-target="#detailModal-{{ $penghuni->id }}"
                                            title="Detail">
                                            <x-heroicon-o-eye class="w-5 h-5" />
                                        </button> --}}
                                        <button type="button"
                                            class="inline-flex items-center justify-center w-9 h-9 bg-yellow-100 text-yellow-600 rounded-md hover:bg-yellow-600 hover:text-white transition"
                                            onclick="openModal('{{ $penghuni->id }}')" title="Detail">
                                            <x-heroicon-o-eye class="w-5 h-5" />
                                        </button>
                                        <!-- Tombol Nonaktifkan -->
                                        <form action="{{ route('penghuni.destroy', $penghuni->id) }}" method="POST"
                                            class="inline" id="form-delete-{{ $penghuni->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                class="inline-flex items-center justify-center w-9 h-9 bg-red-100 text-red-600 rounded-md hover:bg-red-600 hover:text-white transition"
                                                title="Nonaktifkan" onclick="confirmDeactivate({{ $penghuni->id }})">
                                                <x-heroicon-o-trash class="w-5 h-5" />
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <!-- Modal Detail -->
                                {{-- <div id="detailModal-{{ $penghuni->id }}"
                                    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
                                    <div class="bg-white rounded-lg shadow-lg w-96 max-w-full">
                                        <div class="flex justify-between items-center p-4 border-b border-gray-300">
                                            <h5 class="text-xl font-semibold text-gray-800">Detail Penghuni</h5>
                                            <button type="button" class="text-gray-500 hover:text-gray-700"
                                                onclick="closeModal('{{ $penghuni->id }}')">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>

                                        </div>
                                        <div class="p-4 space-y-4">
                                            <div>
                                                <strong>Nama:</strong> {{ $penghuni->name }}<br>
                                                <strong>Email:</strong> {{ $penghuni->email }}<br>
                                                <strong>No. Telepon:</strong> {{ $penghuni->phone }}<br>
                                                <strong>Lokasi Kost:</strong> {{ $penghuni->lokasi_kost }}<br>
                                                <strong>Status:</strong> {{ $penghuni->status }}<br>
                                                @if ($penghuni->image)
                                                    <strong>Gambar:</strong><br>
                                                    <img src="{{ asset('storage/' . $penghuni->image) }}"
                                                        alt="Gambar Penghuni"
                                                        class="img-fluid rounded mt-2 max-h-40 object-cover">
                                                @else
                                                    <strong>Gambar:</strong><br>
                                                    <span>Tidak ada gambar</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex justify-end p-4 border-t border-gray-300">
                                            <button type="button"
                                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                                                onclick="closeModal('{{ $penghuni->id }}')">
                                                Tutup
                                            </button>
                                        </div>
                                    </div>
                                </div> --}}
                                <div id="detailModal-{{ $penghuni->id }}"
                                    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
                                    <div class="bg-white rounded-lg shadow-lg w-96 max-w-full">
                                        <div class="flex justify-between items-center p-4 border-b border-gray-300">
                                            <h5 class="text-xl font-semibold text-gray-800">Detail Penghuni</h5>
                                            <button type="button" class="text-gray-500 hover:text-gray-700"
                                                onclick="closeModal('{{ $penghuni->id }}')">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                        <div class="p-4 space-y-4">
                                            <div>
                                                <strong>Nama:</strong> {{ $penghuni->name }}<br>
                                                <strong>Email:</strong> {{ $penghuni->email }}<br>
                                                <strong>No. Telepon:</strong> {{ $penghuni->phone }}<br>
                                                <strong>Lokasi Kost:</strong> {{ $penghuni->lokasi_kost }}<br>
                                                <strong>Status:</strong> {{ $penghuni->status }}<br>
                                                @if ($penghuni->image)
                                                    <strong>Gambar:</strong><br>
                                                    <img src="{{ asset('storage/' . $penghuni->image) }}"
                                                        alt="Gambar Penghuni"
                                                        class="img-fluid rounded mt-2 max-h-40 object-cover">
                                                @else
                                                    <strong>Gambar:</strong><br>
                                                    <span>Tidak ada gambar</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex justify-end p-4 border-t border-gray-300">
                                            <button type="button"
                                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                                                onclick="closeModal('{{ $penghuni->id }}')">
                                                Tutup
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>
        function confirmDeactivate(id) {
            Swal.fire({
                title: 'Yakin ingin menonaktifkan penghuni ini?',
                text: "Penghuni ini akan diubah statusnya menjadi Tidak Aktif.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Nonaktifkan!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit form untuk menonaktifkan penghuni
                    document.getElementById('form-delete-' + id).submit();
                }
            });
        }

        // Open modal by removing the hidden class
        function openModal(id) {
            document.getElementById('detailModal-' + id).classList.remove('hidden');
        }

        // Close modal by adding the hidden class
        function closeModal(id) {
            document.getElementById('detailModal-' + id).classList.add('hidden');
        }

        $(document).ready(function() {
            $('#penghuni-table').DataTable({
                responsive: true,
                pageLength: 10,
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ entri",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                    paginate: {
                        first: "Awal",
                        last: "Akhir",
                        next: "→",
                        previous: "←"
                    }
                }
            });
        });
    </script>
</x-app-layout>



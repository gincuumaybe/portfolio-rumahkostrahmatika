<?php

namespace App\Http\Controllers;

use App\Models\Penghuni;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\Laporan;


class PenghuniController extends Controller
{


    public function index()
    {
        // // Mengambil data pengguna dengan relasi penghuni
        // $penghunis = User::where('role', 'user')->get(); // Memastikan mengambil data dari tabel users
        // return view('penghuni.index', compact('penghunis'));

        // Eager load 'transaksis' relationship with 'penghunis'
        $penghunis = User::where('role', 'user')->with('penyewaanKost')->get();  // Eager load penghuni's transaksis

        return view('penghuni.index', compact('penghunis'));
    }


    public function create()
    {
        return view('penghuni.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)

    {
        // Validasi data
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'no_telp' => 'required|numeric|unique:users,phone',
            'lokasi_kost' => ['required', 'string', 'in:Gunung_Anyar,Berbek,Rungkut'],
            'password' => 'required|string|min:6',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // Default image null
        $imagePath = null;

        // Handle upload gambar jika ada
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();

            // Simpan ke folder: storage/app/public/images
            $storedPath = $image->storeAs('public/images', $imageName);

            // Ubah jadi path publik
            // $imagePath = str_replace('public/', 'storage/', $storedPath);
            $imagePath = 'images/' . $imageName;
        }

        // Simpan ke tabel users
        $user = User::create([
            'name' => $request->nama,
            'email' => $request->email,
            'phone' => $request->no_telp,
            'lokasi_kost' => $request->lokasi_kost,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'status' => 'nonaktif',  // Set status ke 'nonaktif' secara manual
            'image' => $imagePath,
        ]);


        // // Simpan ke DB (nanti kita buat model dan tabelnya)
        // Penghuni::create([
        //     'user_id' => $user->id,
        //     'name' => $request->nama,
        //     'email' => $request->email,
        //     'phone' => $request->no_telp,
        //     'lokasi_kost' => $request->lokasi_kost,
        //     'password' => Hash::make($request->password),
        //     'role' => 'user',
        //     'status' => 'nonaktif',
        // ]);

        return redirect()->route('penghuni.index')->with('success', 'Penghuni berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $penghuni = User::findOrFail($id);
        return view('penghuni.edit', compact('penghuni'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'no_telp' => 'required|string|max:20',
            'lokasi_kost' => 'required|string|in:Berbek,Gunung_Anyar,Rungkut',
        ]);

        // Menangani nilai lokasi_kost, ganti spasi dengan underscore
        $lokasiKost = str_replace(' ', '_', $request->lokasi_kost);

        $penghuni = User::findOrFail($id);
        $penghuni->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->no_telp,
            'lokasi' => $request->lokasi_kost,
        ]);

        // Cari data di tabel penghunis berdasarkan user_id
        if ($penghuni) {
            $penghuni->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->no_telp,
                'lokasi_kost' => $request->lokasi_kost,
            ]);
        }

        return redirect()->route('penghuni.index')->with('success', 'Data penghuni berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $penghuni = User::findOrFail($id);  // Ambil data pengguna berdasarkan ID


        $penghuni = User::findOrFail($id);

        if ($penghuni->status == 'aktif') {
            $penghuni->status = 'nonaktif';
            $penghuni->save();  // Simpan perubahan statusF
        }

        return redirect()->route('penghuni.index')->with('success', 'Penghuni berhasil dinonaktifkan.');
    }
}

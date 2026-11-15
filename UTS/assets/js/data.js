
 // ===== Fungsi generik untuk memuat data JSON =====
async function muatData(namaFile, daftarKunci) {
    const tbody = document.querySelector(
        ".table-responsive table tbody"
    );
    const loading = document.getElementById("loading-indicator");

    if (!tbody || !loading) return;

    loading.style.display = "block";
    tbody.innerHTML = "";

    try {
        // Simulasi delay jaringan 3 detik
        await new Promise((resolve) => setTimeout(resolve, 3000));

        const res = await fetch(namaFile);

        if (!res.ok) {
            throw new Error(
                "Gagal mengambil data (status " + res.status + ")"
            );
        }

        const data = await res.json();

        data.forEach(function (item) {
            const tr = document.createElement("tr");

            // Membuat kolom berdasarkan daftarKunci
            daftarKunci.forEach(function (kunci) {
                const td = document.createElement("td");
                td.textContent = item[kunci] ?? "";
                tr.appendChild(td);
            });

            // Membuat kolom Aksi
            const tdAksi = document.createElement("td");

            tdAksi.innerHTML =
                '<button type="button">Edit</button> ' +
                '<button type="button" class="btn-hapus">Hapus</button>';

            tr.appendChild(tdAksi);
            tbody.appendChild(tr);
        });

        updateRowCounter();

    } catch (err) {
        const tr = document.createElement("tr");
        const td = document.createElement("td");

        td.colSpan = daftarKunci.length + 1;
        td.textContent = "Gagal memuat data: " + err.message;

        tr.appendChild(td);
        tbody.appendChild(tr);

        updateRowCounter();

    } finally {
        loading.style.display = "none";
    }
}


// ===== Konfigurasi untuk halaman buku =====
function muatDaftarBuku() {
    return muatData(
        "../data/buku.json",
        ["judul", "pengarang", "tahun", "stok", "kategori"]
    );
}


// ===== Konfigurasi untuk halaman anggota =====
function muatDaftarAnggota() {
    return muatData(
        "../data/anggota.json",
        ["no_anggota", "nama", "alamat", "no_hp"]
    );
}


// ===== Menentukan data berdasarkan halaman =====
document.addEventListener("DOMContentLoaded", function () {
    const judul = document.querySelector("main h2");

    if (!judul) return;

    if (judul.textContent.trim() === "Daftar Buku") {
        muatDaftarBuku();
    } else if (judul.textContent.trim() === "Daftar Anggota") {
        muatDaftarAnggota();
    }

    // Tombol Muat Ulang di halaman Daftar Buku
    const tombol = document.getElementById("btn-muat-ulang");

    if (tombol) {
        tombol.addEventListener("click", function () {
            muatDaftarBuku();
        });
    }
});
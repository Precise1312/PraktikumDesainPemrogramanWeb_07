// ===== Hamburger menu (JS-driven, menggantikan checkbox hack) =====
function initNavToggle() {
    const toggleBtn = document.getElementById("nav-toggle-btn");
    const nav = document.querySelector("header nav");
    if (!toggleBtn || !nav) return;

    toggleBtn.addEventListener("click", function () {
        nav.classList.toggle("nav-open");
    });
}

// ===== Konfirmasi hapus (front-end only, belum ke server) =====
function initHapusConfirm() {
    document.addEventListener("submit", async function (e) {
        const form = e.target;
        if (!form.classList.contains("form-hapus")) return;

        e.preventDefault();   // tahan dulu, tunggu jawaban user

        const row = form.closest("tr");
        const nama = row ? row.querySelector("td").textContent.trim() : "data ini";

        const yakin = await tampilkanModal(
            'Yakin ingin menghapus "' + nama + '"? Tindakan ini tidak bisa dibatalkan.',
            "Ya, hapus"
        );

        if (yakin) form.submit();   // kirim form secara manual
    });
}

// ===== Konfirmasi Update =====
function initUpdateConfirm() {
    const form = document.getElementById("form-edit");
    if (!form) return;

    form.addEventListener("submit", async function (e) {
        if (e.defaultPrevented) return;   // validasi gagal, jangan tampilkan modal
        e.preventDefault();

        const yakin = await tampilkanModal("Yakin ingin memperbarui data ini?", "Ya, perbarui");
        if (yakin) form.submit();
    });
}

// ===== Filter/pencarian tabel real-time =====
function initTableFilter() {
    const input = document.getElementById("search-input");
    const table = document.querySelector(".table-responsive table");
    if (!input || !table) return;

    input.addEventListener("keyup", function () {
        const keyword = input.value.toLowerCase();
        const rows = table.querySelectorAll("tbody tr");

        rows.forEach(function (row) {
            const cells = row.querySelectorAll("td");

            const judul = cells[0]?.textContent.toLowerCase() || "";
            const pengarang = cells[1]?.textContent.toLowerCase() || "";

            const cocok =
                judul.includes(keyword) ||
                pengarang.includes(keyword);

            row.style.display = cocok ? "" : "none";
        });

        updateRowCounter();
    });
}

// ===== Validasi form (client-side) =====
function tampilkanError(input, pesan) {
    hapusError(input);
    const span = document.createElement("span");
    span.className = "error";
    span.textContent = pesan;
    input.insertAdjacentElement("afterend", span);
}

function hapusError(input) {
    const next = input.nextElementSibling;
    if (next && next.classList.contains("error")) {
        next.remove();
    }
}

// ===== Validasi real-time =====
const FIELD_WAJIB = {
    judul: "Judul",
    nama: "Nama",
    pengarang: "Pengarang",
    no_anggota: "No. Anggota"
};

// Mengembalikan pesan error, atau "" kalau isinya valid
function pesanError(input) {
    const nilai = input.value.trim();

    if (FIELD_WAJIB[input.name]) {
        return nilai === "" ? FIELD_WAJIB[input.name] + " wajib diisi." : "";
    }
    if (input.name === "tahun") {
        const n = parseInt(nilai, 10);
        const max = new Date().getFullYear();
        return (isNaN(n) || n < 1900 || n > max)
            ? "Tahun harus di antara 1900-" + max + "." : "";
    }
    if (input.name === "stok") {
        const n = parseInt(nilai, 10);
        return (isNaN(n) || n < 0) ? "Stok tidak boleh negatif." : "";
    }
    if (input.name === "isbn") {
        return (nilai !== "" && !/^[0-9-]+$/.test(nilai))
            ? "ISBN hanya boleh berisi angka dan tanda hubung (-)." : "";
    }
    if (input.name === "no_hp") {
        return (nilai !== "" && !/^[0-9+\-\s]+$/.test(nilai))
            ? "No. HP hanya boleh berisi angka, +, dan tanda hubung." : "";
    }
    return "";
}

function periksaField(input) {
    const pesan = pesanError(input);
    if (pesan) {
        tampilkanError(input, pesan);
    } else {
        hapusError(input);
    }
    input.classList.toggle("is-invalid", pesan !== "");
    return pesan === "";
}

function initValidasiForm() {
    const form = document.getElementById("form-tambah") || document.getElementById("form-edit");
    if (!form) return;

    const fields = form.querySelectorAll("input[name], select[name]");

    fields.forEach(function (input) {
        // Saat kolom ditinggalkan: cek. Setelah itu, setiap ketikan langsung dicek.
        input.addEventListener("blur", function () {
            input.dataset.disentuh = "1";
            periksaField(input);
        });
        input.addEventListener("input", function () {
            if (input.dataset.disentuh) periksaField(input);
        });
    });

    form.addEventListener("submit", function (e) {
        let pertama = null;
        fields.forEach(function (input) {
            if (!periksaField(input) && !pertama) pertama = input;
        });

        if (pertama) {
            e.preventDefault();
            pertama.focus();
            showToast("Periksa kembali isian yang ditandai merah.", "error");
        }
    });
}
//ops 4
function updateRowCounter() {
    const table = document.querySelector(".table-responsive table");
    const counter = document.getElementById("row-counter");

    if (!table || !counter) return;

    const rows = table.querySelectorAll("tbody tr");
    const visibleRows = Array.from(rows).filter(function (row) {
        return row.style.display !== "none";
    });

    counter.textContent =
        "Menampilkan " +
        visibleRows.length +
        " dari " +
        rows.length +
        " baris";
}

// ===== Pratinjau foto profil =====
function initPratinjauFoto() {
    const inputFoto = document.getElementById("foto_url");
    const img = document.getElementById("avatar-img");
    const inisial = document.getElementById("avatar-inisial");
    if (!inputFoto || !img || !inisial) return;

    function tampilkanInisial() {
        img.hidden = true;
        inisial.hidden = false;
    }
    function tampilkanFoto() {
        img.hidden = false;
        inisial.hidden = true;
    }

    img.addEventListener("load", tampilkanFoto);
    img.addEventListener("error", tampilkanInisial);

    // gambar sudah gagal dimuat sebelum JS siap
    if (img.getAttribute("src") && img.complete && img.naturalWidth === 0) {
        tampilkanInisial();
    }

    inputFoto.addEventListener("input", function () {
        const url = inputFoto.value.trim();
        if (url === "") {
            img.removeAttribute("src");
            tampilkanInisial();
        } else if (/^https?:\/\//i.test(url)) {
            img.src = url;   // memicu event load atau error di atas
        }
    });
}

// ===== Pilih foto profil dari daftar =====
function initPilihFoto() {
    const besar = document.getElementById("foto-besar");
    const inisial = document.getElementById("foto-besar-inisial");
    const terpilih = document.getElementById("foto-terpilih");
    const opsi = document.querySelectorAll(".foto-opsi");
    if (!besar || !terpilih || opsi.length === 0) return;

    opsi.forEach(function (tombol) {
        tombol.addEventListener("click", function () {
            besar.src = tombol.dataset.src;       // ganti gambar besar
            besar.hidden = false;
            if (inisial) inisial.hidden = true;
            terpilih.value = tombol.dataset.foto; // path yang akan dikirim

            opsi.forEach(function (o) {
                o.classList.remove("dipilih");
                o.setAttribute("aria-pressed", "false");
            });
            tombol.classList.add("dipilih");
            tombol.setAttribute("aria-pressed", "true");
        });
    });
}

// ===== Modal konfirmasi =====
// Mengembalikan Promise: true kalau user menekan "Ya", false kalau batal.
function tampilkanModal(pesan, labelYa) {
    const overlay = document.getElementById("modal-overlay");
    const btnYa = document.getElementById("modal-ya");
    const btnBatal = document.getElementById("modal-batal");

    // Cadangan kalau modal tidak ada di halaman
    if (!overlay) return Promise.resolve(confirm(pesan));

    document.getElementById("modal-pesan").textContent = pesan;
    btnYa.textContent = labelYa || "Ya, lanjutkan";
    overlay.hidden = false;
    btnBatal.focus();

    return new Promise(function (resolve) {
        function tutup(hasil) {
            overlay.hidden = true;
            btnYa.removeEventListener("click", ya);
            btnBatal.removeEventListener("click", batal);
            document.removeEventListener("keydown", tombol);
            resolve(hasil);
        }
        function ya() { tutup(true); }
        function batal() { tutup(false); }
        function tombol(e) { if (e.key === "Escape") tutup(false); }

        btnYa.addEventListener("click", ya);
        btnBatal.addEventListener("click", batal);
        document.addEventListener("keydown", tombol);
    });
}

// ===== Notifikasi custom (toast) =====
function showToast(pesan, tipe) {
    let box = document.getElementById("toast-container");
    if (!box) {
        box = document.createElement("div");
        box.id = "toast-container";
        box.setAttribute("aria-live", "polite");
        document.body.appendChild(box);
    }

    const toast = document.createElement("div");
    toast.className = "toast toast-" + (tipe || "success");
    toast.setAttribute("role", tipe === "error" ? "alert" : "status");

    const teks = document.createElement("span");
    teks.textContent = pesan;   // textContent, bukan innerHTML, supaya aman dari XSS

    const tutup = document.createElement("button");
    tutup.type = "button";
    tutup.className = "toast-tutup";
    tutup.setAttribute("aria-label", "Tutup notifikasi");
    tutup.innerHTML = "&times;";

    function hilang() {
        toast.classList.add("toast-hilang");
        setTimeout(function () { toast.remove(); }, 300);
    }
    tutup.addEventListener("click", hilang);

    toast.append(teks, tutup);
    box.appendChild(toast);
    setTimeout(hilang, 4500);   // hilang sendiri setelah 4,5 detik
}

// Ubah semua pesan flash dari PHP menjadi toast
function initFlashToast() {
    document.querySelectorAll(".flash").forEach(function (el) {
        let tipe = "success";
        if (el.classList.contains("flash-error")) tipe = "error";
        if (el.classList.contains("flash-warning")) tipe = "warning";

        showToast(el.textContent.trim(), tipe);
        el.remove();
    });
}

// ===== Pencarian dinamis dengan Fetch API =====
function initPencarianFetch() {
    const input = document.getElementById("search-input");
    const tbody = document.querySelector(".table-responsive table tbody");
    const pagination = document.querySelector(".pagination");
    if (!input || !tbody) return;

    let timer;
    let controller;

    async function muat(url) {
        if (controller) controller.abort();   // batalkan request lama yang belum selesai
        controller = new AbortController();

        tbody.closest("table").classList.add("loading");
        try {
            const res = await fetch(url, { signal: controller.signal });
            if (!res.ok) throw new Error("status " + res.status);

            const html = await res.text();
            const doc = new DOMParser().parseFromString(html, "text/html");

            const tbodyBaru = doc.querySelector(".table-responsive table tbody");
            if (!tbodyBaru) throw new Error("format halaman tidak sesuai");

            tbody.innerHTML = tbodyBaru.innerHTML;

            const paginationBaru = doc.querySelector(".pagination");
            if (pagination && paginationBaru) {
                pagination.innerHTML = paginationBaru.innerHTML;
            }
            updateRowCounter();
        } catch (err) {
            if (err.name === "AbortError") return;   // dibatalkan sengaja, bukan error
            showToast("Gagal memuat data: " + err.message, "error");
        } finally {
            tbody.closest("table").classList.remove("loading");
        }
    }

    function cari() {
        const url = new URL(window.location.href);
        url.searchParams.set("q", input.value.trim());
        url.searchParams.set("page", "1");
        muat(url.toString());
    }

    // Ketik di kolom cari: tunggu 300 ms setelah berhenti mengetik
    input.addEventListener("input", function () {
        clearTimeout(timer);
        timer = setTimeout(cari, 300);
    });

    // Tombol Cari / Enter: tetap tanpa reload
    const form = input.closest("form");
    if (form) {
        form.addEventListener("submit", function (e) {
            e.preventDefault();
            clearTimeout(timer);
            cari();
        });
    }

    // Klik nomor halaman: tanpa reload
    if (pagination) {
        pagination.addEventListener("click", function (e) {
            const a = e.target.closest("a");
            if (!a) return;
            e.preventDefault();
            muat(a.href);
        });
    }
}

document.addEventListener("DOMContentLoaded", function () {
    initNavToggle();
    initHapusConfirm();
    initPencarianFetch();
    initValidasiForm();
    initUpdateConfirm();
    initFlashToast();
    updateRowCounter();
    initPratinjauFoto();
    initPilihFoto();
});



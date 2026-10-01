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
    document.querySelectorAll(".btn-hapus").forEach(function (btn) {
        btn.addEventListener("click", function () {
            const row = btn.closest("tr");

            const cells = row.querySelectorAll("td");

            let nama = "data ini";

            if (cells.length > 1) {
                nama = cells[1].textContent.trim();
            }

            const yakin = confirm(
                'Yakin ingin menghapus "' + nama + '"?'
            );

            if (yakin && row) {
                row.remove();
                updateRowCounter();
            }
        });
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
            //ops 3
            const teks = row.querySelector("td").textContent.toLowerCase();
            row.style.display = teks.includes(keyword) ? "" : "none";
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

function initValidasiForm() {
    const form = document.getElementById("form-tambah");

    if (!form) return;

    form.addEventListener("submit", function (e) {
        let valid = true;
        // Field wajib
        const requiredFields = [
            {
                name: "judul",
                message: "Judul wajib diisi."
            },
            {
                name: "nama",
                message: "Nama wajib diisi."
            },
            {
                name: "pengarang",
                message: "Pengarang wajib diisi."
            },
            {
                name: "no_anggota",
                message: "No. Anggota wajib diisi."
            }
        ];
        requiredFields.forEach(function (field) {
            const input = form.querySelector(
                "[name='" + field.name + "']"
            );
            if (!input) return;
            if (input.value.trim() === "") {

                tampilkanError(
                    input,
                    field.message
                );
                valid = false;
            } else {
                hapusError(input);
            }

        });
        // Validasi tahun
        const tahun = form.querySelector("[name='tahun']");
        if (tahun) {
            const nilai = parseInt(
                tahun.value,
                10
            );

            if (
                isNaN(nilai) ||
                nilai < 1900 ||
                nilai > 2026
            ) {
                tampilkanError(
                    tahun,
                    "Tahun harus di antara 1900-2026."
                );
                valid = false;

            } else {
                hapusError(tahun);
            }
        }
        // Validasi stok
        const stok = form.querySelector("[name='stok']");
        if (stok) {
            const nilai = parseInt(
                stok.value,
                10
            );
            if (
                isNaN(nilai) ||
                nilai < 0
            ) {
                tampilkanError(
                    stok,
                    "Stok tidak boleh negatif."
                );
                valid = false;
            } else {
                hapusError(stok);
            }
        }


        // Validasi ISBN
        const isbn = form.querySelector("[name='isbn']");

        if (isbn && isbn.value.trim() !== "") {

            const polaISBN = /^[0-9-]+$/;

            if (!polaISBN.test(isbn.value.trim())) {

                tampilkanError(
                    isbn,
                    "ISBN hanya boleh berisi angka dan tanda hubung (-)."
                );

                valid = false;

            } else {

                hapusError(isbn);

            }
        }


        if (!valid) {
            e.preventDefault();
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

document.addEventListener("DOMContentLoaded", function () {
    initNavToggle();
    initHapusConfirm();
    initTableFilter();
    initValidasiForm();
    updateRowCounter();
});



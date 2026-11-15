    </main>

    <footer>
        <p>&copy; 2026 SIMPUS-Mini</p>
        <p>&copy; Boosted by AI</p>
    </footer>
    <div id="modal-overlay" class="modal-overlay" hidden>
        <div class="modal" role="alertdialog" aria-modal="true"
            aria-labelledby="modal-judul" aria-describedby="modal-pesan">
            <h3 id="modal-judul">Konfirmasi</h3>
            <p id="modal-pesan"></p>
            <div class="modal-aksi">
                <button type="button" id="modal-batal">Batal</button>
                <button type="button" id="modal-ya">Ya, hapus</button>
            </div>
        </div>
    </div>
    <script src="<?php echo $base; ?>assets/js/app.js"></script>
    <?php if (!empty($extra_scripts)): foreach ($extra_scripts as $src): ?>
    <script src="<?php echo $src; ?>"></script>
    <?php endforeach;
    endif; ?>
</body>
</html>
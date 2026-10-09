-- Tabel token "Ingat Saya"
CREATE TABLE IF NOT EXISTS remember_tokens (
    id         SERIAL    PRIMARY KEY,
    user_id    INTEGER   NOT NULL REFERENCES users (id) ON DELETE CASCADE,
    selector   CHAR(24)  NOT NULL UNIQUE,
    token_hash CHAR(64)  NOT NULL,
    expires_at TIMESTAMP NOT NULL
);

CREATE INDEX IF NOT EXISTS idx_remember_user ON remember_tokens (user_id);

-- Opsional: batasi nilai role agar tidak ada typo
ALTER TABLE users ADD CONSTRAINT chk_users_role CHECK (role IN ('admin', 'petugas'));

-- Jadikan akunmu admin (ganti username-nya)
UPDATE users SET role = 'admin' WHERE username = 'isi_username_di_sini';
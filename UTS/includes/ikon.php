<?php
function ikon(string $nama): string
{
    $path = [
        'cari'  => '<circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/>',
        'edit'  => '<path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/>',
        'hapus' => '<path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14H6L5 6"/>',
    ][$nama] ?? '';

    return '<svg class="ikon" viewBox="0 0 24 24" width="14" height="14" fill="none" '
         . 'stroke="currentColor" stroke-width="2" stroke-linecap="round" '
         . 'stroke-linejoin="round" aria-hidden="true">' . $path . '</svg>';
}
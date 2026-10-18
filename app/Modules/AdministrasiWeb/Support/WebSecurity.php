<?php

namespace App\Modules\AdministrasiWeb\Support;

class WebSecurity
{
    /**
     * Membersihkan konten HTML dari potensi XSS, script injection, dan event handlers berbahaya.
     */
    public static function cleanHtml(?string $html): string
    {
        if (empty($html)) {
            return '';
        }

        // Hapus script dan style blocks beserta isinya
        $html = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html);
        $html = preg_replace('#<style(.*?)>(.*?)</style>#is', '', $html);

        // Hanya izinkan tag formatting standar
        $allowedTags = '<p><br><strong><b><em><i><u><s><h2><h3><h4><h5><h6><ul><ol><li><a><blockquote><table><thead><tbody><tr><th><td><img><hr><span><div>';
        $cleaned = strip_tags($html, $allowedTags);

        // Hapus event handler on* (seperti onload, onclick, onerror)
        $cleaned = preg_replace('/\s*on[a-zA-Z]+\s*=\s*(["\'][^"\']*["\']|[^>\s]+)/i', '', $cleaned);

        // Hapus href atau src dengan skema javascript: atau vbscript:
        $cleaned = preg_replace('/(href|src)\s*=\s*(["\'])\s*(javascript|vbscript):/i', '$1=$2#', $cleaned);

        return trim($cleaned);
    }
}

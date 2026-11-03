<?php

namespace App\Modules\Kepesantrenan\Services;

use App\Modules\Kepesantrenan\Models\QuranSurah;
use Illuminate\Support\Facades\Schema;

class QuranHelper
{
    /**
     * Master data 114 Surat Al-Qur'an.
     */
    public static function getAllSurahs(): array
    {
        return [
            1 => ['nomor' => 1, 'nama' => 'Al-Fatihah', 'arab' => 'الفاتحة', 'arti' => 'Pembukaan', 'ayat' => 7, 'juz_start' => 1, 'juz_end' => 1, 'turun' => 'Makkiyyah'],
            2 => ['nomor' => 2, 'nama' => 'Al-Baqarah', 'arab' => 'البقرة', 'arti' => 'Sapi Betina', 'ayat' => 286, 'juz_start' => 1, 'juz_end' => 3, 'turun' => 'Madaniyyah'],
            3 => ['nomor' => 3, 'nama' => 'Ali \'Imran', 'arab' => 'آل عمران', 'arti' => 'Keluarga Imran', 'ayat' => 200, 'juz_start' => 3, 'juz_end' => 4, 'turun' => 'Madaniyyah'],
            4 => ['nomor' => 4, 'nama' => 'An-Nisa\'', 'arab' => 'النساء', 'arti' => 'Wanita', 'ayat' => 176, 'juz_start' => 4, 'juz_end' => 6, 'turun' => 'Madaniyyah'],
            5 => ['nomor' => 5, 'nama' => 'Al-Ma\'idah', 'arab' => 'المائدة', 'arti' => 'Jamuan Hidangan', 'ayat' => 120, 'juz_start' => 6, 'juz_end' => 7, 'turun' => 'Madaniyyah'],
            6 => ['nomor' => 6, 'nama' => 'Al-An\'am', 'arab' => 'الأنعام', 'arti' => 'Binatang Ternak', 'ayat' => 165, 'juz_start' => 7, 'juz_end' => 8, 'turun' => 'Makkiyyah'],
            7 => ['nomor' => 7, 'nama' => 'Al-A\'raf', 'arab' => 'الأعراف', 'arti' => 'Tempat Tertinggi', 'ayat' => 206, 'juz_start' => 8, 'juz_end' => 9, 'turun' => 'Makkiyyah'],
            8 => ['nomor' => 8, 'nama' => 'Al-Anfal', 'arab' => 'الأنفال', 'arti' => 'Rampasan Perang', 'ayat' => 75, 'juz_start' => 9, 'juz_end' => 10, 'turun' => 'Madaniyyah'],
            9 => ['nomor' => 9, 'nama' => 'At-Taubah', 'arab' => 'التوبة', 'arti' => 'Pengampunan', 'ayat' => 129, 'juz_start' => 10, 'juz_end' => 11, 'turun' => 'Madaniyyah'],
            10 => ['nomor' => 10, 'nama' => 'Yunus', 'arab' => 'يونس', 'arti' => 'Nabi Yunus', 'ayat' => 109, 'juz_start' => 11, 'juz_end' => 11, 'turun' => 'Makkiyyah'],
            11 => ['nomor' => 11, 'nama' => 'Hud', 'arab' => 'هود', 'arti' => 'Nabi Hud', 'ayat' => 123, 'juz_start' => 11, 'juz_end' => 12, 'turun' => 'Makkiyyah'],
            12 => ['nomor' => 12, 'nama' => 'Yusuf', 'arab' => 'يوسف', 'arti' => 'Nabi Yusuf', 'ayat' => 111, 'juz_start' => 12, 'juz_end' => 13, 'turun' => 'Makkiyyah'],
            13 => ['nomor' => 13, 'nama' => 'Ar-Ra\'d', 'arab' => 'الرعد', 'arti' => 'Guruh', 'ayat' => 43, 'juz_start' => 13, 'juz_end' => 13, 'turun' => 'Madaniyyah'],
            14 => ['nomor' => 14, 'nama' => 'Ibrahim', 'arab' => 'إبراهيم', 'arti' => 'Nabi Ibrahim', 'ayat' => 52, 'juz_start' => 13, 'juz_end' => 14, 'turun' => 'Makkiyyah'],
            15 => ['nomor' => 15, 'nama' => 'Al-Hijr', 'arab' => 'الحجر', 'arti' => 'Gunung Al-Hijr', 'ayat' => 99, 'juz_start' => 14, 'juz_end' => 14, 'turun' => 'Makkiyyah'],
            16 => ['nomor' => 16, 'nama' => 'An-Nahl', 'arab' => 'النحل', 'arti' => 'Lebah', 'ayat' => 128, 'juz_start' => 14, 'juz_end' => 14, 'turun' => 'Makkiyyah'],
            17 => ['nomor' => 17, 'nama' => 'Al-Isra\'', 'arab' => 'الإسراء', 'arti' => 'Memperjalankan di Malam Hari', 'ayat' => 111, 'juz_start' => 15, 'juz_end' => 15, 'turun' => 'Makkiyyah'],
            18 => ['nomor' => 18, 'nama' => 'Al-Kahf', 'arab' => 'الكهف', 'arti' => 'Gua', 'ayat' => 110, 'juz_start' => 15, 'juz_end' => 16, 'turun' => 'Makkiyyah'],
            19 => ['nomor' => 19, 'nama' => 'Maryam', 'arab' => 'مريم', 'arti' => 'Siti Maryam', 'ayat' => 98, 'juz_start' => 16, 'juz_end' => 16, 'turun' => 'Makkiyyah'],
            20 => ['nomor' => 20, 'nama' => 'Ta-Ha', 'arab' => 'طه', 'arti' => 'Tha-Ha', 'ayat' => 135, 'juz_start' => 16, 'juz_end' => 16, 'turun' => 'Makkiyyah'],
            21 => ['nomor' => 21, 'nama' => 'Al-Anbiya\'', 'arab' => 'الأنبياء', 'arti' => 'Para Nabi', 'ayat' => 112, 'juz_start' => 17, 'juz_end' => 17, 'turun' => 'Makkiyyah'],
            22 => ['nomor' => 22, 'nama' => 'Al-Hajj', 'arab' => 'الحج', 'arti' => 'Ibadah Haji', 'ayat' => 78, 'juz_start' => 17, 'juz_end' => 17, 'turun' => 'Madaniyyah'],
            23 => ['nomor' => 23, 'nama' => 'Al-Mu\'minun', 'arab' => 'المؤمنون', 'arti' => 'Orang-orang Beriman', 'ayat' => 118, 'juz_start' => 18, 'juz_end' => 18, 'turun' => 'Makkiyyah'],
            24 => ['nomor' => 24, 'nama' => 'An-Nur', 'arab' => 'النور', 'arti' => 'Cahaya', 'ayat' => 64, 'juz_start' => 18, 'juz_end' => 18, 'turun' => 'Madaniyyah'],
            25 => ['nomor' => 25, 'nama' => 'Al-Furqan', 'arab' => 'الفرقان', 'arti' => 'Pembeda', 'ayat' => 77, 'juz_start' => 18, 'juz_end' => 19, 'turun' => 'Makkiyyah'],
            26 => ['nomor' => 26, 'nama' => 'Asy-Syu\'ara\'', 'arab' => 'الشعراء', 'arti' => 'Para Penyair', 'ayat' => 227, 'juz_start' => 19, 'juz_end' => 19, 'turun' => 'Makkiyyah'],
            27 => ['nomor' => 27, 'nama' => 'An-Naml', 'arab' => 'النمل', 'arti' => 'Semut', 'ayat' => 93, 'juz_start' => 19, 'juz_end' => 20, 'turun' => 'Makkiyyah'],
            28 => ['nomor' => 28, 'nama' => 'Al-Qashash', 'arab' => 'القصص', 'arti' => 'Kisah-kisah', 'ayat' => 88, 'juz_start' => 20, 'juz_end' => 20, 'turun' => 'Makkiyyah'],
            29 => ['nomor' => 29, 'nama' => 'Al-\'Ankabut', 'arab' => 'العنكبوت', 'arti' => 'Laba-laba', 'ayat' => 69, 'juz_start' => 20, 'juz_end' => 21, 'turun' => 'Makkiyyah'],
            30 => ['nomor' => 30, 'nama' => 'Ar-Rum', 'arab' => 'الروم', 'arti' => 'Bangsa Romawi', 'ayat' => 60, 'juz_start' => 21, 'juz_end' => 21, 'turun' => 'Makkiyyah'],
            31 => ['nomor' => 31, 'nama' => 'Luqman', 'arab' => 'لقمان', 'arti' => 'Keluarga Luqman', 'ayat' => 34, 'juz_start' => 21, 'juz_end' => 21, 'turun' => 'Makkiyyah'],
            32 => ['nomor' => 32, 'nama' => 'As-Sajdah', 'arab' => 'السجدة', 'arti' => 'Sujud', 'ayat' => 30, 'juz_start' => 21, 'juz_end' => 21, 'turun' => 'Makkiyyah'],
            33 => ['nomor' => 33, 'nama' => 'Al-Ahzab', 'arab' => 'الأحزاب', 'arti' => 'Golongan Bersekutu', 'ayat' => 73, 'juz_start' => 21, 'juz_end' => 22, 'turun' => 'Madaniyyah'],
            34 => ['nomor' => 34, 'nama' => 'Saba\'', 'arab' => 'سبأ', 'arti' => 'Kaum Saba\'', 'ayat' => 54, 'juz_start' => 22, 'juz_end' => 22, 'turun' => 'Makkiyyah'],
            35 => ['nomor' => 35, 'nama' => 'Fathir', 'arab' => 'فاطر', 'arti' => 'Pencipta', 'ayat' => 45, 'juz_start' => 22, 'juz_end' => 22, 'turun' => 'Makkiyyah'],
            36 => ['nomor' => 36, 'nama' => 'Ya-Sin', 'arab' => 'يس', 'arti' => 'Yasin', 'ayat' => 83, 'juz_start' => 22, 'juz_end' => 23, 'turun' => 'Makkiyyah'],
            37 => ['nomor' => 37, 'nama' => 'Ash-Shaffat', 'arab' => 'الصافات', 'arti' => 'Barisan-barisan', 'ayat' => 182, 'juz_start' => 23, 'juz_end' => 23, 'turun' => 'Makkiyyah'],
            38 => ['nomor' => 38, 'nama' => 'Shad', 'arab' => 'ص', 'arti' => 'Shad', 'ayat' => 88, 'juz_start' => 23, 'juz_end' => 23, 'turun' => 'Makkiyyah'],
            39 => ['nomor' => 39, 'nama' => 'Az-Zumar', 'arab' => 'الزمر', 'arti' => 'Rombongan-rombongan', 'ayat' => 75, 'juz_start' => 23, 'juz_end' => 24, 'turun' => 'Makkiyyah'],
            40 => ['nomor' => 40, 'nama' => 'Ghafir', 'arab' => 'غافر', 'arti' => 'Maha Pengampun', 'ayat' => 85, 'juz_start' => 24, 'juz_end' => 24, 'turun' => 'Makkiyyah'],
            41 => ['nomor' => 41, 'nama' => 'Fushshilat', 'arab' => 'فصلت', 'arti' => 'Yang Dijelaskan', 'ayat' => 54, 'juz_start' => 24, 'juz_end' => 25, 'turun' => 'Makkiyyah'],
            42 => ['nomor' => 42, 'nama' => 'Asy-Syura', 'arab' => 'الشورى', 'arti' => 'Musyawarah', 'ayat' => 53, 'juz_start' => 25, 'juz_end' => 25, 'turun' => 'Makkiyyah'],
            43 => ['nomor' => 43, 'nama' => 'Az-Zukhruf', 'arab' => 'الزخرف', 'arti' => 'Perhiasan', 'ayat' => 89, 'juz_start' => 25, 'juz_end' => 25, 'turun' => 'Makkiyyah'],
            44 => ['nomor' => 44, 'nama' => 'Ad-Dukhan', 'arab' => 'الدخان', 'arti' => 'Kabut Asap', 'ayat' => 59, 'juz_start' => 25, 'juz_end' => 25, 'turun' => 'Makkiyyah'],
            45 => ['nomor' => 45, 'nama' => 'Al-Jatsiyah', 'arab' => 'الجاثية', 'arti' => 'Yang Berlutut', 'ayat' => 37, 'juz_start' => 25, 'juz_end' => 25, 'turun' => 'Makkiyyah'],
            46 => ['nomor' => 46, 'nama' => 'Al-Ahqaf', 'arab' => 'الأحقاف', 'arti' => 'Bukit-bukit Pasir', 'ayat' => 35, 'juz_start' => 26, 'juz_end' => 26, 'turun' => 'Makkiyyah'],
            47 => ['nomor' => 47, 'nama' => 'Muhammad', 'arab' => 'محمد', 'arti' => 'Nabi Muhammad', 'ayat' => 38, 'juz_start' => 26, 'juz_end' => 26, 'turun' => 'Madaniyyah'],
            48 => ['nomor' => 48, 'nama' => 'Al-Fath', 'arab' => 'الفتح', 'arti' => 'Kemenangan', 'ayat' => 29, 'juz_start' => 26, 'juz_end' => 26, 'turun' => 'Madaniyyah'],
            49 => ['nomor' => 49, 'nama' => 'Al-Hujurat', 'arab' => 'الحجرات', 'arti' => 'Kamar-kamar', 'ayat' => 18, 'juz_start' => 26, 'juz_end' => 26, 'turun' => 'Madaniyyah'],
            50 => ['nomor' => 50, 'nama' => 'Qaf', 'arab' => 'ق', 'arti' => 'Qaf', 'ayat' => 45, 'juz_start' => 26, 'juz_end' => 26, 'turun' => 'Makkiyyah'],
            51 => ['nomor' => 51, 'nama' => 'Adz-Dzariyat', 'arab' => 'الذاريات', 'arti' => 'Angin Menerbangkan', 'ayat' => 60, 'juz_start' => 26, 'juz_end' => 27, 'turun' => 'Makkiyyah'],
            52 => ['nomor' => 52, 'nama' => 'Ath-Thur', 'arab' => 'الطور', 'arti' => 'Bukit Tursina', 'ayat' => 49, 'juz_start' => 27, 'juz_end' => 27, 'turun' => 'Makkiyyah'],
            53 => ['nomor' => 53, 'nama' => 'An-Najm', 'arab' => 'النجم', 'arti' => 'Bintang', 'ayat' => 62, 'juz_start' => 27, 'juz_end' => 27, 'turun' => 'Makkiyyah'],
            54 => ['nomor' => 54, 'nama' => 'Al-Qamar', 'arab' => 'القمر', 'arti' => 'Bulan', 'ayat' => 55, 'juz_start' => 27, 'juz_end' => 27, 'turun' => 'Makkiyyah'],
            55 => ['nomor' => 55, 'nama' => 'Ar-Rahman', 'arab' => 'الرحمن', 'arti' => 'Maha Pengasih', 'ayat' => 78, 'juz_start' => 27, 'juz_end' => 27, 'turun' => 'Madaniyyah'],
            56 => ['nomor' => 56, 'nama' => 'Al-Waqi\'ah', 'arab' => 'الواقعة', 'arti' => 'Hari Kiamat', 'ayat' => 96, 'juz_start' => 27, 'juz_end' => 27, 'turun' => 'Makkiyyah'],
            57 => ['nomor' => 57, 'nama' => 'Al-Hadid', 'arab' => 'الحديد', 'arti' => 'Besi', 'ayat' => 29, 'juz_start' => 27, 'juz_end' => 27, 'turun' => 'Madaniyyah'],
            58 => ['nomor' => 58, 'nama' => 'Al-Mujadilah', 'arab' => 'المجادلة', 'arti' => 'Gugatan', 'ayat' => 22, 'juz_start' => 28, 'juz_end' => 28, 'turun' => 'Madaniyyah'],
            59 => ['nomor' => 59, 'nama' => 'Al-Hasyr', 'arab' => 'الحشر', 'arti' => 'Pengusiran', 'ayat' => 24, 'juz_start' => 28, 'juz_end' => 28, 'turun' => 'Madaniyyah'],
            60 => ['nomor' => 60, 'nama' => 'Al-Mumtahanah', 'arab' => 'الممتحنة', 'arti' => 'Wanita yang Diuji', 'ayat' => 13, 'juz_start' => 28, 'juz_end' => 28, 'turun' => 'Madaniyyah'],
            61 => ['nomor' => 61, 'nama' => 'Ash-Shaff', 'arab' => 'الصف', 'arti' => 'Barisan', 'ayat' => 14, 'juz_start' => 28, 'juz_end' => 28, 'turun' => 'Madaniyyah'],
            62 => ['nomor' => 62, 'nama' => 'Al-Jumu\'ah', 'arab' => 'الجمعة', 'arti' => 'Hari Jum\'at', 'ayat' => 11, 'juz_start' => 28, 'juz_end' => 28, 'turun' => 'Madaniyyah'],
            63 => ['nomor' => 63, 'nama' => 'Al-Munafiqun', 'arab' => 'المنافقون', 'arti' => 'Orang-orang Munafik', 'ayat' => 11, 'juz_start' => 28, 'juz_end' => 28, 'turun' => 'Madaniyyah'],
            64 => ['nomor' => 64, 'nama' => 'At-Taghabun', 'arab' => 'التغابن', 'arti' => 'Hari Dinampakkan Kesalahan', 'ayat' => 18, 'juz_start' => 28, 'juz_end' => 28, 'turun' => 'Madaniyyah'],
            65 => ['nomor' => 65, 'nama' => 'Ath-Thalaq', 'arab' => 'الطلاق', 'arti' => 'Perceraian', 'ayat' => 12, 'juz_start' => 28, 'juz_end' => 28, 'turun' => 'Madaniyyah'],
            66 => ['nomor' => 66, 'nama' => 'At-Tahrim', 'arab' => 'التحريم', 'arti' => 'Pengharaman', 'ayat' => 12, 'juz_start' => 28, 'juz_end' => 28, 'turun' => 'Madaniyyah'],
            67 => ['nomor' => 67, 'nama' => 'Al-Mulk', 'arab' => 'الملك', 'arti' => 'Kerajaan', 'ayat' => 30, 'juz_start' => 29, 'juz_end' => 29, 'turun' => 'Makkiyyah'],
            68 => ['nomor' => 68, 'nama' => 'Al-Qalam', 'arab' => 'القلم', 'arti' => 'Pena', 'ayat' => 52, 'juz_start' => 29, 'juz_end' => 29, 'turun' => 'Makkiyyah'],
            69 => ['nomor' => 69, 'nama' => 'Al-Haqqah', 'arab' => 'الحاقة', 'arti' => 'Hari Kiamat Pasti', 'ayat' => 52, 'juz_start' => 29, 'juz_end' => 29, 'turun' => 'Makkiyyah'],
            70 => ['nomor' => 70, 'nama' => 'Al-Ma\'arij', 'arab' => 'المعارج', 'arti' => 'Tempat Naik', 'ayat' => 44, 'juz_start' => 29, 'juz_end' => 29, 'turun' => 'Makkiyyah'],
            71 => ['nomor' => 71, 'nama' => 'Nuh', 'arab' => 'نوح', 'arti' => 'Nabi Nuh', 'ayat' => 28, 'juz_start' => 29, 'juz_end' => 29, 'turun' => 'Makkiyyah'],
            72 => ['nomor' => 72, 'nama' => 'Al-Jinn', 'arab' => 'الجن', 'arti' => 'Jin', 'ayat' => 28, 'juz_start' => 29, 'juz_end' => 29, 'turun' => 'Makkiyyah'],
            73 => ['nomor' => 73, 'nama' => 'Al-Muzzammil', 'arab' => 'المزمل', 'arti' => 'Orang Berselimut', 'ayat' => 20, 'juz_start' => 29, 'juz_end' => 29, 'turun' => 'Makkiyyah'],
            74 => ['nomor' => 74, 'nama' => 'Al-Muddatstsir', 'arab' => 'المدثر', 'arti' => 'Orang Berkemul', 'ayat' => 56, 'juz_start' => 29, 'juz_end' => 29, 'turun' => 'Makkiyyah'],
            75 => ['nomor' => 75, 'nama' => 'Al-Qiyamah', 'arab' => 'القيامة', 'arti' => 'Hari Berbangkit', 'ayat' => 40, 'juz_start' => 29, 'juz_end' => 29, 'turun' => 'Makkiyyah'],
            76 => ['nomor' => 76, 'nama' => 'Al-Insan', 'arab' => 'الإنسان', 'arti' => 'Manusia', 'ayat' => 31, 'juz_start' => 29, 'juz_end' => 29, 'turun' => 'Madaniyyah'],
            77 => ['nomor' => 77, 'nama' => 'Al-Mursalat', 'arab' => 'المرسلات', 'arti' => 'Malaikat Diutus', 'ayat' => 50, 'juz_start' => 29, 'juz_end' => 29, 'turun' => 'Makkiyyah'],
            78 => ['nomor' => 78, 'nama' => 'An-Naba\'', 'arab' => 'النبأ', 'arti' => 'Berita Besar', 'ayat' => 40, 'juz_start' => 30, 'juz_end' => 30, 'turun' => 'Makkiyyah'],
            79 => ['nomor' => 79, 'nama' => 'An-Nazi\'at', 'arab' => 'النازعات', 'arti' => 'Malaikat Mencabut', 'ayat' => 46, 'juz_start' => 30, 'juz_end' => 30, 'turun' => 'Makkiyyah'],
            80 => ['nomor' => 80, 'nama' => '\'Abasa', 'arab' => 'عبس', 'arti' => 'Ia Bermuka Masam', 'ayat' => 42, 'juz_start' => 30, 'juz_end' => 30, 'turun' => 'Makkiyyah'],
            81 => ['nomor' => 81, 'nama' => 'At-Takwir', 'arab' => 'التكوير', 'arti' => 'Menggulung', 'ayat' => 29, 'juz_start' => 30, 'juz_end' => 30, 'turun' => 'Makkiyyah'],
            82 => ['nomor' => 82, 'nama' => 'Al-Infithar', 'arab' => 'الانفطار', 'arti' => 'Terbelah', 'ayat' => 19, 'juz_start' => 30, 'juz_end' => 30, 'turun' => 'Makkiyyah'],
            83 => ['nomor' => 83, 'nama' => 'Al-Muthaffifin', 'arab' => 'المطففين', 'arti' => 'Orang-orang Curang', 'ayat' => 36, 'juz_start' => 30, 'juz_end' => 30, 'turun' => 'Makkiyyah'],
            84 => ['nomor' => 84, 'nama' => 'Al-Insyiqaq', 'arab' => 'الانشقاق', 'arti' => 'Terbelah', 'ayat' => 25, 'juz_start' => 30, 'juz_end' => 30, 'turun' => 'Makkiyyah'],
            85 => ['nomor' => 85, 'nama' => 'Al-Buruj', 'arab' => 'البروج', 'arti' => 'Gugusan Bintang', 'ayat' => 22, 'juz_start' => 30, 'juz_end' => 30, 'turun' => 'Makkiyyah'],
            86 => ['nomor' => 86, 'nama' => 'Ath-Thariq', 'arab' => 'الطارق', 'arti' => 'Yang Datang Malam Hari', 'ayat' => 17, 'juz_start' => 30, 'juz_end' => 30, 'turun' => 'Makkiyyah'],
            87 => ['nomor' => 87, 'nama' => 'Al-A\'la', 'arab' => 'الأعلى', 'arti' => 'Maha Tinggi', 'ayat' => 19, 'juz_start' => 30, 'juz_end' => 30, 'turun' => 'Makkiyyah'],
            88 => ['nomor' => 88, 'nama' => 'Al-Ghasyiyah', 'arab' => 'الغاشية', 'arti' => 'Hari Pembalasan Dahsyat', 'ayat' => 26, 'juz_start' => 30, 'juz_end' => 30, 'turun' => 'Makkiyyah'],
            89 => ['nomor' => 89, 'nama' => 'Al-Fajr', 'arab' => 'الفجر', 'arti' => 'Fajar', 'ayat' => 30, 'juz_start' => 30, 'juz_end' => 30, 'turun' => 'Makkiyyah'],
            90 => ['nomor' => 90, 'nama' => 'Al-Balad', 'arab' => 'البلد', 'arti' => 'Negeri', 'ayat' => 20, 'juz_start' => 30, 'juz_end' => 30, 'turun' => 'Makkiyyah'],
            91 => ['nomor' => 91, 'nama' => 'Asy-Syams', 'arab' => 'الشمس', 'arti' => 'Matahari', 'ayat' => 15, 'juz_start' => 30, 'juz_end' => 30, 'turun' => 'Makkiyyah'],
            92 => ['nomor' => 92, 'nama' => 'Al-Lail', 'arab' => 'الليل', 'arti' => 'Malam', 'ayat' => 21, 'juz_start' => 30, 'juz_end' => 30, 'turun' => 'Makkiyyah'],
            93 => ['nomor' => 93, 'nama' => 'Adh-Dhuha', 'arab' => 'الضحى', 'arti' => 'Waktu Dhuha', 'ayat' => 11, 'juz_start' => 30, 'juz_end' => 30, 'turun' => 'Makkiyyah'],
            94 => ['nomor' => 94, 'nama' => 'Al-Insyirah', 'arab' => 'الشرح', 'arti' => 'Kelapangan Dada', 'ayat' => 8, 'juz_start' => 30, 'juz_end' => 30, 'turun' => 'Makkiyyah'],
            95 => ['nomor' => 95, 'nama' => 'At-Tin', 'arab' => 'التين', 'arti' => 'Buah Tin', 'ayat' => 8, 'juz_start' => 30, 'juz_end' => 30, 'turun' => 'Makkiyyah'],
            96 => ['nomor' => 96, 'nama' => 'Al-\'Alaq', 'arab' => 'العلق', 'arti' => 'Segumpal Darah', 'ayat' => 19, 'juz_start' => 30, 'juz_end' => 30, 'turun' => 'Makkiyyah'],
            97 => ['nomor' => 97, 'nama' => 'Al-Qadr', 'arab' => 'القدر', 'arti' => 'Kemuliaan', 'ayat' => 5, 'juz_start' => 30, 'juz_end' => 30, 'turun' => 'Makkiyyah'],
            98 => ['nomor' => 98, 'nama' => 'Al-Bayyinah', 'arab' => 'البينة', 'arti' => 'Bukti Nyata', 'ayat' => 8, 'juz_start' => 30, 'juz_end' => 30, 'turun' => 'Madaniyyah'],
            99 => ['nomor' => 99, 'nama' => 'Az-Zalzalah', 'arab' => 'الزلزلة', 'arti' => 'Kegoncangan', 'ayat' => 8, 'juz_start' => 30, 'juz_end' => 30, 'turun' => 'Madaniyyah'],
            100 => ['nomor' => 100, 'nama' => 'Al-\'Adiyat', 'arab' => 'العاديات', 'arti' => 'Kuda yang Berlari Kencang', 'ayat' => 11, 'juz_start' => 30, 'juz_end' => 30, 'turun' => 'Makkiyyah'],
            101 => ['nomor' => 101, 'nama' => 'Al-Qari\'ah', 'arab' => 'القارعة', 'arti' => 'Hari Kiamat yang Mengguncang', 'ayat' => 11, 'juz_start' => 30, 'juz_end' => 30, 'turun' => 'Makkiyyah'],
            102 => ['nomor' => 102, 'nama' => 'At-Takatsur', 'arab' => 'التكاثر', 'arti' => 'Bermegah-megahan', 'ayat' => 8, 'juz_start' => 30, 'juz_end' => 30, 'turun' => 'Makkiyyah'],
            103 => ['nomor' => 103, 'nama' => 'Al-\'Ashr', 'arab' => 'العصر', 'arti' => 'Masa / Waktu', 'ayat' => 3, 'juz_start' => 30, 'juz_end' => 30, 'turun' => 'Makkiyyah'],
            104 => ['nomor' => 104, 'nama' => 'Al-Humazah', 'arab' => 'الهمزة', 'arti' => 'Pengumpat', 'ayat' => 9, 'juz_start' => 30, 'juz_end' => 30, 'turun' => 'Makkiyyah'],
            105 => ['nomor' => 105, 'nama' => 'Al-Fil', 'arab' => 'الفيل', 'arti' => 'Gajah', 'ayat' => 5, 'juz_start' => 30, 'juz_end' => 30, 'turun' => 'Makkiyyah'],
            106 => ['nomor' => 106, 'nama' => 'Quraisy', 'arab' => 'قريش', 'arti' => 'Suku Quraisy', 'ayat' => 4, 'juz_start' => 30, 'juz_end' => 30, 'turun' => 'Makkiyyah'],
            107 => ['nomor' => 107, 'nama' => 'Al-Ma\'un', 'arab' => 'الماعون', 'arti' => 'Barang yang Berguna', 'ayat' => 7, 'juz_start' => 30, 'juz_end' => 30, 'turun' => 'Makkiyyah'],
            108 => ['nomor' => 108, 'nama' => 'Al-Kautsar', 'arab' => 'الكوثر', 'arti' => 'Nikmat yang Berlimpah', 'ayat' => 3, 'juz_start' => 30, 'juz_end' => 30, 'turun' => 'Makkiyyah'],
            109 => ['nomor' => 109, 'nama' => 'Al-Kafirun', 'arab' => 'الكافرون', 'arti' => 'Orang-orang Kafir', 'ayat' => 6, 'juz_start' => 30, 'juz_end' => 30, 'turun' => 'Makkiyyah'],
            110 => ['nomor' => 110, 'nama' => 'An-Nashr', 'arab' => 'النصر', 'arti' => 'Pertolongan', 'ayat' => 3, 'juz_start' => 30, 'juz_end' => 30, 'turun' => 'Madaniyyah'],
            111 => ['nomor' => 111, 'nama' => 'Al-Lahab', 'arab' => 'المسد', 'arti' => 'Gejolak Api', 'ayat' => 5, 'juz_start' => 30, 'juz_end' => 30, 'turun' => 'Makkiyyah'],
            112 => ['nomor' => 112, 'nama' => 'Al-Ikhlas', 'arab' => 'الإخلاص', 'arti' => 'Kemurnian Keesaan Allah', 'ayat' => 4, 'juz_start' => 30, 'juz_end' => 30, 'turun' => 'Makkiyyah'],
            113 => ['nomor' => 113, 'nama' => 'Al-Falaq', 'arab' => 'الفلق', 'arti' => 'Waktu Subuh', 'ayat' => 5, 'juz_start' => 30, 'juz_end' => 30, 'turun' => 'Makkiyyah'],
            114 => ['nomor' => 114, 'nama' => 'An-Nas', 'arab' => 'الناس', 'arti' => 'Manusia', 'ayat' => 6, 'juz_start' => 30, 'juz_end' => 30, 'turun' => 'Makkiyyah'],
        ];
    }

    /**
     * Pemetaan bagian surat & rentang ayat per Juz (1 s/d 30).
     * Format per juz: array of [surah_id, ayat_start, ayat_end]
     */
    public static function getJuzSections(int $juz): array
    {
        $map = [
            1 => [
                ['surah' => 1, 'start' => 1, 'end' => 7],
                ['surah' => 2, 'start' => 1, 'end' => 141],
            ],
            2 => [
                ['surah' => 2, 'start' => 142, 'end' => 252],
            ],
            3 => [
                ['surah' => 2, 'start' => 253, 'end' => 286],
                ['surah' => 3, 'start' => 1, 'end' => 92],
            ],
            4 => [
                ['surah' => 3, 'start' => 93, 'end' => 200],
                ['surah' => 4, 'start' => 1, 'end' => 23],
            ],
            5 => [
                ['surah' => 4, 'start' => 24, 'end' => 147],
            ],
            6 => [
                ['surah' => 4, 'start' => 148, 'end' => 176],
                ['surah' => 5, 'start' => 1, 'end' => 81],
            ],
            7 => [
                ['surah' => 5, 'start' => 82, 'end' => 120],
                ['surah' => 6, 'start' => 1, 'end' => 110],
            ],
            8 => [
                ['surah' => 6, 'start' => 111, 'end' => 165],
                ['surah' => 7, 'start' => 1, 'end' => 87],
            ],
            9 => [
                ['surah' => 7, 'start' => 88, 'end' => 206],
                ['surah' => 8, 'start' => 1, 'end' => 40],
            ],
            10 => [
                ['surah' => 8, 'start' => 41, 'end' => 75],
                ['surah' => 9, 'start' => 1, 'end' => 92],
            ],
            11 => [
                ['surah' => 9, 'start' => 93, 'end' => 129],
                ['surah' => 10, 'start' => 1, 'end' => 109],
                ['surah' => 11, 'start' => 1, 'end' => 5],
            ],
            12 => [
                ['surah' => 11, 'start' => 6, 'end' => 123],
                ['surah' => 12, 'start' => 1, 'end' => 52],
            ],
            13 => [
                ['surah' => 12, 'start' => 53, 'end' => 111],
                ['surah' => 13, 'start' => 1, 'end' => 43],
                ['surah' => 14, 'start' => 1, 'end' => 52],
            ],
            14 => [
                ['surah' => 15, 'start' => 1, 'end' => 99],
                ['surah' => 16, 'start' => 1, 'end' => 128],
            ],
            15 => [
                ['surah' => 17, 'start' => 1, 'end' => 111],
                ['surah' => 18, 'start' => 1, 'end' => 74],
            ],
            16 => [
                ['surah' => 18, 'start' => 75, 'end' => 110],
                ['surah' => 19, 'start' => 1, 'end' => 98],
                ['surah' => 20, 'start' => 1, 'end' => 135],
            ],
            17 => [
                ['surah' => 21, 'start' => 1, 'end' => 112],
                ['surah' => 22, 'start' => 1, 'end' => 78],
            ],
            18 => [
                ['surah' => 23, 'start' => 1, 'end' => 118],
                ['surah' => 24, 'start' => 1, 'end' => 64],
                ['surah' => 25, 'start' => 1, 'end' => 20],
            ],
            19 => [
                ['surah' => 25, 'start' => 21, 'end' => 77],
                ['surah' => 26, 'start' => 1, 'end' => 227],
                ['surah' => 27, 'start' => 1, 'end' => 55],
            ],
            20 => [
                ['surah' => 27, 'start' => 56, 'end' => 93],
                ['surah' => 28, 'start' => 1, 'end' => 88],
                ['surah' => 29, 'start' => 1, 'end' => 45],
            ],
            21 => [
                ['surah' => 29, 'start' => 46, 'end' => 69],
                ['surah' => 30, 'start' => 1, 'end' => 60],
                ['surah' => 31, 'start' => 1, 'end' => 34],
                ['surah' => 32, 'start' => 1, 'end' => 30],
                ['surah' => 33, 'start' => 1, 'end' => 30],
            ],
            22 => [
                ['surah' => 33, 'start' => 31, 'end' => 73],
                ['surah' => 34, 'start' => 1, 'end' => 54],
                ['surah' => 35, 'start' => 1, 'end' => 45],
                ['surah' => 36, 'start' => 1, 'end' => 27],
            ],
            23 => [
                ['surah' => 36, 'start' => 28, 'end' => 83],
                ['surah' => 37, 'start' => 1, 'end' => 182],
                ['surah' => 38, 'start' => 1, 'end' => 88],
                ['surah' => 39, 'start' => 1, 'end' => 31],
            ],
            24 => [
                ['surah' => 39, 'start' => 32, 'end' => 75],
                ['surah' => 40, 'start' => 1, 'end' => 85],
                ['surah' => 41, 'start' => 1, 'end' => 46],
            ],
            25 => [
                ['surah' => 41, 'start' => 47, 'end' => 54],
                ['surah' => 42, 'start' => 1, 'end' => 53],
                ['surah' => 43, 'start' => 1, 'end' => 89],
                ['surah' => 44, 'start' => 1, 'end' => 59],
                ['surah' => 45, 'start' => 1, 'end' => 37],
            ],
            26 => [
                ['surah' => 46, 'start' => 1, 'end' => 35],
                ['surah' => 47, 'start' => 1, 'end' => 38],
                ['surah' => 48, 'start' => 1, 'end' => 29],
                ['surah' => 49, 'start' => 1, 'end' => 18],
                ['surah' => 50, 'start' => 1, 'end' => 45],
                ['surah' => 51, 'start' => 1, 'end' => 30],
            ],
            27 => [
                ['surah' => 51, 'start' => 31, 'end' => 60],
                ['surah' => 52, 'start' => 1, 'end' => 49],
                ['surah' => 53, 'start' => 1, 'end' => 62],
                ['surah' => 54, 'start' => 1, 'end' => 55],
                ['surah' => 55, 'start' => 1, 'end' => 78],
                ['surah' => 56, 'start' => 1, 'end' => 96],
                ['surah' => 57, 'start' => 1, 'end' => 29],
            ],
            28 => [
                ['surah' => 58, 'start' => 1, 'end' => 22],
                ['surah' => 59, 'start' => 1, 'end' => 24],
                ['surah' => 60, 'start' => 1, 'end' => 13],
                ['surah' => 61, 'start' => 1, 'end' => 14],
                ['surah' => 62, 'start' => 1, 'end' => 11],
                ['surah' => 63, 'start' => 1, 'end' => 11],
                ['surah' => 64, 'start' => 1, 'end' => 18],
                ['surah' => 65, 'start' => 1, 'end' => 12],
                ['surah' => 66, 'start' => 1, 'end' => 12],
            ],
            29 => [
                ['surah' => 67, 'start' => 1, 'end' => 30],
                ['surah' => 68, 'start' => 1, 'end' => 52],
                ['surah' => 69, 'start' => 1, 'end' => 52],
                ['surah' => 70, 'start' => 1, 'end' => 44],
                ['surah' => 71, 'start' => 1, 'end' => 28],
                ['surah' => 72, 'start' => 1, 'end' => 28],
                ['surah' => 73, 'start' => 1, 'end' => 20],
                ['surah' => 74, 'start' => 1, 'end' => 56],
                ['surah' => 75, 'start' => 1, 'end' => 40],
                ['surah' => 76, 'start' => 1, 'end' => 31],
                ['surah' => 77, 'start' => 1, 'end' => 50],
            ],
            30 => [
                ['surah' => 78, 'start' => 1, 'end' => 40],
                ['surah' => 79, 'start' => 1, 'end' => 46],
                ['surah' => 80, 'start' => 1, 'end' => 42],
                ['surah' => 81, 'start' => 1, 'end' => 29],
                ['surah' => 82, 'start' => 1, 'end' => 19],
                ['surah' => 83, 'start' => 1, 'end' => 36],
                ['surah' => 84, 'start' => 1, 'end' => 25],
                ['surah' => 85, 'start' => 1, 'end' => 22],
                ['surah' => 86, 'start' => 1, 'end' => 17],
                ['surah' => 87, 'start' => 1, 'end' => 19],
                ['surah' => 88, 'start' => 1, 'end' => 26],
                ['surah' => 89, 'start' => 1, 'end' => 30],
                ['surah' => 90, 'start' => 1, 'end' => 20],
                ['surah' => 91, 'start' => 1, 'end' => 15],
                ['surah' => 92, 'start' => 1, 'end' => 21],
                ['surah' => 93, 'start' => 1, 'end' => 11],
                ['surah' => 94, 'start' => 1, 'end' => 8],
                ['surah' => 95, 'start' => 1, 'end' => 8],
                ['surah' => 96, 'start' => 1, 'end' => 19],
                ['surah' => 97, 'start' => 1, 'end' => 5],
                ['surah' => 98, 'start' => 1, 'end' => 8],
                ['surah' => 99, 'start' => 1, 'end' => 8],
                ['surah' => 100, 'start' => 1, 'end' => 11],
                ['surah' => 101, 'start' => 1, 'end' => 11],
                ['surah' => 102, 'start' => 1, 'end' => 8],
                ['surah' => 103, 'start' => 1, 'end' => 3],
                ['surah' => 104, 'start' => 1, 'end' => 9],
                ['surah' => 105, 'start' => 1, 'end' => 5],
                ['surah' => 106, 'start' => 1, 'end' => 4],
                ['surah' => 107, 'start' => 1, 'end' => 7],
                ['surah' => 108, 'start' => 1, 'end' => 3],
                ['surah' => 109, 'start' => 1, 'end' => 6],
                ['surah' => 110, 'start' => 1, 'end' => 3],
                ['surah' => 111, 'start' => 1, 'end' => 5],
                ['surah' => 112, 'start' => 1, 'end' => 4],
                ['surah' => 113, 'start' => 1, 'end' => 5],
                ['surah' => 114, 'start' => 1, 'end' => 6],
            ],
        ];

        return $map[$juz] ?? [];
    }

    /**
     * Hitung total ayat yang ada di dalam suatu Juz (1 s/d 30).
     */
    public static function getJuzTotalAyat(int $juz): int
    {
        $sections = self::getJuzSections($juz);
        $total = 0;
        foreach ($sections as $s) {
            $total += ($s['end'] - $s['start'] + 1);
        }

        return $total;
    }

    /**
     * Pastikan master data 114 Surat Al-Qur'an sudah terisi di database.
     * Jika kosong (misal hosting baru migrate tanpa seed), otomatis lakukan seeding mandiri.
     */
    public static function ensureSurahsSeeded(): void
    {
        try {
            if (Schema::hasTable('pesantren_quran_surahs') && QuranSurah::count() === 0) {
                $surahs = self::getAllSurahs();
                foreach ($surahs as $s) {
                    QuranSurah::updateOrCreate(
                        ['nomor_surah' => $s['nomor']],
                        [
                            'nama_latin' => $s['nama'],
                            'nama_arab' => $s['arab'],
                            'arti' => $s['arti'],
                            'jumlah_ayat' => $s['ayat'],
                            'juz_start' => $s['juz_start'],
                            'juz_end' => $s['juz_end'],
                            'tempat_turun' => $s['turun'],
                        ]
                    );
                }
            }
        } catch (\Throwable $e) {
            // Lewati jika tabel belum siap
        }
    }

    /**
     * Total ayat Al-Qur'an (6.236 ayat).
     */
    public const TOTAL_AYAT_ALQURAN = 6236;
}

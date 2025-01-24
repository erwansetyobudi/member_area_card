<?php
/**
 * @author Drajat Hasan
 * @email drajathasan20@gmail.com
 * @create date 2021-11-04 09:24:23
 * @modify date 2025-01-24
 * @desc Migration untuk tabel usulan_buku
 */

class CreateTable extends \SLiMS\Migration\Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $SQL = "CREATE TABLE IF NOT EXISTS `usulan_buku` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `nama_lengkap` VARCHAR(255) NOT NULL,
            `nomor_anggota` VARCHAR(50) NOT NULL,
            `institution` VARCHAR(255) DEFAULT NULL,
            `kontak` VARCHAR(255) DEFAULT NULL,
            `judul_buku` VARCHAR(255) NOT NULL,
            `pengarang` VARCHAR(255) DEFAULT NULL,
            `tahun_terbit` YEAR DEFAULT NULL,
            `penerbit` VARCHAR(255) DEFAULT NULL,
            `isbn` VARCHAR(20) DEFAULT NULL,
            `kategori` TEXT DEFAULT NULL,
            `format` TEXT DEFAULT NULL,
            `bahasa` VARCHAR(50) DEFAULT NULL,
            `harga` VARCHAR(50) DEFAULT NULL,
            `tautan` TEXT DEFAULT NULL,
            `alasan` TEXT NOT NULL,
            `tanggal_usulan` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `tanggapan` TEXT DEFAULT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        \SLiMS\DB::getInstance()->query($SQL);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $SQL = "DROP TABLE IF EXISTS `usulan_buku`;";
        \SLiMS\DB::getInstance()->query($SQL);
    }
}

<?php
namespace App\Rules;

class NIKValidator
{
    public static function isValid($nik)
    {
        // Validasi panjang
        if (strlen($nik) !== 16) return false;
        
        // Validasi provinsi (2 digit pertama)
        $provinceCode = substr($nik, 0, 2);
        if (!in_array($provinceCode, ['32', '33', '34'])) { // Contoh kode Jawa
            return false;
        }
        
        // Validasi tanggal lahir (ddmmyy)
        $dob = substr($nik, 6, 6);
        $day = substr($dob, 0, 2);
        $month = substr($dob, 2, 2);
        $year = substr($dob, 4, 2);
        
        if (!checkdate($month, $day, '20'.$year)) {
            return false;
        }
        
        return true;
    }
}
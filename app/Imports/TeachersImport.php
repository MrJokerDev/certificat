<?php

namespace App\Imports;

use App\Models\Teacher;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class TeachersImport implements ToModel, WithHeadingRow
{
    use Importable;
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Teacher([
            'ism' => $row['Ism'],
            'familiya' => $row['Familiya'],
            'sharif' => $row['Sharif'],
            'berilgan_sana' => Date::excelToDateTimeObject($row["Berilgan_sana"])->format('d.m.Y'),
            'umumiy' => $row['Umumiy_%'],
            'umumiy_ball' => $row['Umumiy_ball'],
            '1_modul' => $row['1_modul_%'],
            '1_modul_ball' => $row['1_modul_ball'],
            '2_modul' => $row['2_modul_%'],
            '2_modul_ball' => $row['2_modul_ball'],
            '3_modul' => $row['3_modul_%'],
            '3_modul_ball' => $row['3_modul_ball']
        ]);
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: vinod
 * Date: 3/30/2020
 * Time: 8:42 PM
 */

namespace Mondovo\DataTable;


use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ExportToExcel extends DefaultValueBinder implements FromCollection, WithCustomValueBinder
{
    protected $excel_data;

    public function __construct($excel_data)
    {
        $this->excel_data = $excel_data;
    }
    public function bindValue(Cell $cell, $value)
    {
        if (is_numeric($value)) {
            $cell->setValueExplicit($value, DataType::TYPE_NUMERIC);

            return true;
        }elseif (\DateTime::createFromFormat('Y-m-d H:i:s', $value) !== FALSE) {
            $cell->setValueExplicit(Date::dateTimeToExcel($value), DataType::TYPE_STRING);
            return true;
        }

        // else return default behavior
        return parent::bindValue($cell, $value);
    }

    public function collection()
    {
        $data = [
            [
                "name" => "ASDSAD",
                "Age" => 12,
                "date" => "03/31/2020"
            ],
            [
                "name" => "CVBVBCB",
                "Age" => 34,
                "date" => "03/30/2020"
            ]
        ];
        return new Collection($data);
    }
}
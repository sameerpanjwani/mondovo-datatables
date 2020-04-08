<?php
/**
 * Created by PhpStorm.
 * User: vinod
 * Date: 3/30/2020
 * Time: 8:42 PM
 */

namespace Mondovo\DataTable;


use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
//use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class ExportToExcel extends DefaultValueBinder implements FromView, WithCustomValueBinder, WithDrawings
{
    protected $head_rows;
    protected $data_rows;
    protected $file_name;
    protected $report_name;
    protected $report_date;
    protected $logo_location;
    protected $paying_user;

    public function __construct($head_rows, $data_rows, $file_name, $report_name, $report_date, $logo_location, $paying_user)
    {
        $this->head_rows = $head_rows;
        $this->data_rows = $data_rows;
        $this->file_name = $file_name;
        $this->report_name = $report_name;
        $this->report_date = $report_date;
        $this->logo_location = $logo_location;
        $this->paying_user = $paying_user;
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

        return parent::bindValue($cell, $value);
    }

    public function view(): View
    {
        return view('mondovo.datatable.export-excel', $this->getDataArray());
    }

    protected function getDataArray(): array
    {
        return [
            'head_rows' => $this->head_rows,
            'data_rows' => $this->data_rows,
            'file_name' => $this->file_name,
            'report_name' => $this->report_name,
            'report_date' => $this->report_date,
            'paying_user' => $this->paying_user
        ];
    }

    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo');
        $drawing->setPath($this->logo_location);
        $drawing->setHeight(55);
        $drawing->setWidth(152);
        $drawing->setCoordinates('A1');

        return $drawing;
    }
}
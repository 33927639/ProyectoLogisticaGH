<?php
namespace App\Http\Controllers;

use App\Models\TblCustomer;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class CustomerExportController extends Controller
{
    public function exportPdf()
    {
        $customers = TblCustomer::with(['tbl_municipality.tbl_department'])->get();
        $pdf = Pdf::loadView('exports.customers', compact('customers'));
        return $pdf->download('clientes.pdf');
    }
}

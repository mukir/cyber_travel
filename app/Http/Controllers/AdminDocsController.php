<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminDocsController extends Controller
{
    public function index()
    {
        return view('admin.docs');
    }

    public function pdf()
    {
        $pdf = Pdf::loadView('pdf.docs');
        return $pdf->download('System_Documentation.pdf');
    }
}


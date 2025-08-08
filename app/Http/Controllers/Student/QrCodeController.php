<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeController extends Controller
{
    public function show()
    {
        $student = Auth::guard('student')->user();
        
        return view('student.qr-code.show', compact('student'));
    }
    
    public function download()
    {
        $student = Auth::guard('student')->user();
        
        if (!$student->qr_code) {
            return redirect()->route('student.qr-code')->with('error', 'QR Code belum tersedia');
        }
        
        // Generate QR Code as PNG
        $qrCode = QrCode::format('png')
                       ->size(300)
                       ->margin(2)
                       ->generate($student->qr_code);
        
        $fileName = 'QR_' . $student->nis . '_' . $student->name . '.png';
        
        return response($qrCode)
               ->header('Content-Type', 'image/png')
               ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }
    
    public function regenerate(Request $request)
    {
        $student = Auth::guard('student')->user();
        
        // Generate QR code baru
        $newQrCode = $student->regenerateQrCode();
        
        return redirect()->route('student.qr-code')->with('success', 'QR Code berhasil di-generate ulang');
    }
}

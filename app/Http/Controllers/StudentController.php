<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\SchoolClass;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Student::with('schoolClass');
        
        // Filter berdasarkan kelas
        if ($request->filled('class_id')) {
            $query->where('school_class_id', $request->class_id);
        }
        
        // Filter berdasarkan pencarian
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('nis', 'like', '%' . $request->search . '%');
            });
        }
        
        // Pagination per page
        $perPage = $request->filled('per_page') ? (int)$request->per_page : 15;
        $perPage = in_array($perPage, [10, 15, 25, 50, 100]) ? $perPage : 15;
        
        $students = $query->orderBy('name')->paginate($perPage);
        $classes = SchoolClass::orderBy('name')->get();
        
        return view('students.index', compact('students', 'classes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $classes = SchoolClass::orderBy('name')->get();
        $selectedClassId = $request->get('class_id');
        
        return view('students.create', compact('classes', 'selectedClassId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nis' => 'required|string|max:20|unique:students,nis',
            'email' => 'nullable|email|unique:students,email',
            'school_class_id' => 'nullable|exists:school_classes,id',
            'parent_whatsapp_number' => 'required|string|max:20',
        ]);

        Student::create([
            'name' => $request->name,
            'nis' => $request->nis,
            'email' => $request->email, // Optional, no auto-generation
            'password' => Hash::make($request->nis), // Default password = NIS
            'school_class_id' => $request->school_class_id,
            'parent_whatsapp_number' => $request->parent_whatsapp_number,
            'is_active' => true,
        ]);

        return redirect()->route('admin.students.index')
                        ->with('success', 'Siswa berhasil ditambahkan. Password default: NIS siswa.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student)
    {
        $student->load(['schoolClass', 'attendances' => function($query) {
            $query->orderBy('attendance_time', 'desc')->limit(10);
        }]);
        
        return view('students.show', compact('student'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Student $student)
    {
        $classes = SchoolClass::orderBy('name')->get();
        return view('students.edit', compact('student', 'classes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Student $student)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nis' => ['required', 'string', 'max:20', Rule::unique('students')->ignore($student->id)],
            'email' => ['nullable', 'email', Rule::unique('students')->ignore($student->id)],
            'school_class_id' => 'nullable|exists:school_classes,id',
            'parent_whatsapp_number' => 'required|string|max:20',
            'is_active' => 'boolean',
        ]);

        $updateData = [
            'name' => $request->name,
            'nis' => $request->nis,
            'email' => $request->email, // Optional, no auto-generation
            'school_class_id' => $request->school_class_id,
            'parent_whatsapp_number' => $request->parent_whatsapp_number,
            'is_active' => $request->has('is_active'),
        ];

        $student->update($updateData);

        return redirect()->route('admin.students.index')
                        ->with('success', 'Data siswa berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        $student->delete();
        
        return redirect()->back()
                        ->with('success', 'Siswa berhasil dihapus.');
    }
    
    /**
     * Reset password siswa ke NIS
     */
    public function resetPassword(Student $student)
    {
        $student->resetPasswordToDefault();
        
        return redirect()->back()
                        ->with('success', 'Password siswa berhasil direset ke NIS: ' . $student->nis);
    }
    
    /**
     * Toggle active status siswa
     */
    public function toggleStatus(Student $student)
    {
        $student->update([
            'is_active' => !$student->is_active
        ]);
        
        $status = $student->is_active ? 'diaktifkan' : 'dinonaktifkan';
        
        return redirect()->back()
                        ->with('success', 'Status siswa berhasil ' . $status);
    }

    /**
     * Download Excel template for student import
     */
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set headers
        $headers = [
            'A1' => 'Nama Siswa',
            'B1' => 'NIS',
            'C1' => 'Nama Kelas',
            'D1' => 'No. WhatsApp Ortu'
        ];
        
        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
            $sheet->getStyle($cell)->getFont()->setBold(true);
            $sheet->getStyle($cell)->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setRGB('E3F2FD');
        }
        
        // Add example data
        $exampleData = [
            ['John Doe', '12345', 'X IPA 1', '081234567890'],
            ['Jane Smith', '12346', 'X IPS 2', '081234567891'],
            ['Bob Johnson', '12347', 'XI IPA 1', '081234567892']
        ];
        
        $row = 2;
        foreach ($exampleData as $data) {
            $sheet->setCellValue('A' . $row, $data[0]);
            $sheet->setCellValue('B' . $row, $data[1]);
            $sheet->setCellValue('C' . $row, $data[2]);
            $sheet->setCellValue('D' . $row, $data[3]);
            
            // Style example data as light gray
            $sheet->getStyle('A' . $row . ':D' . $row)->getFont()->getColor()->setRGB('888888');
            $row++;
        }
        
        // Add separator row with instruction
        $sheet->setCellValue('A' . $row, 'HAPUS BARIS CONTOH DI ATAS DAN ISI DATA SISWA DI BAWAH INI');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $sheet->getStyle('A' . $row)->getFont()->getColor()->setRGB('FF0000');
        $sheet->mergeCells('A' . $row . ':D' . $row);
        $row++;
        
        // Leave some empty rows for actual data entry
        for ($i = 0; $i < 3; $i++) {
            $sheet->setCellValue('A' . $row, '');
            $sheet->setCellValue('B' . $row, '');
            $sheet->setCellValue('C' . $row, '');
            $sheet->setCellValue('D' . $row, '');
            $row++;
        }
        
        // Auto-size columns for data
        foreach (range('A', 'D') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        // Add notes to the right side (column F)
        $sheet->setCellValue('F1', 'PETUNJUK PENGGUNAAN:');
        $sheet->getStyle('F1')->getFont()->setBold(true);
        $sheet->getStyle('F1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('FFE0B2');
        
        $sheet->setCellValue('F2', '1. Hapus baris contoh (abu-abu)');
        $sheet->setCellValue('F3', '2. Isi data siswa mulai baris kosong');
        $sheet->setCellValue('F4', '3. Format kolom:');
        $sheet->setCellValue('F5', '   • Nama: Nama lengkap siswa');
        $sheet->setCellValue('F6', '   • NIS: Angka/huruf (harus unik)');
        $sheet->setCellValue('F7', '   • Kelas: Nama kelas');
        $sheet->setCellValue('F8', '   • WhatsApp: 08xxxxxxxxxx');
        $sheet->setCellValue('F9', '4. Simpan sebagai Excel (.xlsx)');
        $sheet->setCellValue('F10', '5. Upload ke sistem');
        
        // Auto-size column F for notes
        $sheet->getColumnDimension('F')->setAutoSize(true);
        
        // Create writer and download
        $writer = new Xlsx($spreadsheet);
        $filename = 'Template_Import_Siswa_' . date('Y-m-d') . '.xlsx';
        
        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    /**
     * Import students from Excel file
     */
    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls|max:2048',
            'replace_existing' => 'boolean',
            'create_class' => 'boolean'
        ]);
        
        try {
            DB::beginTransaction();
            
            $file = $request->file('excel_file');
            $replaceExisting = $request->boolean('replace_existing');
            $createClass = $request->boolean('create_class');
            
            // Load the Excel file
            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();
            
            // Remove header row
            array_shift($rows);
            
            $imported = 0;
            $updated = 0;
            $classesCreated = 0;
            $createdClasses = []; // Cache untuk kelas yang sudah dibuat
            $errors = [];
            $rowNumber = 2; // Start from row 2 (after header)
            
            foreach ($rows as $row) {
                // Skip completely empty rows
                if (empty(array_filter($row))) {
                    $rowNumber++;
                    continue;
                }
                
                $name = trim($row[0] ?? '');
                $nis = trim($row[1] ?? '');
                $className = trim($row[2] ?? '');
                $whatsappNumber = trim($row[3] ?? '');
                
                // Skip rows that don't look like student data
                // Check if this row contains notes or non-data content
                if (empty($name) && empty($nis)) {
                    $rowNumber++;
                    continue;
                }
                
                // Skip rows that start with common note patterns
                $nameUpper = strtoupper($name);
                if (strpos($nameUpper, 'CATATAN') !== false || 
                    strpos($nameUpper, 'NAMA SISWA') !== false || 
                    strpos($nameUpper, 'TIPS') !== false ||
                    strpos($nameUpper, '•') !== false ||
                    strpos($nameUpper, '-') === 0) {
                    $rowNumber++;
                    continue;
                }
                
                // Validate required fields - both name and NIS must be filled
                if (empty($name) || empty($nis)) {
                    $errors[] = "Baris {$rowNumber}: Nama dan NIS harus diisi";
                    $rowNumber++;
                    continue;
                }
                
                // Validate NIS format (should be numeric or alphanumeric)
                if (!preg_match('/^[a-zA-Z0-9]+$/', $nis)) {
                    $errors[] = "Baris {$rowNumber}: Format NIS tidak valid (hanya boleh huruf dan angka)";
                    $rowNumber++;
                    continue;
                }
                
                // Validate WhatsApp number format if provided
                if (!empty($whatsappNumber)) {
                    // Remove any non-numeric characters for validation
                    $cleanNumber = preg_replace('/[^0-9]/', '', $whatsappNumber);
                    
                    // Check if it starts with 08 and has proper length (10-13 digits)
                    if (!preg_match('/^08[0-9]{8,11}$/', $cleanNumber)) {
                        $errors[] = "Baris {$rowNumber}: Format nomor WhatsApp tidak valid (gunakan format 08xxxxxxxxxx)";
                        $rowNumber++;
                        continue;
                    }
                }
                
                // Find or validate class
                $schoolClass = null;
                if (!empty($className)) {
                    // Check cache first
                    if (isset($createdClasses[$className])) {
                        $schoolClass = $createdClasses[$className];
                    } else {
                        $schoolClass = SchoolClass::where('name', $className)->first();
                        if (!$schoolClass) {
                            if ($createClass) {
                                // Create new class automatically
                                $schoolClass = SchoolClass::create([
                                    'name' => $className,
                                    'description' => 'Kelas dibuat otomatis dari import Excel'
                                ]);
                                $createdClasses[$className] = $schoolClass; // Cache the created class
                                $classesCreated++;
                            } else {
                                $errors[] = "Baris {$rowNumber}: Kelas '{$className}' tidak ditemukan";
                                $rowNumber++;
                                continue;
                            }
                        }
                    }
                }
                
                // Check if student exists
                $existingStudent = Student::where('nis', $nis)->first();
                
                if ($existingStudent) {
                    if ($replaceExisting) {
                        // Update existing student
                        $existingStudent->update([
                            'name' => $name,
                            'school_class_id' => $schoolClass ? $schoolClass->id : null,
                            'parent_whatsapp_number' => $whatsappNumber,
                        ]);
                        $updated++;
                    } else {
                        $errors[] = "Baris {$rowNumber}: NIS '{$nis}' sudah ada (gunakan opsi update jika ingin menimpa)";
                    }
                } else {
                    // Create new student
                    $student = Student::create([
                        'name' => $name,
                        'nis' => $nis,
                        'school_class_id' => $schoolClass ? $schoolClass->id : null,
                        'parent_whatsapp_number' => $whatsappNumber,
                        'password' => Hash::make($nis), // Default password is NIS
                        'qr_code' => 'student_' . $nis . '_' . Str::random(10),
                        'is_active' => true
                    ]);
                    $imported++;
                }
                
                $rowNumber++;
            }
            
            DB::commit();
            
            // Prepare success message
            $message = "Import selesai! ";
            if ($imported > 0) {
                $message .= "{$imported} siswa baru berhasil diimport. ";
            }
            if ($updated > 0) {
                $message .= "{$updated} siswa berhasil diupdate. ";
            }
            if ($classesCreated > 0) {
                $message .= "{$classesCreated} kelas baru berhasil dibuat. ";
            }
            
            if (!empty($errors)) {
                $message .= "Terdapat " . count($errors) . " kesalahan: " . implode('; ', array_slice($errors, 0, 3));
                if (count($errors) > 3) {
                    $message .= " dan " . (count($errors) - 3) . " kesalahan lainnya.";
                }
                
                return redirect()->route('admin.students.index')
                               ->with('warning', $message);
            }
            
            return redirect()->route('admin.students.index')
                           ->with('success', $message);
                           
        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()->route('admin.students.index')
                           ->with('error', 'Terjadi kesalahan saat import: ' . $e->getMessage());
        }
    }

    /**
     * Export students to Excel file
     */
    public function export(Request $request)
    {
        $request->validate([
            'class_id' => 'nullable|exists:school_classes,id',
            'include_inactive' => 'boolean'
        ]);

        try {
            $classId = $request->input('class_id');
            $includeInactive = $request->boolean('include_inactive');
            
            // Build query
            $query = Student::with('schoolClass');
            
            if ($classId) {
                $query->where('school_class_id', $classId);
            }
            
            if (!$includeInactive) {
                $query->where('is_active', true);
            }
            
            $students = $query->orderBy('name')->get();
            
            // Create spreadsheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Set headers
            $headers = [
                'A1' => 'No',
                'B1' => 'Nama Siswa',
                'C1' => 'NIS',
                'D1' => 'Kelas',
                'E1' => 'No. WhatsApp Ortu',
                'F1' => 'Status'
            ];
            
            foreach ($headers as $cell => $value) {
                $sheet->setCellValue($cell, $value);
                $sheet->getStyle($cell)->getFont()->setBold(true);
                $sheet->getStyle($cell)->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('4472C4');
                $sheet->getStyle($cell)->getFont()->getColor()->setRGB('FFFFFF');
            }
            
            // Add data
            $row = 2;
            foreach ($students as $index => $student) {
                $sheet->setCellValue('A' . $row, $index + 1);
                $sheet->setCellValue('B' . $row, $student->name);
                $sheet->setCellValue('C' . $row, $student->nis);
                $sheet->setCellValue('D' . $row, $student->schoolClass ? $student->schoolClass->name : '-');
                $sheet->setCellValue('E' . $row, $student->parent_whatsapp_number ?: '-');
                $sheet->setCellValue('F' . $row, $student->is_active ? 'Aktif' : 'Tidak Aktif');
                
                // Style inactive students
                if (!$student->is_active) {
                    $sheet->getStyle('A' . $row . ':F' . $row)->getFont()->getColor()->setRGB('888888');
                }
                
                $row++;
            }
            
            // Auto-size columns
            foreach (range('A', 'F') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }
            
            // Add borders
            $sheet->getStyle('A1:F' . ($row - 1))->getBorders()->getAllBorders()
                ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            
            // Generate filename
            $className = $classId ? SchoolClass::find($classId)->name : 'Semua_Kelas';
            $filename = 'Data_Siswa_' . str_replace(' ', '_', $className) . '_' . date('Y-m-d') . '.xlsx';
            
            // Create writer and download
            $writer = new Xlsx($spreadsheet);
            
            // Set headers for download
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            
            $writer->save('php://output');
            exit;
            
        } catch (\Exception $e) {
            return redirect()->route('admin.students.index')
                           ->with('error', 'Terjadi kesalahan saat export: ' . $e->getMessage());
        }
    }
}

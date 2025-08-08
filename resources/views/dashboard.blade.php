@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 text-primary">
                    <i class="bi bi-speedometer2 me-2"></i>Dashboard Admin
                </h1>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.scanner') }}" class="btn btn-primary">
                        <i class="bi bi-qr-code-scan me-2"></i>QR Scanner
                    </a>
                    <small class="text-muted align-self-end">
                        <?php
                            setlocale(LC_TIME, 'id_ID.utf8');
                            $days = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
                            $months = ['January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret', 'April' => 'April', 'May' => 'Mei', 'June' => 'Juni', 'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September', 'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'];
                            $dayName = $days[now()->format('l')];
                            $monthName = $months[now()->format('F')];
                        ?>
                        {{ $dayName }}, {{ now()->format('d ') . $monthName . now()->format(' Y') }}
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Kartu Statistik -->
    <div class="row mb-4">
        <!-- Total Siswa -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card card-stats h-100 shadow-sm">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h5 class="card-title text-uppercase text-muted mb-0">Total Siswa</h5>
                            <span class="h2 font-weight-bold mb-0">{{ $totalStudents }}</span>
                        </div>
                        <div class="col-auto">
                            <div class="stat-icon">
                                <i class="bi bi-people-fill"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hadir Hari Ini -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card card-stats h-100 shadow-sm">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h5 class="card-title text-uppercase text-muted mb-0">Hadir Hari Ini</h5>
                            <span class="h2 font-weight-bold mb-0 text-success">{{ $presentToday }}</span>
                        </div>
                        <div class="col-auto">
                            <div class="stat-icon text-success">
                                <i class="bi bi-person-check-fill"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Terlambat Hari Ini -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card card-stats h-100 shadow-sm">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h5 class="card-title text-uppercase text-muted mb-0">Terlambat</h5>
                            <span class="h2 font-weight-bold mb-0 text-warning">{{ $lateToday }}</span>
                        </div>
                        <div class="col-auto">
                            <div class="stat-icon text-warning">
                                <i class="bi bi-clock-fill"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Kelas -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card card-stats h-100 shadow-sm">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h5 class="card-title text-uppercase text-muted mb-0">Total Kelas</h5>
                            <span class="h2 font-weight-bold mb-0">{{ $totalClasses }}</span>
                        </div>
                        <div class="col-auto">
                            <div class="stat-icon">
                                <i class="bi bi-collection-fill"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Tabel Absensi Terbaru -->
        <div class="col-xl-8 col-lg-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="bi bi-clock-history me-2"></i>Aktivitas Absensi Terbaru
                    </h6>
                </div>
                <div class="card-body">
                    @if($recentAttendances->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Nama Siswa</th>
                                        <th>Kelas</th>
                                        <th>Waktu Absen</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentAttendances as $index => $attendance)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $attendance->student->name }}</strong>
                                            <br>
                                            <small class="text-muted">NIS: {{ $attendance->student->nis }}</small>
                                        </td>
                                        <td>
                                            @if($attendance->student->schoolClass)
                                                <span class="badge bg-secondary">{{ $attendance->student->schoolClass->name }}</span>
                                            @else
                                                <span class="badge bg-warning">Tidak ada kelas</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small>
                                                {{ $attendance->attendance_time->format('d/m/Y') }}<br>
                                                <strong>{{ $attendance->attendance_time->format('H:i:s') }}</strong>
                                            </small>
                                        </td>
                                        <td>
                                            <span class="badge {{ $attendance->getStatusBadgeClass() }}">
                                                {{ $attendance->getStatusLabel() }}
                                            </span>
                                            @if($attendance->scan_method === 'qr_code')
                                                <br><small class="text-muted"><i class="bi bi-qr-code"></i> QR Scan</small>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-inbox display-4 text-muted"></i>
                            <p class="text-muted mt-2">Belum ada data absensi</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Grafik Kehadiran Mingguan -->
        <div class="col-xl-4 col-lg-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="bi bi-bar-chart-fill me-2"></i>Grafik Kehadiran Mingguan
                    </h6>
                    <small class="text-white-50">
                        <?php
                            $startDate = now()->subDays(6);
                            $endDate = now();
                            echo $days[$startDate->format('l')] . ', ' . $startDate->format('d ') . $months[$startDate->format('F')] . 
                                 ' - ' . 
                                 $days[$endDate->format('l')] . ', ' . $endDate->format('d ') . $months[$endDate->format('F')];
                        ?>
                    </small>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 350px;">
                        <canvas id="attendanceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    try {
        // Data untuk grafik
        const weeklyData = @json($weeklyData);
        console.log('Weekly Data:', weeklyData); // Debug
        
        if (!weeklyData || weeklyData.length === 0) {
            console.warn('No weekly data available');
            document.getElementById('attendanceChart').parentElement.innerHTML = 
                '<div class="text-center text-muted py-4"><i class="bi bi-exclamation-triangle"></i><br>Data tidak tersedia</div>';
            return;
        }
        
        const labels = weeklyData.map(item => item.date);
        const presentData = weeklyData.map(item => item.present);
        const lateData = weeklyData.map(item => item.late);
        const izinData = weeklyData.map(item => item.izin);
        const sakitData = weeklyData.map(item => item.sakit);
        const alphaData = weeklyData.map(item => item.alpha);
        
        console.log('Labels:', labels);
        console.log('Present Data:', presentData);
        console.log('Late Data:', lateData);
        console.log('Izin Data:', izinData);
        console.log('Sakit Data:', sakitData);
        console.log('Alpha Data:', alphaData);

        // Konfigurasi Chart.js
        const ctx = document.getElementById('attendanceChart');
        if (!ctx) {
            console.error('Canvas element not found');
            return;
        }
        
        const attendanceChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Hadir',
                        data: presentData,
                        backgroundColor: 'rgba(40, 167, 69, 0.8)',
                        borderColor: 'rgba(40, 167, 69, 1)',
                        borderWidth: 1,
                        borderRadius: 4,
                    },
                    {
                        label: 'Terlambat',
                        data: lateData,
                        backgroundColor: 'rgba(255, 193, 7, 0.8)',
                        borderColor: 'rgba(255, 193, 7, 1)',
                        borderWidth: 1,
                        borderRadius: 4,
                    },
                    {
                        label: 'Izin',
                        data: izinData,
                        backgroundColor: 'rgba(13, 202, 240, 0.8)',
                        borderColor: 'rgba(13, 202, 240, 1)',
                        borderWidth: 1,
                        borderRadius: 4,
                    },
                    {
                        label: 'Sakit',
                        data: sakitData,
                        backgroundColor: 'rgba(108, 117, 125, 0.8)',
                        borderColor: 'rgba(108, 117, 125, 1)',
                        borderWidth: 1,
                        borderRadius: 4,
                    },
                    {
                        label: 'Alpha',
                        data: alphaData,
                        backgroundColor: 'rgba(220, 53, 69, 0.8)',
                        borderColor: 'rgba(220, 53, 69, 1)',
                        borderWidth: 1,
                        borderRadius: 4,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            precision: 0
                        },
                        grid: {
                            color: 'rgba(0,0,0,0.1)'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 15,
                            font: {
                                size: 11
                            }
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: 'rgba(255,255,255,0.1)',
                        borderWidth: 1
                    }
                },
                animation: {
                    duration: 1000,
                    easing: 'easeInOutQuart'
                }
            }
        });
        
        console.log('Chart created successfully');
    } catch (error) {
        console.error('Error creating chart:', error);
        document.getElementById('attendanceChart').parentElement.innerHTML = 
            '<div class="text-center text-danger py-4"><i class="bi bi-exclamation-triangle"></i><br>Error loading chart</div>';
    }
});
</script>
@endsection

<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\Request;

class AdminReportController extends Controller
{
    protected ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->middleware(['auth:sanctum', 'role:admin']);
        $this->reportService = $reportService;
    }

    /**
     * 📤 Export báo cáo (CSV/XLSX)
     */
    public function export(Request $request)
    {
        $filters = $request->only(['status','type','search']);
        $format = $request->input('format', 'csv');
        $maxRows = 10000;

        $reports = $this->reportService->listReportsForAdmin($filters, $maxRows);

        if ($reports->count() === 0) {
            return response()->json(['message' => 'Không có dữ liệu để xuất'], 404);
        }

        $filename = 'reports_' . now()->format('Ymd_His') . '.' . $format;

        if ($format === 'csv') {
            $csv = $this->reportService->exportCsv($reports);
            return response($csv, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\""
            ]);
        }

        if ($format === 'xlsx') {
            $xlsx = $this->reportService->exportXlsx($reports);
            return response($xlsx, 200, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => "attachment; filename=\"$filename\""
            ]);
        }

        return response()->json(['message' => 'Định dạng không hợp lệ'], 400);
    }
}

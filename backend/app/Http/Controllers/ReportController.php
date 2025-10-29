<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateReportRequest;
use App\Models\Report;
use App\Services\ReportService;
use App\Repositories\ReportRepository;
use App\Events\ReportCreated;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    protected ReportService $reportService;
    protected ReportRepository $reportRepository;

    public function __construct(ReportService $reportService, ReportRepository $reportRepository)
    {
        $this->middleware('auth:sanctum');
        $this->reportService = $reportService;
        $this->reportRepository = $reportRepository;
    }

    /**
     * Tạo báo cáo mới
     */
    public function store(CreateReportRequest $request): JsonResponse
    {
        try {
            $report = $this->reportService->createReport($request->validated());

            // Optional image evidence uploads (multipart/form-data with images[])
            if ($request->hasFile('images')) {
                foreach ((array) $request->file('images') as $file) {
                    try {
                        $report->addMedia($file)->toMediaCollection('evidence');
                    } catch (\Throwable $e) {
                        // ignore single file error, continue
                    }
                }
            }
            
            // Dispatch event để thông báo admin
            event(new ReportCreated($report));

            return response()->json([
                'message' => 'Báo cáo đã được gửi thành công',
                'report' => $report->load([]),
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Create report failed: '.$e->getMessage());
            return response()->json([
                'message' => 'Có lỗi xảy ra khi tạo báo cáo',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy danh sách báo cáo của user hiện tại
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['status', 'type', 'search', 'sort', 'order', 'per_page']);
        $reports = $this->reportRepository->getUserReports(Auth::id(), $filters);

        return response()->json($reports);
    }

    /**
     * Xem chi tiết báo cáo
     */
    public function show(Report $report): JsonResponse
    {
        // Chỉ cho phép xem báo cáo của chính user đó
        if (!$this->reportRepository->canUserViewReport(Auth::id(), $report->id)) {
            return response()->json([
                'message' => 'Bạn không có quyền xem báo cáo này'
            ], 403);
        }

        $report->load(['reportable']);

        return response()->json($report);
    }

    /**
     * Lấy thống kê báo cáo của user
     */
    public function stats(): JsonResponse
    {
        $stats = $this->reportService->getUserReportStats(Auth::id());
        return response()->json($stats);
    }

    /**
     * Lấy danh sách lý do báo cáo có thể chọn
     */
    public function getReportReasons(): JsonResponse
    {
        $reasons = $this->reportService->getReportReasons();
        return response()->json($reasons);
    }

    /**
     * Lấy danh sách loại đối tượng có thể báo cáo
     */
    public function getReportableTypes(): JsonResponse
    {
        $types = $this->reportService->getReportableTypes();
        return response()->json($types);
    }
}

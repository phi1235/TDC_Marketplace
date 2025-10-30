<?php

namespace App\Listeners;

use App\Events\ReportCreated;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyAdminOnReportCreated implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ReportCreated $event): void
    {
        $report = $event->report;
        
        // Lấy tất cả admin
        $admins = User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })->get();

        // Gửi notification cho từng admin
        foreach ($admins as $admin) {
            $admin->notifications()->create([
                'type' => 'new_report',
                'title' => 'Có báo cáo mới',
                'message' => "Có báo cáo mới về {$report->reportable_type} #{$report->reportable_id}",
                'data' => [
                    'report_id' => $report->id,
                    'reportable_type' => $report->reportable_type,
                    'reportable_id' => $report->reportable_id,
                    'reason' => $report->reason
                ],
            ]);
        }
    }
}

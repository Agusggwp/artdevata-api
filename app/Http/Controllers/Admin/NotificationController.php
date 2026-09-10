<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessNotification;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\Quotation;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public static function getActiveNotifications()
    {
        $notifications = collect();

        // 1. Lead Follow-ups (due today or within 3 days or past)
        $leads = Lead::whereNotNull('next_follow_up_at')
            ->whereIn('status', ['new', 'contacted', 'qualified', 'negotiation'])
            ->get();

        foreach ($leads as $lead) {
            $isOverdue = $lead->next_follow_up_at->isPast();
            $notifications->push([
                'id' => 'lead_' . $lead->id,
                'category' => 'Lead Follow-up',
                'icon' => 'fa-user-clock text-amber-500 bg-amber-50',
                'title' => ($isOverdue ? 'Jatuh Tempo Follow Up: ' : 'Jadwal Follow Up: ') . $lead->name,
                'message' => 'Prospek ' . ($lead->company_name ?? $lead->name) . ' membutuhkan tindakan follow up.',
                'time' => $lead->next_follow_up_at->diffForHumans(),
                'url' => route('admin.leads.show', $lead->id),
                'is_urgent' => $isOverdue,
            ]);
        }

        // 2. Quotation Expirations (valid_until within 3 days or expired)
        $quotations = Quotation::whereIn('status', ['draft', 'sent', 'viewed'])
            ->where('valid_until', '<=', now()->addDays(3))
            ->get();

        foreach ($quotations as $qt) {
            $isExpired = $qt->valid_until->isPast();
            $notifications->push([
                'id' => 'qt_' . $qt->id,
                'category' => 'Quotation Expiration',
                'icon' => 'fa-file-circle-exclamation text-rose-500 bg-rose-50',
                'title' => ($isExpired ? 'Quotation Kadaluarsa: ' : 'Quotation Mendekati Expired: ') . $qt->quotation_number,
                'message' => 'Penawaran Rp ' . number_format($qt->total, 0, ',', '.') . ' berlaku s/d ' . $qt->valid_until->format('d M Y'),
                'time' => $qt->valid_until->diffForHumans(),
                'url' => route('admin.quotations.show', $qt->id),
                'is_urgent' => $isExpired,
            ]);
        }

        // 3. Project Deadlines (deadline within 5 days or past)
        $projects = Project::whereIn('status', ['planning', 'in_progress', 'ongoing', 'review'])
            ->whereNotNull('deadline')
            ->where('deadline', '<=', now()->addDays(5))
            ->get();

        foreach ($projects as $prj) {
            $isOverdue = $prj->deadline->isPast();
            $notifications->push([
                'id' => 'prj_' . $prj->id,
                'category' => 'Project Deadline',
                'icon' => 'fa-diagram-project text-blue-500 bg-blue-50',
                'title' => ($isOverdue ? 'Deadline Proyek Terlewati: ' : 'Mendekati Deadline Proyek: ') . $prj->name,
                'message' => 'Progress ' . $prj->progress . '% • Target: ' . $prj->deadline->format('d M Y'),
                'time' => $prj->deadline->diffForHumans(),
                'url' => route('admin.projects.show', $prj->id),
                'is_urgent' => $isOverdue,
            ]);
        }

        // 4. Task Deadlines (due_date within 3 days or past)
        $tasks = ProjectTask::whereIn('status', ['todo', 'in_progress', 'review'])
            ->whereNotNull('due_date')
            ->where('due_date', '<=', now()->addDays(3))
            ->take(5)
            ->get();

        foreach ($tasks as $task) {
            $isOverdue = $task->due_date->isPast();
            $notifications->push([
                'id' => 'task_' . $task->id,
                'category' => 'Task Deadline',
                'icon' => 'fa-list-check text-purple-500 bg-purple-50',
                'title' => ($isOverdue ? 'Tugas Terlewati: ' : 'Tugas Mendekati Due Date: ') . $task->title,
                'message' => 'Proyek: ' . ($task->project?->name ?? 'N/A'),
                'time' => $task->due_date->diffForHumans(),
                'url' => route('admin.projects.show', $task->project_id),
                'is_urgent' => $isOverdue,
            ]);
        }

        // 5. Invoice Due Dates (due_date within 5 days or overdue)
        $invoices = Invoice::whereIn('status', ['draft', 'sent', 'overdue'])
            ->whereNotNull('due_date')
            ->where('due_date', '<=', now()->addDays(5))
            ->take(5)
            ->get();

        foreach ($invoices as $inv) {
            $isOverdue = $inv->due_date->isPast();
            $notifications->push([
                'id' => 'inv_' . $inv->id,
                'category' => 'Invoice Due Date',
                'icon' => 'fa-file-invoice-dollar text-emerald-500 bg-emerald-50',
                'title' => ($isOverdue ? 'Invoice Overdue: ' : 'Invoice Jatuh Tempo: ') . $inv->invoice_number,
                'message' => 'Tagihan Rp ' . number_format($inv->total, 0, ',', '.') . ' Klien: ' . $inv->client_name,
                'time' => $inv->due_date->diffForHumans(),
                'url' => route('admin.invoices.show', $inv->id),
                'is_urgent' => $isOverdue,
            ]);
        }

        return $notifications->sortByDesc('is_urgent')->values();
    }

    public function markAllAsRead(Request $request)
    {
        BusinessNotification::whereNull('read_at')->update(['read_at' => now()]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Semua notifikasi ditandai dibaca.');
    }
}

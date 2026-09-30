<?php

namespace App\Http\Controllers;

use App\Models\AuditCategory;
use App\Models\Finding;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FindingController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $findings = Finding::query()->with(['unit', 'category', 'audit', 'followUps'])
            ->when($request->input('q'), function ($q, $v) {
                $q->where(function ($qq) use ($v) {
                    $qq->where('finding_number', 'like', "%{$v}%")
                        ->orWhere('description', 'like', "%{$v}%")
                        ->orWhereHas('unit', fn ($u) => $u->where('name', 'like', "%{$v}%"));
                });
            })
            ->when($request->input('status'), fn ($q, $v) => $q->where('status', $v))
            ->when($request->input('severity'), fn ($q, $v) => $q->where('severity', $v))
            ->when($request->input('category_id'), fn ($q, $v) => $q->where('category_id', $v))
            ->when($request->input('unit_id'), fn ($q, $v) => $q->where('unit_id', $v))
            ->when($user->role === User::ROLE_UNIT, fn ($q) => $q->where('unit_id', $user->unit_id))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'open' => $this->scopedCount($request, 'open'),
            'progress' => $this->scopedCount($request, 'progress'),
            'closed' => $this->scopedCount($request, 'closed'),
        ];

        $categories = AuditCategory::where('is_active', true)->get();
        $units = Unit::where('is_active', true)->get();

        return view('findings.index', compact('findings', 'stats', 'categories', 'units'));
    }

    public function show(Request $request, Finding $finding): View
    {
        $this->authorize('view-finding', $finding);

        $finding->load([
            'unit', 'category', 'question', 'audit.auditor',
            'followUps.user', 'verifications.verifier', 'verifications.followUp',
        ]);

        return view('findings.show', compact('finding'));
    }

    private function scopedCount(Request $request, string $status): int
    {
        $user = $request->user();

        return Finding::query()
            ->where('status', $status)
            ->when($request->input('category_id'), fn ($q, $v) => $q->where('category_id', $v))
            ->when($request->input('unit_id'), fn ($q, $v) => $q->where('unit_id', $v))
            ->when($user->role === User::ROLE_UNIT, fn ($q) => $q->where('unit_id', $user->unit_id))
            ->count();
    }
}

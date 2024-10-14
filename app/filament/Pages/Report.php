<?php

namespace App\Filament\Pages;

use App\Models\drone;
use App\Models\fligh;
use App\Models\team;
use App\Models\User;
use Filament\Pages\Page;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use PDF;

class Report extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    public static ?string $tenantOwnershipRelationshipName = 'teams';
    protected static string $view = 'filament.pages.report';

    public function downloadReport(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $currentTeamId = auth()->user()->teams()->first()->id;

        $flight = fligh::with(['drones', 'battreis', 'equidments', 'users'])
            ->where('teams_id', $currentTeamId) // Filter berdasarkan tim
            ->whereBetween('date_flight', [$startDate, $endDate])
            ->get();
        $user = User::whereHas('teams', function (Builder $query) use ($currentTeamId) {
                $query->where('team_user.team_id', $currentTeamId); // Pastikan nama tabel pivot benar
            })->get();
        $drone = drone::whereHas('teams', function ($query) use ($currentTeamId) {
                $query->where('id', $currentTeamId);
            })->get();
        $team = team::where('id', $currentTeamId)->get();
        $reportDate = now()->format('F j, Y');
        $pdf = PDF::loadView('report.report', compact('flight', 'reportDate', 'user', 'team', 'startDate', 'endDate', 'drone'));
        return $pdf->download('report.pdf');
    }
}

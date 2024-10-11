<?php

namespace App\Filament\Pages;

use App\Models\fligh;
use App\Models\team;
use App\Models\User;
use Filament\Pages\Page;
use Illuminate\Http\Request;
use PDF;

class Report extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.report';

    public function downloadReport(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // $customers = Customers::whereBetween('created_at', [$startDate, $endDate])->get();
        // $projects = Project::whereBetween('created_at', [$startDate, $endDate])->get();

        $flight = fligh::with(['drones', 'battreis', 'equidments', 'users'])
        ->whereBetween('date_flight', [$startDate, $endDate])
        ->get();
        $user = User::all();
        $team = team::all();
        $reportDate = now()->format('F j, Y');
        $pdf = PDF::loadView('report.report', compact('flight', 'reportDate', 'user', 'team', 'startDate', 'endDate'));
        return $pdf->download('report.pdf');
    }
}

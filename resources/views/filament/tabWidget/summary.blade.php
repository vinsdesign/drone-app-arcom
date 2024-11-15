<?php
    use App\Helpers\TranslationHelper;
    $currentTeamId = Auth()->user()->teams()->first()->id;
    //maintenance
    $maintenance_eq = App\Models\maintence_eq::whereHas('teams', function ($query) use ($currentTeamId) {
        $query->where('id', $currentTeamId);
    })
    ->whereNotIn('status', ['completed'])
    ->whereRaw('DATE(date) < ?', [Carbon\Carbon::now()->format('Y-m-d')])
    ->get();


    $maintenance_drone = App\Models\maintence_drone::whereHas('teams', function ($query) use ($currentTeamId){
    $query->where('id', $currentTeamId);
    })->whereNotIn('status', ['completed'])
    ->whereRaw('DATE(date) < ?', [Carbon\Carbon::now()->format('Y-m-d')])
    ->get();

    $countMaintenanceEq = $maintenance_eq->count();
    $countMaintenanceDrone = $maintenance_drone->count();
    $countMaintenance = $countMaintenanceEq + $countMaintenanceDrone;

    //end maintenance
    
    //planned Flight
    $flightSummary = App\Models\fligh::where('teams_id', $currentTeamId)
            ->whereDate('start_date_flight', Carbon\Carbon::today())
            ->orderBy('created_at', 'desc')
            ->get();
    $flightSummaryMonth = App\Models\fligh::where('teams_id', $currentTeamId)
            ->whereDate('start_date_flight', '>=', Carbon\Carbon::now()->startOfMonth())
            ->orderBy('created_at', 'desc')
            ->get();

?>
<style>
    .hide-mission{
        display: none;
    }
    /* Styles for active buttons */ 
    .active-button-day { 
        background-color: #fff;
        color: #f19022;
    } 
</style>
<x-filament-widgets::widget>
    <div class="container mx-auto p-6 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Today's Missions Section -->
            <div class="bg-gradient-to-r from-primary-500 to-primary-300 dark:from-primary-700 dark:bg-gray-800 p-6 rounded-lg shadow-lg transition-transform transform hover:scale-105 flex flex-col">
                {{-- day --}}
                <div class="content-mission flex-grow overflow-y-auto">
                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4 flex-shrink-0">{!! TranslationHelper::translateIfNeeded('Today Organization Missions') !!}</h2>
                    @if($flightSummary->count() < 1)
                        <div class="mb-2 flex justify-between items-center">
                            <span class="font-semibold text-gray-900 dark:text-gray-200">{!! TranslationHelper::translateIfNeeded('No Flight Today.')!!}</span>
                        </div>
                    @endif
                    <div class=" text-gray-800 dark:text-gray-200 overflow-y-auto flex-grow" style="max-height: 300px;"> 
                        @foreach($flightSummary as $item)
                            <div class="flex flex-col border-b border-gray-200 dark:border-gray-600 pb-2">
                                <div class="mb-2 flex justify-between items-center">
                                    <span class="font-semibold text-gray-900 dark:text-gray-200">{{$item->name ?? null}}</span>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">{!! TranslationHelper::translateIfNeeded('Missions Date:')!!} {{$item->start_date_flight ?? null}}</div>
                                </div>
                                <div class="flex justify-between items-center">
                                    <div class="text-sm text-gray-600 dark:text-gray-400">{!! TranslationHelper::translateIfNeeded('Project name:')!!} {{$item->projects->case ?? null}}</div>
                                    <div class="text-red-900 dark:text-red-300">{!! TranslationHelper::translateIfNeeded('Pilot:')!!} {{$item->users->name ?? null}}</div>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">{!! TranslationHelper::translateIfNeeded('Customers:')!!} {{$item->customers->name ?? null}}</span>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">{!! TranslationHelper::translateIfNeeded('Locations:')!!} {{$item->fligh_location->name ?? null}}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                 {{-- Mount --}}
                 <div class="hide-mission content-mission2 flex-grow overflow-y-auto">
                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4 flex-shrink-0">{!! TranslationHelper::translateIfNeeded('Month Organization Missions')!!}</h2>
                    @if($flightSummaryMonth->count() < 1)
                        <div class="mb-2 flex justify-between items-center">
                            <span class="font-semibold text-gray-900 dark:text-gray-200">{!! TranslationHelper::translateIfNeeded('No Flight Monthly.')!!}</span>
                        </div>
                    @endif
                    <div class="text-gray-800 dark:text-gray-200 overflow-y-auto flex-grow" style="max-height: 300px;"> 
                       @foreach($flightSummaryMonth as $item)
                           <div class="flex flex-col border-b border-gray-200 dark:border-gray-600 pb-2">
                               <div class="mb-2 flex justify-between items-center">
                                   <span class="font-semibold text-gray-900 dark:text-gray-200">{{$item->name ?? null}}</span>
                                   <div class="text-sm text-gray-600 dark:text-gray-400">{!! TranslationHelper::translateIfNeeded('Missions Date:')!!} {{$item->start_date_flight ?? null}}</div>
                               </div>
                               <div class="flex justify-between items-center">
                                   <div class="text-sm text-gray-600 dark:text-gray-400">{!! TranslationHelper::translateIfNeeded('Project name:')!!} {{$item->projects->case ?? null}}</div>
                                   <div class="text-red-900 dark:text-red-300">{!! TranslationHelper::translateIfNeeded('Pilot:')!!} {{$item->users->name ?? null}}</div>
                               </div>
                               <div class="flex justify-between items-center">
                                   <span class="text-sm text-gray-600 dark:text-gray-400">{!! TranslationHelper::translateIfNeeded('Customers:')!!} {{$item->customers->name ?? null}}</span>
                                   <div class="text-sm text-gray-600 dark:text-gray-400">{!! TranslationHelper::translateIfNeeded('Locations:')!!} {{$item->fligh_location->name ?? null}}</div>
                               </div>
                           </div>
                       @endforeach
                    </div>
                 </div>      
                 <!-- Tombol --> 
                 <div class="mt-4 flex space-x-2 flex-wrap flex-shrink-0 justify-center">
                    <button id="dayButton" class="active-button-day bg-primary-600 text-white font-semibold rounded-full px-4 py-2 shadow-md hover:bg-primary-100" onclick="missionDayActive()">
                        {!! TranslationHelper::translateIfNeeded('Day Missions')!!}
                    </button>
                    <button id="monthButton" class="bg-primary-600 text-white font-semibold rounded-full px-4 py-2 shadow-md hover:bg-primary-700" onclick="missionMonthActive()">
                       {!! TranslationHelper::translateIfNeeded('This Month')!!}
                    </button>
                </div>
                
            </div>
            <!-- Maintenance Overdue Section -->
            <div class="bg-gradient-to-r from-primary-500 t-primary-300 dark:from-primary-700 dark:bg-gray-800 p-6 rounded-lg shadow-lg transition-transform transform hover:scale-105 flex flex-col">
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4 flex-shrink-0">{!! TranslationHelper::translateIfNeeded('Maintenance Overdue')!!} ({{$countMaintenance}})</h2>
                <div class="text-gray-800 dark:text-gray-200 overflow-y-auto flex-grow" style="max-height: 300px;">
                
                    @if($countMaintenance < 1)
                    <div class="mb-2 flex justify-between items-center">
                        <span class="font-semibold text-gray-900 dark:text-gray-200">{!! TranslationHelper::translateIfNeeded('No Maintenance Overdue')!!}</span>
                    </div>
                    @endif
                    <!--maintenance overdue drones -->
                    @foreach($maintenance_drone as $item)
                        <div class="flex flex-col border-b border-gray-200 dark:border-gray-600 pb-2">
                            <div class="mb-2 flex justify-between items-center">
                                <span class="font-semibold text-gray-900 dark:text-gray-200">{{$item->name??null}}</span>
                                <div class="text-sm text-gray-600 dark:text-gray-400">{!! TranslationHelper::translateIfNeeded('Maintenance Date:')!!} {{$item->date??null}}</div>
                            </div>
                            <div class="flex justify-between items-center">
                                <div class="text-sm text-gray-600 dark:text-gray-400">{!! TranslationHelper::translateIfNeeded('Next Scheduled:')!!} {{$item->date?? null}}</div>
                                @php
                                    $now = Carbon\Carbon::now();
                                    $formatDate = \Carbon\Carbon::parse($item->date)->format('Y-m-d');
                                    $daysOverdueDiff = $now->diffInDays($item->date, false);
                                    $daysOverdueDiff = abs(intval($daysOverdueDiff));
                                @endphp
                                <div class="text-red-900 font dark:text-red-300">{!! TranslationHelper::translateIfNeeded('Overdue:')!!} {{$daysOverdueDiff}}</div>     

                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">{{$item->drone->brand?? null}}</span>
                                <div class="text-sm text-gray-600 dark:text-gray-400">{{$item->drone->name??null}}</div>
                            </div>
                        </div>
                    @endforeach
                    <!-- maintenance overdue Eequipments -->
                    @foreach($maintenance_eq as $item)
                        <div class="flex flex-col border-b border-gray-200 dark:border-gray-600 pb-2">
                            <div class="mb-2 flex justify-between items-center">
                                <span class="font-semibold text-gray-900 dark:text-gray-200">{{$item->name??null}}</span>
                                <div class="text-sm text-gray-600 dark:text-gray-400">{!! TranslationHelper::translateIfNeeded('Maintenance Date:')!!} {{$item->date??null}}</div>
                            </div>
                            <div class="flex justify-between items-center">
                                <div class="text-sm text-gray-600 dark:text-gray-400">{!! TranslationHelper::translateIfNeeded('Next Scheduled:')!!} {{$item->date?? null}}</div>
                                @php
                                    $now = Carbon\Carbon::now();
                                    $formatDate = \Carbon\Carbon::parse($item->date)->format('Y-m-d');
                                    $daysOverdueDiff = $now->diffInDays($item->date, false);
                                    $daysOverdueDiff = abs(intval($daysOverdueDiff));
                                @endphp
                                <div class="text-red-900 font dark:text-red-300">{!! TranslationHelper::translateIfNeeded('Overdue:')!!} {{$daysOverdueDiff}}</div>     
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">{{$item->drone->brand?? null}}</span>
                                <div class="text-sm text-gray-600 dark:text-gray-400">{{$item->drone->name??null}}</div>
                            </div>
                        </div>
                    @endforeach
                    <!--end-->
                </div>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
<script>
    function missionDayActive() {
        const content = document.querySelector('.content-mission');
        const content2  = document.querySelector('.content-mission2');
        content2.classList.add('hide-mission');
        content.classList.remove('hide-mission');
        document.getElementById('dayButton').classList.add('active-button-day');
        document.getElementById('monthButton').classList.remove('active-button-day');
    }
    function missionMonthActive() {
        const content = document.querySelector('.content-mission');
        const content2 = document.querySelector('.content-mission2');
        content.classList.add('hide-mission');
        content2.classList.remove('hide-mission');
        document.getElementById('monthButton').classList.add('active-button-day');
        document.getElementById('dayButton').classList.remove('active-button-day');
    }
</script>


<?php
    use App\Helpers\TranslationHelper;
    $id = request()->get('record');

    $charger = App\Models\BatteryCharger::where('batteris_id', $id)
            ->paginate(10)
            ->appends(['record' => $id]);

    $chargerCount = App\Models\BatteryCharger::where('batteris_id', $id)->count();

    $batteryName = App\Models\Battrei::where('id',$id)->first();

    //edit
    $editValue = request()->get('value');

    $chargerEdit = App\Models\BatteryCharger::where('id', $editValue)->first();



    //clone 
    $cloneValue = request()->get('clone');
    $chargerClone = App\Models\BatteryCharger::where('id', $cloneValue)->first();
    // dd($chargerEdit);

    

    

?>
<head>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .active{
            display: none;
        }
        .hidden-notif
        {
            display: none;
        }
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 50;
            display: block;
        }
    </style>
</head>

<div>
    {{-- header --}}
    <div class="filament-stats-overview-widget p-6 border-b bg-white dark:bg-gray-800 rounded-lg shadow mb-8 mt-8">
        <!-- Title and total drones -->
        <div class="flex flex-col space-y-6 sm:flex-row sm:space-y-0 justify-between items-center">
            <!-- Title Section -->
            <div class="flex items-center space-x-4">
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{!! TranslationHelper::translateIfNeeded('Battery Charges') !!}</h1><br>
            </div>
            <!-- Title Section -->
            <div class="flex space-x-4 ml-auto"> <!-- Tambahkan ml-auto di sini -->
                <button class="px-4 py-2 bg-gray-500 text-white font-semibold rounded-lg 
                                hover:bg-gray-600 dark:hover:bg-gray-400 focus:outline-none focus:ring-2 
                                focus:ring-gray-500 dark:focus:ring-gray-300" onclick="openModal()">
                    {!! TranslationHelper::translateIfNeeded('Add Cycle') !!}
                </button>
            </div>
        </div>
        <h2 class="text-xl font-medium text-gray-500 dark:text-gray-400">{{$chargerCount}}{!! TranslationHelper::translateIfNeeded(' Total for ') !!}{{$batteryName->name}} (Sn: {{$batteryName->serial_P}})</h2>
    </div>
    

    {{-- body --}}
    <div class="block min-h-screen w-full mt-8 mb-8">
        {{-- header body --}}
        <div class="w-full p-4 bg-white dark:bg-gray-800 shadow-lg rounded-lg">

            <div id="tob-bar" class="tob-bar bg-gray-700 dark:bg-gray-900 text-white p-4 rounded-t-lg">
                <div class="flex flex-wrap justify-between items-center gap-4">
                    <div class="flex items-center space-x-2">
                        <h2 class="font-semibold text-xl">
                            {!! TranslationHelper::translateIfNeeded('All Battery Charger ') !!}
                        </h2
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="fixed active inset-0 flex justify-center z-50" style="max-height: 80%">
            <div class="relative space-y-6 bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg max-w-4xl w-full max-h-[80%] overflow-y-auto mx-4 md:mx-auto">
                <!-- Tombol Close -->
                <button type="button"
                    class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-gray-500 text-2xl font-bold p-2"
                    onclick="closeModal()">
                    &times;
                </button>

                <!-- Judul Modal -->
                <h2 class="text-center text-lg font-semibold text-gray-900 dark:text-white">
                    {!! TranslationHelper::translateIfNeeded('Battery Charger Overview')!!}
                </h2>
                <hr class="border-t border-gray-300 dark:border-gray-600 w-24 mx-auto">

                {{-- error massages --}}
                <div id="bodyErrorMassages" style="display: none;" class="rounded-md bg-red-50 p-4 shadow dark:bg-red-800" role="alert">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <!-- Icon Error -->
                            <svg class="h-5 w-5 text-red-400 dark:text-red-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 11-12.728 0m1.414-1.414a9 9 0 0110.899 0m-5.7 5.8a2.25 2.25 0 10-3.18-3.181m0 0a2.25 2.25 0 013.18 3.181m-3.18-3.181L12 12m0 0l3.18-3.18" />
                            </svg>
                        </div>
                        <div class="ml-3 text-sm">
                            <p class="font-medium text-red-800 dark:text-red-200">
                                {!! TranslationHelper::translateIfNeeded('Error: ') !!}
                                <span id="errorMassages"></span>
                            </p>
                        </div>
                        <div class="ml-auto pl-3">
                            <button type="button" onclick="closeMessages()" class="inline-flex rounded-md bg-red-50 text-red-800 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-2 dark:bg-red-800 dark:text-red-200"
                                data-bs-dismiss="alert" aria-label="Close">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Form -->
                <div>
                    @csrf
                    <input id="id_battery" type="hidden" name="id_battery" value="{{$id}}">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <!-- Date Input -->
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Date')!!}</label>
                            <input id="date" type="date" name="date" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                        </div>

                        <!-- Duration Input -->
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Charger Duration')!!}</label>
                            <input 
                                id="duration" 
                                type="text" 
                                name="duration" 
                            
                                class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500" 
                                oninput="this.value = this.value.replace(/[^0-9:]/g, '').replace(/^([0-9]{2})([0-9]{2})/, '$1:$2:');" 
                                placeholder="HH:mm:ss" 
                                value="00:00:00"
                            >
                        </div>
                    </div>
                    <!-- NOtes -->
                    <div class="mt-4 mb-4">
                        <label for="note" class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Notes') !!}</label>
                        <textarea id="note" name="note" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500"></textarea>
                    </div>                    

                    {{-- Voltage --}}

                    <h2 class="text-xl text-center mt-4 dark:text-gray-300">Voltages</h2>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mb-4 mt-4">
                        {{-- pre-flight --}}
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Pre-Flight (V)')!!}</label>
                            <input id="pre-flight" type="number" name="pre-flight" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                        </div>
                        {{-- Post-flight --}}
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Post-Flight (V)')!!}</label>
                            <input id="post-flight" type="number" name="post-flight" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                        </div>

                        {{-- Before Charger --}}
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Before Charger (V)')!!}</label>
                            <input id="before-charger" type="number" name="before-charger" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                        </div>

                            {{-- After Charger --}}
                            <div>
                                <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('After Charger (V)')!!}</label>
                                <input id="after-charger" type="number" name="after-charger" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                            </div>

                    </div>

                    {{-- Capacity --}}

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4 mt-4">
                        <!-- Capacity -->
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Capacity Input (mAHr)')!!}</label>
                            <input id="capacity" type="number" name="capacity" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                        </div>

                        <!-- Duration Input -->
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Cell Resistance Total (mOhm)')!!}</label>
                            <input id="resistance" type="number" name="resistance" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                        </div>
                    </div>

                    {{-- cell --}}

                    <div class="grid grid-cols-4 md:grid-cols-8 gap-12 mt-4 mb-4">
                        <!-- cell1 -->
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Cell 1(mOhm)')!!}</label>
                            <input id="cell" type="number" name="cell" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                        </div>

                        <!-- cell2 -->
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Cell 2(mOhm)')!!}</label>
                            <input id="cell2" type="number" name="cell2" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                        </div>
                        <!-- cell3 -->
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Cell 3(mOhm)')!!}</label>
                            <input id="cell3" type="number" name="cell3" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                        </div>

                        <!-- cell4 -->
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Cell 4(mOhm)')!!}</label>
                            <input id="cell4" type="number" name="cell4" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                        </div>
                        <!-- cell5 -->
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Cell 5(mOhm)')!!}</label>
                            <input id="cell5" type="number" name="cell5" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                        </div>

                        <!-- cell6 -->
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Cell 6(mOhm)')!!}</label>
                            <input id="cell6" type="number" name="cell6" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                        </div>
                        <!-- cell7 -->
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Cell 7(mOhm)')!!}</label>
                            <input id="cell7" type="number" name="cell7" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                        </div>

                        <!-- cell8 -->
                        <div>
                            <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Cell 8(mOhm)')!!}</label>
                            <input id="cell8" type="number" name="cell8" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end mt-4">
                        <button id="triggerButton" type="button" class="button" style="font-size: 16px; background-color: #4A5568; color: white; font-weight: bold; padding: 8px 16px; border-radius: 4px; border: none; cursor: pointer;">
                            <span class="button__text">{!! TranslationHelper::translateIfNeeded('Submit')!!}</span>
                        </button>
                    </div>
                    
                </div>
            </div>
        </div>

        {{-- tabel --}}
        @if($charger->count() > 0)
        @foreach($charger as $item)
        <div class="mr-4 ml-4">
            <div class="mb-0 mt-8">     
                <div class="flex flex-wrap space-x-4 border border-gray-300 rounded-lg p-2 bg-gray-100 dark:bg-gray-800 mx-auto shadow-lg p-4">
                    
                    <!-- Date -->
                    <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2 overflow-hidden">
                        <strong><h2 class="text-xm font-medium text-gray-500 dark:text-gray-400">{!! TranslationHelper::translateIfNeeded('Date:') !!}</h2></strong>
                        <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold truncate">{{$item->date ?? 'no set'}}</p>
                    </div>

                    <!-- voltage-->
                    <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                        <strong><h2 class="text-xm font-medium text-gray-500 dark:text-gray-400">{!! TranslationHelper::translateIfNeeded('Voltage:') !!}</h2></strong>
                        <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Before:')!!} {{$item->before_charger ?? 'no set'}} /{!! TranslationHelper::translateIfNeeded(' After:')!!} {{$item->after_charger ?? 'no set'}}</p>
                        <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Pre-flight:')!!} {{$item->pre_flight ?? 'no set'}} /{!! TranslationHelper::translateIfNeeded(' Post-Flight: ')!!} {{$item->post_flight ?? 'no set'}}</p>
                    </div>

                    {{-- Charger --}}
                    <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                        <strong><h2 class="text-xm font-medium text-gray-500 dark:text-gray-400">{!! TranslationHelper::translateIfNeeded('Charger:') !!}</h2></strong>
                        <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{{$item->capacity ?? 'no set'}}{!! TranslationHelper::translateIfNeeded(' in ')!!} {{$item->duration ?? 'no set'}}</p>
                        <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('total Resistence:')!!} {{$item->resistance ?? 'no set'}}</p>
                    </div>

                    <!-- edit -->
                    <div class="flex justify-center items-center min-w-[150px] mb-2">
                        <button type="button"
                            class="px-4 py-2 bg-gray-500 text-white font-semibold rounded-lg 
                                hover:bg-gray-600 dark:hover:bg-gray-400 focus:outline-none focus:ring-2 
                                focus:ring-gray-500 dark:focus:ring-gray-300"
                            data-value="{{$item->id}}"
                            onclick="openEdit(event)">
                            {!! TranslationHelper::translateIfNeeded('Edit') !!}
                        </button>
                    </div>

                    {{-- clone --}}
                    <div class="flex justify-center items-center min-w-[150px] mb-2">
                        <button type="button"
                            class="px-4 py-2 bg-gray-500 text-white font-semibold rounded-lg 
                                hover:bg-gray-600 dark:hover:bg-gray-400 focus:outline-none focus:ring-2 
                                focus:ring-gray-500 dark:focus:ring-gray-300"
                            data-value="{{$item->id}}"
                            onclick="openClone(event)">
                            {!! TranslationHelper::translateIfNeeded('Clone') !!}
                        </button>
                    </div>

                </div>
            </div>
            <div class="px-4 mt-0">
                <div class="flex items-center justify-between py-4 px-6 border-t border-gray-400 bg-gray-300 dark:bg-gray-700 shadow-lg rounded-b-lg">
                    <div class="flex-1 min-w-[180px]">
                        <p class="text-sm text-gray-700 dark:text-gray-400">
                            <strong>{!! TranslationHelper::translateIfNeeded('Notes:')!!}</strong> {{$item->note ?? null}}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
        @else
            <div class="text-center mb-8 mt-8">
                <strong><h2 class="text-xm font-bold text-gray-500 dark:text-gray-400">{!! TranslationHelper::translateIfNeeded('No Data Charger Found') !!}</h2></strong>
            </div>
        @endif
        <div class="mt-4 p-4">
            {{$charger->links()}}
        </div>
        {{-- end tabel --}}

        {{-- edit Modal --}}
        @if($chargerEdit !== null)
            <div class="fixed edit active inset-0 flex justify-center z-50" style="max-height: 80%">
                <div class="relative space-y-6 bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg max-w-4xl w-full max-h-[80%] overflow-y-auto mx-4 md:mx-auto">
                    <!-- Tombol Close -->
                    <button type="button"
                        class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-gray-500 text-2xl font-bold p-2"
                        onclick="closeEdit()">
                        &times;
                    </button>

                    <!-- Judul Modal -->
                    <h2 class="text-center text-lg font-semibold text-gray-900 dark:text-white">
                        {!! TranslationHelper::translateIfNeeded('Edit Battery Charger Overview')!!}
                    </h2>
                    <hr class="border-t border-gray-300 dark:border-gray-600 w-24 mx-auto">

                    {{-- error massages --}}
                    <div id="bodyErrorMassagesEdit" style="display: none;" class="rounded-md bg-red-50 p-4 shadow dark:bg-red-800" role="alert">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <!-- Icon Error -->
                                <svg class="h-5 w-5 text-red-400 dark:text-red-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 11-12.728 0m1.414-1.414a9 9 0 0110.899 0m-5.7 5.8a2.25 2.25 0 10-3.18-3.181m0 0a2.25 2.25 0 013.18 3.181m-3.18-3.181L12 12m0 0l3.18-3.18" />
                                </svg>
                            </div>
                            <div class="ml-3 text-sm">
                                <p class="font-medium text-red-800 dark:text-red-200">
                                    {!! TranslationHelper::translateIfNeeded('Error: ') !!}
                                    <span id="errorMassagesEdit"></span>
                                </p>
                            </div>
                            <div class="ml-auto pl-3">
                                <button type="button" onclick="closeMessagesEdit()" class="inline-flex rounded-md bg-red-50 text-red-800 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-2 dark:bg-red-800 dark:text-red-200"
                                    data-bs-dismiss="alert" aria-label="Close">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- Form -->
                    <div>
                        @csrf
                        <input id="id_batteryEdit" type="hidden" name="id_batteryEdit" value="{{$id}}">
                        <input id="idItem" type="hidden" name="idItem" value="{{$editValue}}">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <!-- Date Input -->
                            <div>
                                <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Date')!!}</label>
                                <input value="{{$chargerEdit->date}}" id="dateEdit" type="date" name="dateEdit" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                            </div>

                            <!-- Duration Input -->
                            <div>
                                <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Charger Duration')!!}</label>
                                <input 
                                    id="durationEdit" 
                                    type="text" 
                                    name="durationEdit" 
                                
                                    class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500" 
                                    oninput="this.value = this.value.replace(/[^0-9:]/g, '').replace(/^([0-9]{2})([0-9]{2})/, '$1:$2:');" 
                                    placeholder="HH:mm:ss" 
                                    value="{{$chargerEdit->duration}}"
                                >
                            </div>
                        </div>
                        <!-- NOtes -->
                        <div class="mt-4 mb-4">
                            <label for="noteEdit" class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Notes') !!}</label>
                            <textarea id="noteEdit" name="noteEdit" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">{{$chargerEdit->note}}</textarea>
                        </div>                    

                        {{-- Voltage --}}

                        <h2 class="text-xl text-center mt-4 dark:text-gray-300">Voltages</h2>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mb-4 mt-4">
                            {{-- pre-flight --}}
                            <div>
                                <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Pre-Flight (V)')!!}</label>
                                <input value="{{$chargerEdit->pre_flight}}" id="pre-flightEdit" type="number" name="pre-flightEdit" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                            </div>
                            {{-- Post-flight --}}
                            <div>
                                <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Post-Flight (V)')!!}</label>
                                <input value="{{$chargerEdit->post_flight}}" id="post-flightEdit" type="number" name="post-flightEdit" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                            </div>

                            {{-- Before Charger --}}
                            <div>
                                <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Before Charger (V)')!!}</label>
                                <input value="{{$chargerEdit->before_charger}}" id="before-chargerEdit" type="number" name="before-chargerEdit" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                            </div>

                                {{-- After Charger --}}
                                <div>
                                    <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('After Charger (V)')!!}</label>
                                    <input value="{{$chargerEdit->after_charger}}" id="after-chargerEdit" type="number" name="after-chargerEdit" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                                </div>

                        </div>

                        {{-- Capacity --}}

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4 mt-4">
                            <!-- Capacity -->
                            <div>
                                <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Capacity Input (mAHr)')!!}</label>
                                <input value="{{$chargerEdit->capacity}}" id="capacityEdit" type="number" name="capacityEdit" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                            </div>

                            <!-- Duration Input -->
                            <div>
                                <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Cell Resistance Total (mOhm)')!!}</label>
                                <input value="{{$chargerEdit->resistance}}" id="resistanceEdit" type="number" name="resistanceEdit" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                            </div>
                        </div>

                        {{-- cell --}}

                        <div class="grid grid-cols-4 md:grid-cols-8 gap-12 mt-4 mb-4">
                            <!-- cell1 -->
                            <div>
                                <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Cell 1(mOhm)')!!}</label>
                                <input value="{{$chargerEdit->cell1}}" id="cellEdit" type="number" name="cellEdit" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                            </div>

                            <!-- cell2 -->
                            <div>
                                <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Cell 2(mOhm)')!!}</label>
                                <input value="{{$chargerEdit->cell2}}" id="cell2Edit" type="number" name="cell2Edit" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                            </div>
                            <!-- cell3 -->
                            <div>
                                <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Cell 3(mOhm)')!!}</label>
                                <input value="{{$chargerEdit->cell3}}" id="cell3Edit" type="number" name="cell3Edit" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                            </div>

                            <!-- cell4 -->
                            <div>
                                <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Cell 4(mOhm)')!!}</label>
                                <input value="{{$chargerEdit->cell4}}" id="cell4Edit" type="number" name="cell4Edit" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                            </div>
                            <!-- cell5 -->
                            <div>
                                <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Cell 5(mOhm)')!!}</label>
                                <input value="{{$chargerEdit->cell5}}" id="cell5Edit" type="number" name="cell5Edit" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                            </div>

                            <!-- cell6 -->
                            <div>
                                <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Cell 6(mOhm)')!!}</label>
                                <input value="{{$chargerEdit->cell6}}" id="cell6Edit" type="number" name="cell6Edit" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                            </div>
                            <!-- cell7 -->
                            <div>
                                <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Cell 7(mOhm)')!!}</label>
                                <input value="{{$chargerEdit->cell7}}" id="cell7Edit" type="number" name="cell7Edit" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                            </div>

                            <!-- cell8 -->
                            <div>
                                <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Cell 8(mOhm)')!!}</label>
                                <input value="{{$chargerEdit->cell8}}" id="cell8Edit" type="number" name="cell8Edit" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end mt-4">
                            <button id="triggerButtonEdit" type="button" class="button" style="font-size: 16px; background-color: #4A5568; color: white; font-weight: bold; padding: 8px 16px; border-radius: 4px; border: none; cursor: pointer;">
                                <span class="button__text">{!! TranslationHelper::translateIfNeeded('Submit')!!}</span>
                            </button>
                        </div>
                        
                    </div>
                </div>
            </div>
        @endif
        {{-- end edit modal --}}


        {{-- Clone Modal --}}
        @if($chargerClone != null)
            <div class="fixed clone active inset-0 flex justify-center z-50" style="max-height: 80%">
                <div class="relative space-y-6 bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg max-w-4xl w-full max-h-[80%] overflow-y-auto mx-4 md:mx-auto">
                    <!-- Tombol Close -->
                    <button type="button"
                        class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 dark:text-gray-300 dark:hover:text-gray-500 text-2xl font-bold p-2"
                        onclick="closeClone()">
                        &times;
                    </button>

                    <!-- Judul Modal -->
                    <h2 class="text-center text-lg font-semibold text-gray-900 dark:text-white">
                        {!! TranslationHelper::translateIfNeeded('Battery Charger Overview')!!}
                    </h2>
                    <hr class="border-t border-gray-300 dark:border-gray-600 w-24 mx-auto">

                    {{-- error massages --}}
                    <div id="bodyErrorMassagesClone" style="display: none;" class="rounded-md bg-red-50 p-4 shadow dark:bg-red-800" role="alert">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <!-- Icon Error -->
                                <svg class="h-5 w-5 text-red-400 dark:text-red-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 11-12.728 0m1.414-1.414a9 9 0 0110.899 0m-5.7 5.8a2.25 2.25 0 10-3.18-3.181m0 0a2.25 2.25 0 013.18 3.181m-3.18-3.181L12 12m0 0l3.18-3.18" />
                                </svg>
                            </div>
                            <div class="ml-3 text-sm">
                                <p class="font-medium text-red-800 dark:text-red-200">
                                    {!! TranslationHelper::translateIfNeeded('Error: ') !!}
                                    <span id="errorMassagesClone"></span>
                                </p>
                            </div>
                            <div class="ml-auto pl-3">
                                <button type="button" onclick="closeMessagesClone()" class="inline-flex rounded-md bg-red-50 text-red-800 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-2 dark:bg-red-800 dark:text-red-200"
                                    data-bs-dismiss="alert" aria-label="Close">
                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- Form -->
                    <div>
                        @csrf
                        <input id="id_batteryClone" type="hidden" name="id_batteryClone" value="{{$id}}">
    
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <!-- Date Input -->
                            <div>
                                <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Date')!!}</label>
                                <input value="{{$chargerClone->date}}" id="dateClone" type="date" name="dateClone" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                            </div>

                            <!-- Duration Input -->
                            <div>
                                <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Charger Duration')!!}</label>
                                <input 
                                    id="durationClone" 
                                    type="text" 
                                    name="durationClone" 
                                
                                    class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500" 
                                    oninput="this.value = this.value.replace(/[^0-9:]/g, '').replace(/^([0-9]{2})([0-9]{2})/, '$1:$2:');" 
                                    placeholder="HH:mm:ss" 
                                    value="{{$chargerClone->duration}}"
                                >
                            </div>
                        </div>
                        <!-- NOtes -->
                        <div class="mt-4 mb-4">
                            <label for="noteClone" class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Notes') !!}</label>
                            <textarea id="noteClone" name="noteClone" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">{{$chargerClone->note}}</textarea>
                        </div>                    

                        {{-- Voltage --}}

                        <h2 class="text-xl text-center mt-4 dark:text-gray-300">Voltages</h2>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mb-4 mt-4">
                            {{-- pre-flight --}}
                            <div>
                                <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Pre-Flight (V)')!!}</label>
                                <input value="{{$chargerClone->pre_flight}}" id="pre-flightClone" type="number" name="pre-flightClone" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                            </div>
                            {{-- Post-flight --}}
                            <div>
                                <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Post-Flight (V)')!!}</label>
                                <input value="{{$chargerClone->post_flight}}" id="post-flightClone" type="number" name="post-flightClone" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                            </div>

                            {{-- Before Charger --}}
                            <div>
                                <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Before Charger (V)')!!}</label>
                                <input value="{{$chargerClone->before_charger}}" id="before-chargerClone" type="number" name="before-chargerClone" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                            </div>

                                {{-- After Charger --}}
                                <div>
                                    <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('After Charger (V)')!!}</label>
                                    <input value="{{$chargerClone->after_charger}}" id="after-chargerClone" type="number" name="after-chargerClone" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                                </div>

                        </div>

                        {{-- Capacity --}}

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4 mt-4">
                            <!-- Capacity -->
                            <div>
                                <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Capacity Input (mAHr)')!!}</label>
                                <input value="{{$chargerClone->capacity}}" id="capacityClone" type="number" name="capacityClone" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                            </div>

                            <!-- Duration Input -->
                            <div>
                                <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Cell Resistance Total (mOhm)')!!}</label>
                                <input value="{{$chargerClone->resistance}}" id="resistanceClone" type="number" name="resistanceClone" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                            </div>
                        </div>

                        {{-- cell --}}

                        <div class="grid grid-cols-4 md:grid-cols-8 gap-12 mt-4 mb-4">
                            <!-- cell1 -->
                            <div>
                                <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Cell 1(mOhm)')!!}</label>
                                <input value="{{$chargerClone->cell1}}" id="cellClone" type="number" name="cellClone" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                            </div>

                            <!-- cell2 -->
                            <div>
                                <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Cell 2(mOhm)')!!}</label>
                                <input value="{{$chargerClone->cell2}}" id="cell2Clone" type="number" name="cell2Clone" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                            </div>
                            <!-- cell3 -->
                            <div>
                                <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Cell 3(mOhm)')!!}</label>
                                <input value="{{$chargerClone->cell3}}" id="cell3Clone" type="number" name="cell3Clone" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                            </div>

                            <!-- cell4 -->
                            <div>
                                <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Cell 4(mOhm)')!!}</label>
                                <input value="{{$chargerClone->cell4}}" id="cell4Clone" type="number" name="cell4Clone" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                            </div>
                            <!-- cell5 -->
                            <div>
                                <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Cell 5(mOhm)')!!}</label>
                                <input value="{{$chargerClone->cell5}}" id="cell5Clone" type="number" name="cell5Clone" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                            </div>

                            <!-- cell6 -->
                            <div>
                                <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Cell 6(mOhm)')!!}</label>
                                <input value="{{$chargerClone->cell6}}" id="cell6Clone" type="number" name="cell6Clone" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                            </div>
                            <!-- cell7 -->
                            <div>
                                <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Cell 7(mOhm)')!!}</label>
                                <input value="{{$chargerClone->cell7}}" id="cell7Clone" type="number" name="cell7Clone" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                            </div>

                            <!-- cell8 -->
                            <div>
                                <label class="block text-gray-700 dark:text-gray-300">{!! TranslationHelper::translateIfNeeded('Cell 8(mOhm)')!!}</label>
                                <input value="{{$chargerClone->cell8}}" id="cell8Clone" type="number" name="cell8Clone" maxlength="255" class="w-full mt-1 p-2 border dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300 rounded-md focus:ring focus:ring-blue-500">
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end mt-4">
                            <button id="triggerButtonClone" type="button" class="button" style="font-size: 16px; background-color: #4A5568; color: white; font-weight: bold; padding: 8px 16px; border-radius: 4px; border: none; cursor: pointer;">
                                <span class="button__text">{!! TranslationHelper::translateIfNeeded('Submit')!!}</span>
                            </button>
                        </div>
                        
                    </div>
                </div>
            </div>
        @endif
        {{-- end Clone modal --}}
        
    </div>
    {{-- end body --}}
</div>
{{-- jquery --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
{{-- Toggle --}}
<script>
    function closeModal() {
        const contents = document.querySelector('.fixed');
        contents.classList.add('active');
    }
    function openModal() {
        const contents = document.querySelector('.fixed');
        contents.classList.remove('active');     
    }

    function closeEdit() {
        const contents = document.querySelector('.edit');
        contents.classList.add('active');
    }
    function openEdit(event) {
        event.preventDefault();
        const value = event.currentTarget.getAttribute('data-value');
        const url = new URL(window.location.href);
        url.searchParams.set('value', value);
        window.location.href = url.toString();

        sessionStorage.setItem('editValue', 'edit');   
    }
 
    
    //close massages
    function closeMessages() {
        document.getElementById('bodyErrorMassages').style.display = 'none';
    }

    function closeMessagesEdit() {
        document.getElementById('bodyErrorMassagesEdit').style.display = 'none';
    }

    //clone
    function closeClone() {
        const contents = document.querySelector('.clone');
        contents.classList.add('active');
    }
    function openClone(event) {
        event.preventDefault();
        const value = event.currentTarget.getAttribute('data-value');
        const url = new URL(window.location.href);
        url.searchParams.set('clone', value);
        window.location.href = url.toString();

        sessionStorage.setItem('cloneValue', 'clone');   
    }

       //dom
    document.addEventListener('DOMContentLoaded', function () {
        const modalStatus = sessionStorage.getItem('editValue');
        console.log(modalStatus);
        const cloneStatus = sessionStorage.getItem('cloneValue');
        console.log(cloneStatus);

        if (modalStatus === 'edit') {
            const contents = document.querySelector('.edit');
            if (contents) {
                contents.classList.remove('active');
            } else {
                console.log('.edit element not found');
            }
        }

        if (cloneStatus === 'clone') {
            const contents = document.querySelector('.clone');
            if (contents) {
                contents.classList.remove('active');
            } else {
                console.log('.clone element not found');
            }
        }

        // Remove session storage items
        sessionStorage.removeItem('editValue');
        sessionStorage.removeItem('cloneValue');
    });



</script>

{{-- notification --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const notification = document.getElementById('success-notification-project');
        const closeButton = document.getElementById('close-notification-project');
        if (notification) {
            setTimeout(() => {
                notification.style.display = 'none';
            }, 8000); // Hide after 8 seconds

            closeButton.addEventListener('click', () => {
                notification.style.display = 'none';
            });
        }
    });
</script>

{{--ajax Create---}}
<script>
    $(document).ready(function() {
        $('#triggerButton').click(function(e) {
            e.preventDefault(); // Mencegah tombol submit reload halaman
    
            // Ambil semua data dari form
            let formData = {
                _token: '{{ csrf_token() }}',
                id: $('#id_battery').val(),
                date: $('#date').val(),
                duration: $('#duration').val(),
                note: $('#note').val(),
                pre_flight: $('#pre-flight').val(),
                post_flight: $('#post-flight').val(),
                before_charger: $('#before-charger').val(),
                after_charger: $('#after-charger').val(),
                capacity: $('#capacity').val(),
                resistance: $('#resistance').val(),
                cell1: $('#cell').val(),
                cell2: $('#cell2').val(),
                cell3: $('#cell3').val(),
                cell4: $('#cell4').val(),
                cell5: $('#cell5').val(),
                cell6: $('#cell6').val(),
                cell7: $('#cell7').val(),
                cell8: $('#cell8').val(),
            };
    
            let date = $('#date').val().trim() || null;
            let duration = $('#duration').val().trim() || null;
            if(date == null){
                document.getElementById('bodyErrorMassages').style.display = 'block';
                document.getElementById('errorMassages').textContent = 'Date cannot be null';
                setTimeout(() => {
                    document.getElementById('bodyErrorMassages').style.display = 'none';
                }, 5000);
                document.getElementById('bodyErrorMassages').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }else if(duration == null){
                document.getElementById('bodyErrorMassages').style.display = 'block';
                document.getElementById('errorMassages').textContent = 'Duration cannot be null';
                setTimeout(() => {
                    document.getElementById('bodyErrorMassages').style.display = 'none';
                }, 5000);
                document.getElementById('bodyErrorMassages').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }else{
                // Kirim data dengan AJAX
                $.ajax({
                    url: "{{ route('create.charger.battery') }}",
                    type: "POST",
                    data: formData,
                    success: function(response) {
                        alert('Data berhasil dikirim!');
                        console.log(response);
                        window.location.reload();
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                        alert('Terjadi kesalahan, coba lagi.');
                    }
                });
            }
        });
    });
</script>

{{-- ajax edit --}}

<script>
    $(document).ready(function() {
        
        $('#triggerButtonEdit').on('click', function(e) {
            e.preventDefault();  
              
            var formData = {
                _token: $('input[name="_token"]').val(),
                idItem: $('#idItem').val(),
                idEdit: $('#id_batteryEdit').val(),
                dateEdit: $('#dateEdit').val(),
                durationEdit: $('#durationEdit').val(),
                noteEdit: $('#noteEdit').val(),
                pre_flightEdit: $('#pre-flightEdit').val(),
                post_flightEdit: $('#post-flightEdit').val(),
                before_chargerEdit: $('#before-chargerEdit').val(),
                after_chargerEdit: $('#after-chargerEdit').val(),
                capacityEdit: $('#capacityEdit').val(),
                resistanceEdit: $('#resistanceEdit').val(),
                cell1Edit: $('#cell1Edit').val(),
                cell2Edit: $('#cell2Edit').val(),
                cell3Edit: $('#cell3Edit').val(),
                cell4Edit: $('#cell4Edit').val(),
                cell5Edit: $('#cell5Edit').val(),
                cell6Edit: $('#cell6Edit').val(),
                cell7Edit: $('#cell7Edit').val(),
                cell8Edit: $('#cell8Edit').val()
            };

            let date = $('#dateEdit').val().trim() || null;
            let duration = $('#durationEdit').val().trim() || null;
            if(date == null){
                document.getElementById('bodyErrorMassagesEdit').style.display = 'block';
                document.getElementById('errorMassagesEdit').textContent = 'Date cannot be null';
                setTimeout(() => {
                    document.getElementById('bodyErrorMassagesEdit').style.display = 'none';
                }, 5000);
                document.getElementById('bodyErrorMassagesEdit').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }else if(duration == null){
                document.getElementById('bodyErrorMassagesEdit').style.display = 'block';
                document.getElementById('errorMassagesEdit').textContent = 'Duration cannot be null';
                setTimeout(() => {
                    document.getElementById('bodyErrorMassagesEdit').style.display = 'none';
                }, 5000);
                document.getElementById('bodyErrorMassagesEdit').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }else{
                $.ajax({
                    url: '{{ route("edit.charger.battery") }}',
                    type: 'POST',
                    data: formData,

                    success: function(response) {
                        window.location.reload();
                        alert('Data berhasil disimpan!');
                    },

                    error: function(xhr, status, error) {
                        alert('Terjadi kesalahan: ' + error);
                    }
                });
            }
            
        
        });
    });
</script>

{{--ajax Clone--}}
<script>
    $(document).ready(function() {
        $('#triggerButtonClone').click(function(e) {
            e.preventDefault(); // Mencegah tombol submit reload halaman
    
            // Ambil semua data dari form
            let formData = {
                _token: '{{ csrf_token() }}',
                id: $('#id_batteryClone').val(),
                date: $('#dateClone').val(),
                duration: $('#durationClone').val(),
                note: $('#noteClone').val(),
                pre_flight: $('#pre-flightClone').val(),
                post_flight: $('#post-flightClone').val(),
                before_charger: $('#before-chargerClone').val(),
                after_charger: $('#after-chargerClone').val(),
                capacity: $('#capacityClone').val(),
                resistance: $('#resistanceClone').val(),
                cell1: $('#cellClone').val(),
                cell2: $('#cell2Clone').val(),
                cell3: $('#cell3Clone').val(),
                cell4: $('#cell4Clone').val(),
                cell5: $('#cell5Clone').val(),
                cell6: $('#cell6Clone').val(),
                cell7: $('#cell7Clone').val(),
                cell8: $('#cell8Clone').val(),
            };
    
            let date = $('#dateClone').val().trim() || null;
            let duration = $('#durationClone').val().trim() || null;
            if(date == null){
                document.getElementById('bodyErrorMassagesClone').style.display = 'block';
                document.getElementById('errorMassagesClone').textContent = 'Date cannot be null';
                setTimeout(() => {
                    document.getElementById('bodyErrorMassagesClone').style.display = 'none';
                }, 5000);
                document.getElementById('bodyErrorMassagesClone').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }else if(duration == null){
                document.getElementById('bodyErrorMassagesClone').style.display = 'block';
                document.getElementById('errorMassagesClone').textContent = 'Duration cannot be null';
                setTimeout(() => {
                    document.getElementById('bodyErrorMassagesClone').style.display = 'none';
                }, 5000);
                document.getElementById('bodyErrorMassagesClone').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }else{
                // Kirim data dengan AJAX
                $.ajax({
                    url: "{{ route('clone.charger.battery') }}",
                    type: "POST",
                    data: formData,
                    success: function(response) {
                        alert('Data berhasil dikirim!');
                        console.log(response);
                        window.location.reload();
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                        alert('Terjadi kesalahan, coba lagi.');
                    }
                });
            }
        });
    });
</script>





    

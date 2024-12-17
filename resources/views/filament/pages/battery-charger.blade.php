<?php
use App\Helpers\TranslationHelper;
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
        <h2 class="text-xl font-medium text-gray-500 dark:text-gray-400">{!! TranslationHelper::translateIfNeeded('0 Total for Battery (Sn: sajdhkdgu2o9uo)') !!}</h2>
    </div>
    

    {{-- body --}}
    <div class="block min-h-screen w-full mt-8">
        {{-- header body --}}
        <div class="w-full p-4 bg-white dark:bg-gray-800 shadow-lg rounded-lg">

            <div id="tob-bar" class="tob-bar bg-gray-700 dark:bg-gray-900 text-white p-4 rounded-t-lg">
                <div class="flex flex-wrap justify-between items-center gap-4">
                    <div class="flex items-center space-x-2">
                        <h2 class="font-semibold text-xl">
                            {!! TranslationHelper::translateIfNeeded('All Battery Charger') !!}
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
                <div id="bodyErrorMassagesProject" style="display: none;" class="rounded-md bg-red-50 p-4 shadow dark:bg-red-800" role="alert">
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
                                <span id="errorMassagesProject"></span>
                            </p>
                        </div>
                        <div class="ml-auto pl-3">
                            <button type="button" onclick="closeMessagesProject()" class="inline-flex rounded-md bg-red-50 text-red-800 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-2 dark:bg-red-800 dark:text-red-200"
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
                    {{-- <input type="hidden" name="teams_id" value="{{ auth()->user()->teams()->first()->id ?? null }}"> --}}

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
        <div class="mb-2 mt-8">     
            <div class="flex flex-wrap space-x-4 border border-gray-300 rounded-lg p-2 bg-gray-100 dark:bg-gray-800 mx-auto mb-4 shadow-lg p-4">
                
                <!-- Date -->
                <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2 overflow-hidden">
                    <strong><h2 class="text-xm font-medium text-gray-500 dark:text-gray-400">{!! TranslationHelper::translateIfNeeded('Date:') !!}</h2></strong>
                    <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold truncate">{!! TranslationHelper::translateIfNeeded('2024_09_10_[02:50:43]')!!}</p>
                </div>

                <!-- voltage-->
                <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                    <strong><h2 class="text-xm font-medium text-gray-500 dark:text-gray-400">{!! TranslationHelper::translateIfNeeded('Voltage:') !!}</h2></strong>
                    <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Before:')!!} 190 /{!! TranslationHelper::translateIfNeeded(' After:')!!} 200</p>
                    <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('Pre-flight:')!!} 190 /{!! TranslationHelper::translateIfNeeded(' Post-Flight: ')!!} 200</p>
                </div>

                {{-- Charger --}}
                <div class="flex-1 min-w-[150px] mb-2 border-r border-gray-300 pr-2">
                    <strong><h2 class="text-xm font-medium text-gray-500 dark:text-gray-400">{!! TranslationHelper::translateIfNeeded('Charger:') !!}</h2></strong>
                    <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">20000{!! TranslationHelper::translateIfNeeded(' in ')!!} 03:00:00</p>
                    <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold">{!! TranslationHelper::translateIfNeeded('total Resistence:')!!} 8</p>
                </div>

                <!-- edit -->
                <div class="flex justify-center items-center min-w-[150px] mb-2">
                    <a href="#" target="_blank" rel="noopener noreferrer" 
                        class="px-4 py-2 bg-gray-500 text-white font-semibold rounded-lg 
                                hover:bg-gray-600 dark:hover:bg-gray-400 focus:outline-none focus:ring-2 
                                focus:ring-gray-500 dark:focus:ring-gray-300">
                        {!! TranslationHelper::translateIfNeeded('Edit') !!}
                    </a>
                </div>
            </div>
        </div>
        {{-- end tabel --}} 
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
    //messages close
    function closeMessagesProject() {
        document.getElementById('bodyErrorMassagesProject').style.display = 'none';
    }
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

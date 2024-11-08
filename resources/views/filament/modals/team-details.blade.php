@php
use Stichoza\GoogleTranslate\GoogleTranslate;
@endphp
<div class="bg-gray-100 dark:bg-gray-800 p-4 rounded-md shadow">
    <div class="flex justify-between">
        <div class="text-center flex-1">
            <p class="font-medium text-gray-900 dark:text-white">{!! GoogleTranslate::trans('Owner', session('locale') ?? 'en') !!}</p>
            <p class="text-gray-700 dark:text-white">{{ $owner }}</p>
        </div>
        <div class="text-center flex-1">
            <p class="font-medium text-gray-900 dark:text-white">{!! GoogleTranslate::trans('Total Customers', session('locale') ?? 'en') !!}</p>
            <p class="text-gray-700 dark:text-white">{{ $customers_count }}</p>
        </div>
        <div class="text-center flex-1">
            <p class="font-medium text-gray-900 dark:text-white">{!! GoogleTranslate::trans('Total Flight', session('locale') ?? 'en') !!}</p>
            <p class="text-gray-700 dark:text-white">{{ $flighs_count }}</p>
        </div>
        <div class="text-center flex-1">
            <p class="font-medium text-gray-900 dark:text-white">{!! GoogleTranslate::trans('Total Flying Time', session('locale') ?? 'en') !!}</p>
            <p class="text-gray-700 dark:text-white">{{ $total_flight_duration }}</p>
        </div>
    </div>

    <div class="flex justify-between mt-4">
        <div class="text-center flex-1">
            <p class="font-medium text-gray-900 dark:text-white">{!! GoogleTranslate::trans('Total Drones', session('locale') ?? 'en') !!}</p>
            <p class="text-gray-700 dark:text-white">{{ $drones_count }}</p>
        </div>
        <div class="text-center flex-1">
            <p class="font-medium text-gray-900 dark:text-white">{!! GoogleTranslate::trans('Total Batteries', session('locale') ?? 'en') !!}</p>
            <p class="text-gray-700 dark:text-white">{{ $battreis_count }}</p>
        </div>
        <div class="text-center flex-1">
            <p class="font-medium text-gray-900 dark:text-white">{!! GoogleTranslate::trans('Total Equipment', session('locale') ?? 'en') !!}</p>
            <p class="text-gray-700 dark:text-white">{{ $equidments_count }}</p>
        </div>
        <div class="text-center flex-1">
            <p class="font-medium text-gray-900 dark:text-white">{!! GoogleTranslate::trans('Total Flight Locations', session('locale') ?? 'en') !!}</p>
            <p class="text-gray-700 dark:text-white">{{ $fligh_location_count }}</p>
        </div>
    </div>
</div>
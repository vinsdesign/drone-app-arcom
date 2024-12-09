<?php
$teams = Auth()->user()->teams()->first()->id;
$idProjectInMaps = $getRecord()->id;
$case = $getRecord()->case;

$flighID = App\Models\Fligh::where('projects_id', $idProjectInMaps)->get()->count();


$flightMaps = App\Models\Fligh::where('teams_id', $teams)
                              ->where('projects_id', $idProjectInMaps)
                              ->pluck('location_id');

$flightMapst = App\Models\Fligh::where('teams_id', $teams)
        ->where('projects_id', $idProjectInMaps)
        ->with('fligh_location')->get();

$locations = App\Models\fligh_location::whereIn('id', $flightMaps)
    ->with(['flights' => function ($query) {
        $query->select('id', 'location_id', 'name');
    }])
    ->get();
                              
// $location = App\Models\fligh_location::whereIn('id', $flightMaps)->get();
// // dd($flighID);
if($flighID != 0){

    $latitude = App\Models\fligh_location::whereIn('id', $flightMaps)
                                ->with('Projects')
                                ->first()->latitude;
    $longitude = App\Models\fligh_location::whereIn('id', $flightMaps)
                                ->with('Projects')
                                ->first()->longitude;
}


?>
<head>
    
    <style>
        #map {
            height: 500px;
            border-radius: 10px;
            z-index: 0;"
        }

    </style>


</head>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <h2 class="text-2xl md:text-2xl  font-bold text-center text-gray-800 dark:text-gray-100 my-4">
        Flight Maps Overview
    </h2>
    <div id="map" class="w-full h-full rounded-lg shadow-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800">
    </div>


<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // Inisialisasi Map
    // var locations = @json($locations);
    var datatest = @json($flightMapst);

    const map = L.map('map').setView([{{ $latitude ?? 0 }}, {{ $longitude ?? 0 }}], 13);

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
    }).addTo(map);

if({{$flighID}} != 0){
    datatest.forEach(flight => {
    let altitude = '';
    let longitude = '';

    const lastLocation = flight.fligh_location;

    altitude = lastLocation.latitude;
    longitude = lastLocation.longitude;
    locationName =lastLocation.name;

    L.marker([altitude, longitude]).addTo(map)
        .bindPopup(`  
        <section class="p-2 bg-white dark:bg-gray-800 shadow-md rounded-lg max-w-xs mx-auto">
            <header class="font-semibold text-sm text-gray-800 dark:text-gray-100">
                <strong>Lokasi: </strong><span class="text-blue-500 dark:text-blue-400">${locationName}</span>
            </header>
            <section class="font-semibold text-sm text-gray-800 dark:text-gray-100 mt-1">
                <strong>Projects: </strong><span class="text-gray-700 dark:text-gray-300">{{$case}}</span>
            </section>
                        <section class="font-semibold text-sm text-gray-800 dark:text-gray-100 mt-1">
                <strong>Flight Name: </strong><span class="text-gray-700 dark:text-gray-300">${flight.name}</span>
            </section>
            <footer class="mt-2">
                <a href="https://www.google.com/maps?q=${altitude},${longitude}" target="_blank">
                    <button class="w-full bg-blue-600 text-sm text-white py-1 px-2 rounded-lg hover:bg-blue-700 transition duration-200 ease-in-out dark:bg-blue-500 dark:hover:bg-blue-600">
                        Open in Google Maps
                    </button>
                </a>
            </footer>
        </section>
        `).openPopup();
    });
}


// if({{$flighID}} != 0){
//     locations.forEach(location => {
//         // let flightNames = "";
//         // location.flights.forEach(flight => {
//         //     flightNames += ${flight.name};
//         // });
//         var flight = '';
//         location.flights.forEach(fligh => {
//             flight += fligh.name + ', ';
//         });

//         // Hapus koma dan spasi terakhir
//         flight = flight.slice(0, -2);
//         L.marker([location.latitude, location.longitude]).addTo(map)
//         .bindPopup(`  
//         <section class="p-2 bg-white dark:bg-gray-800 shadow-md rounded-lg max-w-xs mx-auto">
//             <header class="font-semibold text-sm text-gray-800 dark:text-gray-100">
//                 <strong>Lokasi: </strong><span class="text-blue-500 dark:text-blue-400">${location.name}</span>
//             </header>
//             <section class="font-semibold text-sm text-gray-800 dark:text-gray-100 mt-1">
//                 <strong>Projects: </strong><span class="text-gray-700 dark:text-gray-300">{{$case}}</span>
//             </section>
//                         <section class="font-semibold text-sm text-gray-800 dark:text-gray-100 mt-1">
//                 <strong>Flight Name: </strong><span class="text-gray-700 dark:text-gray-300">${flight}</span>
//             </section>
//             <footer class="mt-2">
//                 <a href="https://www.google.com/maps?q=${location.latitude},${location.longitude}" target="_blank">
//                     <button class="w-full bg-blue-600 text-sm text-white py-1 px-2 rounded-lg hover:bg-blue-700 transition duration-200 ease-in-out dark:bg-blue-500 dark:hover:bg-blue-600">
//                         Open in Google Maps
//                     </button>
//                 </a>
//             </footer>
//         </section>
//         `).openPopup();
//     });
// }

</script>
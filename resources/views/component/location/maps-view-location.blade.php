<?php

$latitude = $getRecord()->latitude;
$longitude = $getRecord()->longitude;
$address = $getRecord()->name;
$project = $getRecord()->projects_id ?? 0;
$notes = $getRecord()->description;
$case = '';

if($project != 0){
    $case = App\Models\Projects::where('id', $project)->first()->case;
}

?>
<head>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #map {
            height: 500px;
            border-radius: 10px;
            
        }
    </style>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</head>


    <h2 class="text-2xl md:text-2xl font-bold text-center text-gray-800 dark:text-gray-100 my-4">
        Location Maps Overview
    </h2>
    <div id="map" style="z-index: 0;"></div>


<script>
    // Inisialisasi Map
    const map = L.map('map').setView([{{ $latitude }}, {{ $longitude }}], 13);  // Set view berdasarkan data dari database

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
    }).addTo(map);

    //marker di 1 lokasi
    L.marker([{{ $latitude }}, {{ $longitude }}]).addTo(map)
    .bindPopup(`
        <section class="p-2 bg-white dark:bg-gray-800 shadow-md rounded-lg max-w-xs mx-auto">
            <header class="font-semibold text-sm text-gray-800 dark:text-gray-100">
                <strong>Lokasi: </strong><span style="color:blue;">{{$address}}</span>
            </header>
            <section class="font-semibold text-sm text-gray-800 dark:text-gray-100 mt-1">
                <strong>Projects: </strong><span class="text-gray-700 dark:text-gray-300">{{$case}}</span>
            </section>
            <footer class="mt-2">
                <a href="https://www.google.com/maps?q={{$latitude}},{{$longitude }}" target="_blank">
                    <button style="
                        display: block; 
                        width: 100%; 
                        background-color: #2563eb; 
                        color: white; 
                        font-size: 0.875rem; 
                        padding: 0.25rem 0.5rem; 
                        border-radius: 0.375rem; 
                        border: none; 
                        cursor: pointer; 
                        transition: background-color 0.2s ease-in-out;" 
                        onmouseover="this.style.backgroundColor='#1d4ed8'" 
                        onmouseout="this.style.backgroundColor='#2563eb'" 
                        onmousedown="this.style.backgroundColor='#1e40af'" 
                        onmouseup="this.style.backgroundColor='#1d4ed8'">
                        Open in Google Maps
                    </button>
                </a>
            </footer>
        </section>
    `).openPopup();
        
</script>
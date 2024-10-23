<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Example</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<button class="text-xs bg-gray-700 hover:bg-gray-800 text-white font-bold py-1 px-2 rounded">
    <a href="{{ route('filament.admin.resources.drones.create', ['tenant' => auth()->user()->teams()->first()->id]) }}">
        Add New Drone
    </a>
</button>


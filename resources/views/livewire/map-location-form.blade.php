@php
    use App\Helpers\TranslationHelper;
    $sessions = session('notification',0);

    $sesionsError = session('notificationError',0);
@endphp

<div>
    <style>
        .notification {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 50;
        display: block;
        background-color: #28a745;
        color: #fff;
        padding: 10px 20px;
        border-radius: 5px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        font-family: Arial, sans-serif;
        font-size: 14px;*/
    }

    .notificationError {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 50;
        display: block;
        background-color: #b62828;
        color: #fff;
        padding: 10px 20px;
        border-radius: 5px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        font-family: Arial, sans-serif;
        font-size: 14px;*/
    }
    .hidden {
        opacity: 0;
        visibility: hidden;
    }
    </style>
    @if($sessions != 0)
        <div id="success-notification-location" class="notification bg-green-500 text-white p-4 rounded-lg shadow-lg flex items-center justify-between">
            <span>Successfully</span>
            {{-- <button id="close-notification-location" type="button" class="ml-4 text-white hover:text-gray-200 focus:outline-none" onclick="closeMessagesLocation()">
                <i class="fas fa-times"></i>
            </button> --}}
        </div>
    @endif

    @if($sesionsError != 0)
        <div id="error-notification-location" class="notificationError bg-green-500 text-white p-4 rounded-lg shadow-lg flex items-center justify-between">
            <span>Successfully</span>
            <button id="close-notification-location" class="ml-4 text-white hover:text-gray-200 focus:outline-none">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif
    <form wire:submit="create">
        {{ $this->form }}   
        
        <div class="flex justify-end mt-4">
            <button type="submit" 
                style="font-size: 16px; background-color: #4A5568; color: white; font-weight: bold; padding: 8px 16px; border-radius: 4px; border: none; cursor: pointer;"
                class="button px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600" onclick="createLocation()">
                <span class="button__text">{!! TranslationHelper::translateIfNeeded('Submit')!!}</span>
            </button>
        </div>  
    </form>
    
    <x-filament-actions::modals />
</div>
<script>
    setTimeout(function() {
        document.querySelector('.success-notification-location').classList.add('hidden');
    }, 3000);
</script>
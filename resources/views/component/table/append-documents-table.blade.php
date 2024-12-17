<form method="GET" action="{{ route('append.document') }}">
    @csrf
    <input type="hidden" name="flightId" value="{{ $flightId }}">
    
    <table class="table-auto w-full text-sm text-left text-gray-500">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
            <tr>
                <th scope="col" class="px-6 py-3">
                    <input type="checkbox" id="select-all">
                </th>
                <th scope="col" class="px-6 py-3">Document Name</th>
                <th scope="col" class="px-6 py-3">Document Type</th>
                <th scope="col" class="px-6 py-3">Ref Number</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($documents as $document)
                <tr class="bg-white border-b">
                    <td class="px-6 py-4">
                        <input id="input" type="checkbox" name="document_ids[]" value="{{ $document->id }}">
                    </td>
                    <td class="px-6 py-4">{{ $document->name }}</td>
                    <td class="px-6 py-4">{{ $document->type }}</td>
                    <td class="px-6 py-4">{{ $document->refnumber }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</form>
<button type="button" onclick="test()">Append</button>
<script>
    // Select/Deselect All functionality
    document.addEventListener('DOMContentLoaded', function (){
    function test(){
    var Append = document.getElementById('input').val();
    console.log(Append);
    }
    document.getElementById('select-all').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('input[name="document_ids[]"]');
        checkboxes.forEach(checkbox => checkbox.checked = this.checked);
    });
});
</script>

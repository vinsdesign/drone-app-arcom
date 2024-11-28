<a href="{{route('filament.admin.resources.customers.index', [
                                    'tenant' => Auth()->user()->teams()->first()->id,
                                    'record' => $getRecord()->customers_id,
                                ])}}"><p>
    test
</p></a>
{{--belum di gunakan--}}
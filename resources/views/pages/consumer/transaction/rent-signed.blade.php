<?php
use function Laravel\Folio\{middleware, name};
use function Livewire\Volt\{mount, state};

name('rent.signed');
state([
    'uuid' => fn() => request()->uuid,
    'data' => fn() => \App\Models\TandaTangan::where('uuid', $this->uuid)->first(),
    'rent' => fn() => $this->data ? $this->data->model_type::find($this->data->model_id) : null
]);
?>
@volt
<div>
@php
return redirect()->route('consumer.transaction.sign', ['code' => $this->rent->code]);
@endphp
</div>
@endvolt
<?php
use App\Models\User;
use function Livewire\Volt\{mount, state};
use function Laravel\Folio\{middleware, name};

name('admin.rent.signed');
state([
    'uuid' => fn() => request()->uuid,
    'data' => fn() => \App\Models\TandaTangan::where('uuid', $this->uuid)->first(),
    'rent' => fn() => $this->data ? $this->data->model_type::find($this->data->model_id) : null
]);
?>
@volt
<div>
@php
$this->rent->status = 'active';
$this->rent->save();
return redirect()->route('admin.transaction.sign', ['code' => $this->rent->code]);
@endphp
</div>
@endvolt
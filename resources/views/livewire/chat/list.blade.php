<?php
use function Livewire\Volt\{computed, mount, state};
use Cmgmyr\Messenger\Models\Thread;

state([
    'threads' => [],
    'search' => '',
]);

mount(function () {
    $this->loadThreads();
});

$updatedSearch = function(){
    $this->loadThreads();
};

$loadThreads = computed(function() {
    $user = Auth::user();
    $searchTerm = '%' . $this->search . '%';
    
    if ($user->hasRole(['Super Admin', 'Owner'])) {
        $this->threads = Thread::with(['participants.user'])
            ->where(function($query) use ($searchTerm) {
                $query->where('subject', 'like', $searchTerm)
                    ->orWhereHas('participants.user', function ($q) use ($searchTerm) {
                        $q->where('name', 'like', $searchTerm)
                          ->orWhere('email', 'like', $searchTerm);
                    });
            })->orderBy('created_at', 'desc')
            ->get();
    } else {
        $this->threads = Thread::forUser($user->id)
            ->where(function($query) use ($searchTerm) {
                $query->where('subject', 'like', $searchTerm)
                    ->orWhereHas('participants.user', function ($q) use ($searchTerm) {
                        $q->where('name', 'like', $searchTerm)
                          ->orWhere('email', 'like', $searchTerm);
                    });
            })
            ->get();
    }
});
?>
<div class="card w-100 card-flush">
    <div class="card-header pt-7" id="kt_chat_contacts_header">
        <form class="w-100 position-relative" autocomplete="off">
            <i class="ki-outline ki-magnifier fs-3 text-gray-500 position-absolute top-50 ms-5 translate-middle-y"></i>
            <input type="text" class="form-control form-control-solid px-13" wire:model.live="search" placeholder="Cari berdasarkan nama atau email..." />
        </form>
    </div>
    <div class="card-body pt-5" id="kt_chat_contacts_body" wire:poll.5s>
        @forelse($this->threads as $thread)
            @php
                // Get other participants (excluding current user)
                $otherParticipants = $thread->participants->filter(function($participant) {
                    return $participant->user_id !== Auth::id();
                });
                
                // Get first other participant
                $firstParticipant = $otherParticipants->first();
                $user = $firstParticipant->user ?? null;
            @endphp
            
            <div class="d-flex flex-stack py-4">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-45px symbol-circle">
                        <span class="symbol-label bg-light-primary text-primary fs-6 fw-bolder">
                            @if($user)
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            @else
                                {{ strtoupper(substr($thread->subject, 0, 1)) }}
                            @endif
                        </span>
                    </div>
                    <div class="ms-5">
                        <a href="#" wire:click="$parent.selectThread({{ $thread->id }})" class="fs-5 fw-bold text-gray-900 text-hover-primary mb-2">
                            @if($user)
                                {{ $user->name }}
                            @else
                                {{ $thread->subject }}
                            @endif
                        </a>
                        <div class="fw-semibold text-muted">
                            @if($user)
                                {{ $user->email }}
                            @else
                                {{ $thread->participants->pluck('email')->implode(', ') }}
                            @endif
                        </div>
                    </div>
                </div>
                <div class="d-flex flex-column align-items-end ms-2">
                    <span class="text-muted fs-7 mb-1">{{ $thread->updated_at->diffForHumans() }}</span>
                </div>
            </div>
            <div class="separator separator-dashed"></div>
        @empty
            <div class="text-center py-5">
                <p class="text-muted">Belum ada percakapan.</p>
            </div>
        @endforelse
    </div>
</div>
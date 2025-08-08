<?php

use App\Models\Wishlist;
use App\Models\WishlistItem;
use function Livewire\Volt\{computed, state};
use function Laravel\Folio\name;

name('wishlist');

$wishlists = computed(function(){
    return Wishlist::where('user_id', auth()->id())->with('items')->get();
});
$recommendedItems = computed(function(){
    return WishlistItem::inRandomOrder()->limit(4)->get();
});
?>

<x-app>
    @volt
    <div id="kt_app_content" class="app-content flex-column-fluid py-5">
        <div class="container">
            <!-- Welcome Banner -->
            <div class="card bg-light border-0 mb-5 rounded-3 shadow-sm position-relative overflow-hidden">
                <div class="card-body p-4 p-md-5 text-center">
                    <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="alert" aria-label="Close"></button>
                    <h4 class="card-title mb-2">Pakai fitur Koleksi, Wishlist jadi rapi</h4>
                    <p class="mb-3">Kelompokkan barang di Wishlist sesukamu</p>
                    <div class="d-flex justify-content-center">
                        <img src="/images/mascot-wishlist.png" alt="Wishlist Mascot" class="img-fluid" style="max-width: 200px;" loading="lazy">
                    </div>
                    <p class="mt-2">Pakai fitur Koleksi untuk mengelompokkan barang-barang di Wishlist sesukamu.</p>
                    <button type="button" class="btn btn-success mt-3" data-bs-toggle="modal" data-bs-target="#createCollectionModal">Buat Koleksi</button>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mb-4 d-flex flex-column flex-md-row gap-2">
                <div class="input-group w-100 w-md-50">
                    <input type="text" class="form-control" placeholder="Cari Barang" aria-label="Cari Barang">
                    <button class="btn btn-outline-success" type="button">Cari</button>
                </div>
                <button class="btn btn-success w-100 w-md-auto" data-bs-toggle="modal" data-bs-target="#createCollectionModal">Buat Koleksi</button>
            </div>

            <!-- Wishlist Collections -->
            <div class="row g-4">
                @forelse ($this->wishlists as $wishlist)
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm border-0 rounded-3 wishlist-card" draggable="true">
                            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">{{ $wishlist->name }}</h5>
                                <div>
                                    <button class="btn btn-sm btn-light text-primary" data-bs-toggle="modal" data-bs-target="#editCollectionModal{{ $wishlist->id }}"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteWishlist({{ $wishlist->id }})"><i class="fas fa-trash"></i></button>
                                </div>
                            </div>
                            <div class="card-body p-3">
                                <div class="row row-cols-2 g-2">
                                    @forelse ($wishlist->items as $item)
                                        <div class="col">
                                            <div class="card h-100 border-0">
                                                <img src="/images/{{ $item->image }}" class="card-img-top img-fluid rounded" alt="{{ $item->name }}" loading="lazy">
                                                <div class="card-body p-2">
                                                    <p class="card-text small">{{ $item->name }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center text-muted">Belum ada barang.</div>
                                    @endforelse
                                </div>
                            </div>
                            <div class="card-footer bg-light text-center">
                                <a href="/wishlist/{{ $wishlist->id }}" class="btn btn-outline-primary btn-sm">Lihat Semua</a>
                            </div>
                        </div>
                    </div>

                    <!-- Edit Collection Modal -->
                    <div class="modal fade" id="editCollectionModal{{ $wishlist->id }}" tabindex="-1" aria-labelledby="editCollectionModalLabel{{ $wishlist->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title" id="editCollectionModalLabel{{ $wishlist->id }}">Edit Koleksi</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form wire:submit.prevent="updateWishlist({{ $wishlist->id }})">
                                        <div class="mb-3">
                                            <label for="name{{ $wishlist->id }}" class="form-label">Nama Koleksi</label>
                                            <input type="text" class="form-control" id="name{{ $wishlist->id }}" wire:model="name" value="{{ $wishlist->name }}" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info text-center" role="alert">
                            Anda belum memiliki koleksi. Buat koleksi baru untuk memulai!
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Recommended Items -->
            <div class="mt-5">
                <h4 class="mb-3">Rekomendasi Untuk Anda</h4>
                <div class="row row-cols-2 row-cols-md-4 g-3">
                    @forelse ($this->recommendedItems as $item)
                        <div class="col">
                            <div class="card h-100 border-0 shadow-sm">
                                <img src="/images/{{ $item->image }}" class="card-img-top img-fluid rounded" alt="{{ $item->name }}" loading="lazy">
                                <div class="card-body p-2 text-center">
                                    <p class="card-text small mb-1">{{ $item->name }}</p>
                                    <p class="text-muted small mb-0">{{ $item->description }}</p>
                                    <button class="btn btn-sm btn-outline-success mt-1" wire:click="addToWishlist({{ $item->id }})">Tambah</button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted">Tidak ada rekomendasi saat ini.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Create Collection Modal -->
        <div class="modal fade" id="createCollectionModal" tabindex="-1" aria-labelledby="createCollectionModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="createCollectionModalLabel">Buat Koleksi Baru</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="createWishlist">
                            <div class="mb-3">
                                <label for="newName" class="form-label">Nama Koleksi</label>
                                <input type="text" class="form-control" id="newName" wire:model="newName" required>
                            </div>
                            <button type="submit" class="btn btn-success">Buat</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Custom CSS -->
        <style>
            .wishlist-card {
                transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            }
            .wishlist-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 8px 16px rgba(0,0,0,0.1);
            }
            .card-img-top {
                height: 100px;
                object-fit: cover;
            }
            @media (max-width: 576px) {
                .card-body {
                    padding: 1rem;
                }
                .card-title {
                    font-size: 1.1rem;
                }
                .input-group {
                    width: 100%;
                }
            }
        </style>

        <!-- JavaScript for Drag-and-Drop -->
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const cards = document.querySelectorAll('.wishlist-card');
                cards.forEach(card => {
                    card.addEventListener('dragstart', (e) => {
                        e.dataTransfer.setData('text/plain', card.id);
                    });
                });
                const dropZone = document.querySelector('.row.g-4');
                dropZone.addEventListener('dragover', (e) => e.preventDefault());
                dropZone.addEventListener('drop', (e) => {
                    e.preventDefault();
                    const id = e.dataTransfer.getData('text');
                    const draggedCard = document.getElementById(id);
                    dropZone.appendChild(draggedCard);
                });
            });

            function deleteWishlist(id) {
                if (confirm('Yakin ingin menghapus koleksi ini?')) {
                    // Livewire action to delete
                    window.livewire.emit('deleteWishlist', id);
                }
            }
        </script>
    </div>
    @endvolt
</x-app>
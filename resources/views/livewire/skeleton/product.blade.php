<div class="skeleton-container">
    <style>
        .skeleton-placeholder {
            position: relative;
            overflow: hidden;
            background-color: #e9ecef;
            border-radius: 0.5rem;
        }
        
        .skeleton-placeholder::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg, 
                transparent, 
                rgba(255, 255, 255, 0.6), 
                transparent);
            animation: shimmer 1.5s infinite;
        }
        
        @keyframes shimmer {
            0% {
                transform: translateX(-100%);
            }
            100% {
                transform: translateX(100%);
            }
        }
        
        .card-skeleton {
            transition: all 0.3s ease;
            border: 1px solid #e9ecef;
            border-radius: 0.75rem;
            overflow: hidden;
        }
    </style>

    <div class="row g-3 g-md-4">
        @for ($i = 0; $i < 8; $i++)
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card card-skeleton h-100">
                <!-- Gambar Skeleton -->
                <div class="position-relative" style="height: 200px; background-color: #f8f9fa;">
                    <div class="skeleton-placeholder w-100 h-100"></div>
                </div>
                
                <div class="card-body p-4">
                    <!-- Badge Skeleton -->
                    <div class="mb-3">
                        <div class="skeleton-placeholder" style="height: 24px; width: 40%;"></div>
                    </div>
                    
                    <!-- Nama Produk Skeleton -->
                    <div class="mb-3">
                        <div class="skeleton-placeholder" style="height: 20px; width: 100%;"></div>
                        <div class="skeleton-placeholder mt-2" style="height: 20px; width: 90%;"></div>
                    </div>
                    
                    <!-- Harga Skeleton -->
                    <div class="mb-3">
                        <div class="skeleton-placeholder" style="height: 24px; width: 60%;"></div>
                    </div>
                    
                    <!-- Info Tambahan Skeleton -->
                    <div class="d-flex mb-4">
                        <div class="skeleton-placeholder me-3" style="height: 18px; width: 40%;"></div>
                        <div class="skeleton-placeholder" style="height: 18px; width: 40%;"></div>
                    </div>
                    
                    <!-- Tombol Skeleton -->
                    <div class="skeleton-placeholder" style="height: 42px; border-radius: 21px;"></div>
                </div>
            </div>
        </div>
        @endfor
    </div>
</div>
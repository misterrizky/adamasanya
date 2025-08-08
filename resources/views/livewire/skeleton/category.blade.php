<div class="skeleton-container">
    <style>
        .skeleton-circle, .skeleton-line {
            position: relative;
            overflow: hidden;
        }
        .skeleton-circle::after, .skeleton-line::after {
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
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
    </style>

    <div class="row g-3 g-md-4">
        @for ($i = 0; $i < 6; $i++)
        <div class="col-4 col-sm-3 col-md-2 col-lg-2 col-xl-1-5">
            <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden">
                <div class="card-body p-3 d-flex flex-column align-items-center justify-content-center">
                    <div class="skeleton-circle bg-gray-200 rounded-circle mb-3" style="width: 80px; height: 80px;"></div>
                    <div class="w-100 text-center">
                        <div class="skeleton-line bg-gray-200 mx-auto mb-2" style="height: 16px; width: 70%"></div>
                        <div class="skeleton-line bg-gray-200 mx-auto" style="height: 12px; width: 50%"></div>
                    </div>
                </div>
            </div>
        </div>
        @endfor
    </div>
</div>
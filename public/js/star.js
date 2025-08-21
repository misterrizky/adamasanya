document.addEventListener('DOMContentLoaded', () => {
    // Initialize star rating untuk semua elemen dengan class 'star-rating'
    const starContainers = document.querySelectorAll('.star-rating');
    
    starContainers.forEach(container => {
        const stars = container.querySelectorAll('.ki-star');
        
        // Hover effect
        stars.forEach(star => {
            star.addEventListener('mouseover', () => {
                const value = star.dataset.value;
                highlightStars(container, value);
            });

            star.addEventListener('mouseout', () => {
                const currentValue = container.dataset.rating || 0;
                highlightStars(container, currentValue);
            });

            // Click sudah ditangani Livewire via wire:click, tapi kita sync visual
            star.addEventListener('click', () => {
                const value = star.dataset.value;
                container.dataset.rating = value; // Simpan rating di container
                highlightStars(container, value);
            });
        });
    });

    // Function untuk highlight bintang berdasarkan value
    function highlightStars(container, value) {
        const stars = container.querySelectorAll('.ki-star');
        stars.forEach(star => {
            if (star.dataset.value <= value) {
                star.classList.remove('ki-filled', 'text-gray-400');
                star.classList.add('ki-solid', 'text-warning');
            } else {
                star.classList.remove('ki-solid', 'text-warning');
                star.classList.add('ki-filled', 'text-gray-400');
            }
        });
    }

    // Sync dengan Livewire saat navigated
    document.addEventListener('livewire:navigated', () => {
        starContainers.forEach(container => {
            const currentValue = container.dataset.rating || 0;
            highlightStars(container, currentValue);
        });
    });

    // Dengarkan event dari Livewire jika rating berubah
    window.addEventListener('livewire:update', event => {
        const { rating } = event.detail;
        const container = document.querySelector('.star-rating');
        if (container && rating) {
            container.dataset.rating = rating;
            highlightStars(container, rating);
        }
    });
});
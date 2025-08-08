document.addEventListener('DOMContentLoaded', () => { 
    KTMenu.init = function () {
        KTMenu.createInstances();
        KTMenu.initHandlers();
    };
    KTDrawer.init(),
    KTMenu.init(),
    KTScroll.init(),
    KTSticky.init(),
    KTSwapper.init(),
    KTToggle.init(),
    KTScrolltop.init(),
    KTDialer.init(),
    KTImageInput.init(),
    KTPasswordMeter.init();
    KTApp.init(),
    KTThemeMode.init();
    requestNotificationPermission();
    requestLocation();
    if ( document.documentElement ) {
        if ( document.documentElement.hasAttribute("data-bs-theme-mode")) {
            themeMode = document.documentElement.getAttribute("data-bs-theme-mode");
        } else {
            if ( localStorage.getItem("data-bs-theme") !== null ) {
                themeMode = localStorage.getItem("data-bs-theme");
            } else {
                themeMode = defaultThemeMode;
            }
        }
        if (themeMode === "system") {
            themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
        }
        document.documentElement.setAttribute("data-bs-theme", themeMode);
    }
    AOS.init();
});
document.addEventListener('livewire:navigated', () => { 
    KTMenu.init = function () {
        KTMenu.createInstances();
        KTMenu.initHandlers();
    };
    KTDrawer.init(),
    KTMenu.init(),
    KTScroll.init(),
    KTSticky.init(),
    KTSwapper.init(),
    KTToggle.init(),
    KTScrolltop.init(),
    KTDialer.init(),
    KTImageInput.init(),
    KTPasswordMeter.init();
    KTApp.init(),
    KTThemeMode.init();
    requestNotificationPermission();
    requestLocation();
    if ( document.documentElement ) {
        if ( document.documentElement.hasAttribute("data-bs-theme-mode")) {
            themeMode = document.documentElement.getAttribute("data-bs-theme-mode");
        } else {
            if ( localStorage.getItem("data-bs-theme") !== null ) {
                themeMode = localStorage.getItem("data-bs-theme");
            } else {
                themeMode = defaultThemeMode;
            }
        }
        if (themeMode === "system") {
            themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
        }
        document.documentElement.setAttribute("data-bs-theme", themeMode);
    }
    AOS.init();
});
document.addEventListener('livewire:update', () => { 
    KTMenu.init = function () {
        KTMenu.createInstances();
        KTMenu.initHandlers();
    };
    KTDrawer.init(),
    KTMenu.init(),
    KTScroll.init(),
    KTSticky.init(),
    KTSwapper.init(),
    KTToggle.init(),
    KTScrolltop.init(),
    KTDialer.init(),
    KTImageInput.init(),
    KTPasswordMeter.init();
    KTApp.init(),
    KTThemeMode.init();
    requestNotificationPermission();
    requestLocation();
    if ( document.documentElement ) {
        if ( document.documentElement.hasAttribute("data-bs-theme-mode")) {
            themeMode = document.documentElement.getAttribute("data-bs-theme-mode");
        } else {
            if ( localStorage.getItem("data-bs-theme") !== null ) {
                themeMode = localStorage.getItem("data-bs-theme");
            } else {
                themeMode = defaultThemeMode;
            }
        }
        if (themeMode === "system") {
            themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
        }
        document.documentElement.setAttribute("data-bs-theme", themeMode);
    }
    if (window.tns) {
        window.tns({
            container: '.tns-default',
            loop: true,
            swipeAngle: false,
            speed: 2000,
            autoplay: true,
            autoplayTimeout: 18000,
            controls: true,
            nav: false,
            items: 1,
            center: false,
            dots: false,
            prevButton: '#dpo_prev',
            nextButton: '#dpo_next',
            responsive: {
                1200: { items: 3 },
                992: { items: 2 }
            }
        });
    }
    AOS.init();
});
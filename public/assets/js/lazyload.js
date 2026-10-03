// Lazy Loading Fade-In via IntersectionObserver
document.addEventListener("DOMContentLoaded", () => {
    const lazyImages = document.querySelectorAll("img.lazy-img");

    if ("IntersectionObserver" in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add("is-visible");
                    observer.unobserve(entry.target);
                }
            });
        }, {
            rootMargin: "100px 0px",
            threshold: 0.1
        });

        lazyImages.forEach(img => observer.observe(img));
    } else {
        // Fallback für alte Browser
        lazyImages.forEach(img => img.classList.add("is-visible"));
    }
});

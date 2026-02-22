document.addEventListener("DOMContentLoaded", () => {
    const animatedEls = document.querySelectorAll(
        ".fade-in, .slide-up, .zoom-in, .rotate-in"
    );

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add("in-view");
                }
            });
        },
        { threshold: 0.1 }
    );

    animatedEls.forEach((el) => observer.observe(el));
});

document.addEventListener("DOMContentLoaded", () => {
    const elements = document.querySelectorAll(".scroll-animate");

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add("in-view");
                    entry.target.classList.remove("out-view");
                } else {
                    entry.target.classList.remove("in-view");
                    entry.target.classList.add("out-view");
                }
            });
        },
        {
            threshold: 0.1,
        }
    );

    elements.forEach((el) => observer.observe(el));
});

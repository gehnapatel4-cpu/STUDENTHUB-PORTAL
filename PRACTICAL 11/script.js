const themeBtn = document.getElementById("themeBtn");
const savedTheme = localStorage.getItem("theme");

if (savedTheme === "dark") {
    document.body.classList.add("dark-theme");
    if (themeBtn) {
        themeBtn.textContent = "Light Mode";
    }
}

if (themeBtn) {
    themeBtn.addEventListener("click", function() {
        document.body.classList.toggle("dark-theme");
        if (document.body.classList.contains("dark-theme")) {
            localStorage.setItem("theme", "dark");
            themeBtn.textContent = "Light Mode";
        } else {
            localStorage.setItem("theme", "light");
            themeBtn.textContent = "Dark Mode";
        }
    });
}

const menuBtn = document.getElementById("menuBtn");
const mainNav = document.getElementById("mainNav");

if (menuBtn && mainNav) {
    menuBtn.addEventListener("click", function() {
        mainNav.classList.toggle("open");
    });
}

const closeBannerBtn = document.getElementById("closeBannerBtn");
const notificationBanner = document.getElementById("notificationBanner");

if (closeBannerBtn && notificationBanner) {
    closeBannerBtn.addEventListener("click", function() {
        notificationBanner.style.display = "none";
    });
}

const openModalBtn = document.getElementById("openModalBtn");
const closeModalBtn = document.getElementById("closeModalBtn");
const infoModal = document.getElementById("infoModal");

if (openModalBtn && infoModal) {
    openModalBtn.addEventListener("click", function() {
        infoModal.classList.add("show");
    });
}

if (closeModalBtn && infoModal) {
    closeModalBtn.addEventListener("click", function() {
        infoModal.classList.remove("show");
    });
}

if (infoModal) {
    window.addEventListener("click", function(event) {
        if (event.target === infoModal) {
            infoModal.classList.remove("show");
        }
    });
}

const faqQuestions = document.querySelectorAll(".faq-question");

faqQuestions.forEach(function(question) {
    question.addEventListener("click", function() {
        this.classList.toggle("active");
        const answer = this.nextElementSibling;
        if (answer) {
            if (answer.style.display === "block") {
                answer.style.display = "none";
            } else {
                answer.style.display = "block";
            }
        }
    });
});

const slides = document.querySelectorAll(".slide");
const prevBtn = document.getElementById("prevBtn");
const nextBtn = document.getElementById("nextBtn");
let currentSlide = 0;

function showSlide(index) {
    if (slides.length === 0) return;
    if (index >= slides.length) {
        currentSlide = 0;
    } else if (index < 0) {
        currentSlide = slides.length - 1;
    } else {
        currentSlide = index;
    }
    slides.forEach(function(slide) {
        slide.classList.remove("active");
    });
    slides[currentSlide].classList.add("active");
}

if (prevBtn && nextBtn && slides.length > 0) {
    prevBtn.addEventListener("click", function() {
        showSlide(currentSlide - 1);
    });
    nextBtn.addEventListener("click", function() {
        showSlide(currentSlide + 1);
    });
}

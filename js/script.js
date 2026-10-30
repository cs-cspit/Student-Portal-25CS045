// FAQ open/close

const faqQuestions = document.querySelectorAll(".faq-question");

faqQuestions.forEach(function (question) {

    question.addEventListener("click", function () {

        const faqItem = question.parentElement;
        const icon = question.querySelector("span");

        faqItem.classList.toggle("active");

        if (faqItem.classList.contains("active")) {
            icon.textContent = "-";
        } else {
            icon.textContent = "+";
        }
    });

});

// Modal

const openModal = document.getElementById("openModal");
const welcomeModal = document.getElementById("welcomeModal");
const closeModal = document.getElementById("closeModal");

if (openModal) {
    openModal.addEventListener("click", function () {
        welcomeModal.classList.add("show");
    });
}

if (closeModal) {
    closeModal.addEventListener("click", function () {
        welcomeModal.classList.remove("show");
    });
}

// Event Slider

const eventSlides = document.querySelectorAll(".event-slide");
const nextEvent = document.getElementById("nextEvent");
const prevEvent = document.getElementById("prevEvent");

let currentEvent = 0;

function showEvent(index, direction) {

    eventSlides.forEach(function (slide) {
        slide.classList.remove(
            "active",
            "slide-next",
            "slide-previous"
        );
    });

    const currentSlide = eventSlides[index];

    currentSlide.classList.add("active");

    if (direction === "next") {
        currentSlide.classList.add("slide-next");
    } else {
        currentSlide.classList.add("slide-previous");
    }
}

if (nextEvent) {

    nextEvent.addEventListener("click", function () {

        currentEvent++;

        if (currentEvent >= eventSlides.length) {
            currentEvent = 0;
        }

        showEvent(currentEvent, "next");
    });
}

if (prevEvent) {

    prevEvent.addEventListener("click", function () {

        currentEvent--;

        if (currentEvent < 0) {
            currentEvent = eventSlides.length - 1;
        }

        showEvent(currentEvent, "previous");
    });
}

// Hamburger Menu

const menuButton = document.getElementById("menuButton");
const mobileMenu = document.getElementById("mobileMenu");

if (menuButton && mobileMenu) {
    menuButton.addEventListener("click", function () {
        mobileMenu.classList.toggle("show");
    });
}

// Light/Dark Theme

const themeButton = document.getElementById("themeButton");

if (themeButton) {

    const savedTheme = localStorage.getItem("theme");

    if (savedTheme === "dark") {
        document.body.classList.add("dark-theme");
        themeButton.textContent = "☀️ Light Mode";
    }

    themeButton.addEventListener("click", function () {

        document.body.classList.toggle("dark-theme");

        if (document.body.classList.contains("dark-theme")) {
            localStorage.setItem("theme", "dark");
            themeButton.textContent = "☀️ Light Mode";
        } else {
            localStorage.setItem("theme", "light");
            themeButton.textContent = "🌙 Dark Mode";
        }

    });
}

// Notification Banner

const notificationBanner = document.getElementById("notificationBanner");
const closeNotification = document.getElementById("closeNotification");

if (notificationBanner && closeNotification) {
    closeNotification.addEventListener("click", function () {
        notificationBanner.classList.add("hide");
    });
}


/* Practical 5: Registration Form Validation */

const registrationForm = document.getElementById("registrationForm");

if (registrationForm) {
    const nameInput = document.getElementById("name");
    const emailInput = document.getElementById("email");
    const mobileInput = document.getElementById("mobile");
    const passwordInput = document.getElementById("password");
    const confirmPasswordInput = document.getElementById("confirmPassword");
    const courseInput = document.getElementById("course");
    const yearInput = document.getElementById("year");
    const termsInput = document.getElementById("terms");

    const nameRegex = /^[A-Za-z]+(?:[ '-][A-Za-z]+)*$/;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const mobileRegex = /^[6-9]\d{9}$/;
    const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/;

    function validateField(input, errorId, message, isValid) {
        const error = document.getElementById(errorId);

        input.classList.toggle("valid", isValid);
        input.classList.toggle("invalid", !isValid);
        error.textContent = isValid ? "" : message;

        return isValid;
    }

    function validateName() {
        return validateField(
            nameInput, "nameError",
            "Enter a valid name (letters, spaces, hyphens or apostrophes).",
            nameRegex.test(nameInput.value.trim())
        );
    }

    function validateEmail() {
        return validateField(
            emailInput, "emailError",
            "Enter a valid email address.",
            emailRegex.test(emailInput.value.trim())
        );
    }

    function validateMobile() {
        return validateField(
            mobileInput, "mobileError",
            "Enter a valid 10-digit Indian mobile number.",
            mobileRegex.test(mobileInput.value.trim())
        );
    }

    function validatePassword() {
        const password = passwordInput.value;
        const isValid = passwordRegex.test(password);

        validateField(
            passwordInput, "passwordError",
            "Use 8+ characters with uppercase, lowercase, a number and a special character.",
            isValid
        );

        updateStrength(password);
        validateConfirmPassword();

        return isValid;
    }

    function validateConfirmPassword() {
        const isValid =
            confirmPasswordInput.value !== "" &&
            confirmPasswordInput.value === passwordInput.value;

        return validateField(
            confirmPasswordInput, "confirmPasswordError",
            "Passwords do not match.",
            isValid
        );
    }

    function validateCourse() {
        return validateField(
            courseInput, "courseError",
            "Please select a course.",
            courseInput.value !== ""
        );
    }

    function validateYear() {
        return validateField(
            yearInput, "yearError",
            "Please select your year of study.",
            yearInput.value !== ""
        );
    }

    function validateGender() {
        const selected = document.querySelector('input[name="gender"]:checked');
        document.getElementById("genderError").textContent =
            selected ? "" : "Please select your gender.";

        return !!selected;
    }

    function validateTerms() {
        document.getElementById("termsError").textContent =
            termsInput.checked ? "" : "You must accept the terms.";

        return termsInput.checked;
    }

    function updateStrength(password) {
        const bar = document.getElementById("strengthBar");
        const text = document.getElementById("strengthText");

        let score = 0;

        if (password.length >= 8) score++;
        if (/[a-z]/.test(password) && /[A-Z]/.test(password)) score++;
        if (/\d/.test(password)) score++;
        if (/[@$!%*?&]/.test(password)) score++;

        const labels = [
            "Not entered",
            "Weak",
            "Fair",
            "Good",
            "Strong"
        ];

        bar.style.width = (score * 25) + "%";
        bar.style.background =
            score <= 1 ? "#dc2626" :
            score === 2 ? "#f59e0b" :
            score === 3 ? "#2563eb" : "#16a34a";

        text.textContent = "Password strength: " +
            (password === "" ? labels[0] : labels[score]);
    }

    nameInput.addEventListener("input", validateName);
    emailInput.addEventListener("input", validateEmail);
    mobileInput.addEventListener("input", function () {
    // Remove all non-numeric characters
    mobileInput.value = mobileInput.value.replace(/\D/g, "");

    // Allow only 10 digits
    mobileInput.value = mobileInput.value.slice(0, 10);

    validateMobile();
});
    passwordInput.addEventListener("input", validatePassword);
    confirmPasswordInput.addEventListener("input", validateConfirmPassword);
    courseInput.addEventListener("change", validateCourse);
    yearInput.addEventListener("change", validateYear);
    document.querySelectorAll('input[name="gender"]').forEach(function (radio) {
        radio.addEventListener("change", validateGender);
    });
    termsInput.addEventListener("change", validateTerms);

    registrationForm.addEventListener("submit", function (event) {
        event.preventDefault();

        const isValid = [
            validateName(),
            validateEmail(),
            validateMobile(),
            validatePassword(),
            validateConfirmPassword(),
            validateCourse(),
            validateYear(),
            validateGender(),
            validateTerms()
        ].every(Boolean);

        const successMessage = document.getElementById("successMessage");

        if (isValid) {
            successMessage.textContent =
                "Registration form validated successfully!";
        } else {
            successMessage.textContent = "";
            const firstInvalid = registrationForm.querySelector(".invalid");
            if (firstInvalid) firstInvalid.focus();
        }
    });
}


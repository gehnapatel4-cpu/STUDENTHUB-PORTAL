const form = document.getElementById("registrationForm");

const fullName = document.getElementById("fullName");
const email = document.getElementById("email");
const mobile = document.getElementById("mobile");
const password = document.getElementById("password");
const confirmPassword = document.getElementById("confirmPassword");
const course = document.getElementById("course");
const terms = document.getElementById("terms");

const nameError = document.getElementById("nameError");
const emailError = document.getElementById("emailError");
const mobileError = document.getElementById("mobileError");
const passwordError = document.getElementById("passwordError");
const confirmPasswordError = document.getElementById("confirmPasswordError");
const courseError = document.getElementById("courseError");
const yearError = document.getElementById("yearError");
const genderError = document.getElementById("genderError");
const termsError = document.getElementById("termsError");

const strengthBar = document.getElementById("strengthBar");
const strengthText = document.getElementById("strengthText");

const nameRegex = /^[A-Za-z\s]{3,50}$/;
const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[A-Za-z]{2,}$/;
const mobileRegex = /^[6-9]\d{9}$/;
const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]{8,}$/;

function validateName() {
    if (fullName.value.trim() === "") {
        nameError.textContent = "Full name is required.";
        fullName.className = "form-control error";
        return false;
    } else if (!nameRegex.test(fullName.value.trim())) {
        nameError.textContent = "Name must contain only letters and be at least 3 characters.";
        fullName.className = "form-control error";
        return false;
    } else {
        nameError.textContent = "";
        fullName.className = "form-control valid";
        return true;
    }
}

function validateEmail() {
    if (email.value.trim() === "") {
        emailError.textContent = "Email address is required.";
        email.className = "form-control error";
        return false;
    } else if (!emailRegex.test(email.value.trim())) {
        emailError.textContent = "Please enter a valid email address.";
        email.className = "form-control error";
        return false;
    } else {
        emailError.textContent = "";
        email.className = "form-control valid";
        return true;
    }
}

function validateMobile() {
    if (mobile.value.trim() === "") {
        mobileError.textContent = "Mobile number is required.";
        mobile.className = "form-control error";
        return false;
    } else if (!mobileRegex.test(mobile.value.trim())) {
        mobileError.textContent = "Mobile number must be 10 digits starting with 6, 7, 8, or 9.";
        mobile.className = "form-control error";
        return false;
    } else {
        mobileError.textContent = "";
        mobile.className = "form-control valid";
        return true;
    }
}

function checkPasswordStrength(val) {
    let score = 0;
    if (val.length >= 8) score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[a-z]/.test(val)) score++;
    if (/\d/.test(val)) score++;
    if (/[@$!%*?&#]/.test(val)) score++;

    if (val.length === 0) {
        strengthBar.style.width = "0%";
        strengthBar.style.backgroundColor = "transparent";
        strengthText.textContent = "";
    } else if (score <= 2) {
        strengthBar.style.width = "33%";
        strengthBar.style.backgroundColor = "#dc2626";
        strengthText.textContent = "Weak Password";
        strengthText.style.color = "#dc2626";
    } else if (score <= 4) {
        strengthBar.style.width = "66%";
        strengthBar.style.backgroundColor = "#f59e0b";
        strengthText.textContent = "Medium Password";
        strengthText.style.color = "#f59e0b";
    } else {
        strengthBar.style.width = "100%";
        strengthBar.style.backgroundColor = "#16a34a";
        strengthText.textContent = "Strong Password";
        strengthText.style.color = "#16a34a";
    }
}

function validatePassword() {
    checkPasswordStrength(password.value);
    if (password.value === "") {
        passwordError.textContent = "Password is required.";
        password.className = "form-control error";
        return false;
    } else if (!passwordRegex.test(password.value)) {
        passwordError.textContent = "Password must be at least 8 chars with 1 uppercase, 1 lowercase, 1 digit, and 1 special character.";
        password.className = "form-control error";
        return false;
    } else {
        passwordError.textContent = "";
        password.className = "form-control valid";
        return true;
    }
}

function validateConfirmPassword() {
    if (confirmPassword.value === "") {
        confirmPasswordError.textContent = "Please confirm your password.";
        confirmPassword.className = "form-control error";
        return false;
    } else if (confirmPassword.value !== password.value) {
        confirmPasswordError.textContent = "Passwords do not match.";
        confirmPassword.className = "form-control error";
        return false;
    } else {
        confirmPasswordError.textContent = "";
        confirmPassword.className = "form-control valid";
        return true;
    }
}

function validateCourse() {
    if (course.value === "") {
        courseError.textContent = "Please select your course.";
        course.className = "form-control error";
        return false;
    } else {
        courseError.textContent = "";
        course.className = "form-control valid";
        return true;
    }
}

function validateYear() {
    const selectedYear = document.querySelector('input[name="year"]:checked');
    if (!selectedYear) {
        yearError.textContent = "Please select your current year.";
        return false;
    } else {
        yearError.textContent = "";
        return true;
    }
}

function validateGender() {
    const selectedGender = document.querySelector('input[name="gender"]:checked');
    if (!selectedGender) {
        genderError.textContent = "Please select your gender.";
        return false;
    } else {
        genderError.textContent = "";
        return true;
    }
}

function validateTerms() {
    if (!terms.checked) {
        termsError.textContent = "You must agree to the terms and conditions.";
        return false;
    } else {
        termsError.textContent = "";
        return true;
    }
}

fullName.addEventListener("input", validateName);
email.addEventListener("input", validateEmail);
mobile.addEventListener("input", validateMobile);
password.addEventListener("input", validatePassword);
confirmPassword.addEventListener("input", validateConfirmPassword);
course.addEventListener("change", validateCourse);

const yearInputs = document.querySelectorAll('input[name="year"]');
yearInputs.forEach(function(input) {
    input.addEventListener("change", validateYear);
});

const genderInputs = document.querySelectorAll('input[name="gender"]');
genderInputs.forEach(function(input) {
    input.addEventListener("change", validateGender);
});

terms.addEventListener("change", validateTerms);

form.addEventListener("submit", function(event) {
    event.preventDefault();

    const isNameValid = validateName();
    const isEmailValid = validateEmail();
    const isMobileValid = validateMobile();
    const isPasswordValid = validatePassword();
    const isConfirmValid = validateConfirmPassword();
    const isCourseValid = validateCourse();
    const isYearValid = validateYear();
    const isGenderValid = validateGender();
    const isTermsValid = validateTerms();

    if (isNameValid && isEmailValid && isMobileValid && isPasswordValid && isConfirmValid && isCourseValid && isYearValid && isGenderValid && isTermsValid) {
        const studentData = {
            name: fullName.value.trim(),
            email: email.value.trim(),
            mobile: mobile.value.trim(),
            course: course.value,
            year: document.querySelector('input[name="year"]:checked').value,
            gender: document.querySelector('input[name="gender"]:checked').value
        };

        sessionStorage.setItem("registeredStudent", JSON.stringify(studentData));
        window.location.href = "success.html";
    }
});

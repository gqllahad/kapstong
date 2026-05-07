const steps = document.querySelectorAll('.step');
const formSteps = document.querySelectorAll('.form-step');
const progressFill = document.querySelector('.progress-fill');

let currentStep = 0;

// next student ID 
const nextButtonStep1 = document.getElementById("next1");
const nextButtonStep2 = document.getElementById("next2");

// Account details variables
const studentIDStep2 = document.getElementById("studentID1");
const studentIDStep3 = document.getElementById("studentID2");

const studentEmail1 = document.getElementById("studentEmail");
const studentEmail2 = document.getElementById("studentEmail2");

const warningText = document.getElementById("studentID-warning");
const warningTextEmail = document.getElementById("studentEmail-warning");

const passwordInput = document.getElementById("passwordInput");
const confirmPasswordInput = document.getElementById("confirmPasswordInput");

// validations element
const birthInputs = document.querySelectorAll('input[name="signBirth"]');
const telInput = document.querySelector('input[name="signTel"]');


// functions vanilla java
function updateStepper() {
  steps.forEach((step, index) => {
     step.classList.remove("active","completed");

    if(index < currentStep){
        step.classList.add("completed");
        step.textContent = "✔";
    }

    if(index === currentStep){
        step.classList.add("active");
        step.textContent = index + 1;
    }
  });

  formSteps.forEach((form, index) => {
    form.classList.toggle('active', index === currentStep);
  });

  let progress = (currentStep) / (steps.length - 1) * 100;
  progressFill.style.width = progress + "%";

}

function validateStep(stepIndex) {
    const currentForm = formSteps[stepIndex];
    const inputs = currentForm.querySelectorAll("input[required]");

    for (let input of inputs) {
        if (!input.checkValidity()) {
                input.reportValidity(); 
                return false;
            }
    }

    if (stepIndex === 2) { 
        if (confirmPasswordInput.value !== passwordInput.value) {
            showToast("Passwords do not match!", 3000);
            confirmPasswordInput.value = "";
            confirmPasswordInput.focus();
            return false;
        }
    }

    return true; 
}

// Name error handling
document.querySelectorAll('.name-input').forEach(input => {

    input.addEventListener("input", function () {

        this.value = this.value.toUpperCase();

        const regex = /^[A-Z\s]*$/;
        const parent = this.parentElement;
        const warning = parent.querySelector(".name-warning");

        if (!regex.test(this.value)) {
            parent.classList.add("name-error");
            warning.style.display = "block";
            this.setCustomValidity("Invalid characters");
            nextButtonStep1.disabled = true;
        } else {
            parent.classList.remove("name-error");
            warning.style.display = "none";
            this.setCustomValidity("");
            nextButtonStep1.disabled = false;
        }
    });
});

// birthdate error handling

document.querySelectorAll("input[type='date']").forEach(input => {
    if (input.value !== "") {
        input.classList.add("has-value");
    }

    input.addEventListener("change", function () {
        this.classList.toggle("has-value", this.value !== "");
    });
});

birthInputs.forEach(input => {
    input.addEventListener("input", function () {
        const parent = this.parentElement;
        const warning = parent.querySelector(".birth-warning");

        const value = this.value;

        const birthDate = new Date(value);
        if (!value || isNaN(birthDate.getTime())) {
            parent.classList.add("birth-error");
            warning.textContent = "Please enter a valid date.";
            warning.style.display = "block";
            this.setCustomValidity("Invalid date");
            nextButtonStep1.disabled = true;
            return;
        }

        if (birthDate.getFullYear() < 1900) {
            parent.classList.add("birth-error");
            warning.textContent = "Year is too far in the past.";
            warning.style.display = "block";
            this.setCustomValidity("Year too old");
            nextButtonStep1.disabled = true;
            return;
        }

        const today = new Date();
        let age = today.getFullYear() - birthDate.getFullYear();
        const m = today.getMonth() - birthDate.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }

        if (age < 15) {
            parent.classList.add("birth-error");
            warning.textContent = "You must be at least 18 years old.";
            warning.style.display = "block";
            this.setCustomValidity("You must be at least 18 years old.");
            nextButtonStep1.disabled = true;
        } else {
            parent.classList.remove("birth-error");
            warning.style.display = "none";
            this.setCustomValidity("");
            nextButtonStep1.disabled = false;
        }
    });
});

// address handling
document.querySelectorAll("select").forEach(select => {
    select.addEventListener("change", function () {
        if (this.value !== "") {
            this.classList.add("has-value");
        } else {
            this.classList.remove("has-value");
        }
    });
});

// mobile number error handling

telInput.addEventListener("input", function() {
    const parent = this.parentElement;
    let warning = parent.querySelector(".tel-warning");
    const value = this.value.trim();

    

    if (!warning) {
        warning = document.createElement("p");
        warning.classList.add("tel-warning");
        warning.style.color = "var(--declined-color)";
        warning.style.fontSize = "0.6rem";
        warning.style.fontWeight = "600";
        warning.style.display = "none";
        parent.appendChild(warning);
    }

    let digits = this.value.replace(/\D/g, "");

    document.getElementById("rawTel").value = digits;

    if (digits.length > 11) digits = digits.slice(0, 11);

    if (!digits.startsWith("09")) {
        parent.classList.add("tel-error");
        warning.textContent = "Mobile number must start with 09";
        warning.style.display = "block";
        this.setCustomValidity("Invalid mobile number");
        nextButtonStep1.disabled = true;
        return;
    } else {
        parent.classList.remove("tel-error");
        warning.style.display = "none";
        this.setCustomValidity("");
        nextButtonStep1.disabled = false;
    }

    let formatted = "";
    for (let i = 0; i < digits.length; i++) {
        formatted += digits[i];
        if (i === 1 || i === 4 || i === 7) formatted += " ";
    }

    this.value = formatted.trim();

    if (digits.length !== 11) {
        parent.classList.add("tel-error");
        warning.textContent = "Enter a valid mobile number";
        warning.style.display = "block";
        warning.parentElement.classList.add("errorMobile");
        this.setCustomValidity("Invalid mobile number");
        nextButtonStep1.disabled = true;
    } else {
        parent.classList.remove("tel-error");
        warning.style.display = "none";
         warning.parentElement.classList.remove("errorMobile");
        this.setCustomValidity("");
        nextButtonStep1.disabled = false;
    }
});

// manage courses
document.addEventListener("DOMContentLoaded", function() {
    const courseSelect = document.getElementById('signCourse');

    fetch('getCourses.php')
        .then(res => res.json())
        .then(data => {
            courseSelect.innerHTML = '<option value="">Select Course</option>';

            if (data.acro && data.values) {
                data.acro.forEach((acro, index) => {
                    const option = document.createElement('option');
                    option.value = acro;
                    option.textContent = data.values[index];
                    courseSelect.appendChild(option);
                });
            }
        })
        .catch(err => console.error("Error loading courses:", err));
});

// academic year
function getAcademicYear() {
    const today = new Date();
    const currentYear = today.getFullYear();
    const currentMonth = today.getMonth() + 1;

    let startYear, endYear;

    if (currentMonth >= 6) {
        startYear = currentYear;
        endYear = currentYear + 1;
    } else {
        startYear = currentYear - 1;
        endYear = currentYear;
    }

    return startYear + "-" + endYear;
};

document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("academic-year").value = getAcademicYear();
});

// toggle passwords
function togglePassword(inputId, icon) {
    const input = document.getElementById(inputId);

    if (input.type === "password") {
        input.type = "text";
        icon.textContent = "Show"; 
    } else {
        input.type = "password";
        icon.textContent = "Hide";
    }
};

// toast
function showToast(message, duration = 2500) {
    const toast = document.getElementById("toast");

    toast.innerHTML = message; 
    const timerBar = document.createElement("div");
    timerBar.classList.add("toast-timer");
    toast.appendChild(timerBar);

    toast.classList.add("show");

    timerBar.style.transition = `width ${duration}ms linear`;
    setTimeout(() => {
        timerBar.style.width = "0%";
    }, 10); 

    setTimeout(() => {
        toast.classList.remove("show");
    }, duration);
};


//Next
document.querySelectorAll('[id^="next"]').forEach(btn => {
  btn.addEventListener('click', () => {
    if (validateStep(currentStep)) { 
        if (currentStep < formSteps.length - 1) {
            currentStep++;
            updateStepper();
        }
    }
  });
});

// Back
document.querySelectorAll('[id^="prev"]').forEach(btn => {
  btn.addEventListener('click', () => {
    if (currentStep > 0) {
      currentStep--;
      updateStepper();
    }
  });
});


confirmPasswordInput.addEventListener("focus", () => {
    if (!passwordInput.value) {
        showToast("Passwords do not match!", 3000);
        passwordInput.focus();
    }
});


// STUDENT CHECK
studentIDStep2.addEventListener("input", () => {
    studentIDStep3.value = studentIDStep2.value;

    const studentID = studentIDStep2.value.trim();
    if(studentID === "") return;

    fetch(`checkStudentID.php?id=${encodeURIComponent(studentID)}`)
      .then(res => res.json())
      .then(data => {
          if(data.exists) {
              warningText.style.display = "block";
              studentIDStep2.setCustomValidity("Student ID already taken!");
              studentIDStep2.parentElement.classList.add("id-taken");
              nextButtonStep2.disabled = true;
          } else {
              warningText.style.display = "none";
              studentIDStep2.setCustomValidity(""); 
              studentIDStep2.parentElement.classList.remove("id-taken");
              nextButtonStep2.disabled = false;
          }
      })
      .catch(err => console.error(err));
});


studentEmail1.addEventListener("input", () => {
    studentEmail2.value = studentEmail1.value;

    const studentEmail = studentEmail1.value.trim();
    if(studentEmail === "") return;

    const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-z0-9.-]+\.(com|net|org|edu|ph|gov)$/;

    if (!emailRegex.test(studentEmail)) {
        warningTextEmail.style.display = "block";
        warningTextEmail.textContent = "Enter a valid email address (e.g., example@gmail.com)";
        studentEmail1.setCustomValidity("Invalid email format");
        studentEmail1.parentElement.classList.add("email-taken");
        nextButtonStep1.disabled = true;
        return;
    }

    fetch(`checkStudentID.php?email=${encodeURIComponent(studentEmail)}`)
      .then(res => res.json())
      .then(data => {
          if(data.exists) {
              warningTextEmail.style.display = "block";
              studentEmail1.setCustomValidity("Email already been used!");
              studentEmail1.parentElement.classList.add("email-taken");
              nextButtonStep1.disabled = true;
          } else {
              warningTextEmail.style.display = "none";
              studentEmail1.setCustomValidity(""); 
              studentEmail1.parentElement.classList.remove("email-taken");
              nextButtonStep1.disabled = false;
          }
      })
      .catch(err => console.error(err));
});

studentEmail1.addEventListener("blur", checkEmail);

// 1995215248


// document.getElementById('signupForm').addEventListener('submit', (e) => {
//   e.preventDefault();
//   alert("Form submitted!");
// });
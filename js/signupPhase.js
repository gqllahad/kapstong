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
      
        if (input.type === "radio") {
            const name = input.name;
            const checked = currentForm.querySelectorAll(`input[name="${name}"]:checked`);
            if (checked.length === 0) {
                alert(`Please select an option for ${name}.`);
                return false;
            }
        }
    }

    if (stepIndex === 2) { 
        if (confirmPasswordInput.value !== passwordInput.value) {
            alert("Passwords do not match!");
            confirmPasswordInput.value = "";
            confirmPasswordInput.focus();
            return false;
        }
    }

    return true; 
}

// Last name caps
document.querySelectorAll('input[name="lastName"]').forEach(input => {
    input.addEventListener("input", function () {
        this.value = this.value.toUpperCase();
    });
});

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


//account details
studentIDStep2.addEventListener("input", () => {
    studentIDStep3.value = studentIDStep2.value;
});

studentEmail1.addEventListener("input", () => {
    studentEmail2.value = studentEmail1.value;
});

confirmPasswordInput.addEventListener("focus", () => {
    if (!passwordInput.value) {
        alert("Please enter your password first!");
        passwordInput.focus();
    }
});


// STUDENT CHECK
studentIDStep2.addEventListener("input", () => {
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
    const studentEmail = studentEmail1.value.trim();
    if(studentEmail === "") return;

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

// document.getElementById('signupForm').addEventListener('submit', (e) => {
//   e.preventDefault();
//   alert("Form submitted!");
// });
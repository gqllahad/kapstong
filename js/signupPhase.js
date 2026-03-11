const steps = document.querySelectorAll('.step');
const formSteps = document.querySelectorAll('.form-step');
const progressFill = document.querySelector('.progress-fill');

let currentStep = 0;

// Account details variables
const studentIDStep2 = document.getElementById("studentID1");
const studentIDStep3 = document.getElementById("studentID2");

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
        if (!input.value.trim()) {
            alert("Please fill all required fields in this step.");
            input.focus();
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

confirmPasswordInput.addEventListener("focus", () => {
    if (!passwordInput.value) {
        alert("Please enter your password first!");
        passwordInput.focus();
    }
});


// document.getElementById('signupForm').addEventListener('submit', (e) => {
//   e.preventDefault();
//   alert("Form submitted!");
// });
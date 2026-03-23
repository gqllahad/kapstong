const reveals = document.querySelectorAll(".scroll-reveal");
const signUp = document.getElementById("ls-switch");

// Invaded logins
const invaderLogin = document.getElementById("invaderLogin");
const modalInvader = document.getElementById("accessDenied");

const modalLoginError = document.getElementById("errorLogin");
const loginError = document.getElementById("incorrectLogin");

const loginEmail = document.getElementById("loginEmail");
const loginPass = document.getElementById("loginPassword");


function revealOnScroll() {
  const windowHeight = window.innerHeight;

  reveals.forEach((element) => {
    const elementTop = element.getBoundingClientRect().top;
    const revealPoint = 120;

    if (elementTop < windowHeight - revealPoint) {
      element.classList.add("active");
    }
  });
}

signUp.addEventListener("click", () => {
  window.location.href = "signupStudent.php";
});

// login invader
if (invaderLogin && modalInvader) {
  invaderLogin.addEventListener("click", () => {
    modalInvader.style.display = "none";

    const url = new URL(window.location);
    url.searchParams.delete("error");
    window.history.replaceState({}, document.title, url.toString());

    const loginSection = document.getElementById("log-container");
    if (loginSection) loginSection.scrollIntoView({ behavior: "smooth" });
  });
}

// incorrect login

if(loginError){

  const loginSection = document.getElementById("log-container");
  if (loginSection) loginSection.scrollIntoView({ behavior: "smooth" });

  loginError.classList.add("active");
  loginEmail.classList.add("incorrect");
  loginPass.classList.add("incorrect");

  setTimeout(() => {
    loginError.classList.remove("active");
    loginEmail.classList.remove("incorrect");
    loginPass.classList.remove("incorrect");

    const url = new URL(window.location);
    url.searchParams.delete("warning");
    window.history.replaceState({}, document.title, url.toString());

  }, 4000);
}

// login error
if (modalLoginError) {

  setTimeout(() => {
    modalLoginError.style.display = "none";

    const url = new URL(window.location);
    url.searchParams.delete("warning");
    window.history.replaceState({}, document.title, url.toString());

    const loginSection = document.getElementById("log-container");
    if (loginSection) loginSection.scrollIntoView({ behavior: "smooth" });

    }, 2000);
}


window.addEventListener("scroll", revealOnScroll);

window.addEventListener("DOMContentLoaded", revealOnScroll);

revealOnScroll();
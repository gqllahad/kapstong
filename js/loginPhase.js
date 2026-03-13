const reveals = document.querySelectorAll(".scroll-reveal");
const signUp = document.getElementById("ls-switch");

// Invaded logins
const invaderLogin = document.getElementById("invaderLogin");
const modalInvader = document.getElementById("accessDenied");

const modalLoginError = document.getElementById("errorLogin");

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
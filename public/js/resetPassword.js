const logoRedirect = document.getElementById("logoRedirect");
const loadingScreen = document.getElementById("backLoadingScreen");
const submitLoadingScreen = document.getElementById("submitLoadingScreen");

const reveals = document.querySelectorAll(".scroll-reveal");

const invalidEmail = document.getElementById("invalidEmail");
const modalInvader = document.getElementById("accessDenied");

const submitEmail = document.getElementById("submitEmail");

logoRedirect.addEventListener("click", () => {

    loadingScreen.classList.add("show");

    setTimeout(() => {

        window.location.href = "../loginPhase.php";

    }, 1000);

});

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

if (invalidEmail && modalInvader) {
    const url = new URL(window.location);
    url.searchParams.delete("error");
    window.history.replaceState({}, document.title, url.toString());
  invalidEmail.addEventListener("click", () => {
    modalInvader.style.display = "none";
  });

  setTimeout(() => {
    window.location.href = "forgot_password_function/forgotPassword.php";
  },1500);
}

submitEmail.addEventListener("submit", () => {
    submitLoadingScreen.classList.add("show");
});


window.addEventListener("DOMContentLoaded", revealOnScroll);

revealOnScroll();
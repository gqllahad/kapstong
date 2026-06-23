const reveals = document.querySelectorAll(".scroll-reveal");
const signUp = document.getElementById("ls-switch");

// landings
const heroRight = document.querySelector(".hero-right");
const heroLeft = document.querySelector(".hero-left");
const heroTag = document.querySelector(".hero-tag");
const heroTitle = document.querySelector(".hero-title");
const heroDesc = document.querySelector(".hero-desc");
const heroButtons = document.querySelector(".hero-buttons");
const heroStats = document.querySelector(".hero-stats");

// loading
const form = document.getElementById("loginForm");
const loginLoadingScreen = document.getElementById("loginLoadingnScreen");
const loadingScreen = document.getElementById("loadingScreen");
const forgotLoadingScreen = document.getElementById("forgotLoadingScreen");

// Invaded logins
const invaderLogin = document.getElementById("invaderLogin");
const modalInvader = document.getElementById("accessDenied");

const modalLoginError = document.getElementById("errorLogin");
const loginError = document.getElementById("incorrectLogin");

//sucess signup
const modalSignSuccess = document.getElementById("signSuccess");

const loginEmail = document.getElementById("loginEmail");
const loginPass = document.getElementById("loginPassword");

// reset password
const forgotPasswordLink = document.getElementById("forgotPasswordLink");


// functions


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
  loadingScreen.classList.add("show");

  setTimeout(()=>{
    window.location.href = "../Signup/signupStudent.php";
  }, 1000);
  
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

// signup success

if(modalSignSuccess){
    const loginSection = document.getElementById("log-container");
    if (loginSection) loginSection.scrollIntoView({ behavior: "smooth" });

    modalSignSuccess.classList.add("active");

    setTimeout(() => {

      modalSignSuccess.classList.remove("active");
      
      const url = new URL(window.location);
      url.searchParams.delete("success");
      window.history.replaceState({}, document.title, url.toString());

    }, 4000);
}

// login
form.addEventListener("submit" ,function () {
    loginLoadingScreen.classList.add("show");
});

forgotPasswordLink.addEventListener("click", (e) => {

    e.preventDefault();

    forgotLoadingScreen.classList.add("show");

    setTimeout(() => {

        window.location.href ="../Password/forgotPassword.php";

    }, 1000);

});

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

window.addEventListener("load", () => {

  const items = [
    heroRight,
    heroLeft,
    heroTitle,
    heroTag,
    heroDesc,
    heroButtons,
    heroStats
  ];

  items.forEach(el => {
    if (el) el.classList.add("preload");
  });

  setTimeout(() => {
     heroRight?.classList.add("animate-in");
     setTimeout(() => {heroLeft?.classList.add("animate-up");},400);
     setTimeout(() => {heroTag?.classList.add("animate-in-left");},700);
     setTimeout(() => {heroTitle?.classList.add("animate-up");},1200);
     setTimeout(() => { heroDesc?.classList.add("animate-up-delay1");},1700);
     setTimeout(() => {heroButtons?.classList.add("animate-up-delay2");},2200);

  }, 200);

});

window.addEventListener("scroll", revealOnScroll);

window.addEventListener("DOMContentLoaded", revealOnScroll);

revealOnScroll();
const reveals = document.querySelectorAll(".scroll-reveal");
const signUp = document.getElementById("ls-switch");
const signIn = document.getElementById("ls-switch2");
const getStarted = document.getElementById("getStarted");
const login = document.getElementById("login");

// faqs
const faqList = document.getElementById("faqList");
const faqFocused = document.getElementById("faqFocused");
const faqFocusedQuestion = document.getElementById("faqFocusedQuestion");
const faqFocusedAnswer = document.getElementById("faqFocusedAnswer");
const faqBackBtn = document.getElementById("faqBackBtn");

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
const loginSetLoadingScreen = document.getElementById("loginSetLoadingScreen");
const signupSetLoadingScreen = document.getElementById(
  "signupSetloadingScreen",
);
const loadingScreen = document.getElementById("loadingScreen");
const forgotLoadingScreen = document.getElementById("forgotLoadingScreen");

// Invaded logins
const invaderLogin = document.getElementById("invaderLogin");
const modalInvader = document.getElementById("accessDenied");

const modalLoginError = document.getElementById("errorLogin");
const loginError = document.getElementById("incorrectLogin");

// lockout timer
const lockoutBox = document.getElementById("lockoutBox");
const lockoutCountdown = document.getElementById("lockoutCountdown");

//sucess signup
const modalSignSuccess = document.getElementById("signSuccess");

const loginEmail = document.getElementById("loginEmail");
const loginPass = document.getElementById("loginPassword");

// reset password
const forgotPasswordLink = document.getElementById("forgotPasswordLink");

// theme change
const themeToggle = document.getElementById("themeToggle");
const themeIcon = themeToggle?.querySelector("i");

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

if (signUp) {
  signUp.addEventListener("click", () => {
    loadingScreen.classList.add("show");

    setTimeout(() => {
      window.location.href = "../Signup/signupStudent.php";
    }, 1000);
  });
}

if (signIn) {
  signIn.addEventListener("click", () => {
    loginSetLoadingScreen.classList.add("show");

    setTimeout(() => {
      window.location.href = "loginPage.php";
    }, 1000);
  });
}

if (getStarted) {
  getStarted.addEventListener("click", () => {
    signupSetLoadingScreen.classList.add("show");

    setTimeout(() => {
      window.location.href = "../Signup/signupStudent.php";
    }, 1000);
  });
}

if (login) {
  login.addEventListener("click", () => {
    loginSetLoadingScreen.classList.add("show");

    setTimeout(() => {
      window.location.href = "loginPage.php";
    }, 1000);
  });
}
// lockout 
if (lockoutBox && lockoutCountdown) {
  const loginSection = document.getElementById("log-container");
  if (loginSection) loginSection.scrollIntoView({ behavior: "smooth" });

  console.log("Lockout box found, starting timer");
  lockoutBox.classList.add("active");

  let remaining = parseInt(lockoutBox.dataset.remaining, 10);
  console.log("Initial remaining:", remaining);

  function formatTime(totalSeconds) {
    const mins = Math.floor(totalSeconds / 60);
    const secs = totalSeconds % 60;
    return `${mins}:${secs.toString().padStart(2, "0")}`;
  }

  const lockoutTimer = setInterval(() => {
    remaining--;
    lockoutCountdown.textContent = formatTime(remaining);

    if (remaining <= 0) {
      console.log("Countdown reached zero, clearing timer");
      clearInterval(lockoutTimer);
      lockoutBox.innerHTML = "<p>✅ You can try logging in again now.</p>";

      setTimeout(() => {
        console.log("Cleanup timeout firing now"); 
        lockoutBox.classList.remove("active");

        const url = new URL(window.location);
        url.searchParams.delete("locked");
        url.searchParams.delete("email");
        console.log("New URL after cleanup:", url.toString());
        window.history.replaceState({}, document.title, url.toString());
        console.log("Actual address bar now:", window.location.href);
      }, 2000);
    }
  }, 1000);
}

// theme toggle
function applyTheme(mode) {
  document.body.classList.toggle("light-mode", mode === "light");
  if (themeIcon)
    themeIcon.className = mode === "light" ? "bx bx-moon" : "bx bx-sun";
}

applyTheme(localStorage.getItem("kapstong-theme") || "dark");

themeToggle?.addEventListener("click", () => {
  const next = document.body.classList.contains("light-mode")
    ? "dark"
    : "light";
  applyTheme(next);
  localStorage.setItem("kapstong-theme", next);
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

if (modalSignSuccess) {
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
if (form) {
  form.addEventListener("submit", function () {
    loginLoadingScreen.classList.add("show");
  });
}

if (forgotPasswordLink) {
  forgotPasswordLink.addEventListener("click", (e) => {
    e.preventDefault();

    forgotLoadingScreen.classList.add("show");

    setTimeout(() => {
      window.location.href = "../Password/forgotPassword.php";
    }, 1000);
  });
}

// learn more
document.querySelector(".scroll-cue")?.addEventListener("click", () => {
  document.getElementById("log-hows").scrollIntoView({ behavior: "smooth" });
});

// faqs
document.querySelectorAll(".faq-item").forEach((btn) => {
  btn.addEventListener("click", () => {
    const question = btn.querySelector("span").textContent;
    const answer = btn.dataset.answer;

    faqFocusedQuestion.textContent = question;
    faqFocusedAnswer.textContent = answer;

    faqList.classList.add("faq-hidden");
    faqFocused.classList.add("faq-active");
  });
});

if (faqBackBtn) {
  faqBackBtn.addEventListener("click", () => {
    faqFocused.classList.remove("faq-active");
    faqList.classList.remove("faq-hidden");
  });
}

// incorrect login

if (loginError) {
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
    heroStats,
  ];

  items.forEach((el) => {
    if (el) el.classList.add("preload");
  });

  setTimeout(() => {
    heroRight?.classList.add("animate-in");
    setTimeout(() => {
      heroLeft?.classList.add("animate-up");
    }, 400);
    setTimeout(() => {
      heroTag?.classList.add("animate-in-left");
    }, 700);
    setTimeout(() => {
      heroTitle?.classList.add("animate-up");
    }, 1200);
    setTimeout(() => {
      heroDesc?.classList.add("animate-up-delay1");
    }, 1700);
    setTimeout(() => {
      heroButtons?.classList.add("animate-up-delay2");
    }, 2200);
  }, 200);
});

window.addEventListener("scroll", revealOnScroll);

window.addEventListener("DOMContentLoaded", revealOnScroll);

revealOnScroll();

<?php
include("../Shared/kapstongConnection.php");

// $invaderLogin = false;
// $wrongPassword = false;
// $successSignUp = false;
// $successForget = false;

// if (isset($_GET['error'])) {
//   $invaderLogin = true;
// }

// if (isset($_GET['warning'])) {
//   $wrongPassword = true;
// }

// if (isset($_GET['success'])) {
//   $successSignUp = true;
// }

// if (isset($_GET['success_forget'])) {
//   $successForget = true;
// }

?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta property="og:title" content="Granby OJT Monitoring System" />
  <meta property="og:description" content="Fast, reliable RFID-based attendance and OJT monitoring for administrators, coordinators, and students." />
  <meta property="og:image" content="../../public/kapstongImage/logo.jpg" />
  <meta property="og:type" content="website" />
  <title>Granby Monitoring System</title>
  <link rel="icon" type="image/png" href="../../public/kapstongImage/logo.jpg">
  <link rel="stylesheet" href="../../public/css/loginPhase.css" />
  <link
    href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css'
    rel='stylesheet'>
  <meta name="description" content="Kapstong for you and me!" />
</head>

<body>

  <header class="header">
    <div class="container">
      <h1 class="logo-text">
        <img src="../../public/kapstongImage/logo.jpg" class="logo-img" style="border-radius: 50%;">
        <div class="header-title">
          <div>Granby</div>
          <small>OJT Monitoring System </small>
        </div>
      </h1>
      <nav class="nav">
        <a href="#log-start" class="tooltip" data-tooltip="Return to the home page">Home</a>
        <a href="#log-about" class="tooltip" data-tooltip="Learn more about our system">About</a>
        <a href="#log-hows" class="tooltip" data-tooltip="Learn how the system works">How it works</a>
        <a href="#log-help" class="tooltip" data-tooltip="Get help with the system">Help</a>
        <!-- <a href="#log-container" class="tooltip" data-tooltip="Sign in to your account" id="login">Login</a> -->
         <a class="tooltip" data-tooltip="Sign in to your account" id="login">Login</a>
        <button id="themeToggle" class="theme-toggle tooltip" data-tooltip="Toggle light and dark mode" type="button" aria-label="Toggle light and dark mode">
  <i class='bx bx-sun'></i>
</button> 
      </nav>
    </div>

  </header>

  <section class="log-start" id="log-start">

    <div class="hero-overlay"></div>

    <div class="container hero-grid">

      <div class="hero-left scroll-reveal">

        <span class="hero-tag">
          RFID-Powered Attendance Monitoring
        </span>

        <h1 class="hero-title">
          Granby OJT <br>
          <span>Monitoring System</span>
        </h1>

        <p class="hero-desc">
          Fast, reliable RFID-based attendance and OJT monitoring for administrators, coordinators, and students.
        </p>

        <div class="hero-buttons">

          <button class="primary-btn" id="getStarted" >
            Get Started
          </button>

           <button class="secondary-btn" id="ls-switch2">
              Already have an account? Log in
            </button>

        </div>



      </div>

      <!-- <div class="hero-right">

        <div class="rfid-visual">

          <div class="glow-circle"></div>
          <img src="../kapstongImage/Gemini_Generated_Image_2p2ovf2p2ovf2p2o.png" alt="System Symbol" class="hero-symbol">
          <div class="floating-card card1"></div>
        <div class="floating-card card2"></div>

        </div>

      </div> -->

      <div class="hero-right">

        <div class="rfid-visual">
          <div class="platform-shadow"></div>
          <div class="glow-circle"></div>

          <div class="rfid-card">

            <div class="card-noise"></div>

            <div class="card-top">

              <div class="brand">

                <div class="logo">
                  <img src="../../public/kapstongImage/logo.jpg" class="logo-img" style="border-radius: 50%;">
                </div>


              </div>

              <div class="card-middle">

                <div class="student-id">
                  <small>Student Access</small>

                  <h4>Peter Parker</h4>
                  <h3>GRB-XXXX-XXXX</h3>
                </div>

              </div>

              <div class="rfid-icon">
                <span></span>
                <span></span>
                <span></span>
              </div>

            </div>



            <div class="card-bottom">

              <div class="blue-strip"></div>

              <span>ACCESS • CONNECT • SECURE</span>

            </div>

          </div>

        </div>


      </div>

    </div>

    <div class="scroll-cue">
  <span>Learn more</span>
  <i class='bx bx-chevron-down'></i>
</div>

  </section>

  <hr />

  <section class="log-about" id="log-about">
  <div class="about-overlay"></div>
  <div class="about-container container">

    <div class="about-grid">
      <div class="about-copy scroll-reveal">
        <span class="about-tag">RFID-Powered Monitoring System</span>
        <h1>OJT Monitoring <br><span>&amp; Attendance System</span></h1>

        <p class="about-description">
          The OJT Tracking System is a modern attendance and monitoring platform
          designed to simplify the management of On-the-Job Training students through
          RFID technology. The system enables fast, accurate, and contactless attendance
          recording by allowing students to scan their RFID cards upon arrival and departure.
        </p>

        <p class="about-description">
          This system helps coordinators and administrators efficiently monitor student
          attendance, track training records, and reduce manual errors commonly found in
          traditional attendance methods.
        </p>

        <div class="about-stats">
          <div class="about-stat"><strong>3</strong><span>User roles</span></div>
          <div class="about-stat"><strong>RFID</strong><span>Attendance method</span></div>
          <div class="about-stat"><strong>Live</strong><span>Record updates</span></div>
        </div>
      </div>

      <div class="about-features">
        <div class="feature-card">
          <i class="bx bx-scan"></i>
          <div><h3>RFID Attendance</h3><p>Fast and automated attendance logging using RFID technology.</p></div>
        </div>
        <div class="feature-card">
          <i class="bx bx-time-five"></i>
          <div><h3>Real-Time Monitoring</h3><p>Track student attendance records and time logs instantly.</p></div>
        </div>
        <div class="feature-card">
          <i class="bx bx-data"></i>
          <div><h3>Centralized Records</h3><p>Securely manage attendance data and OJT information in one system.</p></div>
        </div>
      </div>
    </div>

  </div>
</section>

  <hr />

  <!-- <section class="log-container" id="log-container">
    <div class="login-overlay"></div>
    <div
      class="container scroll-reveal"
      id="sign-container"
      style="text-align: left; margin: 0 5%">

      <h1>New here?<br>Join <span>Granby</span><br>OJT System.</h1>

      <p>Create your student account to start tracking your OJT hours, attendance, and performance — all in one place.</p>

      <ul class="sign-feature-list">
        <li class="sign-feature-item">
          <span class="sign-feature-icon"><i class='bx bx-wifi'></i></span>
          RFID tap-to-log attendance
        </li>
        <li class="sign-feature-item">
          <span class="sign-feature-icon"><i class='bx bx-time-five'></i></span>
          Automatic time-in &amp; time-out
        </li>
        <li class="sign-feature-item">
          <span class="sign-feature-icon"><i class='bx bx-bar-chart-alt-2'></i></span>
          Live performance tracking
        </li>
      </ul>

      <div class="sign-divider"></div>

      <div class="sign-cta-row">
        <button id="ls-switch" class="ls-switch tooltip" data-tooltip="Create a new account">Create account</button>
        <p class="sign-cta-note">
          OJT students only.<br>
          <span>Free</span> &mdash; takes 2 minutes.
        </p>
      </div>

    </div>

    <form id="loginForm" action="starts.php" method="POST">
      <div class="login-container scroll-reveal">

        <div class="login-header">
          <h2>Welcome back</h2>
          <p>OJT Monitoring System Access Portal</p>
        </div>

        <div class="login-inner-container">

          <div class="login-box">
            <input type="email" name="loginEmail" id="loginEmail" placeholder=" " required />
            <span>Email address</span>
          </div>

          <div class="login-box">
            <input type="password" name="loginPassword" id="loginPassword" placeholder=" " required />
            <span>Password</span>
          </div>

        </div>

        <div class="login-forget">
          <a href="#" id="forgotPasswordLink">Forgot Password?</a>
        </div>

        <div class="login-button">
          <input
            type="submit"
            value="Sign In"
            class="login-submit"
            name="login-submit" />
        </div>

      </div>
    </form>
  </section> -->

  <hr />

  <!-- hows -->
  <section class="log-hows" id="log-hows">
    <div class="hows-overlay"></div>
    <div class="container scroll-reveal">


      <span class="section-tag">System Workflow</span>

      <h1>How the OJT Monitoring System <br> <span>Works</span></h1>

      <p class="how-subtitle">
      The system streamlines student attendance and OJT monitoring through
      RFID technology, providing a faster and more reliable workflow for
      administrators, coordinators, and students.
    </p>

    <div class="how-container">

      <div class="how-individual">
        <span class="step-number">01</span>
        <div class="how-icon"><i class='bx bx-id-card'></i></div>
        <h3>RFID Student Registration</h3>
        <p class="how-text">
          Students are registered into the system with their assigned RFID cards
          and verified OJT information for secure attendance monitoring.
        </p>
      </div>

      <div class="how-individual">
        <span class="step-number">02</span>
        <div class="how-icon"><i class='bx bx-wifi'></i></div>
        <h3>Real-Time Attendance Tracking</h3>
        <p class="how-text">
          Students scan their RFID cards to automatically record attendance,
          time-in, and time-out activities in real time.
        </p>
      </div>

      <div class="how-individual">
        <span class="step-number">03</span>
        <div class="how-icon"><i class='bx bx-bar-chart-alt-2'></i></div>
        <h3>Monitoring &amp; Reports</h3>
        <p class="how-text">
          Coordinators and administrators can monitor student records,
          evaluate attendance, and generate performance reports efficiently.
        </p>
      </div>

    </div>
    </div>
  </section>

  <hr />

  

<!-- help -->
 <section class="log-help" id="log-help">
  <div class="container scroll-reveal">
    <span class="section-tag">Support</span>
    <h1>Need Help? <br><span>We're Here for You</span></h1>
    <p class="help-intro">
      Having trouble with your account or OJT records? Here's how to get support.
    </p>

    <div class="help-grid">
      <div class="help-card">
        <i class='bx bx-envelope'></i>
        <h3>Email Support</h3>
        <p>Email <a href="mailto:granbyojtmonitoring@gmail.com">granbyojtmonitoring@gmail.com</a> for account issues, RFID registration problems, or record disputes. We typically respond within 1–2 business days.</p>
      </div>
      <div class="help-card">
        <i class='bx bx-phone'></i>
        <h3>Call the OJT Office</h3>
        <p>Reach the Granby OJT Coordination Office at <a href="tel:+639927080633">0992-708-0633</a>, Monday–Friday, 8:00 AM–5:00 PM.</p>
      </div>
      <div class="help-card">
        <i class='bx bx-map-pin'></i>
        <h3>Visit in Person</h3>
        <p>Room 204, Granby Colleges of Science and Technology Main Building, for urgent concerns during office hours.</p>
      </div>
    </div>

    <div class="faq-section">
  <span class="section-tag">Frequently Asked Questions</span>

  <div class="faq-focused" id="faqFocused">
    <button class="faq-back" id="faqBackBtn">
      <i class='bx bx-arrow-back'></i> Back to all questions
    </button>
    <h3 class="faq-focused-question" id="faqFocusedQuestion"></h3>
    <p class="faq-focused-answer" id="faqFocusedAnswer"></p>
  </div>

  <div class="faq-list" id="faqList">
    <button class="faq-item" data-answer="Once your account is verified by an administrator, visit the OJT office with your RFID card to have it linked to your account. You'll receive a confirmation once registration is complete.">
      <span>How do I register my RFID card?</span>
      <i class='bx bx-chevron-right'></i>
    </button>

    <button class="faq-item" data-answer="Try tapping the card closer to the scanner and hold for 2–3 seconds. If it still doesn't register, log your time-in manually through your dashboard and report the issue to the OJT office as soon as possible.">
      <span>My RFID card isn't scanning. What do I do?</span>
      <i class='bx bx-chevron-right'></i>
    </button>

    <button class="faq-item" data-answer="Click 'Forgot Password?' on the login page and follow the instructions sent to your registered email address.">
      <span>I forgot my password. How do I reset it?</span>
      <i class='bx bx-chevron-right'></i>
    </button>

    <button class="faq-item" data-answer="Contact your assigned supervisor or the OJT coordinator with the date in question. Attendance disputes are reviewed and corrected within 3 business days.">
      <span>I was marked absent, but I was actually present. What now?</span>
      <i class='bx bx-chevron-right'></i>
    </button>

    <button class="faq-item" data-answer="Your final score combines attendance, task completion, and supervisor performance ratings, weighted according to your program's evaluation criteria. Ask your OJT coordinator for the exact breakdown used for your course.">
      <span>How is my final evaluation score calculated?</span>
      <i class='bx bx-chevron-right'></i>
    </button>
  </div>
</div>
    </div>

  </div>
</section>

<hr>

<!-- footer -->
 <footer class="landing-footer">
  <div class="container footer-grid">
    <div class="footer-brand">
      <img src="../../public/kapstongImage/logo.jpg" class="footer-logo" alt="Kapstong logo">
      <p>Granby OJT Monitoring System</p>
    </div>

    <div class="footer-links">
      <a href="#log-start">Home</a>
      <a href="#log-about">About</a>
      <a href="#log-help">Help</a>
    </div>

    <p class="footer-copyright">© 2026 Granby Colleges of Science and Technology. All rights reserved.</p>
  </div>
</footer>


  <div id="loadingScreen" class="loading-screen">
    <div class="logo-loader">
      <img src="../../public/kapstongImage/logo.jpg" class="logo-img-loading">
    </div>
    <p>Signing up..</p>
    <div class="loading-dots">
      <span></span><span></span><span></span>
    </div>
  </div>

  <div id="loginLoadingnScreen" class="loading-screen">
    <div class="logo-loader">
      <img src="../../public/kapstongImage/logo.jpg" class="logo-img-loading">
    </div>
    <p>Logging in..</p>
    <div class="loading-dots">
      <span></span><span></span><span></span>
    </div>
  </div>

  <div id="loginSetLoadingScreen" class="loading-screen">
    <div class="logo-loader">
      <img src="../../public/kapstongImage/logo.jpg" class="logo-img-loading">
    </div>
    <p>Setting up your account..</p>
    <div class="loading-dots">
      <span></span><span></span><span></span>
    </div>
  </div>

   <div id="signupSetloadingScreen" class="loading-screen">
    <div class="logo-loader">
      <img src="../../public/kapstongImage/logo.jpg" class="logo-img-loading">
    </div>
    <p>Signing up..</p>
    <div class="loading-dots">
      <span></span><span></span><span></span>
    </div>
  </div>

  <div id="forgotLoadingScreen" class="loading-screen">
    <div class="logo-loader">
      <img src="../../public/kapstongImage/logo.jpg" class="logo-img-loading">
    </div>
    <p>Redirecting..</p>
    <div class="loading-dots">
      <span></span><span></span><span></span>
    </div>
  </div>

</body>

<script src="../../public/js/loginPhase.js"></script>

</html>
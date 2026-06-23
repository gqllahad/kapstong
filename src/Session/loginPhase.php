<?php
include("../Shared/kapstongConnection.php");

$invaderLogin = false;
$wrongPassword = false;
$successSignUp = false;
$successForget = false;

if (isset($_GET['error'])) {
  $invaderLogin = true;
}

if (isset($_GET['warning'])) {
  $wrongPassword = true;
}

if (isset($_GET['success'])) {
  $successSignUp = true;
}

if (isset($_GET['success_forget'])) {
  $successForget = true;
}

?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Granby Monitoring System</title>
  <link rel="icon" type="image/png" href="../../public/kapstongImage/logo.jpg">
  <link rel="stylesheet" href="../../public/css/loginPhase.css" />
  <link
    href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css'
    rel='stylesheet'>
  <meta name="description" content="Kapstong for you and me!" />
</head>

<body>

  <?php if ($invaderLogin): ?>
    <div class="modal-backdrop" id="accessDenied">
      <div class="modal-box">
        <h2>Access Denied</h2>
        <p>Please make sure you login first.</p>
        <button id="invaderLogin">Go to login</button>
      </div>
    </div>
  <?php endif; ?>

  <!-- <?php if ($wrongPassword): ?>
    <div class="modal-backdrop" id="errorLogin">
      <div class="modal-box">
        <h2>Error</h2>
        <p>Incorrect email or password.</p>
      </div>
    </div>
  <?php endif; ?> -->


  <header class="header">
    <div class="container">
      <h1 class="logo-text">
        <img src="../kapstongImage/logo.jpg" class="logo-img" style="border-radius: 50%;">
        <div class="header-title">
          <div>Granby</div>
          <small>OJT Monitoring System </small>
        </div>
      </h1>
      <nav class="nav">
        <a href="#log-start">Home</a>
        <a href="#log-hows">Help</a>
        <a href="#log-about">About</a>
        <a href="#log-container" id="login">Login</a>
      </nav>
    </div>

    <?php if ($wrongPassword): ?>
      <div class="incorrectLogin-box" id="incorrectLogin">
        <p>⚠ Incorrect Email or Password!</p>
      </div>

    <?php endif; ?>

    <?php if ($successSignUp): ?>
      <div class="signSuccess-box" id="signSuccess">
        <p>✅ Account Created Successfully!</p>
      </div>

    <?php endif; ?>

    <?php if ($successForget): ?>
      <div class="signSuccess-box" id="signSuccess">
        <p>✅ Account Password Successfully Resetted!</p>
      </div>

    <?php endif; ?>


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
          A modern web-based platform designed to simplify attendance monitoring,
          RFID tracking, and OJT management for students, coordinators, and administrators
          with real-time and secure record handling.
        </p>

        <div class="hero-buttons">

          <button class="primary-btn">
            Get Started
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
                  <img src="../kapstongImage/logo.jpg" class="logo-img" style="border-radius: 50%;">
                </div>


              </div>

              <div class="card-middle">

                <div class="student-id">
                  <small>Student Access</small>

                  <h4>Peter Parker</h4>
                  <h3>GRB-2026-0001</h3>
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

  </section>

  <hr />

  <section class="log-hows" id="log-hows">
    <div class="hows-overlay"></div>
    <div class="container scroll-reveal">


      <span class="section-tag">System Workflow</span>

      <h1>How the OJT Monitoring System <br> Works</h1>

      <p class="how-subtitle">
        The system streamlines student attendance and OJT monitoring through
        RFID technology, providing a faster and more reliable workflow for
        administrators, coordinators, and students.
      </p>

      <div class="how-container">

        <div class="how-individual">

          <div class="step-number">01</div>

          <div class="how-image register"></div>

          <h3>RFID Student Registration</h3>

          <p class="how-text">
            Students are registered into the system with their assigned RFID cards
            and verified OJT information for secure attendance monitoring.
          </p>

        </div>

        <div class="how-individual">

          <div class="step-number">02</div>

          <div class="how-image progress"></div>

          <h3>Real-Time Attendance Tracking</h3>

          <p class="how-text">
            Students scan their RFID cards to automatically record attendance,
            time-in, and time-out activities in real time.
          </p>

        </div>

        <div class="how-individual">

          <div class="step-number">03</div>

          <div class="how-image reports"></div>

          <h3>Monitoring & Reports</h3>

          <p class="how-text">
            Coordinators and administrators can monitor student records,
            evaluate attendance, and generate performance reports efficiently.
          </p>

        </div>

      </div>
    </div>
  </section>

  <hr />

  <section class="log-container" id="log-container">

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
        <button id="ls-switch" class="ls-switch">Create account</button>
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
  </section>

  <hr />

  <section class="log-about" id="log-about">
    <div class="about-overlay"></div>
    <div class="about-container container">


      <span class="about-tag">RFID-Powered Monitoring System</span>

      <h1>OJT Monitoring <br>& Attendance System</h1>

      <p class="about-description">
        The OJT Tracking System is a modern attendance and monitoring platform
        designed to simplify the management of On-the-Job Training students through
        RFID technology. The system enables fast, accurate, and contactless attendance
        recording by allowing students to scan their RFID cards upon arrival and departure.
      </p>

      <p class="about-description">
        This system helps coordinators and administrators efficiently monitor student
        attendance, track training records, and reduce manual errors commonly found in
        traditional attendance methods. With a centralized database and real-time tracking,
        the platform improves reliability, security, and overall management of OJT activities.
      </p>

      <div class="about-features">

        <div class="feature-card">
          <i class="bx bx-scan"></i>
          <h3>RFID Attendance</h3>
          <p>
            Fast and automated attendance logging using RFID technology.
          </p>
        </div>

        <div class="feature-card">
          <i class="bx bx-time-five"></i>
          <h3>Real-Time Monitoring</h3>
          <p>
            Track student attendance records and time logs instantly.
          </p>
        </div>

        <div class="feature-card">
          <i class="bx bx-data"></i>
          <h3>Centralized Records</h3>
          <p>
            Securely manage attendance data and OJT information in one system.
          </p>
        </div>

      </div>

    </div>
  </section>


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
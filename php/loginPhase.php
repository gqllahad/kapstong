<?php
include("kapstongConnection.php");

$invaderLogin = false;
$wrongPassword = false;
$successSignUp = false;

if (isset($_GET['error'])) {
  $invaderLogin = true;
}

if (isset($_GET['warning'])) {
  $wrongPassword = true;
}

if (isset($_GET['success'])) {
  $successSignUp = true;
}

?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Kapstong</title>
  <link rel="stylesheet" href="../css/loginPhase.css" />
  <link
  href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css'
  rel='stylesheet'
>
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
        <img src="../kapstongImage/da3309ad-0163-4fd3-bc04-d26b468c5224.jpg" class="logo-img">
        <div>
          <div>OJT</div>
          <small>Tracking System Granby</small>
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
        <p>Account Created!</p>
      </div>

    <?php endif; ?>


  </header>

  <section class="log-start" id="log-start">

  <div class="hero-overlay"></div>

  <div class="container scroll-reveal hero-content">

    <span class="hero-tag">
      RFID-Powered Attendance Monitoring
    </span>

    <h1 class="typing-text">
    </h1>

    <p class="hero-desc active">
      A modern web-based platform designed to simplify attendance monitoring,
      RFID tracking, and OJT management for students, coordinators, and administrators
      with real-time and secure record handling.
    </p>

    <div class="hero-buttons">

      <button class="primary-btn">
       <a href="rfid_test.php">Get Started</a> 
      </button>

      <button class="secondary-btn">
        Learn More
      </button>

    </div>

    <div class="hero-stats">

      <div class="stat-card">
        <h3>RFID</h3>
        <p>Smart Attendance</p>
      </div>

      <div class="stat-card">
        <h3>Real-Time</h3>
        <p>Monitoring System</p>
      </div>

      <div class="stat-card">
        <h3>Secure</h3>
        <p>Data Management</p>
      </div>

    </div>

  </div>

</section>

  <hr />

  <section class="log-hows" id="log-hows">
  <div class="container scroll-reveal">

    <span class="section-tag">System Workflow</span>

    <h2>How the OJT Tracking System Works</h2>

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

      <span class="hero-tag">
         Student & Administrator Login
      </span>
      
      <h1>Don't have an account?</h1>
      <p>If your an OJT student, Signup now!</p>
      <button id="ls-switch" class="ls-switch">Sign Up</button>
    </div>

    <form action="starts.php" method="POST">
      <div class="login-container scroll-reveal">
        <img src="../kapstongImage/da3309ad-0163-4fd3-bc04-d26b468c5224.jpg" class="logo-img" style="border-radius: 50%;">
        <h2>Sign In</h2>
        <div class="login-inner-container">
          <div class="login-box">
            <input type="email" name="loginEmail" id="loginEmail" placeholder=" " required />
            <span>Email</span>
          </div>

          <div class="login-box">
            <input type="password" name="loginPassword" id="loginPassword" placeholder=" " required />
            <span>Password</span>
          </div>
        </div>

        <div class="login-forget">Forgot Password?</div>

        <div class="login-button">
          <input
            type="submit"
            value="Login"
            class="login-submit"
            name="login-submit" />
        </div>


      </div>
    </form>
  </section>

  <hr />

  <section class="log-about" id="log-about">
  <div class="about-container container">

    <span class="about-tag">RFID-Powered Monitoring System</span>

    <h2>OJT Tracking and Attendance System</h2>

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
</body>

<script src="../js/loginPhase.js"></script>

</html>
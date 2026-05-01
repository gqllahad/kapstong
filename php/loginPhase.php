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
    <div class="container scroll-reveal">
      <h2 class="typing-text"></h2>
      <p class="hero-desc active">
        A web-based system designed to track, monitor, and manage OJT (On-the-Job Training) records efficiently for students and administrators.
      </p>
      <button id="btn">Get Started</button>
    </div>
  </section>

  <hr />

  <section class="log-hows" id="log-hows">
    <div class="container scroll-reveal">
      <h2>How the OJT Tracking System Works</h2>
      <p style="padding : 20px 10px;">
        A simple 3-step process for managing student OJT records efficiently.
      </p>


      <div class="how-container">
        <div class="how-individual">
          <div class="how-image register"></div>
          <h3>Student Registration</h3>
          <p class="how-text">Students create an account using verified school credentials.</p>
        </div>

        <div class="how-individual">
          <div class="how-image progress"></div>
          <h3>Track OJT Progress</h3>
          <p class="how-text">Monitor attendance, hours, and daily OJT activities in real time.</p>
        </div>

        <div class="how-individual">
          <div class="how-image reports "></div>
          <h3>Generate Reports</h3>
          <p class="how-text">Administrators can evaluate and export student performance reports.</p>
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
      <h2>Developers</h2>
      <p>
        Lorem ipsum dolor sit, amet consectetur adipisicing elit. Culpa illum
        consequatur non quibusdam ipsa itaque inventore molestias, aspernatur
        quod sequi dolorem sed consectetur fugiat consequuntur natus cum
        sapiente ea aut.
      </p>
      <button id="btn">haha</button>
    </div>
  </section>
</body>

<script src="../js/loginPhase.js"></script>

</html>
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

<body style="overflow:hidden;margin:0;padding:0;"> 
<div class="header-login-container">
  <a href="loginPhase.php" class="back-home-logo tooltip tooltip-right" data-tooltip="Back to login">
    <img src="../../public/kapstongImage/logo.jpg" alt="Kapstong logo">
    <div class="header-title">
      <span class="header-brand">Granby</span>
      <small class="header-subtitle">OJT Monitoring System</small>
    </div>
  </a>
</div>

<?php if ($invaderLogin): ?>
    <div class="modal-backdrop" id="accessDenied">
      <div class="modal-box">
        <h2>Access Denied</h2>
        <p>Please make sure you login first.</p>
        <button id="invaderLogin">Go to login</button>
      </div>
    </div>
  <?php endif; ?>



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


    <section class="log-container" id="log-container">
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
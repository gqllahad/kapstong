<?php
include("kapstongConnection.php");
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
  <header class="header">
    <div class="container">
      <h1 class="logo-text">
        OJT <br />
        Tracking System Granby
      </h1>
      <nav class="nav">
        <a href="#log-start">Home</a>
        <a href="#log-hows">Help</a>
        <a href="#log-about">About</a>
        <a href="#log-container" id="login">Login</a>
      </nav>
    </div>
  </header>

  <section class="log-start" id="log-start">
    <div class="container scroll-reveal">
      <h2>OJT Tracking Granby</h2>
      <p>
        Lorem ipsum dolor sit, amet consectetur adipisicing elit. Culpa illum
        consequatur non quibusdam ipsa itaque inventore molestias, aspernatur
        quod sequi dolorem sed consectetur fugiat consequuntur natus cum
        sapiente ea aut.
      </p>
      <button id="btn">Click Me</button>
    </div>
  </section>

  <hr />

  <section class="log-hows" id="log-hows">
    <div class="container scroll-reveal">
      <h2>
        How the OJT Tracker <br />
        System works
      </h2>
      <p>Seamless communication designed for easy community interaction.</p>

      <div class="how-container">
        <div class="how-individual">
          <div class="how-image"></div>
          <h3>Create your Profile</h3>
          <p class="how-text">Register with verified local credentials.</p>
        </div>

        <div class="how-individual">
          <div class="how-image"></div>
          <h3>Select notification preference</h3>
          <p class="how-text">
            Choose the type of notifications you want to receive.
          </p>
        </div>

        <div class="how-individual">
          <div class="how-image"></div>
          <h3>Start receiving updates</h3>
          <p class="how-text">
            Get notifications about your local community.
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
      <h1>Don't have an account?</h1>
      <p>This site is safe, if you're a citizen in Barangay Granby</p>
      <button id="ls-switch" class="ls-switch">Sign Up</button>
    </div>

    <form action="starts.php" method="POST">
      <div class="login-container scroll-reveal">
        <h2 style="padding: 3% 3%;">Sign In</h2>
        <div class="login-inner-container">
          <div class="login-box">
            <input type="text" name="loginEmail" required />
            <span>Email</span>
          </div>

          <div class="login-box">
            <input type="password" name="loginPassword" required />
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
    <div class="abount-container">
      <h2>Developers</h2>
      <p>
        Lorem ipsum dolor sit, amet consectetur adipisicing elit. Culpa illum
        consequatur non quibusdam ipsa itaque inventore molestias, aspernatur
        quod sequi dolorem sed consectetur fugiat consequuntur natus cum
        sapiente ea aut.
      </p>
      <button id="btn">CHAHAHA</button>
    </div>
  </section>
</body>

<script src="../js/loginPhase.js"></script>

</html>
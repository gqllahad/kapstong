<?php

require_once("functions.php");



?>

<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Signup Tracking System</title>

    <link rel="stylesheet" href="../css/signupPhase.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>


<body>

    <!-- layout -->
    <div class="layout">


        <main class="content">

            <section class="sign-logo"> <a href="loginPhase.php">
                    <img src="../images/download.jpg" alt="logo">
                </a> </section>

            <section class="upper-text"> SIGN UP</section>

            <div class="stepper">

                <div class="progress-line">
                    <div class="progress-fill"></div>
                </div>

                <div class="step active">1</div>
                <div class="step">2</div>
                <div class="step">3</div>
                <div class="step">4</div>
            </div>

            <form id="signupForm" action="starts.php" method="POST">
                <div class="form-step active">
                    <h3>Step 1: Student Information</h3>
                    <hr>
                    <div class="form-inputs">

                        <div class="sign-box trios">
                            <input name="lastName" type="text" required />
                            <span>Last name</span>
                        </div>
                        <div class="sign-box trios"> <input name="firstName" type="text" required />
                            <span>First name</span>
                        </div>

                        <div class="sign-box trios"> <input name="middleName" type="text" required />
                            <span>Middle name</span>
                        </div>

                        <div class="sign-box trios">
                            <input name="signEmail" type="email" class="full" required />
                            <span>Email</span>
                        </div>
                        <div class="sign-box trios birthdate">
                            <input name="signBirth" type="date" required />
                            <span> Birth date </span>
                        </div>

                        <div class="sign-box trios">
                            <input name="signTel" type="tel" class="duos" required />
                            <span> Moile Number </span>
                        </div>


                        <div class="gender-group full">
                            <label class="section-label">Gender:</label>
                            <div class="gender-options">
                                <label class="gender-option">
                                    <input type="radio" name="gender" value="male" required>
                                    <span>Male</span>
                                </label>
                                <label class="gender-option">
                                    <input type="radio" name="gender" value="female">
                                    <span>Female</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <button class="next-button" type="button" id="next1">Next</button>
                </div>

                <div class="form-step">
                    <h3>Step 2: Academic Information</h3>
                    <hr>
                    <div class="form-inputs">
                        <div class="sign-box duos studentID"> <input name="studentID" id="studentID1" type="text" required />
                            <span>Student ID </span>
                            <p id="studentID-warning" style="color:red; display:none; font-weight : 600; font-size: 0.6rem;">Student ID already taken!</p>
                        </div>

                        <div class="sign-box duos"> <input name="signCourse" type="text" required />
                            <span>Course / Program</span>
                        </div>

                        <div class="sign-box duos"> <input name="signLevel" type="text" required />
                            <span>Year Level</span>
                        </div>

                        <div class="sign-box duos"> <input name="signSY" type="text" required />
                            <span>School Year</span>
                        </div>

                    </div>
                    <hr>

                    <button class="back-button" type="button" id="prev1">Back</button>
                    <button class="next-button" type="button" id="next2">Next</button>

                </div>

                <div class="form-step">
                    <h3>Step 3: Account Info</h3>
                    <hr>
                    <div class="form-inputs">
                        <div class="sign-box full username"> <input id="studentID2" type="text" required readonly />
                            <span>Student ID </span>
                        </div>

                        <div class="sign-box duos"> <input name="signPassword" id="passwordInput" type="password" required />
                            <span>Password</span>
                        </div>

                        <div class="sign-box duos"> <input name="signConfirmPassword" id="confirmPasswordInput" type="password" required />
                            <span> Confirm Password</span>
                        </div>

                    </div>
                    <hr>

                    <button class="back-button" type="button" id="prev1">Back</button>
                    <button class="next-button" type="button" id="next2">Next</button>

                </div>

                <div class="form-step">
                    <h3>Step 4: Finish</h3>
                    <p>Confirm your information and submit!</p>
                    <button name="signupForm" class="next-button" type="submit">Submit</button>
                    <button class="back-button" type="button" id="prev2">Back</button>
                </div>
            </form>


        </main>
    </div>

</body>
<script src="../js/signupPhase.js"></script>

</html>
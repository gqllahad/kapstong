<?php

require_once("functions.php");

?>

<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Signup Tracking System</title>

    <link rel="stylesheet" href="../css/signupPhase.css?v=123"> <!-- ?v=123 (for mobile not working) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>


<body>
    <div id="toast" class="toast"></div>

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

                        <div class="sign-box trios lastName">
                            <input name="lastName" class="name-input" type="text" placeholder=" " required />
                            <span>Last name</span>
                            <p class="name-warning">Only letters are allowed.</p>
                        </div>
                        <div class="sign-box trios firstName"> <input name="firstName" class="name-input" type="text" placeholder=" " required />
                            <span>First name</span>
                            <p class="name-warning">Only letters are allowed.</p>
                        </div>

                        <div class="sign-box trios middleName"> <input name="middleName" class="name-input" type="text" placeholder=" " required />
                            <span>Middle name</span>
                            <p class="name-warning">Only letters are allowed.</p>
                        </div>

                        <div class="sign-box duos studentEmail">
                            <input name="signEmail" type="email" class="full" id="studentEmail" placeholder=" " required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" />
                            <span>Email</span>
                            <p id="studentEmail-warning" style="color:red; display:none; font-weight : 600; font-size: 0.5rem;">Email already been used!</p>
                        </div>

                        <input type="hidden" name="rawTel" id="rawTel">

                        <div class="sign-box duos studentMobile">
                            <input name="signTel" type="tel" class="duos" maxlength="14" required placeholder=" " />
                            <span> Mobile Number </span>
                        </div>

                        <div class="sign-box duos birthdate">
                            <input name="signBirth" type="date" max="2008-03-26" required />
                            <span> Birth date </span>
                            <p class="birth-warning">You must be at least 15 years old.</p>
                        </div>

                        <div class="sign-box duos">

                            <select name="gender" required>
                                <option value="" disabled selected hidden>Select you gender...</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Others">Others</option>
                            </select>
                            <span>Gender</span>
                        </div>

                        <input type="hidden" name="province" value="Cavite">

                        <div class="sign-box trios">
                            <select name="city" required>
                                <option value="" disabled selected hidden>Select City...</option>
                                <option value="Tanza">Tanza</option>
                                <option value="Trece Martires">Trece Martires</option>
                                <option value="Dasmariñas">Dasmariñas</option>
                                <option value="Bacoor">Bacoor</option>
                                <option value="Imus">Imus</option>
                                <option value="General Trias">General Trias</option>
                                <option value="Silang">Silang</option>
                                <option value="Kawit">Kawit</option>
                                <option value="Rosario">Rosario</option>
                                <option value="Noveleta">Noveleta</option>
                                <option value="Naic">Naic</option>
                                <option value="Ternate">Ternate</option>
                                <option value="Maragondon">Maragondon</option>
                                <option value="Indang">Indang</option>
                                <option value="Alfonso">Alfonso</option>
                                <option value="Amadeo">Amadeo</option>
                                <option value="Tagaytay">Tagaytay</option>
                                <option value="Mendez">Mendez</option>
                                <option value="General Emilio Aguinaldo">General Emilio Aguinaldo</option>
                                <option value="Magallanes">Magallanes</option>
                            </select>
                            <span>City / Municipality</span>
                        </div>

                        <div class="sign-box trios">
                            <input name="barangay" type="text" list="barangays" placeholder="" required />
                            <span>Barangay</span>

                            <datalist id="barangays">
                                <option value="Sahud-ulan"></option>
                                <option value="Capipisa"></option>
                                <option value="Biga"></option>
                                <option value="Julugan"></option>
                                <option value="Calibuyo"></option>
                                <option value="Halayhay"></option>
                                <option value="Rosario"></option>
                            </datalist>
                        </div>

                        <div class="sign-box trios">
                            <input name="street" type="text" placeholder="" required />
                            <span>Street / House No.</span>
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

                        <div class="sign-box duos">
                            <select name="signCourse" id="signCourse" required>
                                <option value="" disabled selected hidden></option>
                            </select>
                            <span>Program</span>
                        </div>

                        <div class="sign-box trios">
                            <select name="signLevel" required>
                                <option value="" disabled selected hidden></option>
                                <option value="1st year">1st Year</option>
                                <option value="2nd year">2nd Year</option>
                                <option value="3rd year">3rd Year</option>
                                <option value="4th year">4th Year</option>
                            </select>
                            <span>Year Level</span>
                        </div>

                        <div class="sign-box trios"> <input name="signSY" type="text" id="academic-year" readonly />
                            <span>Academic Year</span>
                        </div>

                        <div class="sign-box trios">
                            <select name="signSemester" required>
                                <option value="" disabled selected hidden></option>
                                <option value="1st semester">1st Semester</option>
                                <option value="2nd semester">2nd Semester</option>
                            </select>
                            <span>Semester</span>
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
                        <div class="sign-box duos username"> <input id="studentID2" type="text" required readonly />
                            <span>Student ID </span>
                        </div>

                        <div class="sign-box duos email"> <input id="studentEmail2" type="email" required readonly />
                            <span>Email </span>
                        </div>

                        <div class="sign-box duos"> <input name="signPassword" id="passwordInput" placeholder=" " type="password" required />

                            <i class="toggle-pass" onclick="togglePassword('passwordInput', this)">👁</i>
                            <span>Password</span>
                        </div>

                        <div class="sign-box duos"> <input name="signConfirmPassword" id="confirmPasswordInput" placeholder=" " type="password" required />
                            <span> Confirm Password</span>
                            <i class="toggle-pass" onclick="togglePassword('confirmPasswordInput', this)">👁</i>
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
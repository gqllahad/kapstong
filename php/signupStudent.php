<?php

require_once("functions.php");

?>

<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Signup Tracking System</title>
    <link rel="icon" type="image/png" href="../kapstongImage/logo.jpg">
    <link rel="stylesheet" href="../css/signupPhase.css?v=123"> <!-- ?v=123 (for mobile not working) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>


<body>

    <div class="ambient">
        <div class="ambient-orb orb1"></div>
        <div class="ambient-orb orb2"></div>
        <div class="ambient-orb orb3"></div>
    </div>

    <div class="toast" id="toast"></div>

    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner"></div>
        <p class="loading-text">Creating your account…</p>
    </div>

    <form id="signupForm" method="POST" action="starts.php">
        <div class="split">

            <!-- LEFT PANEL -->
            <div class="left-panel">
                <div class="brand">
                    <div class="brand-icon"><i class="bi bi-mortarboard"></i></div>
                    <div>
                        <div class="brand-name">KapstongOMS</div>
                        <div class="brand-sub">Student OJT Monitoring System</div>
                    </div>
                </div>

                <div class="left-hero">
                    <div class="left-tag">
                        <div class="left-tag-dot"></div>
                        Academic Year 2025–2026
                    </div>
                    <h1 class="left-heading">
                        Begin your<br>
                        internship monitoring <span class="gradient-text">here.</span>
                    </h1>
                    <p class="left-desc">
                        Manage internship records, track OJT progress, and streamline coordination between students and supervisors — all in one centralized platform.
                    </p>
                    <div class="feature-list">
                        <div class="feature-item">
                            <div class="feature-icon"><i class="bi bi-bar-chart-line"></i></div>
                            <div class="feature-text"><strong>Real-time attendance tracking</strong> — always know your status</div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon"><i class="bi bi-shield-check"></i></div>
                            <div class="feature-text"><strong>Secure student portal</strong> — encrypted and private</div>
                        </div>
                        <!-- <div class="feature-item">
                            <div class="feature-icon">📱</div>
                            <div class="feature-text"><strong>Mobile-first design</strong> — access anytime, anywhere</div>
                        </div> -->
                    </div>
                </div>

                <div class="left-footer">© 2026 KapstongOMS · Cavite · All rights reserved</div>
                <div class="grid-deco"></div>
            </div>

            <!-- RIGHT PANEL -->
            <div class="right-panel">
                <div class="form-container">

                    <div class="form-header">
                        <div class="form-eyebrow">Student Registration</div>
                        <h2 class="form-title">Create your account</h2>
                        <p class="form-subtitle">Complete all steps to register your student profile</p>
                    </div>

                    <!-- STEPPER -->
                    <div class="stepper-wrap">
                        <div class="stepper-track">
                            <div class="stepper-line-bg"></div>
                            <div class="stepper-line-fill" id="progressFill"></div>
                            <div class="step-dots">
                                <div class="step-dot active" id="dot0">1</div>
                                <div class="step-dot" id="dot1">2</div>
                                <div class="step-dot" id="dot2">3</div>
                                <div class="step-dot" id="dot3">✦</div>
                            </div>
                        </div>
                        <div class="step-labels">
                            <div class="step-label active" id="lbl0">Personal</div>
                            <div class="step-label" id="lbl1">Academic</div>
                            <div class="step-label" id="lbl2">Security</div>
                            <div class="step-label" id="lbl3">Confirm</div>
                        </div>
                    </div>

                    <!-- STEP 1: PERSONAL INFO -->
                    <div class="form-step active" id="step1">
                        <div class="glass-card">
                            <h3 class="step-heading">Personal Information</h3>
                            <p class="step-subheading">Tell us about yourself — all fields marked are required</p>
                            <div class="divider"></div>

                            <div class="field-grid col-3" style="margin-bottom:14px;">
                                <div class="field" id="f-lastName">
                                    <i class="ti ti-user field-icon" aria-hidden="true"></i>
                                    <input class="field-input name-input" id="lastName" name="lastName" type="text" placeholder=" " autocomplete="off">
                                    <label class="field-label" for="lastName">Last Name *</label>
                                </div>
                                <div class="field" id="f-firstName">
                                    <i class="ti ti-user field-icon" aria-hidden="true"></i>
                                    <input class="field-input name-input" id="firstName" name="firstName" type="text" placeholder=" " autocomplete="off">
                                    <label class="field-label" for="firstName">First Name *</label>
                                </div>
                                <div class="field">
                                    <i class="ti ti-user field-icon" aria-hidden="true"></i>
                                    <input class="field-input name-input" id="middleName" name="middleName" type="text" placeholder=" " autocomplete="off">
                                    <label class="field-label" for="middleName">Middle Name</label>
                                </div>
                            </div>

                            <div class="field-grid col-2" style="margin-bottom:14px;">
                                <div class="field" id="f-email">
                                    <i class="ti ti-mail field-icon" aria-hidden="true"></i>
                                    <input class="field-input" id="signEmail" name="signEmail" type="email" placeholder=" " autocomplete="off">
                                    <label class="field-label" for="signEmail">Email Address *</label>
                                    <p class="field-warning">Please enter a valid email address</p>
                                </div>
                                <div class="field" id="f-mobile">
                                    <i class="ti ti-device-mobile field-icon" aria-hidden="true"></i>
                                    <input class="field-input" id="signTel" name="signTel" type="tel" placeholder=" " maxlength="14" autocomplete="off">
                                    <label class="field-label" for="signTel">Mobile Number *</label>
                                    <p class="field-warning">Must start with 09 and be 11 digits</p>
                                </div>
                            </div>

                            <div class="field-grid col-2" style="margin-bottom:14px;">
                                <div class="field" id="f-birth">
                                    <i class="ti ti-calendar field-icon" aria-hidden="true"></i>
                                    <input class="field-input" id="signBirth" name="signBirth" type="date" max="2008-05-28" autocomplete="off">
                                    <label class="field-label" for="signBirth" style="top:10px;font-size:9.5px;color:var(--accent-cyan);letter-spacing:0.5px;transform:none;">Birthdate *</label>
                                    <p class="field-warning">You must be at least 15 years old</p>
                                </div>
                                <div>
                                    <label style="font-size:10px;color:var(--text-muted);letter-spacing:0.5px;text-transform:uppercase;display:block;margin-bottom:8px;padding-left:4px;">Sex *</label>
                                    <div class="segmented" id="genderSeg">
                                        <button type="button" class="seg-btn active" data-val="Male">♂ Male</button>
                                        <button type="button" class="seg-btn" data-val="Female">♀ Female</button>
                                        <button type="button" class="seg-btn" data-val="Others">⚧ Others</button>
                                    </div>
                                </div>
                            </div>

                            <div class="field-grid col-1">
                                <div class="field">
                                    <i class="ti ti-map-pin field-icon" aria-hidden="true"></i>
                                    <input class="field-input" id="signAddress" name="signAddress" type="text" placeholder=" " autocomplete="off">
                                    <label class="field-label" for="signAddress">Full Address (Barangay, City) *</label>
                                </div>
                            </div>
                        </div>

                        <div class="btn-row">
                            <button class="btn-primary" id="next1" type="button">
                                <span class="btn-inner">Next Step <i class="ti ti-arrow-right" style="font-size:16px;" aria-hidden="true"></i></span>
                            </button>
                        </div>
                        <div class="signin-row">Already have an account? <a href="loginPhase.php">Sign In</a></div>
                    </div>

                    <!-- STEP 2: ACADEMIC INFO -->
                    <div class="form-step" id="step2">
                        <div class="glass-card">
                            <h3 class="step-heading">Academic Information</h3>
                            <p class="step-subheading">Your enrollment details for Academic Year 2025–2026</p>
                            <div class="divider"></div>

                            <div class="field-grid col-2" style="margin-bottom:14px;">
                                <div class="field" id="f-studentID">
                                    <i class="ti ti-id-badge field-icon" aria-hidden="true"></i>
                                    <input class="field-input" id="studentID" name="studentID" type="text" placeholder=" " autocomplete="off">
                                    <label class="field-label" for="studentID">Student ID *</label>
                                    <p class="field-warning">Student ID already taken</p>
                                </div>
                                <div class="field" style="position:relative;">
                                    <i class="ti ti-book field-icon" aria-hidden="true"></i>
                                    <select class="field-select" id="signCourse" name="signCourse">
                                        <option value="" disabled selected hidden></option>
                                    </select>
                                    <label class="field-label" for="signCourse">Program *</label>
                                    <i class="ti ti-chevron-down select-arrow" aria-hidden="true"></i>
                                </div>
                            </div>

                            <div class="field-grid col-2">
                                <div class="field" style="position:relative;">
                                    <i class="ti ti-school field-icon" aria-hidden="true"></i>
                                    <select class="field-select" id="signLevel" name="signLevel">
                                        <option value="" disabled selected hidden></option>
                                        <option value="1st year">1st Year</option>
                                        <option value="2nd year">2nd Year</option>
                                        <option value="3rd year">3rd Year</option>
                                        <option value="4th year">4th Year</option>
                                    </select>
                                    <label class="field-label" for="signLevel">Year Level *</label>
                                    <i class="ti ti-chevron-down select-arrow" aria-hidden="true"></i>
                                </div>
                                <div class="field" style="position:relative;">
                                    <i class="ti ti-calendar-event field-icon" aria-hidden="true"></i>
                                    <select class="field-select" id="signSemester" name="signSemester">
                                        <option value="" disabled selected hidden></option>
                                        <option value="1st semester">1st Semester</option>
                                        <option value="2nd semester">2nd Semester</option>
                                    </select>
                                    <label class="field-label" for="signSemester">Semester *</label>
                                    <i class="ti ti-chevron-down select-arrow" aria-hidden="true"></i>
                                </div>
                            </div>

                            <input type="hidden" name="signSY" id="academic-year">
                            <input type="hidden" name="province" value="Cavite">
                            <input type="hidden" name="gender" id="genderHidden" value="Male">
                        </div>

                        <div class="btn-row">
                            <button class="btn-ghost" id="prev2" type="button"><i class="ti ti-arrow-left" aria-hidden="true"></i> Back</button>
                            <button class="btn-primary" id="next2" type="button">
                                <span class="btn-inner">Continue <i class="ti ti-arrow-right" style="font-size:16px;" aria-hidden="true"></i></span>
                            </button>
                        </div>
                    </div>

                    <!-- STEP 3: SECURITY -->
                    <div class="form-step" id="step3">
                        <div class="glass-card">
                            <h3 class="step-heading">Security Setup</h3>
                            <p class="step-subheading">Create a strong password to protect your account</p>
                            <div class="divider"></div>

                            <div class="field-grid col-2" style="margin-bottom:14px;">
                                <div class="field">
                                    <i class="ti ti-id-badge field-icon" aria-hidden="true"></i>
                                    <input class="field-input" id="studentID_ro" type="text" placeholder=" " readonly style="opacity:0.6;cursor:not-allowed;">
                                    <label class="field-label" for="studentID_ro" style="top:10px;font-size:9.5px;color:var(--accent-cyan);letter-spacing:0.5px;transform:none;">Student ID</label>
                                </div>
                                <div class="field">
                                    <i class="ti ti-mail field-icon" aria-hidden="true"></i>
                                    <input class="field-input" id="email_ro" type="email" placeholder=" " readonly style="opacity:0.6;cursor:not-allowed;">
                                    <label class="field-label" for="email_ro" style="top:10px;font-size:9.5px;color:var(--accent-cyan);letter-spacing:0.5px;transform:none;">Email</label>
                                </div>
                            </div>

                            <div class="field-grid col-1" style="margin-bottom:14px;">
                                <div class="field" id="f-pw">
                                    <i class="ti ti-lock field-icon" aria-hidden="true"></i>
                                    <input class="field-input pw-input" id="passwordInput" name="signPassword" type="password" placeholder=" " autocomplete="new-password">
                                    <label class="field-label" for="passwordInput">Password *</label>
                                    <button type="button" class="toggle-pw" onclick="togglePw('passwordInput',this)" aria-label="Toggle password visibility">
                                        <i class="ti ti-eye" style="font-size:16px;" aria-hidden="true"></i>
                                    </button>
                                </div>
                                <div class="strength-wrap" id="strengthWrap" style="display:none;">
                                    <div class="strength-bars">
                                        <div class="strength-bar" id="sb1"></div>
                                        <div class="strength-bar" id="sb2"></div>
                                        <div class="strength-bar" id="sb3"></div>
                                        <div class="strength-bar" id="sb4"></div>
                                    </div>
                                    <span class="strength-label" id="strengthLabel">Enter a password</span>
                                </div>
                            </div>

                            <div class="field-grid col-1">
                                <div class="field" id="f-cpw">
                                    <i class="ti ti-lock-check field-icon" aria-hidden="true"></i>
                                    <input class="field-input pw-input" id="confirmPw" name="signConfirmPassword" type="password" placeholder=" " autocomplete="new-password">
                                    <label class="field-label" for="confirmPw">Confirm Password *</label>
                                    <button type="button" class="toggle-pw" onclick="togglePw('confirmPw',this)" aria-label="Toggle confirm password visibility">
                                        <i class="ti ti-eye" style="font-size:16px;" aria-hidden="true"></i>
                                    </button>
                                </div>
                                <div class="match-badge" id="matchBadge" style="display:none;"></div>
                            </div>
                        </div>

                        <div class="btn-row">
                            <button class="btn-ghost" id="prev3" type="button"><i class="ti ti-arrow-left" aria-hidden="true"></i> Back</button>
                            <button class="btn-primary" id="next3" type="button">
                                <span class="btn-inner">Review &amp; Confirm <i class="ti ti-arrow-right" style="font-size:16px;" aria-hidden="true"></i></span>
                            </button>
                        </div>
                    </div>

                    <!-- STEP 4: CONFIRMATION -->
                    <div class="form-step" id="step4">
                        <div class="glass-card">
                            <div class="success-icon-wrap">✦</div>
                            <h3 class="step-heading" style="text-align:center;margin-bottom:4px;">Almost done!</h3>
                            <p class="step-subheading" style="text-align:center;margin-bottom:24px;">Please review your information before submitting</p>
                            <div class="divider"></div>
                            <div class="confirm-grid" id="confirmGrid"></div>
                        </div>

                        <div class="btn-row">
                            <button class="btn-ghost" id="prev4" type="button"><i class="ti ti-arrow-left" aria-hidden="true"></i> Edit</button>
                            <button name="signupForm" class="btn-primary" type="submit"><span class="btn-inner"><i class="ti ti-check" style="font-size:16px;" aria-hidden="true"></i> Create Account</span></button>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </form>
    <!-- <div id="toast" class="toast"></div>

    
    <div class="layout">

        <main class="content">

            <section class="sign-logo"> <a href="loginPhase.php">
                    <img src="../kapstongImage/logo.jpg" alt="logo">
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

                        <div class="sign-box trios lastName required-field">
                            <input name="lastName" class="name-input" type="text" placeholder=" " required />
                            <span>Last name</span>
                            <p class="name-warning">Only letters are allowed.</p>
                        </div>
                        <div class="sign-box trios firstName required-field"> <input name="firstName" class="name-input" type="text" placeholder=" " required />
                            <span>First name</span>
                            <p class="name-warning">Only letters are allowed.</p>
                        </div>

                        <div class="sign-box trios middleName"> <input name="middleName" class="name-input" type="text" placeholder=" " />
                            <span>Middle name</span>
                            <p class="name-warning">Only letters are allowed.</p>
                        </div>

                        <div class="sign-box duos studentEmail required-field">
                            <input name="signEmail" type="email" class="full" id="studentEmail" placeholder=" " required /> 
                            <span>Email</span>
                            <p id="studentEmail-warning" style="color:red; display:none; font-weight : 600; font-size: 0.5rem;">Email already been used!</p>
                        </div>

                        <input type="hidden" name="rawTel" id="rawTel">

                        <div class="sign-box duos studentMobile required-field">
                            <input name="signTel" type="tel" class="duos" maxlength="14" required placeholder=" " />
                            <span> Mobile Number </span>
                        </div>

                        <div class="sign-box duos birthdate required-field">
                            <input name="signBirth" type="date" max="2008-03-26" required />
                            <span> Birth date </span>
                            <p class="birth-warning">You must be at least 15 years old.</p>
                        </div>

                        <div class="sign-box duos required-field">

                            <select name="gender" required>
                                <option value="" disabled selected hidden>Select you gender...</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Others">Others</option>
                            </select>
                            <span>Gender</span>
                        </div>

                        <input type="hidden" name="province" value="Cavite">

                        <div class="sign-box trios required-field">
                            <select name="city" id="citySelect" required>
                                <option value="" disabled selected hidden>Select City...</option>
                                <option value="Tanza">Tanza</option>
                                <option value="Rosario">Rosario</option>
                                <option value="Naic">Naic</option>
                                <option value="Bacoor">Bacoor</option>
                            </select>
                            <span>City / Municipality</span>
                        </div>

                        <div class="sign-box trios required-field">
                            <input name="barangay" id="barangayInput" type="text" list="barangays" placeholder="" required />
                            <span>Barangay</span>

                            <datalist id="barangays"></datalist>
                        </div>

                        <div class="sign-box trios">
                            <input name="street" type="text" placeholder="" />
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
                        <div class="sign-box duos studentID required-field"> <input name="studentID" id="studentID1" type="text" required />
                            <span>Student ID </span>
                            <p id="studentID-warning" style="color:red; display:none; font-weight : 600; font-size: 0.6rem;">Student ID already taken!</p>
                        </div>

                        <div class="sign-box duos required-field">
                            <select name="signCourse" id="signCourse" required>
                                <option value="" disabled selected hidden></option>
                            </select>
                            <span>Program</span>
                        </div>

                        <div class="sign-box duos required-field">
                            <select name="signLevel" required>
                                <option value="" disabled selected hidden></option>
                                <option value="1st year">1st Year</option>
                                <option value="2nd year">2nd Year</option>
                                <option value="3rd year">3rd Year</option>
                                <option value="4th year">4th Year</option>
                            </select>
                            <span>Year Level</span>
                        </div>

                        <input name="signSY" type="text" id="academic-year" hidden />

                        <div class="sign-box duos required-field">
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

                        <div class="sign-box duos required-field"> <input name="signPassword" id="passwordInput" placeholder=" " type="password" required />

                            <i class="toggle-pass" onclick="togglePassword('passwordInput', this)">👁</i>
                            <span>Password</span>
                        </div>

                        <div class="sign-box duos required-field"> <input name="signConfirmPassword" id="confirmPasswordInput" placeholder=" " type="password" required />
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

    <div id="loadingScreen" class="loading-screen">
        <div class="loader"></div>
        <p>Creating your account...</p>
    </div> -->

</body>
<script src="../js/signupPhase.js"></script>

</html>
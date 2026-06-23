 let current = 0;
    const totalSteps = 4;
    const steps = ['step1', 'step2', 'step3', 'step4'];
    const dots = [0, 1, 2, 3].map(i => document.getElementById('dot' + i));
    const lbls = [0, 1, 2, 3].map(i => document.getElementById('lbl' + i));
    const fill = document.getElementById('progressFill');
    let genderVal = 'Male';
    const form = document.getElementById('signupForm');
    const loadingScreen = document.getElementById('loadingOverlay');

    function goTo(n) {
        document.getElementById(steps[current]).classList.remove('active');
        current = n;
        document.getElementById(steps[current]).classList.add('active');
        updateStepper();
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }

    function updateStepper() {
        dots.forEach((d, i) => {
            d.className = 'step-dot';
            lbls[i].className = 'step-label';
            if (i < current) {
                d.classList.add('completed');
                d.textContent = '✔';
                lbls[i].classList.add('completed');
            } else if (i === current) {
                d.classList.add('active');
                d.textContent = i < 3 ? i + 1 : '✦';
                lbls[i].classList.add('active');
            } else {
                d.textContent = i < 3 ? i + 1 : '✦';
            }
        });
        fill.style.width = (current / (totalSteps - 1) * 100) + '%';
    }

    // Gender
    document.querySelectorAll('#genderSeg .seg-btn').forEach(b => {
        b.addEventListener('click', () => {
            document.querySelectorAll('#genderSeg .seg-btn').forEach(x => x.classList.remove('active'));
            b.classList.add('active');
            genderVal = b.dataset.val;
            document.getElementById('genderHidden').value = genderVal;
        });
    });

    // Name
    document.querySelectorAll('.name-input').forEach(inp => {
        inp.addEventListener('input', function() {
            this.value = this.value.toUpperCase().replace(/[^A-Z\s]/g, '');
        });
    });

    // Mobile
    const telIn = document.getElementById('signTel');
    telIn.addEventListener('input', function() {
        let d = this.value.replace(/\D/g, '');
        if (d.length > 11) d = d.slice(0, 11);
        let fmt = '';
        for (let i = 0; i < d.length; i++) {
            fmt += d[i];
            if (i === 1 || i === 4 || i === 7) fmt += ' ';
        }
        this.value = fmt.trim();
        const fld = document.getElementById('f-mobile');
        if (d.length === 11 && d.startsWith('09')) {
            fld.classList.remove('error');
            fld.classList.add('valid');
        } else if (d.length > 0) {
            fld.classList.add('error');
            fld.classList.remove('valid');
        } else {
            fld.classList.remove('error', 'valid');
        }
    });

    // Email
    const emailIn = document.getElementById('signEmail');
    emailIn.addEventListener('input', function() {
        const email = emailIn.value.trim();
        const fld = document.getElementById('f-email');
        const ok = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.value);
        if (!this.value) {
            fld.classList.remove('error', 'valid');
            return;
        }

        fetch(`checkStudentID.php?email=${encodeURIComponent(email)}`)
            .then(res => res.json())
            .then(data => {
                if (data.exists) {
                    fld.classList.add('error');
                    fld.classList.remove('valid');
                    return;
                }
            })
            .catch(err => console.error(err));

        fld.classList.toggle('error', !ok);
        fld.classList.toggle('valid', ok);
        document.getElementById('email_ro').value = this.value;
    });

    // Selects
    document.querySelectorAll('.field-select').forEach(sel => {
        sel.addEventListener('change', function() {
            this.classList.add('has-val');
        });
    });

    // Password strength
    const pwIn = document.getElementById('passwordInput');
    pwIn.addEventListener('input', function() {
        const v = this.value;
        const sw = document.getElementById('strengthWrap');
        if (!v) {
            sw.style.display = 'none';
            return;
        }
        sw.style.display = 'block';
        let score = 0;
        if (v.length >= 8) score++;
        if (/[A-Z]/.test(v)) score++;
        if (/[0-9]/.test(v)) score++;
        if (/[^A-Za-z0-9]/.test(v)) score++;
        const bars = ['sb1', 'sb2', 'sb3', 'sb4'].map(id => document.getElementById(id));
        const clsMap = ['weak', 'weak', 'medium', 'strong'];
        const labelMap = ['Too short', 'Weak', 'Moderate', 'Strong'];
        const colorMap = ['var(--accent-rose)', 'var(--accent-rose)', '#f6ad55', 'var(--accent-emerald)'];
        bars.forEach((b, i) => {
            b.className = 'strength-bar';
            if (i < score) b.classList.add(clsMap[score - 1] || 'weak');
        });
        const lbl = document.getElementById('strengthLabel');
        lbl.textContent = labelMap[score - 1] || 'Too short';
        lbl.style.color = colorMap[score - 1] || 'var(--text-muted)';
    });

    // Confirm password
    const cpwIn = document.getElementById('confirmPw');
    cpwIn.addEventListener('input', function() {
        const badge = document.getElementById('matchBadge');
        const fld = document.getElementById('f-cpw');
        if (!this.value) {
            badge.style.display = 'none';
            fld.classList.remove('error', 'valid');
            return;
        }
        badge.style.display = 'flex';
        if (this.value === pwIn.value) {
            badge.className = 'match-badge matched';
            badge.innerHTML = '<i class="ti ti-check" style="font-size:12px;"></i> Passwords match';
            fld.classList.remove('error');
            fld.classList.add('valid');
        } else {
            badge.className = 'match-badge mismatch';
            badge.innerHTML = '<i class="ti ti-x" style="font-size:12px;"></i> Passwords do not match';
            fld.classList.add('error');
            fld.classList.remove('valid');
        }
    });

    // password
    function togglePw(id, btn) {
        const inp = document.getElementById(id);
        const icon = btn.querySelector('i');
        if (inp.type === 'password') {
            inp.type = 'text';
            icon.className = 'ti ti-eye-off';
        } else {
            inp.type = 'password';
            icon.className = 'ti ti-eye';
        }
    }

    // studentID
    document.getElementById('studentID').addEventListener('input', function() {

        const fld = document.getElementById('f-studentID');

        const studentID = document.getElementById('studentID').value.trim();
        if (studentID === "") return;

        fetch(`checkStudentID.php?id=${encodeURIComponent(studentID)}`)
            .then(res => res.json())
            .then(data => {
                if (data.exists) {
                    fld.classList.add('error');
                    fld.classList.remove('valid');
                    return;
                }
            })
            .catch(err => console.error(err));

        fld.classList.remove('error');
        fld.classList.add('valid');
        document.getElementById('studentID_ro').value = this.value;
    });

    // courses
    document.addEventListener("DOMContentLoaded", function() {
        const courseSelect = document.getElementById('signCourse');

        fetch('getCourses.php')
            .then(res => res.json())
            .then(data => {
                courseSelect.innerHTML = '<option value="" disabled selected hidden></option>';

                if (data.acro && data.values) {
                    data.acro.forEach((acro, index) => {

                        const option = document.createElement('option');
                        option.value = acro;
                        option.textContent = data.values[index];
                        courseSelect.appendChild(option);
                    });
                }
            })
            .catch(err => console.error("Error loading courses:", err));
    });

    // Academic year
    function getAcademicYear() {
        const today = new Date();
        const y = today.getFullYear();
        const m = today.getMonth() + 1;
        return m >= 6 ? y + '-' + (y + 1) : (y - 1) + '-' + y;
    }
    document.getElementById('academic-year').value = getAcademicYear();

    // Validations
    function validateStep1() {
        if (!document.getElementById('lastName').value.trim()) {
            showToast('Last name is required');
            return false;
        }
        if (!document.getElementById('firstName').value.trim()) {
            showToast('First name is required');
            return false;
        }
        const email = document.getElementById('signEmail').value.trim();
        if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            showToast('Enter a valid email address');
            return false;
        }
        const tel = document.getElementById('signTel').value.replace(/\D/g, '');
        if (tel.length !== 11 || !tel.startsWith('09')) {
            showToast('Enter a valid 11-digit mobile number starting with 09');
            return false;
        }
        if (!document.getElementById('signBirth').value) {
            showToast('Birthdate is required');
            return false;
        }
        if (!document.getElementById('signAddress').value.trim()) {
            showToast('Address is required');
            return false;
        }
        return true;
    }

    function validateStep2() {
        if (!document.getElementById('studentID').value.trim()) {
            showToast('Student ID is required');
            return false;
        }
        if (!document.getElementById('signCourse').value) {
            showToast('Please select a program');
            return false;
        }
        if (!document.getElementById('signLevel').value) {
            showToast('Please select year level');
            return false;
        }
        if (!document.getElementById('signSemester').value) {
            showToast('Please select semester');
            return false;
        }
        return true;
    }

    function validateStep3() {
        if (pwIn.value.length < 8) {
            showToast('Password must be at least 8 characters');
            return false;
        }
        if (pwIn.value !== cpwIn.value) {
            showToast('Passwords do not match');
            return false;
        }
        return true;
    }

    function buildConfirm() {
        const courseEl = document.getElementById('signCourse');
        const courseText = courseEl.options[courseEl.selectedIndex]?.text || '';
        const rows = [
            ['Full Name', [document.getElementById('firstName').value, document.getElementById('middleName').value, document.getElementById('lastName').value].filter(Boolean).join(' ')],
            ['Email', document.getElementById('signEmail').value],
            ['Mobile', document.getElementById('signTel').value],
            ['Sex', genderVal],
            ['Birthdate', document.getElementById('signBirth').value],
            ['Address', document.getElementById('signAddress').value],
            ['Student ID', document.getElementById('studentID').value],
            ['Program', courseText],
            ['Year Level', document.getElementById('signLevel').value],
            ['Semester', document.getElementById('signSemester').value],
            ['Academic Year', getAcademicYear()],
        ];
        document.getElementById('confirmGrid').innerHTML = rows.map(([l, v]) =>
            `<div class="confirm-row"><span class="confirm-label">${l}</span><span class="confirm-val">${v||'—'}</span></div>`
        ).join('');
    }

    // nav
    document.getElementById('next1').addEventListener('click', () => {
        if (validateStep1()) goTo(1);
    });
    document.getElementById('next2').addEventListener('click', () => {
        if (validateStep2()) goTo(2);
    });
    document.getElementById('next3').addEventListener('click', () => {
        if (validateStep3()) {
            buildConfirm();
            goTo(3);
        }
    });

    document.getElementById('prev2').addEventListener('click', () => goTo(0));
    document.getElementById('prev3').addEventListener('click', () => goTo(1));
    document.getElementById('prev4').addEventListener('click', () => goTo(2));

    document.getElementById('signupForm').addEventListener('submit', function() {
        showToast('Account created successfully! Welcome aboard.');

        setTimeout(() => {
            loadingScreen.classList.add("show");
        }, 5500);
    });

    // document.getElementById('submitBtn').addEventListener('click', () => {

    //     document.getElementById('loadingOverlay').classList.add('show');

    //     setTimeout(() => {
    //         document.getElementById('loadingOverlay').classList.remove('show');
    //         showToast('Account created successfully! Welcome aboard.');
    //         document.getElementById('signupForm').submit();

    //     }, 2500);
    // });

    function showToast(msg, dur = 3000) {
        const t = document.getElementById('toast');
        t.textContent = msg;
        t.classList.add('show');
        setTimeout(() => t.classList.remove('show'), dur);
    }
const form = document.getElementById("resetPasswordForm");

const newPassword = document.getElementById("newPassword");
const confirmPassword = document.getElementById("confirmPassword");
const errorText = document.getElementById("passwordError");
const strengthText = document.getElementById("passwordStrength");

const loadingScreen = document.getElementById("resetLoadingScreen");

document.addEventListener("DOMContentLoaded", () => {

    const toast = document.getElementById("toast");

    function showToast(message) {
        toast.textContent = message;
        toast.classList.add("show");

        setTimeout(() => {
            toast.classList.remove("show");
        }, 3000);
    }

    const error = "<?= $resetPasswordError ?>";

    if (error === "mismatch") {
        showToast("❌ Passwords do not match");
    }

});

form.addEventListener("submit", function (e) {

    

    if (newPassword.value !== confirmPassword.value) {
        e.preventDefault();
        errorText.textContent = "❌ Passwords do not match";
        return;
    }

    errorText.textContent = "";

    loadingScreen.classList.add("show");
});

newPassword.addEventListener("input", () => {

    const value = newPassword.value;

    if (value.length < 6) {
        strengthText.textContent = "Weak password";
        strengthText.className = "password-strength weak";
    }

    else if (value.length < 10) {
        strengthText.textContent = "Medium strength password";
        strengthText.className = "password-strength medium";
    }

    else {
        strengthText.textContent = "Strong password";
        strengthText.className = "password-strength strong";
    }
});

document.querySelectorAll(".toggle-password").forEach(icon => {

    icon.addEventListener("click", () => {

        const targetId = icon.getAttribute("data-target");
        const input = document.getElementById(targetId);

        const isPassword = input.type === "password";

        input.type = isPassword ? "text" : "password";

        icon.classList.toggle("bx-show");
        icon.classList.toggle("bx-hide");

    });

});
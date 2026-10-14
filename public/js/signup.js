// ISES sign-up interaction layer. IDs are intentionally kept compatible with the Blade view.
document.addEventListener("DOMContentLoaded", () => {
  const password = document.getElementById("password");
  const confirmPassword = document.getElementById("confirmPassword");
  const form = document.querySelector("form");
  const fill = document.getElementById("strengthFill");
  const strengthText = document.getElementById("strengthText");
  const matchText = document.getElementById("passwordMatch");
  if (!password || !confirmPassword || !form) return;
  const setupToggle = (id, input) => {
    const button = document.getElementById(id);
    if (!button) return;
    button.addEventListener("click", () => {
      const visible = input.type === "password";
      input.type = visible ? "text" : "password";
      const icon = button.querySelector("i") || button;
      icon.classList.toggle("fa-eye", !visible);
      icon.classList.toggle("fa-eye-slash", visible);
      button.setAttribute("aria-label", visible ? "Hide password" : "Show password");
      button.setAttribute("aria-pressed", String(visible));
    });
  };
  setupToggle("togglePassword", password);
  setupToggle("toggleConfirmPassword", confirmPassword);
  const requirements = [v => v.length >= 8, v => /[A-Z]/.test(v), v => /[a-z]/.test(v), v => /\d/.test(v), v => /[-_@$!%*?&#]/.test(v)];
  const updateStrength = () => {
    const value = password.value;
    const score = requirements.filter(test => test(value)).length;
    if (fill) fill.style.width = `${score * 20}%`;
    if (strengthText) strengthText.textContent = value ? ["Very weak", "Very weak", "Weak", "Fair", "Good", "Strong"][score] : "Password strength";
  };
  const updateMatch = () => {
    if (!matchText) return;
    if (!confirmPassword.value) { matchText.textContent = ""; matchText.className = "password-match"; return; }
    const matches = password.value === confirmPassword.value;
    matchText.textContent = matches ? "Passwords match." : "Passwords do not match.";
    matchText.className = `password-match ${matches ? "valid" : "invalid"}`;
  };
  password.addEventListener("input", () => { updateStrength(); updateMatch(); });
  confirmPassword.addEventListener("input", updateMatch);
  form.addEventListener("submit", event => {
    const valid = requirements.every(test => test(password.value));
    if (!valid || password.value !== confirmPassword.value) {
      event.preventDefault();
      updateStrength(); updateMatch();
      (password.value !== confirmPassword.value ? confirmPassword : password).focus();
    }
  });
});
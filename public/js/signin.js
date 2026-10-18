// Accessible password visibility toggle for the ISES sign-in view.
document.addEventListener("DOMContentLoaded", () => {
  const button = document.getElementById("togglePassword");
  const input = document.getElementById("password");
  if (!button || !input) return;
  button.addEventListener("click", () => {
    const visible = input.type === "password";
    input.type = visible ? "text" : "password";
    const icon = button.querySelector("i") || button;
    icon.classList.toggle("fa-eye", !visible);
    icon.classList.toggle("fa-eye-slash", visible);
    button.setAttribute("aria-label", visible ? "Hide password" : "Show password");
    button.setAttribute("aria-pressed", String(visible));
  });
});
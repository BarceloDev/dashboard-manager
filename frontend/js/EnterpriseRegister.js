const registerForm = document.querySelector("form");
const errorMessageDiv = document.querySelector(".errorMessage");

// limpar mensagem ao digitar
registerForm.addEventListener("input", () => {
  errorMessageDiv.textContent = "";
  errorMessageDiv.className = "errorMessage";
});

registerForm.addEventListener("submit", async (event) => {
  event.preventDefault();

  const companyName = registerForm
    .querySelector('input[name="companyName"]')
    .value.trim();
  const email = registerForm.querySelector('input[name="email"]').value.trim();
  const password = registerForm
    .querySelector('input[name="password"]')
    .value.trim();
  const telephone = registerForm
    .querySelector('input[name="telephone"]')
    .value.trim();

  if (!companyName || !email || !password || !telephone) {
    errorMessageDiv.textContent = "Preencha todos os campos.";
    errorMessageDiv.className = "errorMessage error";
    return;
  }

  const formData = new FormData(registerForm);

  try {
    const response = await fetch("../../controllers/EnterpriseRegister.php", {
      method: "POST",
      body: formData,
    });
    const result = await response.json();

    if (result.success) {
      errorMessageDiv.textContent = result.message;
      errorMessageDiv.className = "errorMessage success";
      setTimeout(() => {
        window.location.href = "/dasboard/index.html";
      }, 1500);
    } else {
      errorMessageDiv.textContent = result.message;
      errorMessageDiv.className = "errorMessage error";
      setTimeout(() => {
        errorMessageDiv.textContent = "";
        errorMessageDiv.className = "errorMessage";
      }, 5000);
    }
  } catch (err) {
    console.error("Erro no cadastro:", err);
    errorMessageDiv.textContent = "Erro ao cadastrar. Tente novamente.";
    errorMessageDiv.className = "errorMessage error";
    setTimeout(() => {
      errorMessageDiv.textContent = "";
      errorMessageDiv.className = "errorMessage";
    }, 5000);
  }
});

const registerForm = document.querySelector("form");
let errorMessageDiv = document.querySelector(".errorMessage");

if (!errorMessageDiv) {
  // Cria elemento de mensagem se não existir
  const box = document.querySelector(".box") || document.body;
  errorMessageDiv = document.createElement("div");
  errorMessageDiv.className = "errorMessage";
  box.insertBefore(errorMessageDiv, box.firstChild);
}

registerForm.addEventListener("input", () => {
  errorMessageDiv.textContent = "";
  errorMessageDiv.className = "errorMessage";
});

registerForm.addEventListener("submit", async (event) => {
  event.preventDefault();

  const nome = registerForm.querySelector('input[name="nome"]').value.trim();
  const email = registerForm.querySelector('input[name="email"]').value.trim();
  const senha = registerForm.querySelector('input[name="senha"]').value.trim();
  const telefone = registerForm
    .querySelector('input[name="telefone"]')
    .value.trim();
  const enterpriseName = registerForm
    .querySelector('input[name="enterpriseName"]')
    .value.trim();

  if (!nome || !email || !senha || !telefone || !enterpriseName) {
    errorMessageDiv.textContent = "Preencha todos os campos.";
    errorMessageDiv.className = "errorMessage error";
    return;
  }

  const formData = new FormData(registerForm);

  try {
    const response = await fetch(
      "/dashboard-manager/controllers/RegisterEmployeeHere.php",
      {
        method: "POST",
        body: formData,
      },
    );
    const result = await response.json();

    if (result.success) {
      errorMessageDiv.textContent = result.message;
      errorMessageDiv.className = "errorMessage success";
      setTimeout(() => {
        window.location.href =
          "/dashboard-manager/frontend/pages/EnterpriseScreen.html";
      }, 700);
    } else {
      errorMessageDiv.textContent = result.message || "Erro ao cadastrar.";
      errorMessageDiv.className = "errorMessage error";
      setTimeout(() => {
        errorMessageDiv.textContent = "";
        errorMessageDiv.className = "errorMessage";
      }, 5000);
    }
  } catch (err) {
    console.error("Erro no cadastro do funcionário:", err);
    errorMessageDiv.textContent = "Erro ao cadastrar. Tente novamente.";
    errorMessageDiv.className = "errorMessage error";
    setTimeout(() => {
      errorMessageDiv.textContent = "";
      errorMessageDiv.className = "errorMessage";
    }, 5000);
  }
});

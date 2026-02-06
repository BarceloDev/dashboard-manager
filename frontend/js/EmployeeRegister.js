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

  // Validações
  if (!nome || !email || !senha || !telefone || !enterpriseName) {
    errorMessageDiv.textContent = "Preencha todos os campos.";
    errorMessageDiv.className = "errorMessage error";
    return;
  }

  if (!email.includes("@") || email.length < 5) {
    errorMessageDiv.textContent = "Email inválido.";
    errorMessageDiv.className = "errorMessage error";
    return;
  }

  if (senha.length < 3) {
    errorMessageDiv.textContent = "Senha deve ter pelo menos 3 caracteres.";
    errorMessageDiv.className = "errorMessage error";
    return;
  }

  if (nome.length < 2) {
    errorMessageDiv.textContent = "Nome deve ter pelo menos 2 caracteres.";
    errorMessageDiv.className = "errorMessage error";
    return;
  }

  const formData = new FormData(registerForm);
  const submitBtn = registerForm.querySelector('button[type="submit"]');
  submitBtn.disabled = true;
  const originalText = submitBtn.textContent;
  submitBtn.textContent = "Cadastrando...";

  try {
    const response = await fetch(
      "../../../controllers/RegisterEmployeeHere.php",
      {
        method: "POST",
        body: formData,
        credentials: "include",
      },
    );

    const responseText = await response.text();
    console.log("Response Status:", response.status);
    console.log("Response Text:", responseText);

    let result;

    try {
      result = JSON.parse(responseText);
    } catch (e) {
      console.error("JSON Parse Error:", e);
      console.error("Raw Response:", responseText);
      console.error("Response Headers:", response.headers);
      // Tenta extrair mensagem de erro do HTML
      const errorMatch =
        responseText.match(/<title>([^<]+)<\/title>/) ||
        responseText.match(/<h1>([^<]+)<\/h1>/);
      const errorMsg = errorMatch
        ? errorMatch[1]
        : "Resposta do servidor inválida";
      throw new Error(errorMsg);
    }

    if (result.success) {
      errorMessageDiv.textContent =
        result.message ||
        "Funcionário cadastrado com sucesso! Redirecionando...";
      errorMessageDiv.className = "errorMessage success";
      setTimeout(() => {
        window.location.href = "./EmployeeHere.php";
      }, 1000);
    } else {
      errorMessageDiv.textContent =
        result.message || "Erro ao cadastrar funcionário.";
      errorMessageDiv.className = "errorMessage error";
      submitBtn.disabled = false;
      submitBtn.textContent = originalText;
      setTimeout(() => {
        errorMessageDiv.textContent = "";
        errorMessageDiv.className = "errorMessage";
      }, 5000);
    }
  } catch (err) {
    console.error("Erro no cadastro do funcionário:", err);
    errorMessageDiv.textContent =
      err.message || "Erro ao cadastrar funcionário. Tente novamente.";
    errorMessageDiv.className = "errorMessage error";
    submitBtn.disabled = false;
    submitBtn.textContent = originalText;
    setTimeout(() => {
      errorMessageDiv.textContent = "";
      errorMessageDiv.className = "errorMessage";
    }, 5000);
  }
});

const loginForm = document.querySelector("#loginForm");
const errorMessageDiv = document.querySelector(".errorMessage");
let isSubmitting = false;

loginForm.addEventListener("input", () => {
  errorMessageDiv.textContent = "";
  errorMessageDiv.className = "errorMessage";
});

loginForm.addEventListener("submit", async (event) => {
  event.preventDefault();

  if (isSubmitting) return;

  const email = loginForm.querySelector('input[name="email"]').value.trim();
  const password = loginForm
    .querySelector('input[name="password"]')
    .value.trim();

  if (!email || !password) {
    errorMessageDiv.textContent = "Preencha todos os campos.";
    errorMessageDiv.className = "errorMessage error";
    return;
  }

  if (!email.includes("@") || email.length < 5) {
    errorMessageDiv.textContent = "Email inválido.";
    errorMessageDiv.className = "errorMessage error";
    return;
  }

  if (password.length < 3) {
    errorMessageDiv.textContent = "Senha deve ter pelo menos 3 caracteres.";
    errorMessageDiv.className = "errorMessage error";
    return;
  }

  isSubmitting = true;
  const submitBtn = loginForm.querySelector('button[type="submit"]');
  submitBtn.disabled = true;
  submitBtn.textContent = "Entrando...";

  const formData = new FormData();
  formData.append("email", email);
  formData.append("password", password);

  try {
    const response = await fetch("../../controllers/FuncionarioLogin.php", {
      method: "POST",
      body: formData,
      credentials: "include",
    });

    console.log("Response Status:", response.status);
    const responseText = await response.text();
    console.log("Response Text:", responseText);
    let result;

    try {
      result = JSON.parse(responseText);
    } catch (e) {
      console.error("Failed to parse JSON:", e);
      console.error("Response body:", responseText);
      errorMessageDiv.textContent = "Erro ao processar resposta do servidor.";
      errorMessageDiv.className = "errorMessage error";
      isSubmitting = false;
      submitBtn.disabled = false;
      submitBtn.textContent = "Entrar";
      return;
    }

    if (result.success) {
      // Armazenar dados do funcionário no localStorage
      localStorage.setItem(
        "funcionario_nome",
        result.funcionario_nome || "Funcionário",
      );
      localStorage.setItem("funcionario_id", result.funcionario_id || "");

      errorMessageDiv.textContent = result.message + " Redirecionando...";
      errorMessageDiv.className = "errorMessage success";
      setTimeout(() => {
        window.location.href = "./Employee/EmployeeScreen.html";
      }, 1000);
    } else {
      errorMessageDiv.textContent = result.message || "Erro ao fazer login.";
      errorMessageDiv.className = "errorMessage error";
      isSubmitting = false;
      submitBtn.disabled = false;
      submitBtn.textContent = "Entrar";
      setTimeout(() => {
        errorMessageDiv.textContent = "";
        errorMessageDiv.className = "errorMessage";
      }, 5000);
    }
  } catch (err) {
    console.error("Erro no login:", err);
    errorMessageDiv.textContent = "Erro ao fazer login. Tente novamente.";
    errorMessageDiv.className = "errorMessage error";
    isSubmitting = false;
    submitBtn.disabled = false;
    submitBtn.textContent = "Entrar";
    setTimeout(() => {
      errorMessageDiv.textContent = "";
      errorMessageDiv.className = "errorMessage";
    }, 5000);
  }
});

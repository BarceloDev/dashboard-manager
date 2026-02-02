const inputEmail = document.querySelector('input[name="email"]');
const inputPassword = document.querySelector('input[name="password"]');
const errorMessageDiv = document.querySelector(".errorMessage");
const loginForm = document.querySelector("form");

inputEmail.addEventListener("input", () => {
  errorMessageDiv.textContent = "";
  errorMessageDiv.className = "errorMessage";
});

inputPassword.addEventListener("input", () => {
  errorMessageDiv.textContent = "";
  errorMessageDiv.className = "errorMessage";
});

loginForm.addEventListener("submit", async (event) => {
  event.preventDefault();

  const email = inputEmail.value.trim();
  const password = inputPassword.value.trim();

  if (!email || !password) {
    errorMessageDiv.textContent = "Preencha todos os campos.";
    errorMessageDiv.className = "errorMessage error";
    return;
  }

  const formData = new FormData(loginForm);

  try {
    const response = await fetch("controllers/EnterpriseLogin.php", {
      method: "POST",
      body: formData,
    });

    const result = await response.json();

    if (result.success) {
      errorMessageDiv.textContent = result.message;
      errorMessageDiv.className = "errorMessage success";

      setTimeout(() => {
        // Redirecionamento robusto e log para depuração
        const redirectPath = "frontend/pages/EnterpriseScreen.html";
        const redirectUrl = new URL(redirectPath, window.location.href).href;
        console.log("Redirecionando para:", redirectUrl);
        window.location.href = redirectUrl;
      }, 200);
    } else {
      errorMessageDiv.textContent = result.message;
      errorMessageDiv.className = "errorMessage error";

      setTimeout(() => {
        errorMessageDiv.textContent = "";
        errorMessageDiv.className = "errorMessage";
      }, 10000);
    }
  } catch (error) {
    console.error("Erro:", error);
    errorMessageDiv.textContent = "Erro ao processar o login. Tente novamente.";
    errorMessageDiv.className = "errorMessage error";

    setTimeout(() => {
      errorMessageDiv.textContent = "";
      errorMessageDiv.className = "errorMessage";
    }, 5000);
  }
});

const saleForm = document.querySelector("#saleForm");
const errorMessageDiv = document.querySelector(".errorMessage");
let isSubmitting = false;

// Verificar autenticação ao carregar página
window.addEventListener("load", () => {
  const funcionarioNome = localStorage.getItem("funcionario_nome");
  if (!funcionarioNome) {
    // Sem dados de autenticação, redirecionar para login
    window.location.href = "../FuncionarioLogin.html";
    return;
  }
});

saleForm.addEventListener("input", () => {
  errorMessageDiv.textContent = "";
  errorMessageDiv.className = "errorMessage";
});

saleForm.addEventListener("submit", async (event) => {
  event.preventDefault();

  if (isSubmitting) return;

  const cliente = saleForm.querySelector('input[name="cliente"]').value.trim();
  const produto = saleForm.querySelector('input[name="produto"]').value.trim();
  const price = parseFloat(
    saleForm.querySelector('input[name="price"]').value.trim(),
  );

  // Validações
  if (!cliente || !produto || isNaN(price)) {
    errorMessageDiv.textContent = "Preencha todos os campos.";
    errorMessageDiv.className = "errorMessage error";
    return;
  }

  if (cliente.length < 2) {
    errorMessageDiv.textContent =
      "Nome do cliente deve ter pelo menos 2 caracteres.";
    errorMessageDiv.className = "errorMessage error";
    return;
  }

  if (produto.length < 2) {
    errorMessageDiv.textContent =
      "Nome do produto deve ter pelo menos 2 caracteres.";
    errorMessageDiv.className = "errorMessage error";
    return;
  }

  if (price <= 0) {
    errorMessageDiv.textContent = "Preço deve ser maior que zero.";
    errorMessageDiv.className = "errorMessage error";
    return;
  }

  if (price > 999999) {
    errorMessageDiv.textContent = "Preço não pode ser maior que R$ 999.999,00.";
    errorMessageDiv.className = "errorMessage error";
    return;
  }

  const formData = new FormData();
  formData.append("cliente", cliente);
  formData.append("produto", produto);
  formData.append("price", price);

  isSubmitting = true;
  const submitBtn = saleForm.querySelector('input[type="submit"]');
  submitBtn.disabled = true;

  try {
    const response = await fetch("../../../controllers/RegisterSale.php", {
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
      return;
    }

    if (result.success) {
      errorMessageDiv.textContent = result.message + " Redirecionando...";
      errorMessageDiv.className = "errorMessage success";
      saleForm.reset();
      setTimeout(() => {
        window.location.href = "./EmployeeScreen.html";
      }, 1500);
    } else {
      errorMessageDiv.textContent =
        result.message || "Erro ao registrar venda.";
      errorMessageDiv.className = "errorMessage error";
      isSubmitting = false;
      submitBtn.disabled = false;
      setTimeout(() => {
        errorMessageDiv.textContent = "";
        errorMessageDiv.className = "errorMessage";
      }, 5000);
    }
  } catch (err) {
    console.error("Erro ao registrar venda:", err);
    errorMessageDiv.textContent = "Erro ao registrar venda. Tente novamente.";
    errorMessageDiv.className = "errorMessage error";
    isSubmitting = false;
    submitBtn.disabled = false;
    setTimeout(() => {
      errorMessageDiv.textContent = "";
      errorMessageDiv.className = "errorMessage";
    }, 5000);
  }
});

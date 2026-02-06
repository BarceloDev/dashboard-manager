// Verificar autenticação ao carregar página
window.addEventListener("load", async () => {
  const funcionarioNome = localStorage.getItem("funcionario_nome");
  if (!funcionarioNome) {
    // Sem dados de autenticação, redirecionar para login
    window.location.href = "../FuncionarioLogin.html";
    return;
  }

  await loadSalesHistory();
});

async function loadSalesHistory() {
  const loadingDiv = document.getElementById("loading");
  const errorDiv = document.getElementById("errorMessage");
  const statsDiv = document.getElementById("stats");
  const salesTable = document.getElementById("salesTable");
  const noSalesDiv = document.getElementById("noSales");

  try {
    const url = "../../../controllers/GetSalesHistory.php";
    console.log("Tentando acessar URL:", url);
    console.log("URL absoluta seria:", window.location.origin + "/dashboard-manager/controllers/GetSalesHistory.php");
    
    const response = await fetch(url, {
      method: "GET",
      credentials: "include",
    });

    console.log("Response Status:", response.status);
    console.log("Response URL:", response.url);
    const responseText = await response.text();
    console.log("Response Text:", responseText);
    let result;

    try {
      result = JSON.parse(responseText);
    } catch (e) {
      console.error("Erro ao fazer parse do JSON:", e);
      console.error("Resposta:", responseText);
      throw new Error("Erro ao processar resposta do servidor");
    }

    if (!result.success) {
      throw new Error(result.message || "Erro ao buscar vendas");
    }

    loadingDiv.style.display = "none";

    if (result.vendas.length === 0) {
      noSalesDiv.style.display = "block";
      return;
    }

    // Exibir estatísticas
    document.getElementById("totalSales").textContent = result.totalVendas;
    document.getElementById("totalValue").textContent =
      "R$ " +
      result.valorTotal.toLocaleString("pt-BR", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });
    statsDiv.style.display = "grid";

    // Preencher tabela
    const tbody = document.getElementById("salesBody");
    tbody.innerHTML = "";

    result.vendas.forEach((venda) => {
      const row = document.createElement("tr");
      row.innerHTML = `
        <td>${escapeHtml(venda.cliente)}</td>
        <td>${escapeHtml(venda.produto)}</td>
        <td class="price">R$ ${parseFloat(venda.price).toLocaleString("pt-BR", { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
      `;
      tbody.appendChild(row);
    });

    salesTable.style.display = "table";
  } catch (err) {
    console.error("Erro ao carregar histórico:", err);
    loadingDiv.style.display = "none";
    errorDiv.textContent =
      err.message || "Erro ao carregar histórico de vendas";
    errorDiv.classList.add("show");
  }
}

// Função para escapar caracteres HTML e evitar XSS
function escapeHtml(text) {
  const map = {
    "&": "&amp;",
    "<": "&lt;",
    ">": "&gt;",
    '"': "&quot;",
    "'": "&#039;",
  };
  return text.replace(/[&<>"']/g, (m) => map[m]);
}

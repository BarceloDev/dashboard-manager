<?php

require_once __DIR__ . '/../../../config/connection.php';

class DashBoard
{
    private $conn;

    public function __construct($dbConnection)
    {
        $this->conn = $dbConnection;
    }

    public function getEnterpriseStats($enterpriseId)
    {
        $stats = [];

        // Total Users
        $query = "SELECT COUNT(*) as total_users FROM notesRegister WHERE enterprise_id = :enterpriseId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':enterpriseId', $enterpriseId);
        $stmt->execute();
        $stats['total_users'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_users'];

        // Active Projects
        $query = "SELECT COUNT(*) as active_projects FROM projects WHERE enterprise_id = :enterpriseId AND status = 'active'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':enterpriseId', $enterpriseId);
        $stmt->execute();
        $stats['active_projects'] = $stmt->fetch(PDO::FETCH_ASSOC)['active_projects'];

        // Pending Tasks
        $query = "SELECT COUNT(*) as pending_tasks FROM tasks WHERE enterprise_id = :enterpriseId AND status = 'pending'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':enterpriseId', $enterpriseId);
        $stmt->execute();
        $stats['pending_tasks'] = $stmt->fetch(PDO::FETCH_ASSOC)['pending_tasks'];

        return $stats;
    }

    // Retorna todas as vendas registradas em notesRegister
    public function getSales()
    {
        $query = "SELECT id, vendedor, cliente, produto, price FROM notesRegister";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Retorna o lucro total (soma da coluna price) das vendas
    public function getTotalProfit()
    {
        $query = "SELECT COALESCE(SUM(price), 0) as total_profit FROM notesRegister";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return isset($row['total_profit']) ? (float) $row['total_profit'] : 0.0;
    }

    // Gera o HTML com cards para cada venda (retorna string)
    public function renderSalesCards(array $sales)
    {
        $html = '';
        foreach ($sales as $sale) {
            $produto = htmlspecialchars($sale['produto'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $vendedor = htmlspecialchars($sale['vendedor'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $cliente = htmlspecialchars($sale['cliente'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $price = number_format($sale['price'], 2, ',', '.');

            $html .= '<div class="sale-card card" style="width: 18rem; margin: 10px;">';
            $html .= '<div class="card-body">';
            $html .= '<h5 class="card-title">'.$produto.'</h5>';
            $html .= '<h6 class="card-subtitle mb-2 text-muted">Vendedor: '.$vendedor.'</h6>';
            $html .= '<p class="card-text">Cliente: '.$cliente.'</p>';
            $html .= '<p class="card-text"><strong>Preço: R$ '.$price.'</strong></p>';
            $html .= '</div></div>';
        }
        return $html;
    }
}

// Se o arquivo for acessado diretamente pelo navegador, renderiza a página com os cards
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    // $pdo é definido em connection.php
    $dashboard = new DashBoard($pdo);
    $sales = $dashboard->getSales();

    $cardsHtml = $dashboard->renderSalesCards($sales);
    $totalProfit = $dashboard->getTotalProfit();
    $totalFormatted = number_format($totalProfit, 2, ',', '.');

    ?>
    <!doctype html>
    <html lang="pt-BR">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link
            rel="stylesheet"
            href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
        />
        <link rel="stylesheet" href="../../style/global.css" />
        <link rel="stylesheet" href="../../style/DashBoard.css" />
        <title>Dashboard da empresa</title>
    </head>
    <body>
    <div class="container">
        <div class="box">
            <div class="title">
                <h2>Vendas Registradas</h2>
                <h4>Lucro total: R$ <?php echo $totalFormatted; ?></h4>
            </div>
            <div class="line"></div>
            <div class="dashboard-sales">
                <?php if (empty($sales)): ?>
                    <div class="alert alert-info">Nenhuma venda registrada.</div>
                <?php else: ?>
                    <div class="sales-container">
                        <?php echo $cardsHtml; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    </body>
    </html>
    <?php
}

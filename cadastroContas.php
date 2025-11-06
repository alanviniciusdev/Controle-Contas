<?php
require_once 'conexao.php';

$status = '';

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $conta = trim($_POST['conta'] ?? '');
    $valor = floatval($_POST['valor'] ?? '');
    $vencimento = trim($_POST['vencimento'] ?? '');
    $status = $_POST['status'] ?? '';

    if (empty($conta) || empty($valor) || empty($vencimento) || $status === "") {
        $mensagem = "⚠️ Preencha todos os campos!";
    } elseif ($valor <= 0) {
        $mensagem = "⚠️ O valor deve ser maior que 0!";
    } elseif (strtotime($vencimento) < strtotime(date("Y-m-d"))) {
        $mensagem = "⚠️ A data de vencimento não pode ser anterior a hoje!";
    } else {
        $stmt = $conn->prepare("INSERT INTO contas (nome, valor, vencimento, paga) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sdsi", $conta, $valor, $vencimento, $status);

        if ($stmt->execute()) {
            $mensagem = $status == 1 ? "✅ Conta paga cadastrada com sucesso!" : "✅ Conta pendente cadastrada com sucesso!";
        } else {
            $mensagem = "❌ Erro ao cadastrar conta!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cadastro de Contas</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f3f6fa;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100vh;
        margin: 0;
    }

    h1 {
        color: #333;
        margin-bottom: 20px;
    }

    .form-container {
        background-color: #fff;
        padding: 30px 40px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        width: 320px;
    }

    form {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    input, select {
        padding: 10px 12px;
        border: 1px solid #ccc;
        border-radius: 8px;
        font-size: 15px;
        transition: 0.3s;
    }

    input:focus, select:focus {
        border-color: #0078d7;
        outline: none;
        box-shadow: 0 0 5px rgba(0,120,215,0.3);
    }

    button {
        padding: 10px;
        border: none;
        border-radius: 8px;
        background-color: #0078d7;
        color: white;
        font-weight: bold;
        cursor: pointer;
        transition: 0.3s;
    }

    button:hover {
        background-color: #005fa3;
    }

    .link-btn {
        background-color: #6c757d;
        text-decoration: none;
        display: inline-block;
        text-align: center;
    }

    .link-btn:hover {
        background-color: #5a6268;
    }

    .mensagem {
        margin-bottom: 15px;
        text-align: center;
        font-weight: bold;
        padding: 10px;
        border-radius: 8px;
    }

    .mensagem.ok {
        background-color: #d4edda;
        color: #155724;
    }

    .mensagem.erro {
        background-color: #f8d7da;
        color: #721c24;
    }

</style>
</head>
<body>

<div class="form-container">
    <h1>Cadastrar Conta</h1>

    <?php if (!empty($mensagem)): ?>
        <div class="mensagem <?= strpos($mensagem, '✅') !== false ? 'ok' : 'erro' ?>">
            <?= htmlspecialchars($mensagem) ?>
        </div>
    <?php endif; ?>

    <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST">
        <input type="text" name="conta" placeholder="Tipo de Conta">
        <input type="number" name="valor" step="0.01" placeholder="Valor da Conta">
        <input type="date" name="vencimento">

        <label for="status">Status da Conta</label>
        <select name="status" required>
            <option value="">Selecione o Status</option>
            <option value="1" <?= $status == 1 ? 'selected' : '' ?>>Paga</option>
            <option value="0" <?= $status == 0 ? 'selected' : '' ?>>Pendente</option>
        </select>

        <button type="submit">Cadastrar Conta</button>
    </form>

    <a href="listarContas.php" class="link-btn" style="margin-top:15px;">
        <button class="link-btn">📋 Listar Contas</button>
    </a>
</div>

</body>
</html>

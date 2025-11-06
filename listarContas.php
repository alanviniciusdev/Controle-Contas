<?php
require_once 'conexao.php';

// Atualiza o status se o formulário for enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['novo_status'])) {
    $id = intval($_POST['id']);
    $novo_status = intval($_POST['novo_status']);

    $stmt = $conn->prepare("UPDATE contas SET paga = ? WHERE id = ?");
    $stmt->bind_param("ii", $novo_status, $id);
    $stmt->execute();
    echo "<div class='container-msg'";
    echo "<p id='mensagem'><span><i class='fa-solid fa-check'></i></span>Status atualizado com sucesso!</p>";
    echo "</div>";
}

// Busca os dados atualizados
$sql = "SELECT * FROM contas";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Contas</title>
    <link rel="stylesheet" href="css/lista.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        .container-msg {
            width: 320px;
            height: 120px;
            background-color: green;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 2px;
            box-shadow: 5px 5px 30px -10px rgba(0,0,0,0.75);
-webkit-box-shadow: 5px 5px 30px -10px rgba(0,0,0,0.75);
-moz-box-shadow: 5px 5px 30px -10px rgba(0,0,0,0.75);
        }

        #mensagem {
            font-size: 18px;
        }

        #mensagem span {
            font-size: 20px;
        }
        

        #mensagem {
            transition: opacity 0.3s ease;
            color: #fff;
            animation: desaparecer 3s forwards;
        }

        #mensagem.hidden {
            opacity: 0;
        }
    </style>
</head>
<body>

    <div class="container">

        <h1>Sua Lista de Contas</h1>

        <?php
        if ($result->num_rows > 0) {
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>Conta</th><th>Valor</th><th>Vencimento</th><th>Status</th><th>Ação</th></tr>";

            while ($conta = $result->fetch_assoc()) {
                $statusClass = $conta['paga'] ? 'status-pago' : 'status-pendente';
                $statusTexto = $conta['paga'] ? 'Paga' : 'Pendente';

                echo "<tr>";
                echo "<td>" . htmlspecialchars($conta['nome']) . "</td>";
                echo "<td>" . 'R$ ' . number_format($conta['valor'], 2 , '.', '.') . "</td>";
                echo "<td>" . date('d/m/Y', strtotime($conta['vencimento'])) . "</td>";
                echo "<td class='$statusClass'>$statusTexto</td>";
                echo "<td>
                    <form method='POST' style='display:inline'>
                        <input type='hidden' name='id' value='{$conta['id']}'>
                        <select name='novo_status'>
                            <option value='1' " . ($conta['paga'] ? 'selected' : '') . ">Paga</option>
                            <option value='0' " . (!$conta['paga'] ? 'selected' : '') . ">Pendente</option>
                        </select>
                        <button type='submit'>Atualizar</button>
                    </form>
                </td>";
                echo "</tr>";
            }

            echo "</table>";
        } else {
            echo "Nenhuma conta cadastrada.";
        }
        ?>

    <a href="cadastroContas.php">
        <button class="cadastrar-btn">Cadastrar Nova Conta</button>
    </a>
    </div>
    
    <script>
        setTimeout(() => {
            const msg = document.getElementById("mensagem");
            if (msg) msg.classList.add("hidden");
        }, 3000);
    </script>
</body>
</html>
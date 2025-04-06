<?php
session_start();
if (!isset($_SESSION['username'])) { // Verifica se o utilizador está logado
    header("refresh:5;url=auth/login.php");
    die("Acesso Restrito");
}

$temperatura = array( // Array associativa para armazenar os dados da temperatura
    "valor" => file_get_contents("api/temperatura/valor.txt"),
    "hora" => file_get_contents("api/temperatura/hora.txt"),
    "nome" => file_get_contents("api/temperatura/nome.txt"),
    "log" => file_get_contents("api/temperatura/log.txt")
);

$servo = array( // Array associativa para armazenar os dados da servo
    "valor" => file_get_contents("api/servo/valor.txt"),
    "hora" => file_get_contents("api/servo/hora.txt"),
    "nome" => file_get_contents("api/servo/nome.txt"),
    "log" => file_get_contents("api/servo/log.txt")
);

$us = array( // Array associativa para armazenar os dados da ultrasonico
    "valor" => file_get_contents("api/ultrasonico/valor.txt"),
    "hora" => file_get_contents("api/ultrasonico/hora.txt"),
    "nome" => file_get_contents("api/ultrasonico/nome.txt"),
    "log" => file_get_contents("api/ultrasonico/log.txt")
);

function formatNumber($number)
{ // Formata o número para duas casas decimais e remove zeros à direita
    return rtrim(rtrim(number_format($number, 2, '.', ''), '0'), '.');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="5">
    <link rel="icon" href="assets/imagens/favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>

<body>
    <?php include("add-ons/nav.php"); ?>
    <div class="container">
        <div class="row mt-4">
            <div id="title-header" class="col-md-3 ps-5">
                <h1>Servidor IoT</h1>
                <h6 id="user-name"><img src="assets/imagens/user.png" alt="Fotografia do utilizador" width="25"> <?php echo $_SESSION['username'] ?></h6>
            </div>
            <div id="clock-header" class="col-md-6">
                <div class="world-clock">
                    <div class="clock">
                        <h4>Lisboa, Portugal</h4>
                        <p id="lisbon-time"></p>
                    </div>
                    <div class="clock">
                        <h4>New York, USA</h4>
                        <p id="newyork-time"></p>
                    </div>
                    <div class="clock">
                        <h4>Tokyo, Japão</h4>
                        <p id="tokyo-time"></p>
                    </div>
                </div>
            </div>
            <div id="logo-header" class="col-md-3 pe-5">
                <a href="https://www.ipleiria.pt/estg" target="_blank"><img class="estg" src="assets/imagens/estg.png"
                        alt="Politécnico de Leiria"></a>
            </div>
        </div>
        <div class="row mt-2 mb-2">
            <div class="col-sm-4 mb-2">
                <div class="card text-center">
                    <div class="card-header sensor">
                        <p class="mb-0">Temperatura: <?php echo formatNumber($temperatura['valor']); ?>º</p>
                    </div>
                    <div class="card-body">
                        <img src="assets/imagens/temperature-high.png" alt="temperatura">
                    </div>
                    <div class="card-footer">
                        <p class="mb-0"><span class="fw-bold">Atualizado: </span><?php echo $temperatura["hora"]; ?> - <a
                                href="history.php?nome=temperatura">Histórico</a></p>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 mb-2">
                <div class="card text-center">
                    <div class="card-header sensor">
                        <p class="mb-0">Cancela: <?php echo $servo['valor']; ?> </p>
                    </div>
                    <div class="card-body">
                        <img src="assets/imagens/humidity-high.png" alt="humidade">
                    </div>
                    <div class="card-footer">
                        <p class="mb-0"><span class="fw-bold">Atualizado: </span><?php echo $servo["hora"]; ?> - <a
                                href="history.php?nome=servo">Histórico</a></p>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 mb-2">
                <div class="card text-center">
                    <div class="card-header atuador">
                        <p class="mb-0">Distancia Cancela: <?php echo formatNumber($temperatura['valor']); ?>cm </p>
                    </div>
                    <div class="card-body">
                        <img src="assets/imagens/light-on.png" alt="led">
                    </div>
                    <div class="card-footer">
                        <p class="mb-0"><span class="fw-bold">Atualizado: </span><?php echo $us["hora"]; ?> <a
                                href="history.php?nome=ultrasonico">Histórico</a></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4 mb-4">
            <div class="card">
                <div class="card-header fw-bold">
                    Tabela de Sensores
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped text-center">
                        <thead class="table-dark">
                            <tr>
                                <th>Tipo de Dispositivo IoT</th>
                                <th>Valor</th>
                                <th>Data de Atualização</th>
                                <th>Estado Alertas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><a href="history.php?nome=temperatura"><?php echo $temperatura["nome"]; ?></a></td>
                                <td><?php echo formatNumber($temperatura['valor']); ?>º</td>
                                <td><?php echo $temperatura["hora"]; ?></td>
                                <?php
                                switch (true) {
                                    case ($temperatura["valor"] >= 40.00):
                                        echo "<td><span class='badge bg-danger'>Crítico</span></td>";
                                        break;
                                    case ($temperatura["valor"] > 20.00 && $temperatura["valor"] < 40.00):
                                        echo "<td><span class='badge bg-warning'>Elevado</span></td>";
                                        break;
                                    case ($temperatura["valor"] <= 20.00):
                                        echo "<td><span class='badge bg-success'>Normal</span></td>";
                                        break;
                                    default:
                                        echo "<td class='badge bg-secondary'>Número negativo | Erro de sensor!!</td>";
                                        break;
                                }
                                ?>
                            </tr>
                            <tr>
                                <td><a href="history.php?nome=servo"><?php echo $servo["nome"]; ?></a></td>
                                <td><?php echo formatNumber($servo['valor']); ?>º</td>
                                <td><?php echo $servo["hora"]; ?></td>
                                <?php
                                switch (true) {
                                    case "servo":
                                        if ($servo["valor"] >= 80.00) {
                                            echo "<td><span class='badge bg-success'>Fechado</span></td>";
                                        } elseif ($servo["valor"] < 80.00) {
                                            echo "<td><span class='badge bg-danger'>Aberto</span></td>";
                                        }
                                        break;
                                    default:
                                        echo "<td class='badge bg-secondary'>Número negativo | Erro de sensor!!</td>";
                                        break;
                                }
                                ?>
                            </tr>
                            <tr>
                                <td><a href="history.php?nome=ultrasonico"><?php echo $us["nome"]; ?></a></td>
                                <td><?php echo formatNumber($us["valor"]); ?>cm</td>
                                <td><?php echo $us["hora"]; ?></td>
                                <?php
                                switch (true) {
                                    case ($us["valor"] >= 100.00):
                                        echo "<td><span class='badge bg-danger'>Muito Longe</span></td>";
                                        break;
                                    case ($us["valor"] > 50.00 && $us["valor"] < 100.00):
                                        echo "<td><span class='badge bg-danger'>Longe</span></td>";
                                        break;
                                    case ($us["valor"] <= 50.00 && $us["valor"] > 20.00):
                                        echo "<td><span class='badge bg-warning'>+/- Longe</span></td>";
                                        break;
                                    case ($us["valor"] <= 20.00 && $us["valor"] > 10.00):
                                        echo "<td><span class='badge bg-warning'>Perto</span></td>";
                                        break;
                                    case ($us["valor"] <= 10.00 && $us["valor"] > 0.00):
                                        echo "<td><span class='badge bg-success'>Muito Perto</span></td>";
                                        break;
                                    default:
                                        echo "<td class='badge bg-secondary'>Número negativo | Erro de sensor!!</td>";
                                        break;
                                }
                                ?>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php include("add-ons/footer.php"); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="assets/script.js" defer></script>
</body>

</html>
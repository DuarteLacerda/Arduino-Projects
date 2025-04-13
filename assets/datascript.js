async function atualizarSensores() {
  try {
    const resposta = await fetch("api/data.php");
    const dados = await resposta.json();

    for (const sensor in dados) {
      const valorSpan = document.getElementById(`valor-${sensor}`);
      const horaSpan = document.getElementById(`hora-${sensor}`);
      const statuSpan = document.getElementById(`status-${sensor}`);

      if (valorSpan && horaSpan && statuSpan) {
        let valorBruto = dados[sensor].valor.trim(); // valor original
        let valorNum = parseFloat(valorBruto); // para comparação
        let valorFormatado = valorBruto; // para mostrar

        const valorSpanTable = document.querySelector(
          `#tabela-sensores td span#valor-${sensor}`
        );
        const horaSpanTable = document.querySelector(
          `#tabela-sensores td span#hora-${sensor}`
        );

        console.log(
          "Sensor:",
          sensor,
          "Elementos:",
          valorSpan,
          horaSpan,
          statuSpan
        ); // Adicionado para depuração

        if (sensor === "led") {
          const estado = valorBruto == "1" ? "Ligado" : "Desligado";
          valorFormatado = estado;
          switch (estado) {
            case "Ligado":
              statuSpan.innerHTML =
                "<span class='badge bg-success'>Ligado</span>";
              break;
            case "Desligado":
              statuSpan.innerHTML =
                "<span class='badge bg-primary'>Alto</span>";
              break;
          }
        } else if (sensor === "temperatura") {
          valorFormatado = `${valorBruto}ºC`;
          if (valorNum > 40) {
            statuSpan.innerHTML =
              "<span class='badge bg-danger'>Muito Alta</span>";
          } else if (valorNum > 30) {
            statuSpan.innerHTML =
              "<span class='badge bg-warning text-dark'>Alta</span>";
          } else if (valorNum > 20) {
            statuSpan.innerHTML =
              "<span class='badge bg-success'>Normal</span>";
          } else if (valorNum > 10) {
            statuSpan.innerHTML = "<span class='badge bg-primary'>Baixa</span>";
          } else {
            statuSpan.innerHTML =
              "<span class='badge bg-danger'>Muito Baixa</span>";
          }
        } else if (sensor === "humidade") {
          valorFormatado = `${valorBruto}%`;
          if (valorNum > 70) {
            statuSpan.innerHTML = "<span class='badge bg-danger'>Alta</span>";
          } else if (valorNum < 30) {
            statuSpan.innerHTML = "<span class='badge bg-primary'>Baixa</span>";
          } else {
            statuSpan.innerHTML =
              "<span class='badge bg-success'>Normal</span>";
          }
        } else if (sensor === "distancia") {
          valorFormatado = `${valorBruto} cm`;
          if (valorNum < 10) {
            statuSpan.innerHTML = "<span class='badge bg-danger'>Perigo</span>";
          } else if (valorNum < 20) {
            statuSpan.innerHTML =
              "<span class='badge bg-warning'>Alerta</span>";
          } else {
            statuSpan.innerHTML =
              "<span class='badge bg-success'>Segurança</span>";
          }
        } else if (sensor === "angulo") {
          valorFormatado = `${valorBruto}º`;
          if (valorNum > 90) {
            statuSpan.innerHTML = "<span class='badge bg-danger'>Alto</span>";
          } else if (valorNum < 30) {
            statuSpan.innerHTML = "<span class='badge bg-primary'>Baixo</span>";
          } else {
            statuSpan.innerHTML =
              "<span class='badge bg-success'>Estável</span>";
          }
        } else if (sensor === "ventoinha") {
          valorFormatado = `${valorBruto} RPM`;
          if (valorNum > 2000) {
            statuSpan.innerHTML = "<span class='badge bg-danger'>Alto</span>";
          } else if (valorNum < 1000) {
            statuSpan.innerHTML = "<span class='badge bg-primary'>Baixo</span>";
          } else {
            statuSpan.innerHTML =
              "<span class='badge bg-success'>Normal</span>";
          }
        }

        valorSpan.textContent = ` ${valorFormatado}`;
        horaSpan.textContent = dados[sensor].hora;
        valorSpanTable.textContent = ` ${valorFormatado}`;
        horaSpanTable.textContent = dados[sensor].hora;
      }
    }
  } catch (erro) {
    console.error("Erro ao carregar os dados dos sensores:", erro);
  }
}

setInterval(atualizarSensores, 1000);

<?php
require_once __DIR__ . '/verifica.php';
$flash = flash_take();
?>
<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=League+Gothic&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="../css/doar.css">
    <title> Adota Pet </title>
  </head>

  <body>
    <header>
      <a href="inicio.php" class="logo"> Adota Pet </a>

      <nav>
        <a href="inicio.php"> Home </a>
        <a href="institucional2.html"> Institucional </a>
        <a href="ong2.html"> ONG's </a>
        <a href="adote2.html"> Quero Adotar </a>
        <a href="doar.php"> Quero Doar </a>
        <a href="denuncia.php" class="active"> Denuncia </a>
        <a href="logout.php"> Sair </a>
      </nav>
      <div class="trilho" id="trilho">
        <div class="indicador"></div>
    </div>
    </header>

    <section class="container">
      <div class="list">

          <div class="item active">
              <div class="animal-img">
                  <img src="../img/animal2.webp" alt="">
              </div>
              <div class="content">
                  <p class="animal-information">
                    Conheça os canais de denúncia e as leis que protegem os animais
                  </p>
                  <h2>
                    Denuncie maus-tratos
                  </h2>
                  <p class="description">
                    Testemunhou ou suspeita de maus-tratos a animais? Não se cale! 
                    Denuncie e ajude a proteger aqueles que não podem se defender.
                  </p>
              </div>
          </div>
      </div>

    </section>

    <main id="form_container">
      <div id="form_header">
        <h1 id="form_title">
          Formulário para denúncia
        </h1>
      </div>

      <form action="efetuar_denuncia.php" method="POST">
        <input type="hidden" name="hidden" value="1">
        <input type="hidden" name="csrf_token" value="<?= escape_html(csrf_token()) ?>">
        <?= render_flash_message($flash) ?>
        <div id="input_container">
          <div class="input-box">
            <label for="titulo" class="form-label">Titulo da denúncia</label>
          <div class="input-field">
            <input type="text" name="titulo" id="titulo" class="form-control" maxlength="160" required>
            <i class="fa-solid fa-paw"></i>
          </div>
        </div>

          <div class="input-box">
            <label for="data_denuncia" class="form-label">Data da denúncia</label>
          <div class="input-field">
            <input type="date" name="data_denuncia" id="data_denuncia" class="form-control" required>
            <i class="fa-solid fa-paw"></i>
          </div>
        </div>

          <div class="input-box">
            <label for="descricao" class="form-label">Descrição da denúncia</label>
          <div class="input-field">
            <textarea name="descricao" id="descricao" class="form-control" minlength="20" maxlength="4000" required></textarea>
            <i class="fa-solid fa-paw"></i>
          </div>
        </div>
        </div>

        <button type="submit" class="btn-default">
          <i class="fa-solid fa-check">
            Enviar
          </i>
        </button>
      </form>
    </main>

    <section id="adote" class="section-p1 section-m1">
      <div class="adotetext">
          <h4>Adote um amiguinho</h4>
          <p>Eles estão esperando por você!</p>
      </div>
    </section>

    <footer class="section-p1">
      <div class="col">
          <img class="logo" src="../img/logo.webp" width="90" alt="">
          <h4>Contato</h4>
          <p><strong>Endereço:</strong> Rincão, SP</p>
          <p><strong>Telefone:</strong>+55169999-9999 | +55169999-9999</p>
          <div class="follow">
              <h4>Nos siga</h4>
              <div class="icon">
                  <a href="https://www.linkedin.com/in/andersonluiscosta/" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn de Anderson Luis Costa"><i class="fab fa-linkedin" aria-hidden="true"></i></a>
                  <a href="https://github.com/andertrunks" target="_blank" rel="noopener noreferrer" aria-label="GitHub de Anderson Luis Costa"><i class="fab fa-github" aria-hidden="true"></i></a>
                  <a href="https://www.facebook.com/andersonluis.costa" target="_blank" rel="noopener noreferrer" aria-label="Facebook de Anderson Luis Costa"><i class="fab fa-facebook" aria-hidden="true"></i></a>
                  <a href="https://www.instagram.com/anderluiscosta/" target="_blank" rel="noopener noreferrer" aria-label="Instagram de Anderson Luis Costa"><i class="fab fa-instagram" aria-hidden="true"></i></a>
                  <a href="https://x.com/anderluiscosta" target="_blank" rel="noopener noreferrer" aria-label="X de Anderson Luis Costa"><i class="fab fa-x-twitter" aria-hidden="true"></i></a>
              </div>
          </div>
      </div>

      <div class="col">
          <h4>Sobre</h4>
          <a href="#">Sobre Nós</a>
          <a href="#">Perguntas Frequentes</a>
          <a href="#">Política de Privacidade</a>
          <a href="#">Termos & Condições</a>
          <a href="#">Entre em contato</a>
      </div>

      <div class="col">
          <h4>Colabore</h4>
          <a href="#">Doe qualquer valor</a>
          <a href="#">Seja uma Empresa Parceira</a>
      </div>

      <div class="copyright">
          <p>&copy 2024, Todos os direitos reservados - Naju</p>
      </div>
    </footer>



    <script src="../js/scripts.js"></script>
  </body>
</html>

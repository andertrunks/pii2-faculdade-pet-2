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
        <a href="adote.php"> Quero Adotar </a>
        <a href="doar.php" class="active"> Quero Doar </a>
        <a href="denuncia.php"> Denuncia </a>
        <a href="logout.php"> Sair </a>
      </nav>
      <button type="button" class="trilho" id="trilho" aria-label="Alternar tema claro e escuro" aria-pressed="false"><span class="indicador" aria-hidden="true"></span></button>
    </header>

    <section class="container">
      <div class="list">

          <div class="item active">
              <div class="animal-img">
                  <img src="../img/pets.png" alt="">
              </div>
              <div class="content">
                  <p class="animal-information">
                      Doação responsável
                  </p>
                  <h2>
                      Coloque um pet para adoção
                  </h2>
                  <p class="description">
                      Se você precisa encontrar um novo lar para um animal, conte com a nossa ajuda.
                      Divulgue informações e características do pet para encontrar um tutor responsável.
                  </p>
              </div>
          </div>
      </div>

    </section>

    <main id="form_container">
      <div id="form_header">
        <h1 id="form_title">
          Formulário para doar
        </h1>
      </div>

      <form action="cadastro_doar.php" method="POST">
        <input type="hidden" name="hidden" value="1">
        <input type="hidden" name="csrf_token" value="<?= escape_html(csrf_token()) ?>">
        <?= render_flash_message($flash) ?>
        <div id="input_container">
          <div class="input-box">
            <label for="nome_pet" class="form-label">Nome do pet</label>
          <div class="input-field">
            <input type="text" name="nome_pet" id="nome_pet" class="form-control" placeholder="Kiara" maxlength="120" required>
            <i class="fa-solid fa-paw"></i>
          </div>
        </div>

          <div class="input-box">
            <label for="idade_pet" class="form-label">Idade do pet</label>
          <div class="input-field">
            <input type="text" name="idade_pet" id="idade_pet" class="form-control" placeholder="3 anos" maxlength="40" required>
            <i class="fa-solid fa-paw"></i>
          </div>
        </div>

          <div class="input-box">
            <label for="nome" class="form-label">Nome para contato</label>
          <div class="input-field">
            <input type="text" name="nome" id="nome" class="form-control" placeholder="João" autocomplete="name" maxlength="120" required>
            <i class="fa-solid fa-paw"></i>
          </div>
        </div>

          <div class="input-box">
            <label for="tel" class="form-label">Telefone para contato</label>
          <div class="input-field">
            <input type="tel" name="telefone" id="telefone" class="form-control" placeholder="(00) 00000-0000" autocomplete="tel" maxlength="25" required>
            <i class="fa-solid fa-paw"></i>
          </div>
        </div>

          <div class="input-box">
            <label for="email" class="form-label">Email para contato</label>
          <div class="input-field">
            <input type="email" name="email" id="email" class="form-control" placeholder="exemplo@gmail.com" autocomplete="email" maxlength="254" required>
            <i class="fa-solid fa-paw"></i>
          </div>
        </div>

          <div class="input-box">
            <label for="cep" class="form-label">CEP</label>
          <div class="input-field">
            <input type="text" name="cep" id="cep" class="form-control" placeholder="00000-000" inputmode="numeric" pattern="[0-9]{5}-?[0-9]{3}" maxlength="9" autocomplete="postal-code" required>
            <i class="fa-solid fa-paw"></i>
          </div>
        </div>

          <div class="input-box">
            <label for="cidade" class="form-label">Cidade</label>
          <div class="input-field">
            <input type="text" name="cidade" id="cidade" class="form-control" placeholder="São Paulo" autocomplete="address-level2" maxlength="120" required>
            <i class="fa-solid fa-paw"></i>
          </div>
        </div>

          <div class="input-box">
            <label for="uf" class="form-label">UF</label>
          <div class="input-field">
            <input type="text" name="uf" id="uf" class="form-control" placeholder="SP" autocomplete="address-level1" pattern="[A-Za-z]{2}" maxlength="2" required>
            <i class="fa-solid fa-paw"></i>
          </div>
        </div>

          <div class="input-box">
            <label for="sobre" class="form-label">Sobre o pet</label>
          <div class="input-field">
            <textarea name="sobre" id="sobre" class="form-control" placeholder="Descrição do pet" minlength="10" maxlength="2000" required></textarea>
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
          <p><strong>Atendimento:</strong> <a href="informacoes.html#contato">fale com a equipe</a></p>
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
          <a href="institucional.html">Sobre o projeto</a>
          <a href="informacoes.html#faq">Perguntas frequentes</a>
          <a href="informacoes.html#privacidade">Política de privacidade</a>
          <a href="informacoes.html#termos">Termos de uso</a>
          <a href="informacoes.html#contato">Entre em contato</a>
      </div>

      <div class="col">
          <h4>Colabore</h4>
          <a href="login.php">Cadastre-se para colaborar</a>
          <a href="informacoes.html#parcerias">Seja uma organização parceira</a>
      </div>

      <div class="copyright">
          <p>&copy; 2026 Adota Pet. Projeto acadêmico.</p>
      </div>
    </footer>



    <script src="../js/scripts.js"></script>
  </body>
</html>

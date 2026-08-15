<?php
require_once __DIR__ . '/verifica.php';
require_once __DIR__ . '/AnimalRepository.php';
$animalId = filter_var($_GET['animal_id'] ?? null, FILTER_VALIDATE_INT);
$animal = $animalId === false || $animalId < 1 ? null : (new AnimalRepository($pdo))->find($animalId);
if ($animal === null || (string) $animal['status'] !== 'disponivel') {
    redirect_with_flash('adote.php', 'error', 'Escolha um animal disponível antes de preencher o formulário.');
}
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
        <a href="adote.php" class="active"> Quero Adotar </a>
        <a href="doar.php"> Quero Doar </a>
        <a href="denuncia.php"> Denuncia </a>
        <a href="logout.php"> Sair </a>
      </nav>
      <button type="button" class="trilho" id="trilho" aria-label="Alternar tema claro e escuro" aria-pressed="false"><span class="indicador" aria-hidden="true"></span></button>
    </header>

    <section class="container">
      <div class="list">

          <div class="item active">
              <div class="animal-img">
                  <img src="<?= escape_html((string) $animal['imagem']) ?>" alt="<?= escape_html((string) $animal['nome']) ?>, animal escolhido para adoção">
              </div>
              <div class="content">
                  <p class="animal-information">
                      As informações fornecidas serão confidenciais e usadas apenas para fins de avaliação do processo de adoção
                  </p>
                  <h2>
                      Adoção responsável
                  </h2>
                  <p class="description">
                      Agradecemos o seu interesse em adotar um de nossos animais! Para garantir o 
                      bem-estar dos nossos pets e encontrar o lar ideal para cada um, pedimos que 
                      você preencha este formulário com atenção.
                  </p>
              </div>
          </div>
      </div>

    </section>

    <main id="form_container">
      <div id="form_header">
        <h1 id="form_title">
          Solicitação para adotar <?= escape_html((string) $animal['nome']) ?>
        </h1>
      </div>

      <form action="recebe_form.php" method="POST">
        <input type="hidden" name="animal_id" value="<?= (int) $animal['id_animal'] ?>">
        <input type="hidden" name="csrf_token" value="<?= escape_html(csrf_token()) ?>">
        <?= render_flash_message($flash) ?>
        <div id="input_container">
          <div class="input-box">
            <label for="nome" class="form-label">Nome completo</label>
          <div class="input-field">
            <input type="text" name="nome" id="nome" class="form-control" autocomplete="name" maxlength="120" required>
            <i class="fa-solid fa-paw"></i>
          </div>
        </div>

          <div class="input-box">
            <label for="telefone" class="form-label">Telefone</label>
          <div class="input-field">
            <input type="tel" name="telefone" id="telefone" class="form-control" autocomplete="tel" maxlength="25" required>
            <i class="fa-solid fa-paw"></i>
          </div>
        </div>

          <div class="input-box">
            <label for="idade" class="form-label">Idade</label>
          <div class="input-field">
            <input type="number" name="idade" id="idade" class="form-control" min="18" max="120" required>
            <i class="fa-solid fa-paw"></i>
          </div>
        </div>

          <div class="input-box">
            <label for="profissao" class="form-label">Profissão</label>
          <div class="input-field">
            <input type="text" name="profissao" id="profissao" class="form-control" maxlength="120" required>
            <i class="fa-solid fa-paw"></i>
          </div>
        </div>

          <div class="input-box">
            <label for="residencia" class="form-label">Tipo de residência (casa/ap)</label>
          <div class="input-field">
            <input type="text" name="residencia" id="residencia" class="form-control" maxlength="120" required>
            <i class="fa-solid fa-paw"></i>
          </div>
        </div>

          <div class="input-box">
            <label for="espaco" class="form-label">Há espaço externo seguro para o animal?</label>
          <div class="input-field">
            <input type="text" name="espaco" id="espaco" class="form-control" maxlength="500" required>
            <i class="fa-solid fa-paw"></i>
          </div>
        </div>

          <div class="input-box">
            <label for="acordo" class="form-label">Todos os moradores estão de acordo com a adoção?</label>
          <div class="input-field">
            <input type="text" name="acordo" id="acordo" class="form-control" maxlength="500" required>
            <i class="fa-solid fa-paw"></i>
          </div>
        </div>

          <div class="input-box">
            <label for="teve_animais" class="form-label">Já teve animais de estimação?</label>
          <div class="input-field">
            <input type="text" name="animais" id="animais" class="form-control" maxlength="500" required>
            <i class="fa-solid fa-paw"></i>
          </div>
        </div>

        <div class="input-box">
            <label for="pq_animais" class="form-label">Por que não possui mais esses animais?</label>
          <div class="input-field">
            <input type="text" name="pq_animais" id="pq_animais" class="form-control" maxlength="1000" required>
            <i class="fa-solid fa-paw"></i>
          </div>
        </div>

        <div class="input-box">
            <label for="tempo" class="form-label">Quanto tempo do seu dia poderá dedicar ao animal?</label>
          <div class="input-field">
            <input type="text" name="tempo" id="tempo" class="form-control" maxlength="500" required>
            <i class="fa-solid fa-paw"></i>
          </div>
        </div>

        <div class="input-box">
            <label for="deseja_adotar" class="form-label">Por que deseja adotar um animal de estimação?</label>
          <div class="input-field">
            <input type="text" name="deseja_adotar" id="deseja_adotar" class="form-control" maxlength="1000" required>
            <i class="fa-solid fa-paw"></i>
          </div>
        </div>

        <div class="input-box">
            <label for="ciente" class="form-label">Está ciente da importância da vacinação e vermifugação?</label>
          <div class="input-field">
            <input type="text" name="ciente" id="ciente" class="form-control" maxlength="500" required>
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

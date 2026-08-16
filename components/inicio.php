<?php require_once __DIR__ . '/verifica.php'; ?>
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
    <link rel="stylesheet" href="../css/index.css?v=20260816-1">
    <title> Adota Pet </title>
  </head>

  <body>
    <header>
      <a href="inicio.php" class="logo"> Adota Pet </a>
      <nav>
        <a href="inicio.php" class="active"> Home </a>
        <a href="institucional2.html"> Institucional </a>
        <a href="ong2.html"> ONG's </a>
        <a href="adote.php"> Quero Adotar </a>
        <a href="doar.php"> Quero Doar </a>
        <a href="denuncia.php"> Denuncia </a>
        <a href="logout.php"> Sair </a>
      </nav>
      <button type="button" class="trilho" id="trilho" aria-label="Alternar tema claro e escuro" aria-pressed="false"><span class="indicador" aria-hidden="true"></span></button>
    </header>

    <section class="home">
      <div class="home-img">
        <img src="../img/logo-home.webp" alt="">
      </div>
      <div class="home-content">
        <h1> Bem-vindo(a) ao <span>Adota Pet</span></h1>
        <h3 class="typing-text"> Aqui você pode <span></span></h3>
        <p> Nesta página, você encontrará tudo o que precisa para fazer a diferença na vida de animais em situação de vulnerabilidade. Juntos podemos construir um mundo mais justo e amoroso para os animais. </p>
        <ul class="home-highlights">
          <li class="li-index"> Adote um amigo: Dê um lar cheio de amor para um pet que precisa de uma nova chance.</li>
          <li class="li-index"> Denuncie maus-tratos: Não se cale, proteja os animais e garanta que os responsáveis sejam punidos.</li>
          <li class="li-index"> Apoie ONGs: Conheça o trabalho das ONGs de proteção animal e ajude a salvar vidas.</li>
        </ul>
        <div class="home-actions">
          <div class="social-icons">
            <a href="https://www.linkedin.com/in/andersonluiscosta/" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn de Anderson Luis Costa"><i class="fa-brands fa-linkedin" aria-hidden="true"></i></a>
            <a href="https://github.com/andertrunks" target="_blank" rel="noopener noreferrer" aria-label="GitHub de Anderson Luis Costa"><i class="fa-brands fa-github" aria-hidden="true"></i></a>
            <a href="https://www.facebook.com/andersonluis.costa" target="_blank" rel="noopener noreferrer" aria-label="Facebook de Anderson Luis Costa"><i class="fa-brands fa-facebook" aria-hidden="true"></i></a>
            <a href="https://www.instagram.com/anderluiscosta/" target="_blank" rel="noopener noreferrer" aria-label="Instagram de Anderson Luis Costa"><i class="fa-brands fa-instagram" aria-hidden="true"></i></a>
            <a href="https://x.com/anderluiscosta" target="_blank" rel="noopener noreferrer" aria-label="X de Anderson Luis Costa"><i class="fa-brands fa-x-twitter" aria-hidden="true"></i></a>
          </div>
          <div class="container1">
            <a href="adote.php" class="btn">Vamos adotar?</a>
          </div>
        </div>
      </div>
    </section>

    <section id="feature" class="section-p1">
      <a class="fe-box" href="denuncia.php" aria-label="Denunciar maus-tratos">
          <img src="../img/features/denuncia_icone.webp" alt="">
          <h6>Denuncie Maus Tratos</h6>
      </a>
      <a class="fe-box" href="ong2.html" aria-label="Localizar uma ONG próxima">
        <img src="../img/features/localizacao.png" alt="">
        <h6>Localize uma ONG próxima a você</h6>
      </a>
      <a class="fe-box" href="adote.php" aria-label="Encontrar um pet para adoção">
        <img src="../img/features/pet1_icone.webp" alt="">
        <h6>Ache seu Pet</h6>
      </a>
      <a class="fe-box" href="adote.php" aria-label="Escolher um pet para iniciar a adoção">
        <img src="../img/features/pet2_icone.webp" alt="">
        <h6>Adoção Completa</h6>
      </a>
    </section>

    <section class="container">
      <div class="list">

      <div class="item active">
              <div class="animal-img">
                  <img src="../img/animal1.webp" alt="">
              </div>
              <div class="content">
                  <p class="animal-information">
                      Encontre o pet ideal para você
                  </p>
                  <h2>
                      Adote um amigo
                  </h2>
                  <p class="description">
                      Abra seu coração e lar para um pet que precisa de uma segunda
                      chance. A adoção é um ato de amor que transforma vidas, tanto a
                      do animal quanto a sua.
                  </p>
                  <a class="information" href="adote.php">Saiba Mais</a>
              </div>
          </div>

          <div class="item">
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
                  <a class="information" href="denuncia.php">Saiba Mais</a>
              </div>
          </div>

          <div class="item">
              <div class="animal-img">
                  <img src="../img/animal3.webp" alt="">
              </div>
              <div class="content">
                  <p class="animal-information">
                      Apoie as ONGs e faça a diferença na vida de milhares de animais
                  </p>
                  <h2>
                      ONGs de proteção animal
                  </h2>
                  <p class="description">
                      Descubra o trabalho incrível das ONGs que dedicam suas vidas a
                      resgatar, cuidar e encontrar lares para animais abandonados e 
                      maltratados.
                  </p>
                  <a class="information" href="ong2.html">Saiba Mais</a>
              </div>
          </div>
      </div>


      <div class="arrows">
          <button id="prev" type="button" aria-label="Conteúdo anterior"><img src="../img/arrow.png" alt=""></button>
          <button id="next" type="button" aria-label="Próximo conteúdo"><img src="../img/arrow.png" alt=""></button>
      </div>

      <div class="indicators">
          <div class="number">01</div>
          <ul>
              <li class="active"></li>
              <li></li>
              <li></li>
          </ul>
      </div>
  </section>

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

  <script src="../js/scripts.js?v=20260816-1"></script>
  </body>
</html>

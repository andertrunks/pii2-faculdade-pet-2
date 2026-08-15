<?php

declare(strict_types=1);

function render_app_header(string $active, bool $authenticated): void
{
    $home = $authenticated ? 'inicio.php' : 'index.html';
    $institutional = $authenticated ? 'institucional2.html' : 'institucional.html';
    $ong = $authenticated ? 'ong2.html' : 'ong.html';
    ?>
    <header class="app-header">
      <a href="<?= escape_html($home) ?>" class="logo">Adota Pet</a>
      <nav aria-label="Navegação principal">
        <a href="<?= escape_html($home) ?>"<?= $active === 'home' ? ' class="active" aria-current="page"' : '' ?>>Home</a>
        <a href="<?= escape_html($institutional) ?>"<?= $active === 'institutional' ? ' class="active" aria-current="page"' : '' ?>>Institucional</a>
        <a href="<?= escape_html($ong) ?>"<?= $active === 'ong' ? ' class="active" aria-current="page"' : '' ?>>ONGs</a>
        <a href="adote.php"<?= $active === 'adote' ? ' class="active" aria-current="page"' : '' ?>>Quero Adotar</a>
        <?php if ($authenticated): ?>
          <a href="doar.php">Quero Doar</a>
          <a href="denuncia.php">Denúncia</a>
          <a href="logout.php">Sair</a>
        <?php else: ?>
          <a href="login.php">Entrar</a>
        <?php endif; ?>
      </nav>
      <button type="button" class="trilho" id="trilho" aria-label="Alternar tema claro e escuro" aria-pressed="false"><span class="indicador" aria-hidden="true"></span></button>
    </header>
    <?php
}

function render_app_footer(): void
{
    ?>
    <footer class="section-p1">
      <div class="col">
        <img class="logo" src="../img/logo.webp" width="90" alt="Símbolo do Adota Pet">
        <h2>Adota Pet</h2>
        <p>Plataforma acadêmica de adoção responsável e proteção animal.</p>
      </div>
      <div class="col">
        <h2>Informações</h2>
        <a href="institucional.html">Sobre o projeto</a>
        <a href="informacoes.html#faq">Perguntas frequentes</a>
        <a href="informacoes.html#privacidade">Privacidade</a>
        <a href="informacoes.html#termos">Termos de uso</a>
        <a href="informacoes.html#contato">Contato</a>
      </div>
      <div class="col">
        <h2>Participe</h2>
        <a href="adote.php">Adote com responsabilidade</a>
        <a href="login.php">Cadastre-se para colaborar</a>
        <a href="ong.html">Encontre uma ONG</a>
      </div>
      <div class="copyright"><p>&copy; 2026 Adota Pet. Projeto acadêmico.</p></div>
    </footer>
    <?php
}

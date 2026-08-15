<?php

declare(strict_types=1);

require_once __DIR__ . '/conexao.php';
require_once __DIR__ . '/AnimalRepository.php';
require_once __DIR__ . '/layout.php';

$authenticated = filter_var($_SESSION['id_cadastro'] ?? null, FILTER_VALIDATE_INT) !== false;
$animalId = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
$repository = new AnimalRepository($pdo);
$animal = $animalId === false || $animalId < 1 ? null : $repository->find($animalId);
if ($animal === null) {
    http_response_code(404);
}
$related = $animal === null ? [] : $repository->related((string) $animal['especie'], (int) $animal['id_animal']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../css/adote.css">
  <title><?= $animal === null ? 'Animal não encontrado' : escape_html((string) $animal['nome']) ?> | Adota Pet</title>
</head>
<body>
  <a class="skip-link" href="#conteudo">Pular para o conteúdo</a>
  <?php render_app_header('adote', $authenticated); ?>
  <main id="conteudo">
    <?php if ($animal === null): ?>
      <section class="empty-state section-p1">
        <h1>Animal não encontrado</h1>
        <p>O cadastro solicitado não existe ou não está mais disponível.</p>
        <a class="primary-action" href="adote.php">Voltar ao catálogo</a>
      </section>
    <?php else: ?>
      <section id="prodetails" class="section-p1 animal-detail">
        <div class="single-pro-image">
          <img src="<?= escape_html((string) $animal['imagem']) ?>" alt="<?= escape_html((string) $animal['nome']) ?> para adoção">
        </div>
        <div class="single-pro-details">
          <p class="eyebrow"><?= escape_html((string) $animal['cidade']) ?>, <?= escape_html((string) $animal['uf']) ?></p>
          <h1>Conheça <?= escape_html((string) $animal['nome']) ?></h1>
          <dl class="animal-facts">
            <div><dt>Espécie</dt><dd><?= escape_html((string) $animal['especie']) ?></dd></div>
            <div><dt>Raça</dt><dd><?= escape_html((string) $animal['raca']) ?></dd></div>
            <div><dt>Sexo</dt><dd><?= escape_html((string) $animal['sexo']) ?></dd></div>
            <div><dt>Idade</dt><dd><?= escape_html((string) $animal['idade_texto']) ?></dd></div>
            <div><dt>Porte</dt><dd><?= escape_html((string) $animal['porte']) ?></dd></div>
          </dl>
          <p><?= escape_html((string) $animal['descricao']) ?></p>
          <?php if ((string) $animal['status'] !== 'disponivel'): ?>
            <p class="status-note">Este animal não está recebendo novas solicitações no momento.</p>
          <?php elseif ($authenticated): ?>
            <a class="primary-action" href="formulario_adocao.php?animal_id=<?= (int) $animal['id_animal'] ?>">Quero adotar <?= escape_html((string) $animal['nome']) ?></a>
          <?php else: ?>
            <a class="primary-action" href="login.php">Entre para iniciar a adoção</a>
            <p class="helper-text">O login é necessário para associar a solicitação à sua conta.</p>
          <?php endif; ?>
        </div>
      </section>
      <?php if ($related !== []): ?>
        <section id="prod1" class="section-p1" aria-labelledby="related-title">
          <h2 id="related-title">Outros animais que esperam por um lar</h2>
          <div class="pro-container compact-grid">
            <?php foreach ($related as $item): ?>
              <a class="pro" href="animal.php?id=<?= (int) $item['id_animal'] ?>">
                <img src="<?= escape_html((string) $item['imagem']) ?>" alt="<?= escape_html((string) $item['nome']) ?> para adoção" loading="lazy">
                <div class="des"><span><?= escape_html((string) $item['cidade']) ?>, <?= escape_html((string) $item['uf']) ?></span><h3><?= escape_html((string) $item['nome']) ?></h3><strong><?= escape_html((string) $item['idade_texto']) ?></strong></div>
              </a>
            <?php endforeach; ?>
          </div>
        </section>
      <?php endif; ?>
    <?php endif; ?>
  </main>
  <?php render_app_footer(); ?>
  <script src="../js/darklight.js"></script>
</body>
</html>

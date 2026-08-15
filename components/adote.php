<?php

declare(strict_types=1);

require_once __DIR__ . '/conexao.php';
require_once __DIR__ . '/AnimalRepository.php';
require_once __DIR__ . '/layout.php';

$authenticated = filter_var($_SESSION['id_cadastro'] ?? null, FILTER_VALIDATE_INT) !== false;
$allowedSpecies = ['Cão', 'Gato'];
$species = trim((string) ($_GET['especie'] ?? ''));
$species = in_array($species, $allowedSpecies, true) ? $species : null;
$animals = (new AnimalRepository($pdo))->available($species);
$flash = flash_take();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Conheça os animais disponíveis para adoção responsável no Adota Pet.">
  <link rel="stylesheet" href="../css/adote.css">
  <title>Animais para adoção | Adota Pet</title>
</head>
<body>
  <a class="skip-link" href="#conteudo">Pular para o conteúdo</a>
  <?php render_app_header('adote', $authenticated); ?>
  <?= render_flash_message($flash) ?>
  <main id="conteudo">
    <section class="catalog-hero section-p1">
      <div>
        <p class="eyebrow">Adoção responsável</p>
        <h1>Encontre um novo companheiro</h1>
        <p>Conheça cada história, avalie sua rotina e envie uma solicitação vinculada ao animal escolhido.</p>
      </div>
      <form method="get" class="catalog-filter">
        <label for="especie">Filtrar por espécie</label>
        <select id="especie" name="especie">
          <option value="">Todos</option>
          <?php foreach ($allowedSpecies as $option): ?>
            <option value="<?= escape_html($option) ?>"<?= $species === $option ? ' selected' : '' ?>><?= escape_html($option) ?></option>
          <?php endforeach; ?>
        </select>
        <button type="submit">Aplicar filtro</button>
      </form>
    </section>
    <section id="prod1" class="section-p1" aria-labelledby="catalog-title">
      <h2 id="catalog-title"><?= $species === null ? 'Animais disponíveis' : escape_html($species) . 's disponíveis' ?></h2>
      <?php if ($animals === []): ?>
        <p class="empty-state">Nenhum animal disponível neste filtro no momento.</p>
      <?php else: ?>
        <div class="pro-container">
          <?php foreach ($animals as $animal): ?>
            <a class="pro" href="animal.php?id=<?= (int) $animal['id_animal'] ?>" aria-label="Conhecer <?= escape_html((string) $animal['nome']) ?>">
              <img src="<?= escape_html((string) $animal['imagem']) ?>" alt="<?= escape_html((string) $animal['nome']) ?>, <?= escape_html(strtolower((string) $animal['especie'])) ?> para adoção" loading="lazy">
              <div class="des">
                <span><?= escape_html((string) $animal['cidade']) ?>, <?= escape_html((string) $animal['uf']) ?></span>
                <h3><?= escape_html((string) $animal['nome']) ?></h3>
                <p><?= escape_html((string) $animal['especie']) ?> · <?= escape_html((string) $animal['sexo']) ?> · <?= escape_html((string) $animal['porte']) ?></p>
                <strong><?= escape_html((string) $animal['idade_texto']) ?></strong>
              </div>
            </a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </section>
  </main>
  <?php render_app_footer(); ?>
  <script src="../js/darklight.js"></script>
</body>
</html>

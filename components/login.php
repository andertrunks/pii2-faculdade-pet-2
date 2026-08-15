<?php
require_once __DIR__ . '/functions.php';
start_secure_session();
send_security_headers();
$flash = flash_take();
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="../css/login.css">
    <title> Adota Pet </title>
</head>

<body>
  <a href="index.html" class="back-link" aria-label="Voltar para a página inicial"><i class="fa-solid fa-circle-left" aria-hidden="true"></i> Voltar</a>
    <?= render_flash_message($flash) ?>
    <div class="container" id="container">
        <div class="form-container sign-up">
            <form action="efetuar_login.php" method="POST">
            <input type="hidden" name="hidden" value="1">
            <input type="hidden" name="csrf_token" value="<?= escape_html(csrf_token()) ?>">
                <h1>Cadastrar</h1>
                <span>Crie sua conta com email e senha</span>
                <label class="visually-hidden" for="cadastro-nome">Nome</label>
                <input id="cadastro-nome" type="text" name="name" placeholder="Nome" autocomplete="name" maxlength="120" required>
                <label class="visually-hidden" for="cadastro-email">Email</label>
                <input id="cadastro-email" type="email" name="email" placeholder="Email" autocomplete="email" maxlength="254" required>
                <label class="visually-hidden" for="cadastro-senha">Senha</label>
                <input id="cadastro-senha" type="password" name="password" placeholder="Senha (mínimo de 8 caracteres)" autocomplete="new-password" minlength="8" maxlength="255" required>
                <label class="visually-hidden" for="cep">CEP</label>
                <input type="text" name="cep" id="cep" placeholder="CEP" inputmode="numeric" pattern="[0-9]{5}-?[0-9]{3}" maxlength="9" autocomplete="postal-code" required>
                <p id="cep-status" class="field-status" role="status" aria-live="polite"></p>
                <label class="visually-hidden" for="rua">Rua</label>
                <input type="text" name="rua" id="rua" placeholder="Rua" autocomplete="street-address" maxlength="160" required>
                <label class="visually-hidden" for="cidade">Cidade</label>
                <input type="text" name="cidade" id="cidade" placeholder="Cidade" autocomplete="address-level2" maxlength="120" required>
                <label class="visually-hidden" for="uf">UF</label>
                <input type="text" name="uf" id="uf" placeholder="UF" autocomplete="address-level1" pattern="[A-Za-z]{2}" maxlength="2" required>

                <button type="submit">Cadastrar</button>
            </form>
        </div>
        <div class="form-container sign-in">
            <form action="logar.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?= escape_html(csrf_token()) ?>">
                <h1>Entrar</h1>
                <button type="button" class="trilho" id="trilho" aria-label="Alternar tema claro e escuro" aria-pressed="false"><span class="indicador" aria-hidden="true"></span></button>
                <span>Use seu email e senha cadastrados</span>
                <label class="visually-hidden" for="login-email">Email</label>
                <input id="login-email" type="email" name="email" placeholder="Email" autocomplete="email" maxlength="254" required>
                <label class="visually-hidden" for="login-senha">Senha</label>
                <input id="login-senha" type="password" name="password" placeholder="Senha" autocomplete="current-password" maxlength="255" required>
                <button type="submit">Entrar</button>
            </form>
        </div>
        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-left">
                    <h1>Bem-vindo(a) de volta, aumigo!</h1>
                    <p>Entre para usar todos os recursos do site.</p>
                    <button type="button" class="hidden" id="login">Entrar</button>
                </div>
                <div class="toggle-panel toggle-right">
                    <h1>Olá, Aumigo!</h1>
                    <p>Cadastre-se para usar todos os recursos do site</p>
                    <button type="button" class="hidden" id="register">Cadastrar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="../js/login.js"></script>
</body>

</html>

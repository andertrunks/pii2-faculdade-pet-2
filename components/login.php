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
    <script>
    
        function limpa_formulário_cep() {
                //Limpa valores do formulário de cep.
                document.getElementById('rua').value=("");
                document.getElementById('cidade').value=("");
                document.getElementById('uf').value=("");
        }
    
        function meu_callback(conteudo) {
            if (!("erro" in conteudo)) {
                //Atualiza os campos com os valores.
                document.getElementById('rua').value=(conteudo.logradouro);
                document.getElementById('cidade').value=(conteudo.localidade);
                document.getElementById('uf').value=(conteudo.uf);
            } //end if.
            else {
                //CEP não Encontrado.
                limpa_formulário_cep();
                alert("CEP não encontrado.");
            }
        }
            
        function pesquisacep(valor) {
    
            //Nova variável "cep" somente com dígitos.
            var cep = valor.replace(/\D/g, '');
    
            //Verifica se campo cep possui valor informado.
            if (cep != "") {
    
                //Expressão regular para validar o CEP.
                var validacep = /^[0-9]{8}$/;
    
                //Valida o formato do CEP.
                if(validacep.test(cep)) {
    
                    //Preenche os campos com "..." enquanto consulta webservice.
                    document.getElementById('rua').value="...";
                    document.getElementById('cidade').value="...";
                    document.getElementById('uf').value="...";
    
                    //Cria um elemento javascript.
                    var script = document.createElement('script');
    
                    //Sincroniza com o callback.
                    script.src = 'https://viacep.com.br/ws/'+ cep + '/json/?callback=meu_callback';
    
                    //Insere script no documento e carrega o conteúdo.
                    document.body.appendChild(script);
    
                } //end if.
                else {
                    //cep é inválido.
                    limpa_formulário_cep();
                    alert("Formato de CEP inválido.");
                }
            } //end if.
            else {
                //cep sem valor, limpa formulário.
                limpa_formulário_cep();
            }
        };
    
    </script>
    <title> Adota Pet </title>
</head>

<body>
  <a href="index.html" class="icon"><i class="fa-solid fa-circle-left"> Voltar </i></a>
    <?= render_flash_message($flash) ?>
    <div class="container" id="container">
        <div class="form-container sign-up">
            <form action="efetuar_login.php" method="POST">
            <input type="hidden" name="hidden" value="1">
            <input type="hidden" name="csrf_token" value="<?= escape_html(csrf_token()) ?>">
                <h1>Cadastrar</h1>
                <div class="social-icons">
                    <a href="#" class="icon"><i class="fa-brands fa-google-plus-g"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-instagram"></i></a>
                </div>
                <span>ou use seu email</span>
                <input type="text" name="name" placeholder="Nome" autocomplete="name" maxlength="120" required>
                <input type="email" name="email" placeholder="Email" autocomplete="email" maxlength="254" required>
                <input type="password" name="password" placeholder="Senha (mínimo de 8 caracteres)" autocomplete="new-password" minlength="8" maxlength="255" required>
                <input type="text" name="cep" id="cep" placeholder="CEP" inputmode="numeric" pattern="[0-9]{5}-?[0-9]{3}" maxlength="9" autocomplete="postal-code" onblur="pesquisacep(this.value);" required>
                <input type="text" name="rua" id="rua" placeholder="Rua" autocomplete="street-address" maxlength="160" required>
                <input type="text" name="cidade" id="cidade" placeholder="Cidade" autocomplete="address-level2" maxlength="120" required>
                <input type="text" name="uf" id="uf" placeholder="UF" autocomplete="address-level1" pattern="[A-Za-z]{2}" maxlength="2" required>

                <button>Cadastrar</button>
            </form>
        </div>
        <div class="form-container sign-in">
            <form action="logar.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?= escape_html(csrf_token()) ?>">
                <h1>Entrar</h1>
                <div class="trilho" id="trilho">
                    <div class="indicador"></div>
                </div>
                <div class="social-icons">
                    <a href="#" class="icon"><i class="fa-brands fa-google-plus-g"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-instagram"></i></a>
                </div>
                <span>ou use seu email e senha</span>
                <input type="email" name="email" placeholder="Email" autocomplete="email" maxlength="254" required>
                <input type="password" name="password" placeholder="Senha" autocomplete="current-password" maxlength="255" required>
                <button>Entrar</button>
            </form>
        </div>
        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-left">
                    <h1>Bem-vindo(a) de volta, aumigo!</h1>
                    <p>Entre para usar todos os recursos do site.</p>
                    <button class="hidden" id="login">Entrar</button>
                </div>
                <div class="toggle-panel toggle-right">
                    <h1>Olá, Aumigo!</h1>
                    <p>Cadastre-se para usar todos os recursos do site</p>
                    <button class="hidden" id="register">Cadastrar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="../js/login.js"></script>
</body>

</html>

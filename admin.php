<?php
// =========================================================
// ÁREA DO ADMINISTRADOR
// =========================================================
//
// Esta página permite ao administrador:
//
// 1. Fazer login;
// 2. Visualizar usuários;
// 3. Editar usuários;
// 4. Salvar alterações;
// 5. Excluir usuários.
//
// =========================================================

// =========================================================
// INICIA A SESSÃO
// =========================================================
//
// A sessão será utilizada para saber se o administrador
// já realizou o login.
//
// =========================================================
session_start();

// =========================================================
// CONEXÃO COM O BANCO
// =========================================================
$servidor = "localhost";
$usuario = "root";
$senhaBanco = "";

// IMPORTANTE:
// O nome deve ser o mesmo utilizado no banco.sql
$banco = "portfolio1";

try {
// Cria a conexão usando PDO
$pdo = new PDO(
"mysql:host=$servidor;dbname=$banco;charset=utf8mb4",
$usuario,
$senhaBanco
);

// Configura o PDO para mostrar erros
$pdo->setAttribute(
PDO::ATTR_ERRMODE,
PDO::ERRMODE_EXCEPTION
);

} catch (PDOException $erro) {
// Caso aconteça algum erro na conexão
die(
"Erro ao conectar com o banco de dados: "
. $erro->getMessage()
);
}

// =========================================================
// DADOS DO ADMINISTRADOR
// =========================================================
//
// ATENÇÃO:
//
// Estes dados são apenas para o projeto didático.
//
// Em um sistema real, o login do administrador deve

// ser armazenado de forma segura no banco.
//
// =========================================================
$ADMIN_USUARIO = "admin";
$ADMIN_SENHA = "123456";

// Variável usada para mostrar mensagens
$mensagem = "";

// =========================================================
// LOGIN DO ADMINISTRADOR
// =========================================================
if (isset($_POST["entrar"])) {

// Recebe o usuário digitado
$login =
$_POST["login"] ?? "";

// Recebe a senha digitada
$senha =
$_POST["senha_admin"] ?? "";

// =====================================================
// VERIFICA SE OS DADOS ESTÃO CORRETOS
// =====================================================
if (
$login === $ADMIN_USUARIO &&
$senha === $ADMIN_SENHA
) {

// =================================================
// CRIA UMA VARIÁVEL DE SESSÃO

// =================================================
//
// A partir deste momento sabemos que o usuário
// está autenticado como administrador.
//
// =================================================
$_SESSION["administrador_logado"] = true;

} else {
// Login incorreto
$mensagem =
"Usuário ou senha do administrador inválidos.";
}
}

// =========================================================
// SAIR DO SISTEMA
// =========================================================
if (isset($_GET["sair"])) {

// Destrói a sessão
session_destroy();

// Volta para a página de login
header("Location: admin.php");
exit;
}

// =========================================================
// EXCLUIR USUÁRIO

// =========================================================
//
// Só pode excluir se o administrador estiver logado.
//
// =========================================================
if (
isset($_GET["excluir"]) &&
isset($_SESSION["administrador_logado"])
) {

// Recebe o ID enviado pela URL
$id =
filter_input(
INPUT_GET,
"excluir",
FILTER_VALIDATE_INT
);

// Verifica se o ID é válido
if ($id) {

// =================================================
// DELETE
// =================================================
//
// Remove o usuário cujo ID foi informado.
//
// =================================================
$consulta =
$pdo->prepare(
"DELETE FROM usuarios WHERE id = ?"
);

// Executa o DELETE
$consulta->execute([$id]);

// Mostra mensagem
$mensagem =
"Usuário excluído com sucesso.";
}
}

// =========================================================
// SALVAR ALTERAÇÃO
// =========================================================
//
// Esta parte será executada quando o administrador
// clicar no botão "Salvar".
//
// =========================================================
if (
isset($_POST["salvar"]) &&
isset($_SESSION["administrador_logado"])
) {

// Recebe o ID
$id =
filter_input(
INPUT_POST,
"id",
FILTER_VALIDATE_INT
);

// Recebe o novo nome
$nome =
trim(
$_POST["nome"] ?? ""
);

// Recebe o novo e-mail
$email =
trim(
$_POST["email"] ?? ""
);

// =====================================================
// VALIDAÇÃO
// =====================================================
if (
!$id ||
empty($nome) ||
!filter_var(
$email,
FILTER_VALIDATE_EMAIL
)
) {
$mensagem =
"Informe um ID, nome e e-mail válidos.";
} else {

try {

// =============================================
// UPDATE
// =============================================
//
// Atualiza nome e e-mail do usuário.
//
// =============================================
$consulta =
$pdo->prepare(
"UPDATE usuarios
SET nome = ?, email = ?
WHERE id = ?"
);

// Executa o UPDATE
$consulta->execute([
$nome,
$email,
$id
]);

// Mensagem de sucesso
$mensagem =
"Dados atualizados com sucesso.";

} catch (PDOException $erro) {

// Caso o e-mail já pertença a outro usuário
$mensagem =
"Não foi possível salvar. "
. "Verifique se o e-mail já pertence "
. "a outro usuário.";
}
}
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta

name="viewport"
content="width=device-width, initial-scale=1.0"
>
<title>Administrador</title>

<!-- Utiliza o mesmo CSS do projeto -->
<link
rel="stylesheet"
href="estilo.css"
>
</head>

<body>

<!-- =====================================================
CABEÇALHO
===================================================== -->
<header>
<h1>
Área do Administrador
</h1>

<nav>
<!-- Voltar para o portfólio -->
<a href="index.html">
Voltar para o Portfólio
</a>

<?php if (
isset(
$_SESSION["administrador_logado"]
)

): ?>
|
<!-- Link para sair -->
<a href="admin.php?sair=1">
Sair
</a>
<?php endif; ?>
</nav>
</header>

<main>
<section>

<?php
// =========================================================
// VERIFICA SE O ADMINISTRADOR ESTÁ LOGADO
// =========================================================
if (
!isset(
$_SESSION["administrador_logado"]
)
):
?>

<!-- =================================================
TELA DE LOGIN
================================================= -->
<h2>
Login do Administrador
</h2>

<!-- Mostra mensagem de erro -->
<?php if (!empty($mensagem)): ?>
<p>
<strong>
<?=
htmlspecialchars($mensagem)
?>
</strong>
</p>
<?php endif; ?>

<!-- =================================================
FORMULÁRIO DE LOGIN
================================================= -->
<form
method="POST"
action="admin.php"
>

<!-- USUÁRIO -->
<p>
<label for="login">
Usuário:
</label>
<br>
<input
type="text"
id="login"

name="login"
required
>
</p>

<!-- SENHA -->
<p>
<label for="senha_admin">
Senha:
</label>
<br>
<input
type="password"
id="senha_admin"
name="senha_admin"
required
>
</p>

<!-- BOTÃO ENTRAR -->
<p>
<button
type="submit"
name="entrar"
>
Entrar
</button>
</p>

</form>

<!-- =================================================
DADOS PARA TESTE
================================================= -->
<p>
<small>
Projeto didático:
<br>
Usuário:
<strong>admin</strong>
<br>
Senha:
<strong>123456</strong>
</small>
</p>

<?php else: ?>

<!-- =================================================
ÁREA INTERNA DO ADMINISTRADOR
================================================= -->
<h2>
Usuários cadastrados
</h2>

<!-- =================================================
MOSTRA MENSAGEM DE SUCESSO OU ERRO
================================================= -->
<?php if (!empty($mensagem)): ?>
<p>

<strong>
<?=
htmlspecialchars($mensagem)
?>
</strong>
</p>
<?php endif; ?>

<?php
// =====================================================
// SELECT
// =====================================================
//
// Busca os usuários cadastrados no banco.
//
// =====================================================
$consulta =
$pdo->query(
"SELECT id, nome, email, criado_em
FROM usuarios
ORDER BY id DESC"
);

// Transforma os resultados em um array
$usuarios =
$consulta->fetchAll(
PDO::FETCH_ASSOC
);
?>

<!-- =================================================
VERIFICA SE EXISTEM USUÁRIOS

================================================= -->
<?php if (count($usuarios) === 0): ?>
<p>
Nenhum usuário cadastrado.
</p>

<?php else: ?>

<!-- =================================================
TABELA DE USUÁRIOS
================================================= -->
<table class="tabela-admin">

<!-- CABEÇALHO DA TABELA -->
<thead>
<tr>
<th>
ID
</th>
<th>
Nome
</th>
<th>
E-mail
</th>
<th>
Data
</th>
<th>
Ações
</th>

</tr>
</thead>

<!-- CORPO DA TABELA -->
<tbody>

<?php foreach (
$usuarios as $u
): ?>

<tr>

<!--

=================================================

FORMULÁRIO DE EDIÇÃO
=================================================

-->

<form
method="POST"
action="admin.php"
>

<!-- ID -->
<td>
<?=
(int)$u["id"]
?>

<!-- O ID fica escondido,
mas será enviado para o PHP -->
<input
type="hidden"

name="id"
value="<?=
(int)$u["id"]
?>"
>
</td>

<!-- NOME -->
<td>
<input
type="text"
name="nome"
value="<?=
htmlspecialchars(
$u["nome"]
)
?>"
required
>
</td>

<!-- E-MAIL -->
<td>
<input
type="email"
name="email"
value="<?=
htmlspecialchars(
$u["email"]
)
?>"
required
>
</td>

<!-- DATA DO CADASTRO -->
<td>
<?=
htmlspecialchars(
$u["criado_em"]
)
?>
</td>

<!--

=================================================

BOTÕES
=================================================

-->

<td class="acoes">

<!-- BOTÃO SALVAR -->
<button
class="btn-salvar"
type="submit"
name="salvar"
>
Salvar
</button>

<!-- BOTÃO EXCLUIR -->
<a
href="admin.php?excluir=<?=
(int)$u["id"]
?>"
onclick="
return confirm(
'Tem certeza que deseja excluir este

usuário?'

);

"
>
<button
class="btn-excluir"
type="button"
>
Excluir
</button>
</a>

</td>

</form>

</tr>

<?php endforeach; ?>

</tbody>
</table>

<?php endif; ?>

<?php endif; ?>

</section>
</main>

<!-- =====================================================
RODAPÉ
===================================================== -->

<footer>
<hr>
<p>
© 2026 - Desenvolvido por Luiz Santos
</p>
</footer>

</body>
</html>
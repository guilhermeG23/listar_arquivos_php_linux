<style>
/*CSS - Simples*/
.tabela {
	text-align: center;
	width: 50%;
	margin-top: 10px;
}
.tabela tr td {
	border: 1px solid black;
}
.tabela td {
	padding: 5px;
}
.tabela thead {
	background-color: black;
	color: white;
	font-weight: bold;
}
</style>

<!--Form para operacao-->
<title>Listar aquivos Linux</title>
<p>Operação somente funciona em sistemas Linux, diretório default é sempre o onde o index.php estiver: <?=shell_exec("pwd");?></p>
<form action="." method="POST">
	<label for="caminho">Digite o caminho do diretorio desejado: </label>
	<input type="text" id="caminho" name="caminho" required/>
	<button type="submit">Procurar</button>
<form>

<?php
#Funcao para retorno dos tipos de aruquivos
function tipo_arquivo($caminho, $informacao) {
	#Pega se no final do caminho existe uma / para uso do comando interno file do shell
	$final = substr($caminho, strlen($caminho)-1, 1);
	$tipo_arquivo = "";
	#Confirmar se / existe
	if ($final != "/") {
		$tipo_arquivo = shell_exec("file $caminho/$informacao");
	} else {
		$tipo_arquivo = shell_exec("file $caminho$informacao");
	}
	#Retorna o tipo do arquivo na segunda parte do array feita pelo explode
	return explode(": ", $tipo_arquivo)[1];
}

#Limpar espaços vazios
function limpar_vazios($limpar) {
	$saida_array = [];
	foreach ($limpar as $valor) {
		#Se não for vazio, vai para o array de retorno
		if (($valor !== "") && ($valor !== " ")) {
			array_push($saida_array, $valor);
		}
	}
	#retorno
	return $saida_array;
}

#Confirmar se existe um post do form
if (isset($_POST["caminho"])) {
	$caminho = $_POST["caminho"];
	#Confirmar que existe valor do post
	if (strlen($caminho) != 0) {
		#Confirmar que o diretorio existe
		if (file_exists($caminho)) {
			#Comando interno do linux para extrair os arquivos do diretorio
			$retorno = shell_exec("ls -lh $caminho | awk '{print $5\" \"$9}'");
			$retorno = explode("\n", $retorno);
			#Retira osvazios do comando ls -lh
			$retorno = limpar_vazios($retorno);
			#Confirma que existe algo além do TOTAL no diretorio
			if (count($retorno) > 0) {
			
?>
				<table class="tabela">
					<thead>
						<tr>
							<td colspan="3">Diretório procurado: <?=$caminho;?></td>
						</tr>
						<tr>
							<td>Tipo</td>
							<td>Tamanho</td>
							<td>Nome</td>
						</tr>
					</thead>
					<tbody>
<?php
				#Lista os arquivos do diretorio
				foreach ($retorno as $linha) {
					if (strlen($linha) > 1) {
						#Linha composta do primeiro explode "<tipo> - <tamanho> - <nome>"
						#Explode para apresentar 
						$informacao = explode(" ", $linha);
						echo "<tr>
							<td>" . tipo_arquivo($caminho, $informacao[1]) . "</td>
							<td>" . $informacao[0] . "</td>
							<td>" . $informacao[1] . "</td>
						</tr>";
					}
					
				}
?>
					</tbody>
				</table>
<?php
#Mensagens do ocorrido caso não for possível mostrar os arquivos do diretorio
			} else {
				echo "<p>Não há arquivos dentro do diretório atual! Diretório atual é: $caminho</p>";
			}
		} else {
			echo "<p>Diretório não existe! Diretório digitado é: $caminho</p>";
		} 
	}else {
		echo "<p>Erro inesperdo, caminho vazio!</p>";
	}
}



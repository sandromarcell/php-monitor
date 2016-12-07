<!--
   Copyright 2016 Sandro Marcell <smarcell@mail.com>
   
   This program is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation; either version 2 of the License, or
   (at your option) any later version.
   
   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.
   
   You should have received a copy of the GNU General Public License
   along with this program; if not, write to the Free Software
   Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
   MA 02110-1301, USA.   
-->

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title>Monitor de status</title>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta name="author" content="Sandro Marcell" />
	<meta name="generator" content="Geany 1.24.1" />
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0" />	
	<link rel="stylesheet" type="text/css" href="css/styles.css" />
	<link rel="icon" type="image/x-icon" href="imagens/favicon.ico" />
	<script type="text/javascript" src="js/jquery-2.2.3.min.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			<?php
			require 'include/config.inc.php';
			echo 'var intervalo = ' . INTERVALO . ';';
			?>

			$(function() { atualizarTabela(); });

			function atualizarTabela() {
				function montarTabela(data) {
					var status;
					var destaque;
					var conteudo;
					var indices = data.length;

					conteudo = '<tr class="titulo">';
					conteudo += '<td class="oculto"></td>';
					conteudo += '<td colspan="4">MONITOR DE STATUS</td>';
					conteudo += '</tr>';
					conteudo += '<tr class="info">';
					conteudo += '<td>Status</td>';
					conteudo += '<td>Host</td>';
					conteudo += '<td>IP</td>';
					conteudo += '<td>Tempo de resposta</td>';
					conteudo += '<td>&Uacute;ltima atualiza&ccedil;&atilde;o</td>';
					conteudo += '</tr>';

					for (var i = 0; i < indices; i++) {
						status = 'online';
						destaque = null;

						if (data[i].Status === 'OFFLINE') {
							status = 'offline';
							destaque = 'negrito';
						}

						conteudo += '<tr>';
						conteudo += '<td class="' + status + '">' + data[i].Status + '</td>';
						conteudo += '<td class="' + destaque + '">' + data[i].Host + '</td>';
						conteudo += '<td class="' + destaque + '">' + data[i].IP + '</td>';
						conteudo += '<td class="' + destaque + '">' + data[i]['Tempo de resposta'] + '</td>';
						conteudo += '<td class="' + destaque + '">' + data[i]['&Uacute;ltima atualiza&ccedil;&atilde;o'] + '</td>';
						conteudo += '</tr>';
					}

					$('.carregando').hide();

					$('#tbl').html(conteudo).trigger('update');

					$('#tbl').on('update', function() { // Alarme sonoro
						if ($('td').hasClass('offline')) {
							var audio = new Audio('audio/alarme.ogg');
							audio.autoplay = true;
							audio.play();
						}
						$('#tbl').off('update');
					});
				}

				$.ajax({
					url: 'data.php',
					type: 'GET',
					cache: false,
					async: true,
					dataType: 'json',
					success: montarTabela
				});
			}
			setInterval(atualizarTabela, intervalo);
		});

		$(window).load(function() { // Efeito "blink"
			setInterval(function() {
				$('[class="offline"]').fadeOut(500);
				$('[class="offline"]').fadeIn(500);
			}, 1000);
		});
	</script>
</head>
<body>
	<div class="carregando"></div>
	<div>
		<table id="tbl"></table>
	</div>
	<div class="rodape">
		<strong>Monitor de status</strong> &copy; 2016 <a href="mailto:smarcell@mail.com">Sandro Marcell</a><br />
		<?php
		setlocale(LC_TIME, 'pt_BR.UTF-8');
		echo '<span class="data">' . strftime('%a, %d de %B de %Y', strtotime('today')) . '</span>';
		?>
	</div>
</body>
</html>

<?php

/*
 * Copyright 2016 Sandro Marcell <smarcell@mail.com>
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA.
 */

require 'include/config.inc.php';
require 'include/funcoes.inc.php';

$status = array();
$indices = count($hosts);

for ($i = 0; $i < $indices; $i++) {
	/* Chamada da funcao "validarHost" para validacao das entradas */
	$host = validarHost($hosts[$i]['host']);
	if ($host === FALSE)
		continue;

	/* Chamada da funcao "checarHost" para verificacao dos status */
	$latencia = checarHost($host);
	$tempo = date('H\hi\m\i\n');

	if ($latencia === FALSE) {
		$status[$i] = array(
			'Status' => 'OFFLINE',
			'Host' => $hosts[$i]['desc'],
			'IP' => $host,
			'Tempo de resposta' => 'x',
			'&Uacute;ltima atualiza&ccedil;&atilde;o' => $tempo
		);
		continue;
	}

	$status[$i] = array(
		'Status' => 'ONLINE',
		'Host' => $hosts[$i]['desc'],
		'IP' => $host,
		'Tempo de resposta' => $t_resp = $latencia <= 1 ? '1' : "$latencia",
		'&Uacute;ltima atualiza&ccedil;&atilde;o' => $tempo
	);
}

/* Converte o vetor "status" em objeto do tipo json */
$json = json_encode(array_values($status));

echo "$json";

?>

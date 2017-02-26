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
 
function validarHost($i) {
	/*
	 * NOME: validarHost 
	 * PARAMETROS: endereco IP, dominio ou URL
	 * RETORNO: endereco IP ou FALSE
	 *
	 * Esta funcao verifica se o IP informado esta em formato valido
	 * ou retorna o endereco IP caso a entrada seja um dominio ou URL.
	 * 
	 * Obs.: Esta funcao pode se tornar lenta ao se validar somente dominios ou URL's.
	 */
	 if (filter_var($i, FILTER_VALIDATE_IP)) {
		 return $i;
	 } else {
		 putenv('RES_OPTIONS="rotate timeout:1 attempts:1"');
		 $ip = gethostbyname($i);
		 if (filter_var($ip, FILTER_VALIDATE_IP))
			return $ip;
	 }

	 return FALSE;
}

function checarHost($host, $porta = NULL, $timeout = 3) {
	/*
	 * NOME: checarHost
	 * PARAMETROS: host, *porta, *timeout (* = opcional)
	 * RETORNO: Tempo de latencia ou FALSE
	 *
	 * Esta funcao opera de duas formas, de acordo com os parametros passados:
	 *
	 * > checarHost($host);
	 * 
	 * Utiliza a funcao 'exec' e via comando 'ping' do S.O. checa o host e retorna
	 * o tempo de latencia caso a checagem seja bem sucedida ou FALSE caso contrario.
	 *
	 * > checharHost($host, $porta, $timeout)
	 *
	 * Utiliza a funcao 'fsockopen' e checa no host se a porta especificada esta respondendo,
	 * retornando o tempo de latencia caso a checagem seja bem sucedida ou FALSE caso contrario.
	 */
	if (isset($porta)) {
		$tempo_inicial = microtime(TRUE);
		$fd = fsockopen($host, $porta, $errno, $errstr, $timeout);

		if (is_resource($fd)) {
			fclose($fd);
			return(round((microtime(TRUE) - $tempo_inicial) * 1000));
		}

		return FALSE;
	}

	/* !! Esta implementacao via "exec" foi testada somente em sistemas Linux !! */	
	$ping = 'ping -n -U -i 0.2 -c 5 -W 1 ' . escapeshellcmd($host);
	exec($ping, $saida, $retorno);
	$saida = array_values(array_filter($saida));
	$rtt = array_slice($saida, -1)[0];

	if (!empty($rtt)) {
		$temp_resp = preg_match('/rtt min\/avg\/max\/mdev = [\d\.]+\/([\d\.]+)\/[\d\.]+\/[\d\.]+\sms/', $rtt, $padrao);
		if ($temp_resp > 0)
			return(round($padrao[1]));
	}

	return FALSE;
}

?>

<?php	
	class Database {
		private $_connection;
		private static $_instance; //The single instance
		/*
		Get an instance of the Database
		@return Instance
		*/
		public static function getInstance() {
			if(!self::$_instance) {
				// If no instance then make one
				self::$_instance=new self();
			}
			return self::$_instance;
		}
		// Constructor
		private function __construct() {
			$this->_connection=new mysqli(BDS, BDU, BDP, BDN);
			// Error handling
			if(mysqli_connect_error()) {
				trigger_error("Failed to conect to MySQL: " . mysqli_connect_error(), E_USER_ERROR);
			}
			else {
				$this->_connection->set_charset("utf8");
			}
		}
		// Magic method clone is empty to prevent duplication of connection
		private function __clone() {}
		// Get mysqli connection
		public function getConnection() {
			return $this->_connection;
		}
	}

	class SQL extends Database {
		public static $insert_id = 0;
		public static $error = "";

		public static function run($sql,$retorno = true) {
			if ($retorno) {
				$result = parent::getInstance()->getConnection()->query($sql);
				if (!$result) {
					self::$error = parent::getInstance()->getConnection()->error;
				}
			}
			else {
				$result = parent::getInstance()->getConnection()->query($sql,MYSQLI_USE_RESULT);
			}
			self::$insert_id = !empty(parent::getInstance()->getConnection()->insert_id) ? parent::getInstance()->getConnection()->insert_id : 0;
			return $result;
		}

		public static function getArray($obj) {
			$res = array();
			if ($obj->num_rows > 0) {
				while ($row = $obj->fetch_assoc()) {
					$res[] = $row;
				}
			}
			return $res;
		}
	}

	class sys_utils {
		public $mysqli = null;
		public $mysqli_erro = "";
		public $mysqli_last_op_msg = "";
		public $mysqli_last_op_flag = "";
		private $user_agent = "IdeiasFrescas::curl";
		private $td_stack = array();

		public function __construct() {
			//$this->db_connect();
			$this->mysqli_last_op_msg = "";
			$this->mysqli_last_op_flag = "";
		}

            //validar chave
        public function validarK() {
            $headers = getallheaders();
			if (isset($headers["X-High-Authentication"])) {
                $k = filter_var($headers["X-High-Authentication"],FILTER_SANITIZE_STRING);
                $res = SQL::run("SELECT id FROM ".BDPX."_clientes where chave='$k'");                
                return $res && $res->num_rows > 0;                
			}
        } 
        public function identificarC(){
            $headers = getallheaders();         
            if (isset($headers["X-High-Authentication"])) {
                $k = filter_var($headers["X-High-Authentication"],FILTER_SANITIZE_STRING);
                $res = SQL::run("SELECT id FROM ".BDPX."_clientes where chave='$k'");
                if($res->num_rows > 0){
                    $n = $res->fetch_assoc();
                    return ($n["id"]);
                }
            }
            return 0;
        }
        public function createXML($field, $row){
            foreach($field as $socialEntity){
                if($socialEntity!=""){
                    $data = "<".$socialEntity.">".$row."</".$socialEntity.">";

                    file_put_contents($socialEntity."teste.txt",$data,FILE_APPEND);
                }
            }
        } 
        
        public function obterDados() {
            $x = file_get_contents('php://input');
            $body = filter_var_array(json_decode($x,true),FILTER_SANITIZE_STRING);
            return $body;
        }
		
		//Devolve um array com a listagem de países com código e nome, respeitando o IDIOMA.
		public function db_get_paises($idioma = '') {
			if ($idioma == '') $idioma = IDIOMA;
			$paises = array();
			$res = SQL::run("SELECT codigo_pais,nome from ".BDPX."_aux_paises where idioma='".$idioma."' order by nome ASC");
			while ($row = $res->fetch_assoc()) {
				$paises[$row["codigo_pais"]] = $row["nome"];
			}
			return $paises;
		}
		//Devolve o nome do país correspondente ao código, tendo em conta o IDIOMA.
		public function db_ident_pais($codigo,$idioma = '') {
			if ($idioma == '') $idioma = IDIOMA;
			$res = SQL::run("SELECT nome from ".BDPX."_aux_paises where codigo_pais='".strtoupper($codigo)."' and idioma='".$idioma."'");
			if ($res->num_rows > 0) {
				$n = $res->fetch_assoc();
				return($n["nome"]);
			}
		}		
		//funções para ler configurações
		public function cfg_ler($parametro) {
			$result = SQL::run("SELECT config_valor from ".BDPX ."_configs where config_param='$parametro'");
			if ($result && $result->num_rows > 0) {
				if ($result->num_rows == 1) {
					$result->data_seek(0);
					$c = $result->fetch_assoc();
					return($c["config_valor"]);
				}
				else {
					return $result->fetch_assoc();
				}
			}
			else {
				return '';
			}
			$result->free();
		}
		//Ordena arrays directamente da base de dados, pelos campos escolhidos e na língua pedida: args($array,$lingua,'$campos,SORT_ASC')
		public function db_order_by() {
			$args = func_get_args();
			$data = array_shift($args);
			$lingua = array_shift($args);
			foreach ($args as $field) {
				if (is_string($field)) {
					foreach($data as $key => $row) {
						$unix = strtotime($row[$field]);
						$json = json_decode($row[$field],true);
						if (!is_numeric($row[$field])) {
							if ($unix != false) $data[$key][$field] = $unix;
							elseif (!empty($json)) $data[$key][$field] = $json[$lingua];
						}
					}
				}
			}
			foreach ($args as $n => $field) {
				if (is_string($field)) {
					$tmp = array();
					foreach ($data as $key => $row)
						$tmp[$key] = $row[$field];
					$args[$n] = $tmp;
				}
			}
			$args[] = &$data;
			call_user_func_array('array_multisort', $args);
			return array_pop($args);
		}
		/******************************************
		 *          SECÇÃO DE HELPERS             *
		 ******************************************/

		//Efectua um pedido externo compactível com o mod_security.
		public function auxPost($url, $data=null, $port=80,$optional_headers = 'Content-type:application/x-www-form-urlencoded') {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_HEADER, 0);
			if (!empty($data)) curl_setopt($ch, CURLOPT_URL, $url."?".$data);
			else curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 5);
			curl_setopt($ch, CURLOPT_PORT, $port);
    		curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
			$response = curl_exec($ch);
			//$info = curl_getinfo($ch);
			curl_close($ch);
			return $response;
		}

		//Devolve a estrutura de um array de forma a ser facilmente perceptível, podendo envia-la por email.
		public $output_var = "";
		public function debug($variavel,$email=null,$deep=0) {
			global $output_var;
			if ($deep == 0) $output_var = "";
			$filler = str_repeat("&nbsp;",8);
			$forward = str_repeat($filler,$deep);
			foreach ($variavel as $ind => $linha) {
                if (!is_array($linha)) {
                	if (!strstr($linha,'s:2:"')) {
                		$output_var .= $forward."<b>".$ind.":</b> ".var_export($linha,true)."<br>";
                	} else {
	                    $output_var .= $forward."<b>".$ind.":</b><br>";
	                    $another = unserialize($linha);
	                    $this->debug($another,null,$deep + 1);
                	}
                } else {
                    $output_var .= $forward."<b>".$ind.":</b><br>";
                    $this->debug($linha,null,$deep + 1);
                }
            }
            if ($deep == 0) {
	            if (!empty($email)) {
	            	mail($email,"DEBUG",$output_var,"Content-Type: text/html;charset=utf-8");
	            } else echo $output_var;
            }
		}

		//Função flexivel para contabilizar paginação de um determinado recurso.
		public function pagecount($pag,$reg_pag,$item) {
			$pag_ant =$pag-1;
		    $pag_seg =$pag+1;
		    $pag_ini=($reg_pag*$pag)-$reg_pag;
			if (is_resource($item)) {	$num_reg = mysql_num_rows($item);	}
			if (is_int($item)) 		{	$num_reg = $item;					}
			if (is_array($item)) 	{	$num_reg = count($item);			}

            if ($num_reg<=$reg_pag) {	$num_pag=1;           }
            else if (($num_reg%$reg_pag)==0) {             $num_pag=$num_reg/$reg_pag;            }
            else {        	$num_pag=$num_reg/$reg_pag + 1;             }

			return(array("tp"=>$num_pag,"off1"=>$pag_ini,"off2"=>$reg_pag,"pga"=>$pag_ant,"pgs" => $pag_seg));
        }     
		//Devolve o menu de paginação.
		public function pagegen($cur_page,$total_pages,$pag_ant,$pag_seg,$num_pag=3,$last_first=false) {
	        $url = parse_url($_SERVER['REQUEST_URI']);
	        if(isset($url["query"])) parse_str($url["query"],$query);
	        else $query = array();
	        $query["pagina"] = "PAG";

	        $q = http_build_query($query);
			/*
                <a href="#">1</a>
                <a class="active">3</a>
            */
	        $template = '<div class="pagination">
					        <div class="center">
									<ul>
					{PAGS}
					</ul>
					</div>
				</div>';
	        $setas = "";

	        $lbl1 = "";
	        $lbl2 = "";
	        $paginas = "";
	        if (($pag_ant) && ($cur_page>1)) {
	            $paginas .= '<li><a class="button small grey" href="'.$url["path"]."?".str_replace("PAG",$pag_ant,$q).'"><i class="fa fa-chevron-left"></i></a></li>';
	        }
	        else {
	            $paginas .= '';
	        }
	        $max = $num_pag;
	        $shift = round($num_pag/2);
	        $total_pages = floor($total_pages);
	        $max_links = $max+2;
	        $h=1;

	        if($total_pages>=$max_links){
	            if(($cur_page>=$max_links-$shift)&&($cur_page<=$total_pages-$shift)){
	                $max_links = $cur_page+$shift;
	                $h=$max_links-$max;
	            }
	            if($cur_page>=$total_pages-$shift+1){
	                $max_links = $total_pages+1;
	                $h=$max_links-$max;
	            }
	        }
	        else{
	            $h=1;
	            $max_links = $total_pages+1;
	        }

	        for ($i=$h;$i<$max_links;$i++){
	            if($i==$cur_page)	{
	                $paginas .= '<li class="current"><a class="button small grey">'.$i.'</a></li>';
	            }
	            else{
	                $paginas .= '<li><a class="button small grey" href="'.$url["path"]."?".str_replace("PAG",$i,$q).'">'.$i.'</a></li>';
	            }

	        }
	        if(($cur_page >=1)&&($cur_page!=$total_pages)){
	            $paginas .= '<li><a class="button small grey" href="'.$url["path"]."?".str_replace("PAG",$pag_seg,$q).'"><i class="fa fa-chevron-right"></i></a></li>';
	        }
	        else {
	            $paginas .= '';
	        }
	        $template = str_replace(array("{SETAS}","{PAGS}"),array($setas,$paginas),$template);

	        if ($total_pages == 1) $template = "";

	        return ($template);
	    }
	    //Devolve o conteúdo de uma directoria
		public function fs_dir($pasta,$opcoes = array(),$ordem = array()) {
	        $files = array();
	        if (file_exists($pasta)) {
	    		$dir  = new DirectoryIterator($pasta);
	    	    foreach ($dir as $chave => $file) {
	    		    if ($file->isDot() == false) {
	    				$arr = array(
	    					"file" => $pasta . "/" . $file->getFilename()
	    				);
	    				if (in_array("data",$opcoes)) {
	    					$arr["mtime"] = $file->getMTime();
	    				}
	    				if (in_array("bytes",$opcoes)) {
	    					$arr["bytes"] = $file->getSize();
	    				}
	    				if (in_array("legenda",$opcoes)) {
	    				    $IMG = new sys_imagens($pasta . "/" . $file->getFilename());
	    					$legs = $IMG->getLegenda();
	    					if (is_array($legs)) {
	    							//este função é por causa dos pngs
	    					    //string assegura que caracteres extra ficam de fora
	    					    $leg = trim(array_pop($legs));
	    						$arr["legenda"] = json_decode($leg,true);
	    					    unset($leg);
	    					}
	    					else {
	    						$arr["legenda"] = json_decode($legs,true);
	    						if (!is_array($arr["legenda"])) $arr["legenda"] = [];
	    					}
	    					unset($IMG);
	    				}

	    				$files[] = $arr;
	       			}
	    		}

	    		//re-ordenar os ficheiros conforme o array vindo da BD
	    		if (count($ordem) > 0) {
                    //ke se f*** a performance... doggy style algo
                    $nova_ordem = array();
                    foreach($ordem as $ofile) {
                        foreach($files as $ff) {
                            if (basename($ff["file"]) == $ofile) {
                                $nova_ordem[] = $ff;
                            }
                        }
                    }

                    if (count($ordem) != count($files)) {
                    	foreach($files as $ofile) {
                    		if (!in_array(basename($ofile["file"]),$ordem)) {
                    			$nova_ordem[] = $ofile;
                    		}
                    	}
                    }

                    //var_dump($nova_ordem);
                    $files = $nova_ordem;
                }
	        }
			return $files;
	    }
				
		public function partition( $list, $p ) {
              $listlen = count( $list );
              $partlen = floor( $listlen / $p );
              $partrem = $listlen % $p;
              $partition = array();
              $mark = 0;
              for ($px = 0; $px < $p; $px++) {
                  $incr = ($px < $partrem) ? $partlen + 1 : $partlen;
                  $partition[$px] = array_slice( $list, $mark, $incr );
                  $mark += $incr;
              }
              return $partition;
          }

		//Devolve o caminho relativo de um realpath
		public function fs_relativePath($relative_path) {
            $realpath=realpath($relative_path);
            $htmlpath=str_replace($_SERVER['DOCUMENT_ROOT'],'',$realpath);
            return $htmlpath;
        }
        //Devolve o caminho relativo de um caminho em relação a outro
        public function fs_getRelativePath($from, $to) {
    		// some compatibility fixes for Windows paths
    		$from = is_dir($from) ? rtrim($from, '\/') . '/' : $from;
    		$to   = is_dir($to)   ? rtrim($to, '\/') . '/'   : $to;
    		$from = str_replace('\\', '/', $from);
    		$to   = str_replace('\\', '/', $to);

		    $from     = explode('/', $from);
		    $to       = explode('/', $to);
		    $relPath  = $to;

		    foreach($from as $depth => $dir) {
		        // find first non-matching dir
		        if($dir === $to[$depth]) {
		            // ignore this directory
		            array_shift($relPath);
		        } else {
		            // get number of remaining dirs to $from
		            $remaining = count($from) - $depth;
		            if($remaining > 1) {
		                // add traversals up to first matching dir
		                $padLength = (count($relPath) + $remaining - 1) * -1;
		                $relPath = array_pad($relPath, $padLength, '..');
		                break;
		            } else {
		                $relPath[0] = './' . $relPath[0];
		            }
		        }
		    }
		    return implode('/', $relPath);
		}		
		
		/******************************************
		 *          SECÇÃO DE ARRAYS             *
		 ******************************************/
		public function array_sortbykey(&$array, $subkey="id", $sort_ascending=true) {
			if (count($array))	$temp_array[key($array)] = array_shift($array);
		    foreach($array as $key => $val){
        		$offset = 0;
        		$found = false;
        		foreach($temp_array as $tmp_key => $tmp_val) {
            		if(!$found and strtolower($val[$subkey]) > strtolower($tmp_val[$subkey])) {
	                	$temp_array = array_merge(    (array)array_slice($temp_array,0,$offset),
		                    array($key => $val),
		                    array_slice($temp_array,$offset)
		                  );
	                	$found = true;
	            	}
	            	$offset++;
	        	}
	        	if(!$found) $temp_array = array_merge($temp_array, array($key => $val));
    		}
		    if ($sort_ascending) $array = array_reverse($temp_array);
		    else $array = $temp_array;
		}
		//Faz shuffle num array, mantendo as associações
		public function array_shuffle_assoc(&$array) {
            $new = array();
            $keys = array_keys($array);
            shuffle($keys);
            foreach($keys as $key) {
                $new[$key] = $array[$key];
            }
            $array = $new;
            return true;
    	}
    	//verificar se o array é associativo ou sequencial
    	public function array_is_assoc(array $arr) {
    		if (array() === $arr) return false;
    		return array_keys($arr) !== range(0, count($arr) - 1);
		}
		//Procura determinada string num array e retorna o elemento do array completo.
		public function find_in_array($array,$search){
			foreach($array as $key=>$element){
				$valid = strpos($element, $search);
				if($valid === 0){
					return $element;
					break;
				}
			}
		}
		/******************************************
		 *          SECÇÃO DE STRINGS             *
		 ******************************************/
		//Função para limpeza do nome de ficheiros.
		public function st_clean_filename($valor) {
			$result = strtolower($valor);
			$replace="";
			$pattern="/([[:alnum:]_\.-]*)/";
			$result=str_replace(str_split(preg_replace($pattern,$replace,$result)),$replace,$result);
			$result = str_replace(array("-","_"),array("",""),$result);
			return ($result);
		}
		//Função para limpeza de HTML.
		public function st_clean_html($valor,$html=true) {
			//$t = stripslashes(nl2br($valor));
			$t = stripslashes($valor);
			if ($html) {
				$t = htmlentities(trim($t),ENT_QUOTES,"UTF-8");
			}
			// Damn pesky carriage returns...
		    $t = str_replace("\r\n", "\n", $t);
		    $t = str_replace("\r", "\n", $t);
		    // JSON requires new line characters be escaped
		    $t = str_replace("\n", "\\n", $t);
			return ($t);
		}
		//Devolve uma string tratada para poder usar em URL. Pode ser também usada para outras finalidades, desde que envolvam remover caracteres especiais.
    	public function st_clean_link($nome) {
    		$nome = str_replace(array('[\', \']'), '', $nome);
            $nome = preg_replace('/\[.*\]/U', '', $nome);
            $nome = preg_replace('/&(amp;)?#?[a-z0-9]+;/i', '-', $nome);
            $nome = htmlentities($nome, ENT_COMPAT, 'utf-8');
            $nome = preg_replace('/&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|lsquo|rsquo);/i', '\\1', $nome );
            $nome = preg_replace(array('/[^a-z0-9]/i', '/[-]+/') , '-', $nome);
            $nome = str_replace("-amp-","-&amp;-",$nome);
            return strtolower(trim($nome, '-'));
		}
		//Devolve a string correspondente do resizer.
        public function st_size($modo, $w, $h, $pic, $per="") {
			$pic = str_replace("/", "@", $pic);
			return "sizer/".$modo."/".$w."/".$h."/".$pic."/".$per;
		}
		//Devolve datas formatadas com o locale a nosso gosto.
		public function st_locale_date($valor,$formato_out="%d de %B de %Y",$formato_in="Y-m-d") {
			$data = DateTime::createFromFormat($formato_in, $valor);
			return strftime($formato_out,$data->getTimeStamp());
		}
		//Devolve de um array serializado o texto na lingua correcta.
		public function st_parse_idioma($valor,$idioma) {
			//echo $valor;
			$valor = ltrim($valor);
			$valor = rtrim($valor);
			$temp = json_decode($valor,true);
			//echo str_replace("\r\n","",$temp[$idioma]);
			if(isset($temp[$idioma])) return($temp[$idioma]);
			else return("");
		}
		//Devolve o conteúdo do TinyMCE com o fix de imagens e com mais opções
		public function st_parse_texto($texto,$stripTags=false) {
			$texto = str_replace('../../../../downloads','downloads',$texto);
			$texto = html_entity_decode($texto);
			if ($stripTags) return strip_tags($texto);
			else return $texto;
		}
		//Redireciona para uma página usando Javascript e não mantendo (ou mantendo) o histórico.
		//Corta uma string respeitando a integridade das palavras, e finalizando com o texto pretendido.
		public function st_crop_sentence($strText, $intLength, $strTrail) {
			//tira as tags HTML e excesso de espaços
			$strText = trim(preg_replace('/(\s)+/', " ", strip_tags($strText)));
			$wsCount = 0;
			$intTempSize = 0;
    		$intTotalLen = 0;
    		$intLength = $intLength - strlen($strTrail);
		    $strTemp = "";

		    if (strlen($strText) > $intLength) {
        		$arrTemp = explode(" ", $strText);
		        foreach ($arrTemp as $index=>$x) {
        		    if (strlen($strTemp) <= $intLength) $strTemp .= " " . $x;
        		    if($index == 0) $strTemp = ltrim($strTemp);
		        }
        		$CropSentence = $strTemp . $strTrail;
		    } else {
        		$CropSentence = $strText;
		    }
			return ($CropSentence);
		}
		/******************************************
		 *          SECÇÃO DE NUMEROS             *
		 ******************************************/
		//Devolve a conversão numérica de uma string para uso em contas.
		public function math_float_val($valor) {
			//converte string para número calculável
			return floatval(preg_replace('#^([-]*[0-9\.,\' ]+?)((\.|,){1}([0-9-]{1,2}))*$#e', "str_replace(array('.', ',', \"'\", ' '), '', '\\1') . '.\\4'", $valor));
		}
		//Devolve a formatação de um número para o formato de preço sem ou com 2 casas decimais.
		public function math_format_price($preco, $clearZeros=true) {
			$valor = money_format('%!.2n', $preco);
			if(strstr($valor, ".00") && $clearZeros) $valor = str_replace(".00", "", $valor);
			return($valor);
		}
		//Devolve o valor do IVA de um preço já com IVA.
		public function math_get_iva($preco, $iva) {
			return (float)$preco - $this->math_get_preco_base($preco, $iva);
		}
		//Devolve o valor de um preço base mais o IVA.
		public function math_add_iva($preco, $iva) {
			return ((float)$preco * (1 + (float)$iva / 100));
		}
		//Devolve o preço base de um valor já com IVA.
		public function math_get_preco_base($preco, $iva) {
			return ((float)$preco / (1 + (float)$iva / 100));
		}
		//Devolve a percentagem de IVA, mediante o valor inicial e final de preço.
		public function math_get_iva_perc($preco_ini, $preco_fin) {
			return ($preco_ini * $preco_fin - 100);
		}
		//Devolve o valor do desconto de um preço já com desconto.
		public function math_get_desconto($preco, $desconto) {
			return (float)$preco - $this->math_get_preco_base($preco, $desconto);
		}
		//Devolve o valor de um preço base com o desconto aplicado.
		public function math_add_desconto($preco, $desconto) {
			return ((float)$preco * (1 + (float)$desconto / 100));
		}
		//Devolve o preço base de um valor já com desconto.
		public function math_get_valor_base($preco, $desconto) {
			return ((float)$preco / (1 + (float)$desconto / 100));
		}
		//Devolve a percentagem de desconto, mediante o valor inicial e final de preço.
		public function math_get_desconto_perc($preco_ini, $preco_fin) {
			return ((float)$preco_ini * (float)$preco_fin - 100);
		}
		//Para calculos de ratios ou outras situações em que seja necessária uma regra de 3 simples. Recebe 4 argumentos, dos quais um deles é um 'x', e a função devolve o valor do mesmo.
		public function math_ratio_calc($par_a1,$par_a2,$par_b1,$par_b2) {
			if (strtolower($par_a1) == 'x') return $par_b1 * $par_a2 / $par_b2;
			elseif (strtolower($par_a2) == 'x') return $par_a1 * $par_b2 / $par_b1;
			elseif (strtolower($par_b1) == 'x') return $par_a1 * $par_b2 / $par_a2;
			elseif (strtolower($par_b2) == 'x') return $par_b1 * $par_a2 / $par_a1;
			else return null;
		}
		//Devolve o aspect ratio de uma imagem, num formato compacto como 16/9, ou 4/3 ou outro.
		public function math_aspect_ratio($medida1,$medida2) {
			if ($medida1 > $medida2) {
				$ratio = $medida2 / $medida1;
				$dir = true;
			} else {
				$ratio = $medida1 / $medida2;
				$dir = false;
			}
			$num = 0;
			$checker = true;
			$res = 0;
			while ($checker) {
				$num++;
				$res = $num * $ratio;
				if ((int)$res == $res) $checker = false;
			}
			if ($dir) return $num.'/'.$res;
			else return $res.'/'.$num;
		}
		//Devolve o aspect ratio de uma imagem, num formato compacto como 16/9, ou 4/3 ou outro.
		public function math_aspect_ratio_image($imagem) {
			$sizes = getimagesize($imagem);
			return $this->math_aspect_ratio($sizes[0],$sizes[1]);
		}
		//Calcular hà quanto tempo passou desde a data indicada
		public function mate_date_ago_calc($data2,$data1=0) {
			if ($data1 == 0) $data1 = time();
			$seconds_ago = ($data1 - $data2);

			if ($seconds_ago >= 31536000) {
    			return [intval($seconds_ago / 31536000),"anos"];
			} elseif ($seconds_ago >= 2419200) {
    			return [intval($seconds_ago / 2419200),"meses"];
			} elseif ($seconds_ago >= 86400) {
			    return [intval($seconds_ago / 86400),"dias"];
			} elseif ($seconds_ago >= 3600) {
			    return [intval($seconds_ago / 3600),"horas"];
			} elseif ($seconds_ago >= 60) {
			    return[intval($seconds_ago / 60),"minutos"];
			} else {
			    return[$seconds_ago,"segundos"];
			}
		}		
	}	
?>

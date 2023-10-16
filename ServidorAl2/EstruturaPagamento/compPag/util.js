var janelaPagamento;
var pagamentoId;



function pagamentoBoleto(tipoFat,registro) {
		showAguardando();
		var dadoC = {
            T: "BO",
            FAT: tipoFat,
			REG: registro
        }
		$.ajax({
            url: 'pagamento.php',
            type: 'post',
            dataType: 'json',
            contentType: 'application/json',
            success: function (dataC) {
				console.log(JSON.stringify(dataC));
                if(dataC.status_code == 201){
					//showAlert('O boleto foi gerado');
					window.open(dataC.lk+dataC.boleto._links[0].href);
					hideAguardando();
					finalizar('O boleto foi gerado');				
				}else{
					hideAguardando();
					var erro = dataC.details[0].description;
					if(erro=='card_number is invalid'){
							erro = 'Cartão inválido.';
					}
					//alert('Não foi possível efetuar o pagamento<br>'+erro+'(1)');
					showAlert('Não foi possível efetuar o pagamento<br>'+erro+'(1)');
				}
            },
            data: JSON.stringify(dadoC)
        });
	}

	function pagamentoCredito(tipoFat,registro) {
		showAguardando();
		var dadoC = {
            T: "C",
            C:$("#cc-numero").val()
        }
		$.ajax({
            url: 'pagamento.php',
            type: 'post',
            dataType: 'json',
            contentType: 'application/json',
            success: function (dataC) {
				console.log(JSON.stringify(dataC));
                if(dataC.status_code == 201){
					
					var datoCC = {
						T: "CC",
						FAT: tipoFat,
						REG: registro,
						CTC:  dataC.number_token,
						CN:  retiraAcento($("#cc-nome").val()),
						CB:  $("#cc-bandeira").val(),
						CE:  $("#cc-expiracao").val(),
						CV:  $("#cc-cvv").val()
						
					}

					//$('#target').html('sending..');

					$.ajax({
						url: 'pagamento.php',
						type: 'post',
						dataType: 'json',
						contentType: 'application/json',
						success: function (dataCC) {
							hideAguardando();
							if(dataCC.status_code == 200){
								if(dataCC.status == 'APPROVED'){
									//showAlert('Pagamento efetuado com sucesso.');
									finalizar('Pagamento efetuado com sucesso.');	
								}
 							
							}else{
								hideAguardando();
								var erro = dataCC.details[0].description_detail;
								if(erro=='\"cardholder_name\" is not allowed to be empty'){
									erro = 'O nome não pode ser vazio ';
								}
								if(erro=='\"security_code\" is not allowed to be empty'){
									erro = 'O cvv não pode ser vazio ';
								}
								if(erro=='\"expiration_month\" is not allowed to be empty'){
									erro = 'A expiração do cartão não pode ser vazia ';
								}
								
								if(dataCC.status_code == 402){
									showAlert('Pagamento Negado(2)');
								}else{
									showAlert('Não foi possível efetuar o pagamento<br>'+erro+'(2)');
								}
							}
						},
						data: JSON.stringify(datoCC)
					});				
				}else{
					hideAguardando();
					var erro = dataC.details[0].description;
					if(erro=='card_number is invalid'){
							erro = 'Cartão inválido.';
					}
					//alert('Não foi possível efetuar o pagamento<br>'+erro+'(1)');
					showAlert('Não foi possível efetuar o pagamento<br>'+erro+'(1)');
				}
            },
            data: JSON.stringify(dadoC)
        });
	}

   function pagamentoDebito(tipoFat,registro) {
        showAguardando();
		var dadoC = {
            T: "C",
            C:$("#cd-numero").val()
        }

        //$('#target').html('sending..');

        $.ajax({
            url: 'pagamento.php',
            type: 'post',
            dataType: 'json',
            contentType: 'application/json',
            success: function (dataC) {
				console.log(JSON.stringify(dataC));
                if(dataC.status_code == 201){
					
					var dadoCD = {
						T: "CD",
						FAT: tipoFat,
						REG: registro,
						CTC:  dataC.number_token,
						CN:  retiraAcento($("#cd-nome").val()),
						CB:  $("#cd-bandeira").val(),
						CE:  $("#cd-expiracao").val(),
						CV:  $("#cd-cvv").val(),
						CT:  $("#cd-cvv").val()
						
					}

					//$('#target').html('sending..');

					$.ajax({
						url: 'pagamento.php',
						type: 'post',
						dataType: 'json',
						contentType: 'application/json',
						success: function (dataCD) {
							console.log(dataCD);
							if(dataCD.hasOwnProperty('redirect_url')){
								var form = document.createElement("form");
								form.setAttribute("method", "post");
								form.setAttribute("action", dataCD.redirect_url);
								form.setAttribute("target", 'pagamento');
								/*
								 $.each(dataCD.post_data, function(k, v) {
									console.log(k,v);
									var input = document.createElement('input');
									input.type = 'hidden';
									input.name = k;
									input.value = v;
									form.appendChild(input);
								 });
								 */
								var input = document.createElement('input');
								input.type = 'hidden';
								input.name = 'MD';
								input.value = dataCD.post_data.issuer_payment_id;
								form.appendChild(input); 
								input = document.createElement('input');
								input.type = 'hidden';
								input.name = 'PaReq';
								input.value = dataCD.post_data.payer_authentication_request;
								form.appendChild(input); 
								input = document.createElement('input');
								input.type = 'hidden';
								input.name = 'TermUrl';
								input.value = 'https://app.plenasaude.com.br/AliancaNet/services/retornoPagamentoDebito.php?ID='+dataCD.payment_id;
								pagamentoId = dataCD.payment_id;
								form.appendChild(input); 
							
								 
								document.body.appendChild(form);
								
								janelaPagamento = window.open("pagamento.htm", 'pagamento');
								 
								 //window.open("post.htm", 'teste', windowoption);
								 
								form.submit();
            
								document.body.removeChild(form);
								
								setTimeout(verificaPagamentoDebito, 5000);
 							
							}else{
								hideAguardando();
								//alert('Não foi possivel efetuar o pagamento(2)');
								var erro = dataCD.details[0].description_detail;
								if(erro=='\"cardholder_name\" is not allowed to be empty'){
									erro = 'O nome não pode ser vazio ';
								}
								if(erro=='\"security_code\" is not allowed to be empty'){
									erro = 'O cvv não pode ser vazio ';
								}
								if(erro=='\"expiration_month\" is not allowed to be empty'){
									erro = 'A expiração do cartão não pode ser vazia ';
								}
								
								
								showAlert('Não foi possível efetuar o pagamento<br>'+erro+'(2)');
							}
						},
						data: JSON.stringify(dadoCD)
					});				
				}else{
					hideAguardando();
					var erro = dataC.details[0].description;
					if(erro=='card_number is invalid'){
							erro = 'Cartão inválido.';
					}
					//alert('Não foi possível efetuar o pagamento<br>'+erro+'(1)');
					showAlert('Não foi possível efetuar o pagamento<br>'+erro+'(1)');
				}
            },
            data: JSON.stringify(dadoC)
        });
    }
	
	

    function retiraAcento(palavra){  
        var com_acento = 'áàãâäéèêëíìîïóòõôöúùûüçÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÖÔÚÙÛÜÇ´`^¨~';    
        var sem_acento = 'aaaaaeeeeiiiiooooouuuucAAAAAEEEEIIIIOOOOOUUUUC     ';  
                                                                      
        for (l in palavra){  
            for (l2 in com_acento){  
                if (palavra[l] == com_acento[l2]){  
                    palavra=palavra.replace(palavra[l],sem_acento[l2]);  
                }  
            }  
        }  
        return palavra;  
      
    } 
	

	
	function timerDebito(){
		setTimeout(verificaPagamentoDebito, 2000);
	}
	
	
	function vaiParaPagina(link){
		setTimeout(()=>{
				window.location.href=link;
		}, 2000);
		
	}
	
	function verificaPagamentoDebito(){
		
		//console.log(janelaPagamento);
		$.ajax({
			url: 'pagamento.php',
			type: 'post',
			dataType: 'json',
			contentType: 'application/json',
			success: function (dataFCD) {
				if(dataFCD.hasOwnProperty('AG')){
					if(dataFCD.AG == 'OK'){
						timerDebito();
					}else{
						janelaPagamento.close();
						//finalizaPagamentoDebito(dataFCD.PARES,dataFCD.ID,dataFCD.IDC);
						if(dataFCD.status.toUpperCase() == 'APPROVED'){
							hideAguardando();
							finalizar('Pagamento efetuado com sucesso.');													
						}else{
							hideAguardando();
							finalizar('Pagamento não foi aprovado.');
						}
						
					}
				}
			},
			data: JSON.stringify({T:"FCD",ID:pagamentoId})
		});			
	}
	
	function showAguardando(){
		$("#loadMe").modal({
		  backdrop: "static", //remove ability to close modal with click
		  keyboard: false, //remove option to close with keyboard
		  show: true //Display loader!
		});
	}
	
	function hideAguardando(){
		$("#loadMe").modal("hide");
	}
	
	function showAlert(msg){
		$("#textoAlert").html(msg);
		$("#alert").modal({
		  //backdrop: "static", //remove ability to close modal with click
		  //keyboard: false, //remove option to close with keyboard
		  //show: true //Display loader!
		});
		hideAguardando();
	}
	
	function finalizar(msg){
		$("#textoFinalizacao").html(msg);
		$("#finalizacao").modal({
		  backdrop: "static", //remove ability to close modal with click
		  keyboard: false, //remove option to close with keyboard
		  //show: true //Display loader!
		});
		
		if($("#retorno").val()!=''){
			vaiParaPagina($("#retorno").val());
		}
	}
	
	function tipoPag(tipo){
		if(tipo=='CR'){
		$("#Tipo").html('	<p   style="color: white;bottom: 50px;"> '+
										'Será aceito as seguintes bandeiras na função crédito ' + 
										' '+
										'<p/>'+
										'<img class="img-fluid" style="max-width: 70% !important;" src="compPag/Imgcredito.png" >'
									);
		}else if(tipo=='DE'){
		$("#Tipo").html('	<p   style="color: white;bottom: 50px;"> '+
										'Será aceito as seguintes bandeiras na função débito ' + 
										' '+
										'<p/>'+
										'<img class="img-fluid" style="max-width: 70% !important;" src="compPag/ImagemDebito.png" >'
									);			
		}else{
			$("#Tipo").html('');			
		}
	}
	function validacaoNumero(idCampo,IdCombo){
		var valor = $("#"+idCampo).val();
		if(valor=='4'){
			$("#"+IdCombo).val('visa');
		}else if(valor=='5'){
			$("#"+IdCombo).val('mastercard');
		}else if((valor == '34')||(valor == '37')){
			$("#"+IdCombo).val('amex');
		}else if((valor == '38')||(valor == '60')){
			$("#"+IdCombo).val('hipercard');
		}else if((valor == '636368')||(valor == '438935')||(valor == '504175')||(valor == '451416')||(valor == '509048')||(valor == '509067')||
				 (valor == '509049')||(valor == '509069')||(valor == '509050')||(valor == '509074')||(valor == '509068')||(valor == '509040')||
				 (valor == '509045')||(valor == '509051')||(valor == '509046')||(valor == '509066')||(valor == '509047')||(valor == '509042')||
				 (valor == '509052')||(valor == '509043')||(valor == '509064')||(valor == '509040')||
				 (valor == '36297')||(valor == '5067')||(valor == '4576')||(valor == '4011')){
			$("#"+IdCombo).val('elo');
		}
		
	}
	
$(document).ready(function(){
	$('#cc-expiracao').inputmask({"mask": "99/99"});
	$('#cd-expiracao').inputmask({"mask": "99/99"});  
});
	
	

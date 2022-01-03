<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require('../../header.php');
require( CONTROLADOR . 'compras.php');
?>
<br>
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
            <li class="breadcrumb-item active" aria-current="page">Imporar y Actualizar</li>
        </ol>
    </nav>
    <form id="importar_productos">
        <div class="form-row align-items-center">
            <div class="col-sm-3 my-1">
                <label for="cod_proveedor">Proveedor</label>
                <select id="cod_proveedor" name="cod_proveedor" class="form-control">
                    <option selected>Seleccione...</option>
                    <option value="1">CHACOMER</option>
                    <option value="2">COMAGRO</option>
                    <option value="3">GLOBO</option>
                    <option value="4">AMRS</option>
                    <option value="14">AMRS - IPHONE</option> 
                    <option value="16">AMRS - PERFUMES</option>
                    <option value="17">AMRS - JOYAS</option>
                    <option value="20">AMRS - JOYAS 2</option> 
                    <option value="18">AMRS - COLEMAN</option>
                    <option value="15">AMRS - VARIOS 3</option>
                    <option value="19">AMRS - VARIOS 3.1</option>
                    <option value="25">AMRS- ELECTRONICA SAMSUNG</option> 
                    <option value="26">AMRS- HUAWEI</option>
                    <option value="5">NGO</option>
                    <option value="6">MEGA-TELEVISORES</option>
                    <option value="7">MEGA-CELULARES</option>
                    <option value="8">MEGA-INFORMATICA</option> 
                    <option value="9">MEGA-VARIOS</option> 
                    <option value="10">BONUS</option>
                    <option value="11">SUEÑOLAR</option> 
                    <option value="12">MOVELMAX</option> 
                    <option value="13">HINODE</option> 
                    <option value="21">MUSIC HALL</option>
                    <option value="22">TODO COSTURA</option>
                    <option value="23">CHENSON</option>
                    <option value="24">CONSUMER</option>
                    <option value="27">COMPAÑIA RC</option>
                </select>
            </div>

            <div class="col-sm-5 my-1">
            </div>

            <div class="col-sm-3 my-1">
                <label for="importar_archivo">Importar desde archivo</label>
                <input type="file" class="form-control-file" name="file" id="file" accept = ".csv" >
            </div>

            <div class="col-1 my-1">
                <button type="button" id="importar_boton" class="btn btn-primary">Cargar</button>
            </div>
        </div>
    </form>
    <br>
    <div id="resultado">
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="Modal_Importar" data-backdrop="static" tabindex="-1" aria-labelledby="Modal_ImportarLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="Modal_ImportarLabel">Importar Productos</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
      </button>
  </div>
  <div id="modal_body" class="modal-body text-center">
    <button class="btn btn-primary" type="button" disabled>
      <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
      Procesando...
  </button>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary cerrar" data-dismiss="modal">Cerrar</button>
</div>
</div>
</div>
</div>

<?php
require('../../footer.php');
?>
<script>

    $(document).ready(function(){

       $("#importar_boton").prop("disabled",true);

   });

    $("#file,#cod_proveedor").change(function(){

        var cod_proveedor = $("#cod_proveedor").val();
        var file = $('#file').val();
        
        if(cod_proveedor>0 && file.length>0){

         $("#importar_boton").prop("disabled",false);

     }else{

         $("#importar_boton").prop("disabled",true);

     }
 });

    $("#cod_proveedor").change(function(){

        var cod_proveedor = $(this).val();
        if(cod_proveedor>0){
            $.ajax({
                type:'POST',
                url:"productos_proveedor.php",
                data:{
                    cod_proveedor : cod_proveedor
                },
                success:function(resp){
                    $("#resultado").html(resp);    
                }
            });
        }
    });

    $("#importar_boton").click(function(){

        event.preventDefault();
        var form = $('#importar_productos')[0];
        var data = new FormData(form);
        
        var r = confirm("Esta acción Actualizara la Base de Datos, está seguro que desea continuar?");
        if (r == true) {

          $('#Modal_Importar').modal('toggle')
          $.ajax({
            type: "POST",
            enctype: 'multipart/form-data',
            url: "productos_importar.php",
            data: data,
            processData: false,
            contentType: false,
            cache: false,
            timeout: 600000,
            success: function (resp) {
                console.log(resp);
                $("#modal_body").html(resp);
            }
        });
      } 
  });

    $(".cerrar").click(function(){
      var cod_proveedor = $("#cod_proveedor").val();
      $.ajax({
        type:'POST',
        url:"productos_proveedor.php",
        data:{
            cod_proveedor : cod_proveedor
        },
        success:function(resp){
            $("#resultado").html(resp);    
        }
    });
  }); 

</script>

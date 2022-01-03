<?php 
require('../../controlador/main.php');
require( CONTROLADOR . 'geoposicion.php');
?>
<style>
  #map,#info {
    height: 400px;
    width: 100%;
  }

  /* Optional: Makes the sample page fill the window. */
  html, body {
    height: 100%;
    margin: 0;
    padding: 0;
  }
</style>


<script>
      // Initialize and add the map
      function initMap() {

        var uluru;
        var map;
        var marker;

        if(navigator.geolocation) {

          navigator.geolocation.getCurrentPosition(function(position){

            uluru = {
              lat: position.coords.latitude, 
              lng: position.coords.longitude
            };

            alert('entra en el navigator.geolocation');
            alert(position.coords.latitude);

            /*enviar al from*/
            document.getElementById('latitud').value  = position.coords.latitude;
            document.getElementById('longitud').value = position.coords.longitude;            

          }); 

          //temporal
          uluru = {lat: -25.265529, lng: -57.582381 };
          document.getElementById('latitud').value  = -25.265529;
          document.getElementById('longitud').value = -57.58238;


        }else{

          uluru = {lat: -25.265529, lng: -57.582381 };  /* Facilandia  */
          x.innerHTML = "Geolocation is not supported by this browser.";

        }

        map     = new google.maps.Map(document.getElementById('map'), {zoom: 16, center: uluru});
        marker  = new google.maps.Marker({position: uluru, map: map, draggable:true});
        marker.addListener('dragend', function(event) {

          var coordenadas = event.latLng.toString();
          coordenadas = coordenadas.replace('(','');
          coordenadas = coordenadas.replace(')','');

          var lista = coordenadas.split(",");

          document.getElementById('latitud').value  = lista[0];
          document.getElementById('longitud').value = lista[1];

          window.setTimeout(function() {
            map.panTo(marker.getPosition());
          }, 200);

        });
      }
    </script>

<!--
    <script>

      function initMap() {

        if (navigator.geolocation) {

          navigator.geolocation.getCurrentPosition(function(position){

            var ubicacion = {
                lat: position.coords.latitude, 
                lng: position.coords.longitude
            };

            /*enviar al from*/
            document.getElementById('latitud').value = position.coords.latitude;
            document.getElementById('longitud').value =position.coords.longitude;


            // The map, centered at Uluru
            var map = new google.maps.Map(
            document.getElementById('map'), {
              zoom: 16, 
              center: ubicacion              
            });

            // The marker, positioned at Uluru
            var marker = new google.maps.Marker({
              position: ubicacion, 
              map: map,
              draggable:true
            });  
  
            marker.addListener('dragend', function(event) {

              var coordenadas = event.latLng.toString();
              coordenadas = coordenadas.replace('(','');
              coordenadas = coordenadas.replace(')','');

              var lista = coordenadas.split(",");

              document.getElementById('latitud').value  = lista[0];
              document.getElementById('longitud').value = lista[1];

              window.setTimeout(function() {
                map.panTo(marker.getPosition());
              }, 200);

          });  
         
         //  Aqui mas acciones
            
        });

        } else {

          uluru = {lat: -25.265529, lng: -57.582381 };  /* Facilandia  */
          x.innerHTML = "Geolocation is not supported by this browser.";

        } 
      }
    </script>
  -->
























  <?php 

  if(isset( $_GET['token'])){ 
    $_SESSION['token'] =  $_GET['token']; 
  }

  if(isset($_GET['zona'])){
    $_SESSION['zona'] =  $_GET['zona'];
  }

  $geo = new Geoposicion;
  $geo->empresa       = $_COOKIE['empresa'];
  $geo->cuenta        = $_SESSION['cuenta'];
  $geo->usuario       = $_COOKIE['usuario'];
  $geo->departamento  = $_COOKIE['perfil'];
  $geo->zona          = $_SESSION['zona'];

  if(isset($_POST['latitud'])  && isset($_POST['longitud'])){

    $geo->latitud       = $_POST['latitud'];
    $geo->longitud      = $_POST['longitud'];
    $geo->localizacion  = $_POST['locacion'];
    $geo->agregar_ubicacion();

  }
  ?>  

  <body>
    <div class="container">
      <br>
      <h4>Marcar Ubicación</h4>
      <div id="map"></div>
      <br>
      <div id="info">

        <form action="" method="POST" name="fomulario" id="formulario" class="form">
         <input type="text" class="form-control" readonly name="latitud" id="latitud">
         <input type="text" class="form-control" readonly name="longitud" id="longitud">


         <div class="form-group">
          <label for="locacion">Localización</label>
          <select name="locacion" id="locacion" class="form-control">
            <option value="1">Hogar</option>
            <option value="2">Familiar</option>
            <option value="3">Trabajo</option>
            <option value="0">Otro</option> 
          </select>
        </div> 

        <div class="form-group">
          <input name="verifica" id="verifica" type="checkbox" required="required">  
          <label for="verifica">Confirmo que he marcado el sitio correcto.</label>
        </div>

        <div class="form-group"> 
          <button type="submit" class="btn btn-primary">Guardar</button>  
        </div>
      </form>
    </div>
  </div>
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBjKGJYB-JB18m4AWuD8Swp4iwQoc_EOXw&callback=initMap" async defer></script>  


  <?php require('../footer.php'); ?>

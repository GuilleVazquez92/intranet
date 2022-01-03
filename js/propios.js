/**Mostrar el**/
$('#mostrar-nav').on('click',function(){
	$('#menu-nav').toggleClass('mostrar');
})

function iframeLoaded() {
  var iFrameID = document.getElementById('datos-iframe');
  if(iFrameID) {
        // here you can make the height, I delete it first, then I make it again
        iFrameID.height = "";
        iFrameID.height = iFrameID.contentWindow.document.body.scrollHeight + "px";
  }   
}


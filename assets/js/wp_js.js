$(document).ready(function(){
	//console.log(document.cookie);
	$('#myModal').css('display', 'block');
	$('.close').on('click', function(){
		$('#myModal').css('display', 'none');
	});
// $('#myBtn').on('click', function(){
	
// });

$('#wc_location_product_007').on('change', function(){
	var location_id = $(this).val();
	$.ajax({
		url: ajax_url,
		data:{
			action: 'wc_location_product',
			location_id: location_id
		},
		type: 'POST',
		success: function(res){
			if(res==1){
			location.reload(true);	
			}
			console.log(res);
		},
		error: function(err){
			console.log(err);
		}
	});
	
});

$('.location_switcher').on('change', function(){
	var now = new Date();
	var time = now.getTime();
	time += (3600*1000);
	now.setTime(time);
	document.cookie = "wc_location_product_id="+$(this).val()+"; expires="+now.toUTCString()+"; path=/;";
	//console.log(document.cookie);
	location.reload(true);
});

});




// Get the modal,
//var modal = document.getElementById('myModal');

// Get the button that opens the modal
//var btn = document.getElementById("myBtn");

// Get the <span> element that closes the modal
//var span = document.getElementsByClassName("close")[0];

// When the user clicks on the button, open the modal
// btn.onclick = function() {
//     modal.style.display = "block";
// }

// When the user clicks on <span> (x), close the modal
// span.onclick = function() {
//     modal.style.display = "none";
// }

// When the user clicks anywhere outside of the modal, close it
// window.onclick = function(event) {
//     if (event.target == modal) {
//         modal.style.display = "none";
//     }
// } 
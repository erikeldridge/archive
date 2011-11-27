<style>
.outline{
	border: 1px solid red;
}
</style>
<input/>
<ul>a</ul>
<script>
var ul = document.getElementsByTagName('ul')[0],
	i,
	outlined = null,
	handleKeyDown = function(event){
		console.log(event.keyCode);
		switch(event.keyCode){
			case 40://down
				if(outlined && outlined.nextSibling){
					outlined.className = '';
					outlined = outlined.nextSibling;
				}else{
					if('outline' === ul.lastChild.className){//handle case of wrapping around end of list
						ul.lastChild.className = '';
					}
					outlined = ul.getElementsByTagName('li')[0];
				}
				outlined.className = 'outline';
				break;
			case 38://up
				if(outlined && outlined.previousSibling){
					outlined.className = '';
					outlined = outlined.previousSibling;
					outlined.className = 'outline';
				}
				break;
			case 13://return
				if(outlined){
					console.log(outlined);
				}
				break;
		}
	};
for(i = 0; i < 5; i++){
	li = document.createElement('li');
	li.innerHTML = i;
	ul.appendChild(li);
}
document.body.onkeydown = handleKeyDown;
document.getElementsByTagName('input')[0].focus();
</script>
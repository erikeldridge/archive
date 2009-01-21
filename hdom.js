var hdom = function(root){
	var table = {},
		index = {},
	walk = function(node, callback, val, depth){
		val = val || '';
		depth = depth || 0;
		val = callback(node, val, depth);
		for(var i = 0; i < node.childNodes.length; i++){
			walk(node.childNodes[i], callback, val, depth + 1);
		}
	};
	//init
	walk(root, function(node, val, depth){
		if( node.nodeType == 8  || ( (node.nodeType == 3) && !(/[^\t\n\r ]/.test(node.data)) ) ){
			return '';
		}
		if(undefined === index[val + node.nodeName]){
			index[val + node.nodeName] = 0;
		}else{
			index[val + node.nodeName]++;
		}
		// var hash = val + node.nodeName + index[val + node.nodeName];//<-- simple version
		if(val){//<-- increased readability version
			var hash = val + '_' + node.nodeName.toLowerCase() + index[val + node.nodeName];
		}else{
			var hash = node.nodeName.toLowerCase() + index[val + node.nodeName];
		}
		table[hash] = node;
		return hash;
	});
	return {
		'table':table
	};
};

// a very simple method to generate a hash table representation of a dom tree
var hashdom = function(root){
	var ignorable = function(node){//@ref http://developer.mozilla.org/en/Whitespace_in_the_DOM
		return ( node.nodeType == 8 ) || //returns true for a comment node or 
			( (node.nodeType == 3) && //a text node that's all
				!(/[^\t\n\r ]/.test(node.data)) ); //whitespace 
	},
	table = {},
	index = {},
	walk = function(node, hash){
		var name = node.nodeName,
			test,
			i;
		if(!index[hash + name]){
			index[hash + name] = 0;
		}
		test = hash + name + index[hash + name];
		if(table[test]){//test if hash already exists
			index[hash + name] += 1;//if so, increment index for that hash val
		}
		hash += name + index[hash + name];
		table[hash] = node;
		for(i = 0; i < node.childNodes.length; i++){
			if(!ignorable(node.childNodes[i])){
				walk(node.childNodes[i], hash);
			}
		}
	},
	get = function(hash){
		return table[hash];
	},
	add = function(child, hash){
		var parent = table[hash],
			name = child.nodeName,
			test;
		if(!index[hash + name]){
			index[hash + name] = 0;
		}
		test = hash + name + index[hash + name];
		if(table[test]){//test if hash already exists
			index[hash + name]++;//if so, increment index for that hash val
		}
		hash += name + index[hash + name];
		table[hash] = child;
		parent.appendChild(child);
	},
	del = function(childHash, parentHash){
		var parent = table[parentHash],
			child = table[childHash],
			name = child.nodeName,
			i;
		for(i = 0; i < child.childNodes.length; i++){//remove all descendent entries from table
			del(childHash + child.childNodes[i].nodeName + index[childHash + child.childNodes[i].nodeName], 
				childHash);
		}
		parent.removeChild(child);
		table[childHash] = null;
		if(0 > index[parentHash + name] - 1){
			index[parentHash + name] = null;
		}else{
			index[parentHash + name]--;
		}
	};
	//init
	walk(root, '');
	return {
		'table':table,
		'index':index,
		'get':get,
		'add':add,
		'del':del
	};
};

function validate(){
  var args = Array.prototype.shift.call(arguments);

  for(var i = 0; i < arguments.length; i++){

    var type = arguments[i];
    var value = args[i];

    if( validate.is(type, value) ){
      continue;
    }

    var msg = value + " is not a " + type;

    var error = new Error(msg)

    error.arguments = args;
    error.type = 'invalid argument error';

    for(var key in error) {
      console.log(key + ':', error[key]);
    }

    throw error;
  }
}
/**
 * syntactic sugar for determining object type
 * @credit QUnit
 */
validate.is = function( type, obj ) {
  return validate.objectType( obj ) == type;
};
/**
 * unambiguous method for determining object type
 * @credit QUnit
 */
validate.objectType = function( obj ) {
  if (typeof obj === "undefined") {
      return "undefined";

  // consider: typeof null === object
  }
  if (obj === null) {
      return "null";
  }

  var type = Object.prototype.toString.call( obj )
    .match(/^\[object\s(.*)\]$/)[1] || '';

  switch (type) {
      case 'Number':
          if (isNaN(obj)) {
              return "nan";
          } else {
              return "number";
          }
      case 'String':
      case 'Boolean':
      case 'Array':
      case 'Date':
      case 'RegExp':
      case 'Function':
          return type.toLowerCase();
  }
  if (typeof obj === "object") {
      return "object";
  }
  return undefined;
};
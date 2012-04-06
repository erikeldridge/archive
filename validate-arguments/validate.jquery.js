/**
 * Validate arguments by checking type, or testing with regexp or function
 *
 * @param   arguments a sequence of arguments looking like 
 *                    arguments, test1, test2 ..., e.g. 
 *                    arguments, 'string', (/[a-z]+/) ...
 * @throws  invalidArgumentError
 * @return  undefined if validate.isActive is false
 * @note    pretty prints invalidArgumentError to log, if avail.
 */
function validate(/* arguments, test1, test2 ... */){

  if(!validate.isActive){
    return;
  }

  // Shift the arguments to validate off this fn's argument list.
  var args = Array.prototype.shift.call(arguments);

  for(var i = 0; i < arguments.length; i++){

    var test = arguments[i];
    var value = args[i];

    if( validate.is('string', test) ){

      if( validate.is(test, value) ){
        continue;
      }else{
        var msg = value + " is not a " + test;
      }

    }else if( validate.is('regexp', test) ){

      if( test.test(value) ){
        continue;
      }else{
        var msg = value + " does not match " + test;
      }

    }else if( validate.is('function', test) ){

      if( test(value) ){
        continue;
      }else{
        var msg = value + " does not pass " + test;
      }

    }else{
      var msg = "Invalid test type! Valid types are: string, regexp, function";
    }

    var error = new Error(msg);

    error.arguments = args;
    error.type = 'invalidArgumentError';

    validate.prettyPrint(error);

    throw error;
  }
}

/**
 * Setting for turning validator on/off 
 */
validate.isActive = true;

/**
 * Abstracted object logger for cross-browser safety
 * @param key is the object key
 * @param value is the object value
 */
validate.log = function(key, value){
  window.console && console.log && console.log(key, value);
};

/**
 * Dumps object fields to log
 * @obj the obj to pretty print
 */
validate.prettyPrint = function(obj){
  $.each(obj, function(key, value){
    validate.log(key + ':', value);
  });
};

/**
 * Syntactic sugar for determining object type
 */
validate.is = function( type, obj ) {
  return $.type( obj ) == type;
};

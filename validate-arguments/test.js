module('validate', {
  setup: function(){
    validate.isActive = true;
  }
});

test('all types', function(){

  var types = ['undefined', 'null', 'nan', 'number', 'string', 'boolean', 'array', 'date', 'regexp', 'function', 'object'];

  expect(types.length);

  var validValues = [undefined, null, NaN, 1, 'asd', true, [], new Date(), (/^foo$/), function(){}, {}];
  var invalidValues = ['undefined', 'null', 'NaN', '1', 123, 'true', '[]', 'new Date()', '(/^foo$/)', 'function(){}', '{}'];

  for(var i = 0; i < types.length; i++){

    var type = types[i];
    var valid = validValues[i];
    var invalid = invalidValues[i];

    var rabbit = function(arg){
      validate(arguments, type);
    };

    try{
      rabbit(valid);
    }catch(e){
      console.log('validate - error - debug info: ', i, type, valid, invalid);
      ok(false, "should not throw error on valid arg ");
    }

    try{
      rabbit(invalid);
    }catch(e){
      equal(e.message, invalid + " is not a " + type, "should throw error on invalid arg");
    }
  }

});


test('multiple args', function(){

  expect(3);

  function rabbit(foo, bar){
    validate(arguments, 'number', 'string');
  }

  try{
    rabbit('123', '456');
  }catch(e){
    equal(e.message, "123 is not a number", "should throw error on string arg");
  }

  try{
    rabbit(123, 456);
  }catch(e){
    equal(e.message, "456 is not a string", "should throw error on string arg");
  }

  rabbit(123, '456');
  ok(true, "two numbers should pass");
});

test('prettyPrint', function(){

  expect(2);

  // mock validate.log
  var cache = validate.log;
  validate.log = function(key, value){
    strictEqual(key, 'foo:', "should pass correct key");
    strictEqual(value, 'bar', "should pass correct key");
  };

  validate.prettyPrint({'foo':'bar'});

  // reset log
  validate.log = cache;

});

test('isActive setting', function(){

  expect(1);

  validate.isActive = false;

  function rabbit(foo){
    validate(arguments, 'string');
  }

  try{
    rabbit(456);
  }catch(e){
    ok(false, "should deactivate validation when false");
  }

  validate.isActive = true;

  try{
    rabbit(123);
  }catch(e){
    ok(true, "should activate validation when true");
  }

});

test('regexp test for alpha string', function(){

  expect(1);

  function rabbit(foo){
    validate(arguments, (/[a-z]+/) );
  }

  try{
    rabbit('asd');
  }catch(e){
    ok(false, "should pass on alpha chars");
  }

  try{
    rabbit(123);
  }catch(e){
    ok(true, "should fail on numeric input");
  }

});

test('functional test for hash', function(){

  expect(1);

  function rabbit(foo){
    validate(arguments, function(arg){ return 'bar' === arg.foo; } );
  }

  try{
    rabbit({foo:'bar'});
  }catch(e){
    ok(false, "should pass w/ valid input");
  }

  try{
    rabbit("{foo:'bar'}");
  }catch(e){
    ok(true, "should fail on invalid input");
  }

});
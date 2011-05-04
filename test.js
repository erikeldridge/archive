module('validate');

test('single string arg', function(){

  expect(1);

  function rabbit(foo){
    validate(arguments, 'string');
  }

  try{
    rabbit(123);
  }catch(e){
    ok(e.message === "123 is not a string");
  }

});

test('multi string arg', function(){

  expect(3);

  function rabbit(foo, bar){
    validate(arguments, 'string', 'string');
  }

  try{
    rabbit(123, 'asd');
  }catch(e){
    ok(e.message === "123 is not a string");
  }
  try{
    rabbit('asd', 456);
  }catch(e){
    ok(e.message === "456 is not a string");
  }

  rabbit('asd', '456');
  ok(true, "two strings should pass");
});

test('single number arg', function(){

  expect(3);

  function rabbit(foo){
    validate(arguments, 'number');
  }

  try{
    rabbit('123');
  }catch(e){
    equal(e.message, "123 is not a number", "should throw error on string arg");
  }

  try{
    rabbit(NaN);
  }catch(e){
    equal(e.message, "NaN is not a number", "should throw error on NaN arg");
  }

  rabbit(456);
  ok(true, "a single number should pass");
});

test('multi number arg', function(){

  expect(3);

  function rabbit(foo, bar){
    validate(arguments, 'number', 'number');
  }

  try{
    rabbit('123', 456);
  }catch(e){
    equal(e.message, "123 is not a number", "should throw error on string arg");
  }

  try{
    rabbit(123, '456');
  }catch(e){
    equal(e.message, "456 is not a number", "should throw error on string arg");
  }

  rabbit(123, 456);
  ok(true, "two numbers should pass");
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
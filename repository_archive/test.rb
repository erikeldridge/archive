def deep_compare(hash1, hash2)

  # handle nested hashes
  if hash1.kind_of? Hash and hash2.kind_of? Hash

    # compare each key in hash 1 w/ corresponding key in hash 2
    hash1.keys.all? do |key|
      if hash2.has_key? key
        deep_compare(hash1[key], hash2[key])
      else
        false
      end
    end

  # handle leaves
  else
    hash1 == hash2
  end

end

class Hash
  def deep_include?(sub_hash)
    sub_hash.keys.all? do |key|
      self.has_key?(key) && if sub_hash[key].is_a?(Hash)
        self[key].is_a?(Hash) && self[key].deep_include?(sub_hash[key])
      else
        self[key] == sub_hash[key]
      end
    end
  end
end

require "test/unit"

class BasicTest < Test::Unit::TestCase
  def test_two_nonmatching_hashes
    assert_equal( false, deep_compare({"a" => 1 }, {"a" => 2}) )
  end
  def test_two_matching_hashes
    assert_equal( true, deep_compare({"a" => 1 }, {"a" => 1}) )
  end
  def test_two_matching_nested_hashes
    assert_equal( true, deep_compare({"a" => {"b" => 1} }, {"a" => {"b" => 1} }) )
  end
  def test_two_nonmatching_nested_hashes
    assert_equal( false, deep_compare({"a" => {"b" => 1} }, {"a" => {"b" => 2} }) )
  end
  def test_two_matching_nested_hashes_with_other_attributes
    assert_equal( true, deep_compare({"a" => {"b" => 1} }, {"a" => {"b" => 1, "c" => 2} }) )
  end
end
<?php 
class Beginner
{
	public function helloWorld()
	{
		echo "Hello World";
	}

	public function sayHello($name)
	{
		echo "Hello ".$name;
	}

	public function palindrome($word)
	{
		if ($word == strrev($word))
			echo "The word '".$word."' is a palindrome"; 
		else
			echo "The word '".$word."' is NOT a palindrome";
	}

	public function factorial($num)
	{
		if ($num>0)
			return $num*$this->factorial($num-1);
		else 
			return 1;
	}
}

?>
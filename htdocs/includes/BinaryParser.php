<?php
/******************************************************

-[ DEVELOPER ]-----------------------------------------
Jonas Raoni Soares Silva
------------------------
jonasraoni@gmail.com
http://www.joninhas.ath.cx:666

-[ CLASS ]---------------------------------------------
My user account at phpclasses.org
http://www.phpclasses.org/browse.html/author/84147.html

Get the latest version of this class at:
http://www.phpclasses.org/browse.html/package/2454.html

-[ VERSION & HISTORY ]---------------------------------
v1.0
  2005-07-21 - Class creation


--
My birthday is tomorrow haha XD And I'm sick and tired
of this idiot computer, it's boring... Gotta travell =]


******************************************************/


#--[ Constants ]---------------------------------------
//as i didnt found these constants, i emulated them with some help from the manual XD
define( "NOT_A_NUMBER", acos(1.01) );
define( "POSITIVE_INFINITY", -log(0) );
define( "NEGATIVE_INFINITY", log(0) );


#--[ BinaryBuffer Class ]------------------------------------
class BinaryBuffer{
	private $bigEndian = false,
	$buffer = array();
	public $length = 0;

	function __construct( $bigEndian = false, $buffer = '' ){
		$this->bigEndian = $bigEndian;
		$this->setBuffer( $buffer );
	}

	private function shl( $a, $b ){
		for( ; $b--; $a = ( ( $a %= 0x7fffffff + 1 ) & 0x40000000 ) == 0x40000000 ? $a * 2 : ( $a - 0x40000000 ) * 2 + 0x7fffffff + 1 );
		return $a;
	}

	public function setBuffer( $data ){
		if( gettype( $data ) == 'string' && strlen( $data ) ){
			for( $this->buffer = array(), $i = strlen( $data ); $i; $this->buffer[] = ord( $data[--$i] ) );
			if( $this->bigEndian )
				$this->buffer = array_reverse( $this->buffer );
			$this->length = count( $this->buffer );
		}
	}

	public function getBuffer(){
		return $this->buffer;
	}

	public function hasNeededBits( $neededBits ){
		return count( $this->buffer ) >= -( -$neededBits >> 3 );
	}

	public function checkBuffer( $neededBits ){
		if( !$this->hasNeededBits( $neededBits ) )
			throw new Exception( __METHOD__ . ": missing bytes" );
	}

	public function byte( $i ){
		return $this->buffer[ $i ];
	}

	public function setEndian( $bigEndian ){
		if( $this->$bigEndian != $bigEndian )
			$this->buffer = array_reverse( $this->buffer );
	}

	public function readBits( $start, $length ){
		if( $start < 0 || $length <= 0 )
			return 0;
		$this->checkBuffer( $start + $length );
		$offsetRight = $start % 8;
		$curByte = count( $this->buffer ) - ( $start >> 3 ) - 1;
		$lastByte = count( $this->buffer ) + ( -( $start + $length ) >> 3 );
		$diff = $curByte - $lastByte;
		$sum = ( ( $this->buffer[ $curByte ] >> $offsetRight ) & ( ( 1 << ( $diff ? 8 - $offsetRight : $length ) ) - 1 ) ) + ( $diff && ( $offsetLeft = ( $start + $length ) % 8 ) ? ( $this->buffer[ $lastByte++ ] & ( ( 1 << $offsetLeft ) - 1 ) ) << ( $diff-- << 3 ) - $offsetRight : 0 );
		for( ; $diff; $sum += self::shl( $this->buffer[ $lastByte++ ], ( $diff-- << 3 ) - $offsetRight ) );
		return $sum;
	}
}

#--[ BinaryParser Class ]------------------------------
class BinaryParser{
	public $bigEndian = false;

	function __construct( $bigEndian = false ){
		$this->bigEndian = $bigEndian;
	}

 	protected function decodeFloat( $data, $precisionBits, $exponentBits ){
		$bias = pow( 2, $exponentBits - 1 ) - 1;
		$buffer = new BinaryBuffer( $this->bigEndian, $data );
		$buffer->checkBuffer( $precisionBits + $exponentBits + 1 );
		$signal = $buffer->readBits( $precisionBits + $exponentBits, 1 );
		$exponent = $buffer->readBits( $precisionBits, $exponentBits );
		$significand = 0;
		$divisor = 2;
		$curByte = $buffer->length + ( -$precisionBits >> 3 ) - 1;
		do {
			for( $byteValue = $buffer->byte( ++$curByte ), $startBit = ( $startBit = $precisionBits % 8 ) ? $startBit : 8, $mask = 1 << $startBit; $mask >>= 1; $divisor *= 2 )
				if( $byteValue & $mask )
					$significand += 1 / $divisor;
		} while( $precisionBits -= $startBit );
		return $exponent == ( $bias << 1 ) + 1 ? ( $significand ? NOT_A_NUMBER : ( $signal ? NEGATIVE_INFINITY : POSITIVE_INFINITY ) ) : ( 1 + $signal * -2 ) * ( $exponent || $significand ? ( !$exponent ? pow( 2, -$bias + 1 ) * $significand : pow( 2, $exponent - $bias ) * ( 1 + $significand ) ) : 0 );
	}

	protected function decodeInt( $data, $bits, $signed ){
		$buffer = new BinaryBuffer( $this->bigEndian, $data );
		$x = $buffer->readBits( 0, $bits );
		return $signed && $x >= ( $max = pow( 2, $bits ) ) / 2 ? $x - $max : $x;
	}

	protected function encodeFloat( $data, $precisionBits, $exponentBits ){
		$bias = pow( 2, $exponentBits - 1 ) - 1;
		$minExp = -$bias + 1;
		$maxExp = $bias;
		$minUnnormExp = $minExp - $precisionBits;
		$status = is_nan( $n = (float)$data ) || $n == NEGATIVE_INFINITY || $n == POSITIVE_INFINITY ? $n : 0;
		$exp = 0;
		$len = 2 * $bias + 1 + $precisionBits + 3;
		$bin = array_pad( array(), $len, 0 );
		$signal = ( $n = $status !== 0 ? 0 : $n ) < 0;
		$n = abs( $n );
		$intPart = floor( $n );
		$floatPart = $n - $intPart;
		for( $i = $bias + 2; $intPart && $i; $bin[--$i] = abs( $intPart % 2 ), $intPart = floor( $intPart / 2 ) );
		for( $i = $bias + 1; $floatPart > 0 && $i; )
			if( $bin[++$i] = ( ( $floatPart *= 2 ) >= 1 ) - 0 )
				--$floatPart;
		for( $i = -1; ++$i < $len && !$bin[$i]; );
		$i = ( $exp = $bias + 1 - $i ) >= $minExp && $exp <= $maxExp ? $i + 1 : $bias + 1 - ( $exp = $minExp - 1 );
		if( $bin[( $lastBit = $precisionBits - 1 + $i ) + 1] ){
			if( !( $rounded = $bin[$lastBit] ) )
				for( $j = $lastBit + 2; !$rounded && $j < $len; $rounded = $bin[$j++] );
			for( $j = $lastBit + 1; $rounded && --$j >= 0; )
				if( $bin[$j] = !$bin[$j] - 0 )
					$rounded = 0;
		}
		for( $i = $i - 2 < 0 ? -1 : $i - 3; ++$i < $len && !$bin[$i]; );
		if( ( $exp = $bias + 1 - $i ) >= $minExp && $exp <= $maxExp )
			++$i;
		else if( $exp < $minExp ){
			if( $exp != $bias + 1 - $len && $exp < $minUnnormExp )
				throw new Exception( __METHOD__ . ": underflow" );
				$i = $bias + 1 - ( $exp = $minExp - 1 );
		}
		if( $intPart || $status !== 0 ){
			throw new Exception( __METHOD__ . ": " . ( $intPart ? "overflow" : $status ) );
			$exp = $maxExp + 1;
			$i = $bias + 2;
			if( $status == NEGATIVE_INFINITY )
				$signal = 1;
			else if( is_nan( $status ) )
				$bin[$i] = 1;
		}
		for( $n = abs( $exp + $bias ), $j = $exponentBits + 1, $result = ""; --$j; $result = ( $n % 2 ) . $result, $n = $n >>= 1 );
		$result = ( $signal ? "1" : "0" ) . $result . implode( "", array_slice( $bin, $i, $precisionBits ) );
		for( $n = 0, $j = 0, $i = strlen( $result ), $r = array(); $i; $j = ( $j + 1 ) % 8 ){
			$n += ( 1 << $j ) * $result[--$i];
			if( $j == 7 ){
				$r[] = chr( $n );
				$n = 0;
			}
		}
		$r[] = $n ? chr( $n ) : "";
		return implode( "", ( $this->bigEndian ? array_reverse( $r ) : $r ) );
	}

	protected function encodeInt( $data, $bits, $signed ){
	 	if( $data >= ( $max = pow( 2, $bits ) ) || $data < -( $max >> 1 ) )
			throw new Exception( __METHOD__ . ": overflow" );
		if( $data < 0 )
			$data += $max;
		for( $r = array(); $data; $r[] = chr( $data % 256 ), $data = floor( $data / 256 ) );
		for( $bits = -( -$bits >> 3 ) - count( $r ); $bits--; $r[] = "\0" );
		return implode( "", ( $this->bigEndian ? array_reverse( $r ) : $r ) );
	}

	public function toSmall   ( $data ){ return $this->decodeInt( $data,  8, true  ); }
	public function fromSmall ( $data ){ return $this->encodeInt( $data,  8, true  ); }
	public function toByte    ( $data ){ return $this->decodeInt( $data,  8, false ); }
	public function fromByte  ( $data ){ return $this->encodeInt( $data,  8, false ); }
	public function toShort   ( $data ){ return $this->decodeInt( $data, 16, true  ); }
	public function fromShort ( $data ){ return $this->encodeInt( $data, 16, true  ); }
	public function toWord    ( $data ){ return $this->decodeInt( $data, 16, false ); }
	public function fromWord  ( $data ){ return $this->encodeInt( $data, 16, false ); }
	public function toInt     ( $data ){ return $this->decodeInt( $data, 32, true  ); }
	public function fromInt   ( $data ){ return $this->encodeInt( $data, 32, true  ); }
	public function toDWord   ( $data ){ return $this->decodeInt( $data, 32, false ); }
	public function fromDWord ( $data ){ return $this->encodeInt( $data, 32, false ); }
	public function toFloat   ( $data ){ return $this->decodeFloat( $data, 23, 8   ); }
	public function fromFloat ( $data ){ return $this->encodeFloat( $data, 23, 8   ); }
	public function toDouble  ( $data ){ return $this->decodeFloat( $data, 52, 11  ); }
	public function fromDouble( $data ){ return $this->encodeFloat( $data, 52, 11  ); }
}
?>
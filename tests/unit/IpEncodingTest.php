<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class IpEncodingTest extends TestCase
{
    /**
     * Test the IP encoding functionality.
     *
     * @return void
     */
    public function testEncodeIpIPv4()
    {
        require_once 'includes/functions.php';
        $ip = '192.168.0.1';
        $expected = 'c0a80001';
        $result = encode_ip($ip);
        $this->assertEquals($expected, $result);
    }

    public function testEncodeIpIPv6()
    {
        require_once 'includes/functions.php';
        $ip = '2001:0db8:85a3:0000:0000:8a2e:0370:7334';
        $expected = '20010db885a3000000008a2e03707334';
        $result = encode_ip($ip);
        $this->assertEquals($expected, $result);
    }


    public function testDecodeIpReturnsIPv4FormatForIPv4EncodedInput()
    {
        require_once 'includes/functions.php';
        $hexIp = 'c0a80001'; // IPv4 address encoded in hex format
        
        $decodedIp = decode_ip($hexIp);
        
        $this->assertEquals('192.168.0.1', $decodedIp);
    }
    
    public function testDecodeIpReturnsIPv6FormatForIPv6EncodedInput()
    {
        require_once 'includes/functions.php';
        $hexIp = '20010db885a3000000008a2e03707334'; // IPv6 address encoded in hex format
        
        $decodedIp = decode_ip($hexIp);
        
        $this->assertEquals('2001:db8:85a3::8a2e:370:7334', $decodedIp);
    }
        
    

}



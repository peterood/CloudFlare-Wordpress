<?php

use CloudFlare\IpRewrite;

class Rewrite_Test extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        IpRewrite::reset();
    }

    public function tearDown()
    {
        unset($_SERVER["HTTP_CF_CONNECTING_IP"]);
        unset($_SERVER["REMOTE_ADDR"]);
    }
    
    public function testOnCloudFlareIPv4()
    {
        $remote_addr = '103.21.244.2';
        $connecting_ip = '8.8.8.8';
        
        $_SERVER["REMOTE_ADDR"] = $remote_addr;
        $_SERVER["HTTP_CF_CONNECTING_IP"] = $connecting_ip;
        
        $this->assertTrue(IpRewrite::isCloudFlare());
        $this->assertEquals(IpRewrite::getRewrittenIP(), $connecting_ip);
        $this->assertEquals(IpRewrite::getOriginalIP(), $remote_addr);
    }
    
    public function testOffCloudFlareIPv4()
    {
        $remote_addr = '8.8.8.8';
        
        $_SERVER["REMOTE_ADDR"] = $remote_addr;
        
        $this->assertFalse(IpRewrite::isCloudFlare());
        $this->assertFalse(IpRewrite::getRewrittenIP());
        $this->assertEquals(IpRewrite::getOriginalIP(), $remote_addr);
    }
    
    public function testOffCloudFlareIPv4FakeModCloudflare()
    {
        $remote_addr = '8.8.8.8';
        $connecting_ip = '8.8.4.4';
        
        $_SERVER["REMOTE_ADDR"] = $remote_addr;
        $_SERVER["HTTP_CF_CONNECTING_IP"] = $connecting_ip;
        
        $this->assertTrue(IpRewrite::isCloudFlare());
        $this->assertFalse(IpRewrite::getRewrittenIP());
        $this->assertEquals(IpRewrite::getOriginalIP(), $remote_addr);
    }
    
    public function testOnlyProcessOnce()
    {
        $remote_addr = '108.162.192.2';
        $connecting_ip = '8.8.8.8';
        
        $_SERVER["REMOTE_ADDR"] = $remote_addr;
        $_SERVER["HTTP_CF_CONNECTING_IP"] = $connecting_ip;
        
        $this->assertTrue(IpRewrite::isCloudFlare());
        $this->assertEquals(IpRewrite::getRewrittenIP(), $connecting_ip);
        $this->assertEquals(IpRewrite::getOriginalIP(), $remote_addr);
        
        // swap values and expect the original still, since it only allows one run per load
        $remote_addr2 = '103.21.244.2';
        $connecting_ip2 = '8.8.4.4';
        
        $_SERVER["REMOTE_ADDR"] = $remote_addr2;
        $_SERVER["HTTP_CF_CONNECTING_IP"] = $connecting_ip2;
        
        $this->assertTrue(IpRewrite::isCloudFlare());
        $this->assertEquals(IpRewrite::getRewrittenIP(), $connecting_ip);
        $this->assertEquals(IpRewrite::getOriginalIP(), $remote_addr);
    }
    
    public function testOnCloudFlareIPv6()
    {
        $remote_addr = '2803:f800::23';
        $connecting_ip = '2001:4860:4860::8888';
        
        $_SERVER["REMOTE_ADDR"] = $remote_addr;
        $_SERVER["HTTP_CF_CONNECTING_IP"] = $connecting_ip;
        
        $this->assertTrue(IpRewrite::isCloudFlare());
        $this->assertEquals(IpRewrite::getRewrittenIP(), $connecting_ip);
        $this->assertEquals(IpRewrite::getOriginalIP(), $remote_addr);
    }
    
    public function testOffCloudFlareIPv6()
    {
        $remote_addr = '2001:4860:4860::8888';
        
        $_SERVER["REMOTE_ADDR"] = $remote_addr;
        
        $this->assertFalse(IpRewrite::isCloudFlare());
        $this->assertFalse(IpRewrite::getRewrittenIP());
        $this->assertEquals(IpRewrite::getOriginalIP(), $remote_addr);
    }
}

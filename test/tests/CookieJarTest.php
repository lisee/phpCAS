<?php
require_once 'PHPUnit/Framework.php';

require_once dirname(__FILE__).'/../../source/CAS/CookieJar.php';


/**
 * Test harness for the cookie Jar to allow us to test protected methods.
 *
 */
class CAS_CookieJarExposed extends CAS_CookieJar {
    public function __call($method, array $args = array()) {
        if (!method_exists($this, $method))
            throw new BadMethodCallException("method '$method' does not exist");
        return call_user_method_array($method, $this, $args);
    }
}


/**
 * Test class for verifying the operation of cookie handling methods used in
 * serviceWeb() proxy calls.
 *
 *
 * Generated by PHPUnit on 2010-09-07 at 13:33:53.
 */
class CookieJarTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var CASClient
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
	$this->cookieArray = array();
        $this->object = new CAS_CookieJarExposed($this->cookieArray);

        $this->serviceUrl_1 = 'http://service.example.com/lookup/?action=search&query=username';
        $this->responseHeaders_1 = array(
		'HTTP/1.1 302 Found',
		'Date: Tue, 07 Sep 2010 17:51:54 GMT',
		'Server: Apache/2.2.3 (Red Hat)',
		'X-Powered-By: PHP/5.1.6',
		'Set-Cookie: SID=k1jut1r1bqrumpei837kk4jks0; path=/',
		'Expires: Thu, 19 Nov 1981 08:52:00 GMT',
		'Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0',
		'Pragma: no-cache',
		'Location: https://cas.example.edu:443/cas/login?service=http%3A%2F%2Fservice.example.edu%2Flookup%2F%3Faction%3Dsearch%26query%3Dusername',
		'Content-Length: 525',
		'Connection: close',
		'Content-Type: text/html; charset=UTF-8',
        );
        $this->serviceUrl_1b = 'http://service.example.com/lookup/?action=search&query=another_username';
        $this->serviceUrl_1c = 'http://service.example.com/make_changes.php';

        // Verify that there are no cookies to start.
	$this->assertEquals(0, count($this->object->getCookies($this->serviceUrl_1)));
	$this->assertEquals(0, count($this->object->getCookies($this->serviceUrl_1b)));
	$this->assertEquals(0, count($this->object->getCookies($this->serviceUrl_1c)));

	// Add service cookies as if we just made are request to serviceUrl_1
	// and recieved responseHeaders_1 as the header to the response.
        $this->object->storeCookies($this->serviceUrl_1, $this->responseHeaders_1);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {

    }

/*********************************************************
 * Tests of public (interface) methods
 *********************************************************/

    /**
     * Verify that our first response will set a cookie that will be available to
     * the same URL.
     */
    public function test_public_getCookies_SameUrl()
    {
        // Verify that our cookie is available.
        $cookies = $this->object->getCookies($this->serviceUrl_1);
        $this->assertEquals(1, count($cookies));
        $this->assertEquals('k1jut1r1bqrumpei837kk4jks0', $cookies['SID']);
    }

    /**
     * Verify that our first response will set a cookie that is available to a second
     * request to a different url on the same host.
     */
    public function test_public_getCookies_SamePathDifferentQuery()
    {
        // Verify that our cookie is available.
        $cookies = $this->object->getCookies($this->serviceUrl_1b);
        $this->assertEquals(1, count($cookies));
        $this->assertEquals('k1jut1r1bqrumpei837kk4jks0', $cookies['SID']);
    }

    /**
     * Verify that our first response will set a cookie that is available to a second
     * request to a different url on the same host.
     */
    public function test_public_getCookies_DifferentPath()
    {
        // Verify that our cookie is available.
        $cookies = $this->object->getCookies($this->serviceUrl_1c);
        $this->assertEquals(1, count($cookies));
        $this->assertEquals('k1jut1r1bqrumpei837kk4jks0', $cookies['SID']);
    }

    /**
     * Verify that our cookies set with the 'secure' token will only go to https URLs.
     */
    public function test_public_getCookies_Secure()
    {
        $headers = array('Set-Cookie: person="bob jones"; path=/; Secure');
        $url = 'https://service.example.com/lookup/?action=search&query=username';
        $this->object->storeCookies($url, $headers);

        // Ensure that only the SID cookie not available to non https URLs
        $cookies = $this->object->getCookies('http://service.example.com/lookup/');
        $this->assertArrayHasKey('SID', $cookies);
        $this->assertEquals('k1jut1r1bqrumpei837kk4jks0', $cookies['SID']);
        $this->assertArrayNotHasKey('person', $cookies);

        // Ensure that the SID cookie is avalailable to https urls.
        $cookies = $this->object->getCookies('https://service.example.com/lookup/');
        $this->assertArrayHasKey('SID', $cookies);
        $this->assertEquals('k1jut1r1bqrumpei837kk4jks0', $cookies['SID']);
        $this->assertArrayHasKey('person', $cookies);
        $this->assertEquals('bob jones', $cookies['person']);
    }

    /**
     * Verify that our cookies set with the 'secure' token will only go to https URLs.
     */
    public function test_public_getCookies_SecureLC()
    {
        $headers = array('Set-Cookie: person="bob jones"; path=/; secure');
        $url = 'https://service.example.com/lookup/?action=search&query=username';
        $this->object->storeCookies($url, $headers);

        // Ensure that only the SID cookie not available to non https URLs
        $cookies = $this->object->getCookies('http://service.example.com/lookup/');
        $this->assertArrayHasKey('SID', $cookies);
        $this->assertEquals('k1jut1r1bqrumpei837kk4jks0', $cookies['SID']);
        $this->assertArrayNotHasKey('person', $cookies);

        // Ensure that the SID cookie is avalailable to https urls.
        $cookies = $this->object->getCookies('https://service.example.com/lookup/');
        $this->assertArrayHasKey('SID', $cookies);
        $this->assertEquals('k1jut1r1bqrumpei837kk4jks0', $cookies['SID']);
        $this->assertArrayHasKey('person', $cookies);
        $this->assertEquals('bob jones', $cookies['person']);
    }

    /**
     * Verify that when no domain is set for the cookie, it will be unavailable
     * to other hosts
     */
    public function test_public_getCookies_DifferentHost()
    {
        // Verify that our cookie isn't available when the hostname is changed.
        $cookies = $this->object->getCookies('http://service2.example.com/make_changes.php');
        $this->assertEquals(0, count($cookies));

        // Verify that our cookie isn't available when the domain is changed.
        $cookies = $this->object->getCookies('http://service.example2.com/make_changes.php');
        $this->assertEquals(0, count($cookies));

        // Verify that our cookie isn't available when the tdl is changed.
        $cookies = $this->object->getCookies('http://service.example.org/make_changes.php');
        $this->assertEquals(0, count($cookies));
    }

    /**
     * Verify that cookies are getting stored in our storage array.
     *
     */
    public function test_public_storeCookies() {
	$array = array();
	$cookieJar = new CAS_CookieJar($array);
	$this->assertEquals(0, count($array));
	$cookieJar->storeCookies($this->serviceUrl_1, $this->responseHeaders_1);
	$this->assertEquals(1, count($array));
    }

    /**
     * Verify that cookie header with max-age value will be available for that
     * length of time.
     */
    public function test_public_storeCookies_MaxAge() {
	// Verify that we have on cookie to start.
	$this->assertEquals(1, count($this->object->getCookies($this->serviceUrl_1)));

	// Send set-cookie header to remove the cookie
	$headers = array('Set-Cookie2: person="bob jones"; path=/; max-age=2');
	$this->object->storeCookies($this->serviceUrl_1, $headers);

	// Ensure that the cookie exists after 1 second
	sleep(1);
	$cookies = $this->object->getCookies($this->serviceUrl_1);
	$this->assertArrayHasKey('person', $cookies);
	$this->assertEquals('bob jones', $cookies['person']);

	// Wait 3 total seconds and then ensure that the cookie has been removed
	sleep(2);
	$cookies = $this->object->getCookies($this->serviceUrl_1);
	$this->assertArrayNotHasKey('person', $cookies);
    }

    /**
     * Verify that cookie header with max-age=0 will remove the cookie.
     * Documented in RFC2965 section 3.2.2
     * http://www.ietf.org/rfc/rfc2965.txt
     *
     */
    public function test_public_storeCookies_RemoveViaMaxAge0() {
	// Verify that we have on cookie to start.
	$this->assertEquals(1, count($this->object->getCookies($this->serviceUrl_1)));

	// Send set-cookie header to remove the cookie
	$headers = array('Set-Cookie2: SID=k1jut1r1bqrumpei837kk4jks0; path=/; max-age=0');
	$this->object->storeCookies($this->serviceUrl_1, $headers);

	$this->assertEquals(0, count($this->object->getCookies($this->serviceUrl_1)));
    }

    /**
     * Verify that cookie header with expires in the past will remove the cookie.
     * Documented in RFC2965 section 3.2.2
     * http://www.ietf.org/rfc/rfc2965.txt
     *
     */
    public function test_public_storeCookies_RemoveViaExpiresPast() {
	// Verify that we have on cookie to start.
	$this->assertEquals(1, count($this->object->getCookies($this->serviceUrl_1)));

	// Send set-cookie header to remove the cookie
	$headers = array('Set-Cookie: SID=k1jut1r1bqrumpei837kk4jks0; path=/; expires=Fri, 31-Dec-2009 23:59:59 GMT');
	$this->object->storeCookies($this->serviceUrl_1, $headers);

	$this->assertEquals(0, count($this->object->getCookies($this->serviceUrl_1)));
    }

    /**
     * Verify that cookie header that expires in the past will not be stored.
     *
     * http://www.ietf.org/rfc/rfc2965.txt
     *
     */
    public function test_public_storeCookies_DontStoreExpiresPast() {
	// Verify that we have on cookie to start.
	$this->assertEquals(1, count($this->object->getCookies($this->serviceUrl_1)));

	// Send set-cookie header to remove the cookie
	$headers = array('Set-Cookie: bob=jones; path=/; expires='.gmdate('D, d-M-Y H:i:s e', time() - 90000));
	$this->object->storeCookies($this->serviceUrl_1, $headers);

	$cookies = $this->object->getCookies($this->serviceUrl_1);
	$this->assertEquals(1, count($cookies));
	$this->assertArrayNotHasKey('jones', $cookies);
    }

    /**
     * Verify that cookie header that expires in the futre will not be removed.
     *
     * http://www.ietf.org/rfc/rfc2965.txt
     *
     */
    public function test_public_storeCookies_ExpiresFuture() {
	// Verify that we have on cookie to start.
	$this->assertEquals(1, count($this->object->getCookies($this->serviceUrl_1)));

	// Send set-cookie header to remove the cookie
	$headers = array('Set-Cookie: bob=jones; path=/; expires='.gmdate('D, d-M-Y H:i:s e', time() + 600));
	$this->object->storeCookies($this->serviceUrl_1, $headers);

	$cookies = $this->object->getCookies($this->serviceUrl_1);
	$this->assertEquals(2, count($cookies));
	$this->assertEquals('jones', $cookies['bob']);
    }

    /**
     * Test the inclusion of a semicolon in a quoted cookie value.
     *
     * Note: As of September 12th, the current implementation is known to
     * fail this test since it explodes values on the semicolon symbol. This
     * behavior is not ideal but should be ok for most cases.
     */
    public function test_public_storeCookies_QuotedSemicolon()
    {
	$headers = array('Set-Cookie: SID="hello;world"; path=/; domain=.example.com');
        $this->object->storeCookies($this->serviceUrl_1, $headers);

        $cookies = $this->object->getCookies($this->serviceUrl_1b);

        $this->assertType('array', $cookies);
        $this->assertEquals('hello;world', $cookies['SID'], "\tNote: The implementation as of Sept 15, 2010 makes the assumption \n\tthat semicolons will not be present in quoted attribute values. \n\tWhile attribute values that contain semicolons are allowed by \n\tRFC2965, they are hopefully rare enough to ignore for our purposes.");
        $this->assertEquals(1, count($cookies));
    }

    /**
     * Test the inclusion of an equals in a quoted cookie value.
     *
     * Note: As of September 12th, the current implementation is known to
     * fail this test since it explodes values on the equals symbol. This
     * behavior is not ideal but should be ok for most cases.
     */
    public function test_public_storeCookies_QuotedEquals()
    {
	$headers = array('Set-Cookie: SID="hello=world"; path=/; domain=.example.com');
        $this->object->storeCookies($this->serviceUrl_1, $headers);

        $cookies = $this->object->getCookies($this->serviceUrl_1b);

        $this->assertType('array', $cookies);
        $this->assertEquals('hello=world', $cookies['SID'], "\tNote: The implementation as of Sept 15, 2010 makes the assumption \n\tthat equals symbols will not be present in quoted attribute values. \n\tWhile attribute values that contain equals symbols are allowed by \n\tRFC2965, they are hopefully rare enough to ignore for our purposes.");
        $this->assertEquals(1, count($cookies));
    }


    /**
     * Test the inclusion of an escaped quote in a quoted cookie value.
     */
    public function test_public_storeCookies_QuotedEscapedQuote()
    {
	$headers = array('Set-Cookie: SID="hello\"world"; path=/; domain=.example.com');
        $this->object->storeCookies($this->serviceUrl_1, $headers);

        $cookies = $this->object->getCookies($this->serviceUrl_1b);

        $this->assertType('array', $cookies);
        $this->assertEquals('hello"world', $cookies['SID']);
        $this->assertEquals(1, count($cookies));
    }

/*********************************************************
 * Tests of protected (implementation) methods
 *
 * Most of these should likely be reworked to test their edge
 * cases via the two public methods to allow refactoring of the
 * protected methods without breaking the tests.
 *********************************************************/


    /**
     * Test the basic operation of parseCookieHeaders.
     */
    public function test_protected_parseCookieHeaders()
    {
        $cookies = $this->object->parseCookieHeaders($this->responseHeaders_1, 'service.example.com');

        $this->assertType('array', $cookies);
        $this->assertEquals(1, count($cookies));
        $this->assertEquals('SID', $cookies[0]['name']);
        $this->assertEquals('k1jut1r1bqrumpei837kk4jks0', $cookies[0]['value']);
        $this->assertEquals('/', $cookies[0]['path']);
        $this->assertEquals('service.example.com', $cookies[0]['domain']);
        $this->assertFalse($cookies[0]['secure']);
    }

    /**
     * Test the addition of a domain to the parsing of cookie headers
     */
    public function test_protected_parseCookieHeaders_WithDomain()
    {
	$headers = array('Set-Cookie: SID=k1jut1r1bqrumpei837kk4jks0; path=/; domain=.example.com');
        $cookies = $this->object->parseCookieHeaders($headers, 'service.example.com');

        $this->assertType('array', $cookies);
        $this->assertEquals(1, count($cookies));
        $this->assertEquals('SID', $cookies[0]['name']);
        $this->assertEquals('k1jut1r1bqrumpei837kk4jks0', $cookies[0]['value']);
        $this->assertEquals('/', $cookies[0]['path']);
        $this->assertEquals('.example.com', $cookies[0]['domain']);
        $this->assertFalse($cookies[0]['secure']);
    }

    /**
     * Test the addition of a domain to the parsing of cookie headers
     */
    public function test_protected_parseCookieHeaders_WithHostname()
    {
	$headers = array('Set-Cookie: SID=k1jut1r1bqrumpei837kk4jks0; path=/; domain=service.example.com');
        $cookies = $this->object->parseCookieHeaders($headers, 'service.example.com');

        $this->assertType('array', $cookies);
        $this->assertEquals(1, count($cookies));
        $this->assertEquals('SID', $cookies[0]['name']);
        $this->assertEquals('k1jut1r1bqrumpei837kk4jks0', $cookies[0]['value']);
        $this->assertEquals('/', $cookies[0]['path']);
        $this->assertEquals('service.example.com', $cookies[0]['domain']);
        $this->assertFalse($cookies[0]['secure']);
    }

    /**
     * Test the usage of a hostname that is different from the default URL.
     */
    public function test_protected_parseCookieHeaders_NonDefaultHostname()
    {
	$headers = array('Set-Cookie: SID=k1jut1r1bqrumpei837kk4jks0; path=/; domain=service2.example.com');
        $cookies = $this->object->parseCookieHeaders($headers, 'service.example.com');

        $this->assertType('array', $cookies);
        $this->assertEquals(1, count($cookies));
        $this->assertEquals('SID', $cookies[0]['name']);
        $this->assertEquals('k1jut1r1bqrumpei837kk4jks0', $cookies[0]['value']);
        $this->assertEquals('/', $cookies[0]['path']);
        $this->assertEquals('service2.example.com', $cookies[0]['domain']);
        $this->assertFalse($cookies[0]['secure']);
    }

    /**
     * Test the the inclusion of a path in the cookie.
     */
    public function test_protected_parseCookieHeaders_WithPath()
    {
	$headers = array('Set-Cookie: SID=k1jut1r1bqrumpei837kk4jks0; path=/something/; domain=service2.example.com');
        $cookies = $this->object->parseCookieHeaders($headers, 'service.example.com');

        $this->assertType('array', $cookies);
        $this->assertEquals(1, count($cookies));
        $this->assertEquals('SID', $cookies[0]['name']);
        $this->assertEquals('k1jut1r1bqrumpei837kk4jks0', $cookies[0]['value']);
        $this->assertEquals('/something/', $cookies[0]['path']);
        $this->assertEquals('service2.example.com', $cookies[0]['domain']);
        $this->assertFalse($cookies[0]['secure']);
    }

    /**
     * Test the addition of a 'Secure' parameter
     */
    public function test_protected_parseCookieHeaders_Secure()
    {
	$headers = array('Set-Cookie: SID=k1jut1r1bqrumpei837kk4jks0; Secure; path=/something/; domain=service2.example.com');
        $cookies = $this->object->parseCookieHeaders($headers, 'service.example.com');

        $this->assertType('array', $cookies);
        $this->assertEquals(1, count($cookies));
        $this->assertEquals('SID', $cookies[0]['name']);
        $this->assertEquals('k1jut1r1bqrumpei837kk4jks0', $cookies[0]['value']);
        $this->assertEquals('/something/', $cookies[0]['path']);
        $this->assertEquals('service2.example.com', $cookies[0]['domain']);
        $this->assertTrue($cookies[0]['secure']);
    }

    /**
     * Test the addition of a 'Secure' parameter that is lower-case
     */
    public function test_protected_parseCookieHeaders_SecureLC()
    {
	$headers = array('Set-Cookie: SID=k1jut1r1bqrumpei837kk4jks0; secure; path=/something/; domain=service2.example.com');
        $cookies = $this->object->parseCookieHeaders($headers, 'service.example.com');

        $this->assertType('array', $cookies);
        $this->assertEquals(1, count($cookies));
        $this->assertEquals('SID', $cookies[0]['name']);
        $this->assertEquals('k1jut1r1bqrumpei837kk4jks0', $cookies[0]['value']);
        $this->assertEquals('/something/', $cookies[0]['path']);
        $this->assertEquals('service2.example.com', $cookies[0]['domain']);
        $this->assertTrue($cookies[0]['secure']);
    }

    /**
     * Test the inclusion of a trailing semicolon
     */
    public function test_protected_parseCookieHeaders_trailingSemicolon()
    {
	$headers = array('Set-Cookie: SID="hello world"; path=/;');
        $cookies = $this->object->parseCookieHeaders($headers, 'service.example.com');

        $this->assertType('array', $cookies);
        $this->assertEquals(1, count($cookies));
        $this->assertEquals('SID', $cookies[0]['name']);
        $this->assertEquals('hello world', $cookies[0]['value']);
        $this->assertEquals('/', $cookies[0]['path']);
        $this->assertEquals('service.example.com', $cookies[0]['domain']);
        $this->assertFalse($cookies[0]['secure']);
    }

    /**
     * Test setting a single service cookie
     */
    public function test_protected_setCookie()
    {
        $cookies = $this->object->getCookies($this->serviceUrl_1c);
        $this->assertType('array', $cookies);
        $this->assertEquals(1, count($cookies));
        $this->assertEquals('k1jut1r1bqrumpei837kk4jks0', $cookies['SID']);
    }

    /**
     * Test setting a single service cookie
     */
    public function test_protected_storeCookie_WithDuplicates()
    {
	$headers = array('Set-Cookie: SID="hello world"; path=/');
        $cookiesToSet = $this->object->parseCookieHeaders($headers, 'service.example.com');
        $this->object->storeCookie($cookiesToSet[0]);

        $headers = array('Set-Cookie: SID="goodbye world"; path=/');
        $cookiesToSet = $this->object->parseCookieHeaders($headers, 'service.example.com');
        $this->object->storeCookie($cookiesToSet[0]);

        $cookies = $this->object->getCookies($this->serviceUrl_1c);
        $this->assertType('array', $cookies);
        $this->assertEquals(1, count($cookies));
        $this->assertEquals('goodbye world', $cookies['SID']);
    }

    /**
     * Test setting two service cookies
     */
    public function test_protected_storeCookie_TwoCookies()
    {
        // Second cookie
        $headers = array('Set-Cookie: message="hello world"; path=/');
        $cookiesToSet = $this->object->parseCookieHeaders($headers, 'service.example.com');
        $this->object->storeCookie($cookiesToSet[0]);


        $cookies = $this->object->getCookies($this->serviceUrl_1c);
        $this->assertType('array', $cookies);
        $this->assertEquals(2, count($cookies));
        $this->assertEquals('k1jut1r1bqrumpei837kk4jks0', $cookies['SID']);
        $this->assertEquals('hello world', $cookies['message']);
    }

    /**
     * Test setting two service cookies
     */
    public function test_protected_storeCookie_TwoCookiesOneAtDomain()
    {

        // Second cookie
        $headers = array('Set-Cookie: message="hello world"; path=/; domain=.example.com');
        $cookiesToSet = $this->object->parseCookieHeaders($headers, 'service.example.com');
        $this->object->storeCookie($cookiesToSet[0]);


        $cookies = $this->object->getCookies($this->serviceUrl_1c);
        $this->assertType('array', $cookies);
        $this->assertEquals(2, count($cookies));
        $this->assertEquals('k1jut1r1bqrumpei837kk4jks0', $cookies['SID']);
        $this->assertEquals('hello world', $cookies['message']);
    }

    /**
     * Test matching a domain cookie.
     */
    public function test_protected_cookieMatchesTarget_DomainCookie()
    {
        $headers = array('Set-Cookie: message="hello world"; path=/; domain=.example.com');
        $cookies = $this->object->parseCookieHeaders($headers, 'otherhost.example.com');

        $this->assertTrue($this->object->cookieMatchesTarget($cookies[0], parse_url('http://service.example.com/make_changes.php')));
    }

}
?>

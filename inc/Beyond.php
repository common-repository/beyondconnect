<?php

/**
 * @package beyondConnectPlugin
 */

namespace Inc;


use Cassandra\Date;

final class  Beyond
{
    public static function isGuid(string $guid): bool
    {
        return preg_match('/^[a-f\d]{8}(-[a-f\d]{4}){4}[a-f\d]{8}$/i', $guid);
    }

    public static function getVisitorID(): string
    {
        if (!empty($_COOKIE['bc_session'])) {
            return $_COOKIE['bc_session'];
        }

        $visitorID = Beyond::createGUID();
        $hashed = hash('sha1', $visitorID);

        setcookie('bc_session', $hashed, 0, '/');
        return $hashed;
    }

    public static function createGUID(): string
    {

        // Create a token
        $token = $_SERVER['HTTP_HOST'];
        $token .= $_SERVER['REQUEST_URI'];
        $token .= uniqid(rand(), true);

        // GUID is 128-bit hex
        $hash = strtoupper(md5($token));

        // Create formatted GUID
        $guid = '';

        // GUID format is XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX for readability
        $guid .= substr($hash, 0, 8) .
            '-' .
            substr($hash, 8, 4) .
            '-' .
            substr($hash, 12, 4) .
            '-' .
            substr($hash, 16, 4) .
            '-' .
            substr($hash, 20, 12);

        return $guid;
    }

    public static function getODataString($entity, $wporg_atts): string
    {
        $expandstring = self::getODataSubString($entity, $wporg_atts, 1, 9);

        return self::getODataQueryString($entity,
            empty($wporg_atts['function']) ? '' : $wporg_atts['function'],
            empty($wporg_atts['select']) ? '' : $wporg_atts['select'],
            $expandstring,
            empty($wporg_atts['filter']) ? '' : $wporg_atts['filter'],
            empty($wporg_atts['top']) ? '' : $wporg_atts['top'],
            empty($wporg_atts['skip']) ? '' : $wporg_atts['skip'],
            empty($wporg_atts['orderby']) ? '' : $wporg_atts['orderby']);
    }

    public static function getODataSubString($entity, $wporg_atts, $min, $max): string
    {
        $substring = '';

        for ($x = $min; $x < $max; $x++) {
            if (!empty($wporg_atts['expand' . $x])) {
                $substring .= (empty($substring) ? '' : ',') . self::getODataExpandString($wporg_atts['expand' . $x],
                        empty($wporg_atts['select' . $x]) ? '' : $wporg_atts['select' . $x],
                        self::getODataSubString($entity, $wporg_atts, ($x * 10), (($x * 10) + 9)),
                        empty($wporg_atts['filter' . $x]) ? '' : $wporg_atts['filter' . $x],
                        empty($wporg_atts['top' . $x]) ? '' : $wporg_atts['top' . $x],
                        empty($wporg_atts['skip' . $x]) ? '' : $wporg_atts['skip' . $x],
                        empty($wporg_atts['orderby' . $x]) ? '' : $wporg_atts['orderby' . $x]);
            }
        }

        return $substring;
    }

    public static function getODataExpandString(string $entity, string $select, string $expand, string $filter, string $top, string $skip, string $orderby): string
    {
        $expandstring = '';

        if (!empty($select))
            $expandstring .= (empty($expandstring) ? '' : ';') . '$select=' . $select;

        if (!empty($expand))
            $expandstring .= (empty($expandstring) ? '' : ';') . '$expand=' . $expand;

        if (!empty($filter))
            $expandstring .= (empty($expandstring) ? '' : ';') . '$filter=' . $filter;

        if (!empty($top))
            $expandstring .= (empty($expandstring) ? '' : ';') . '$top=' . $top;

        if (!empty($skip))
            $expandstring .= (empty($expandstring) ? '' : ';') . '$skip=' . $skip;

        if (!empty($orderby))
            $expandstring .= (empty($expandstring) ? '' : ';') . '$orderby=' . $orderby;

        return $entity . (empty($expandstring) ? '' : ('(' . $expandstring . ')'));
    }

    public static function getODataQueryString(string $entity, string $function, string $select, string $expand, string $filter, string $top, string $skip, string $orderby): string
    {
        $querystring = '';

        if (!empty($select))
            $querystring .= (empty($querystring) ? '' : '&') . '$select=' . $select;

        if (!empty($expand))
            $querystring .= (empty($querystring) ? '' : '&') . '$expand=' . $expand;

        if (!empty($filter))
            $querystring .= (empty($querystring) ? '' : '&') . '$filter=' . $filter;

        if (!empty($top))
            $querystring .= (empty($querystring) ? '' : '&') . '$top=' . $top;

        if (!empty($skip))
            $querystring .= (empty($querystring) ? '' : '&') . '$skip=' . $skip;

        if (!empty($orderby))
            $querystring .= (empty($querystring) ? '' : '&') . '$orderby=' . $orderby;

        return $entity . (empty($function) ? '' : '/' . $function) . (empty($querystring) ? '' : '?' . $querystring);
    }

    public static function shiftODataAtts($wporg_atts, $shift): array
    {
        foreach ($wporg_atts as $key => $value) {
            $int = filter_var($key, FILTER_SANITIZE_NUMBER_INT);
            $string = str_replace($int, '', $key);

            $wporg_atts_neu[$string . $shift . $int] = $value;

            if ( ! str_contains( $string, 'select' )
                 && ! str_contains( $string, 'expand' )
                 && ! str_contains( $string, 'filter' )
                 && ! str_contains( $string, 'top' )
                 && ! str_contains( $string, 'skip' )
                 && ! str_contains( $string, 'orderby' ) ) {
                $wporg_atts_neu[$key] = $value;
            }
        }

        return $wporg_atts_neu;
    }

    public static function getValues(string $path, string $returnField, bool $onlyStatus = false)
    {
        return self::requestValues($path, $returnField, $onlyStatus);
    }

    public static function getUrls(string $path): array
    {
        $urls = array();

        $urls[] = $path;

        if (function_exists('weglot_create_url_object')) {
            foreach (weglot_get_destination_languages() as $key => $value) {
                $urls[] = weglot_create_url_object($path)->getForLanguage($value);
            }
        }

        return $urls;
    }

    public static function getUrl(string $path): string
    {
        if (function_exists('weglot_create_url_object')) {
            return weglot_create_url_object(empty($path) ? '/' : $path)->getForLanguage(weglot_get_current_language());
        } else
            return empty($path) ? '/' : $path;
    }

    public static function getLanguage(): string
    {
        if (function_exists('weglot_get_current_language')) {
            return weglot_get_current_language();
        } else
            return 'de';
    }

    public static function requestValues(string $path, string $returnField, bool $onlyStatus)
    {
        $option = get_option('beyondconnect_option');
        $url = $option['Url'];

        $token = self::getToken();
        if (empty($token))
            return null;

        $header = array(
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        );


        $defaults = array(
            'method' => 'GET',
            'headers' => $header,
            'timeout' => 20,  //increase timeout from 5 to 20
        );

        $response = wp_remote_get($url . $path, $defaults);

        if (is_wp_error($response) && $response->get_error_code() === 401) {
	        error_log('beyondConnect: requestValues: Error 401');
            delete_transient('bc_token');
            return self::requestValues($path, $returnField, $onlyStatus);
        }

        if ($onlyStatus) {
            return wp_remote_retrieve_response_message($response);
        }

        if (is_wp_error($response)) {
            echo $response->get_error_message() . "<br />";
        }

        if (empty($returnField))
            $jsonRaw = json_decode("[" . wp_remote_retrieve_body($response) . "]", true);
        else
            $jsonRaw = json_decode(wp_remote_retrieve_body($response), true);

        if (empty($returnField)) {
            return $jsonRaw;
        } else {
            return empty($jsonRaw[$returnField]) ? null : $jsonRaw[$returnField];
        }
    }

	private static function getToken(): ?string
    {
		$savedTransient = get_transient('bc_token');
		$savedDecoded = json_decode($savedTransient, true);
		$safedTokenString = $savedDecoded != null  ? $savedDecoded["access_token"] : null;
	    $safedExpires = $savedDecoded != null ? $savedDecoded["expires"] : null;
	    $safedRefreshToken = $savedDecoded != null ? $savedDecoded["refresh_token"] : null;
	    $savedRefreshTokenString = $safedRefreshToken != null ? $safedRefreshToken["token_string"] : null;


        if (!empty($safedTokenString))
        {

	        $expiringDate = strtotime($safedExpires);
	        $currentDate = current_time('U', true);

            if ($currentDate < $expiringDate)
            {
	            return $safedTokenString;
			}
        }

        if (!empty($savedRefreshTokenString) && !empty($safedTokenString))
        {
	        //Request Token from Refresh Token
	        $returnedToken = self::requestTokens($savedRefreshTokenString, $safedTokenString);

            if (!empty ($returnedToken))
            {
	            return $returnedToken;
            }
			else
			{
				error_log('beyondConnect: getToken: Request Token from Refresh Token failed');
			}

        }
        //Request new Token
        return self::requestTokens(null, null);
    }

    private static function requestTokens(?string $refreshtoken, ?string $accesstoken): ?string
    {
        $option = get_option('beyondconnect_option');
        $url = $option['Url'];
        $userName = $option['Username'];
        $key = $option['Key'];

        if ($refreshtoken === null || $accesstoken === null) {
			$tokenUrl = 'token';
            $data = array(
                'grant_type' => 'password',
                'username' => $userName,
                'password' => $key);
        }
        else
        {
	        $tokenUrl = 'refresh';
            $data = array(
	            'refreshtoken' => $refreshtoken,
                'accesstoken' => $accesstoken);
        }

        $defaults = array(
            'method' => 'POST',
            'headers' => array('Content-Type: application/x-www-form-urlencoded'),
            'body' => http_build_query($data),
        );

        $response = wp_remote_get($url . $tokenUrl, $defaults);

        if (is_wp_error($response)) {
            echo $response->get_error_message() . "<br />";
        }

		$body = wp_remote_retrieve_body($response);

        $jsonRaw = json_decode($body, true);


        //Save Tokens to Transient
        //Expiration for both one day, because Refresh needs Access Token
        set_transient('bc_token', $body ?? null, DAY_IN_SECONDS);

        return $jsonRaw['access_token'] ?? null;
    }

    public static function setValues(string $path, string $method, array $body, string $returnField, bool $onlyStatus)
    {
	    $token = self::getToken();
	    if (empty($token))
		    return null;

		$option = get_option('beyondconnect_option');
        $url = $option['Url'];

        $header = array(
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        );

        if ($method === 'DELETE') {
            $defaults = array(
                'method' => $method,
                'headers' => $header
            );
        } else {
            $defaults = array(
                'method' => $method,
                'headers' => $header,
                'body' => json_encode($body),
            );
        }

        if ($method === 'DELETE') {
            $response = wp_remote_request($url . $path, $defaults);
        } else {
            $response = wp_remote_post($url . $path, $defaults);
        }

	    if (is_wp_error($response) && $response->get_error_code() === 401) {
			error_log('beyondConnect: setValues: Error 401');
		    delete_transient('bc_token');
		    return self::requestValues($path, $returnField, $onlyStatus);
	    }

        if (is_wp_error($response)) {
            return $response;
        }

        if ($onlyStatus) {
            return wp_remote_retrieve_response_message($response);
        }

        if (empty($returnField))
            $jsonRaw = json_decode("[" . wp_remote_retrieve_body($response) . "]", true);
        else
            $jsonRaw = json_decode(wp_remote_retrieve_body($response), true);

        if (empty($returnField)) {
            return $jsonRaw;
        } else {
            return empty($jsonRaw[$returnField]) ? null : $jsonRaw[$returnField];
        }
    }

    public static function getStringBetween(string $string, string $start, string $end): string
    {
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini === false) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }

    public static function strpos_arr($haystack, $needle): false|int
    {
        if (!is_array($needle)) $needle = array($needle);
		{
		    foreach ( $needle as $what ) {
			    if ( ( $pos = strpos( $haystack, $what ) ) !== false ) {
				    return $pos;
			    }
		    }
	    }
        return false;
    }
}
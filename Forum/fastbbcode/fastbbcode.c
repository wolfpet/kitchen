/*
  +----------------------------------------------------------------------+
  | PHP Version 5                                                        |
  +----------------------------------------------------------------------+
  | Copyright (c) 1997-2007 The PHP Group                                |
  +----------------------------------------------------------------------+
  | This source file is subject to version 3.01 of the PHP license,      |
  | that is bundled with this package in the file LICENSE, and is        |
  | available through the world-wide-web at the following url:           |
  | http://www.php.net/license/3_01.txt                                  |
  | If you did not receive a copy of the PHP license and are unable to   |
  | obtain it through the world-wide-web, please send a note to          |
  | license@php.net so we can mail you a copy immediately.               |
  +----------------------------------------------------------------------+
  | Author: Dmitriy Fitisov dmitriy@radier.ca                            |
  +----------------------------------------------------------------------+
*/

//#define DEBUG

/* $Id$ */

#ifdef HAVE_CONFIG_H
#include "config.h"
#endif

#ifndef DEBUG
#include "php.h"
#include "php_ini.h"
#include "ext/standard/info.h"
#include "php_fastbbcode.h"
#define PRINTF
#else
#define estrdup _strdup
#define emalloc malloc
#define efree free
#include <stdio.h>
#include <string.h>
#include <stdlib.h>
#define PRINTF printf
#endif


#ifndef DEBUG

/* If you declare any globals in php_fastbbcode.h uncomment this:
ZEND_DECLARE_MODULE_GLOBALS(fastbbcode)
*/

/* True global resources - no need for thread safety here */
static int le_fastbbcode;

/* {{{ fastbbcode_functions[]
*
* Every user visible function must have an entry in fastbbcode_functions[].
*/
zend_function_entry fastbbcode_functions[] = {
	PHP_FE(bbcode, NULL)		/* For testing, remove later. */
	{
		NULL, NULL, NULL
	}	/* Must be the last line in fastbbcode_functions[] */
};
/* }}} */

/* {{{ fastbbcode_module_entry
*/
zend_module_entry fastbbcode_module_entry = {
#if ZEND_MODULE_API_NO >= 20010901
	STANDARD_MODULE_HEADER,
#endif
	"fastbbcode",
	fastbbcode_functions,
	PHP_MINIT(fastbbcode),
	PHP_MSHUTDOWN(fastbbcode),
	NULL,		/* Replace with NULL if there's nothing to do at request start */
	NULL,	/* Replace with NULL if there's nothing to do at request end */
	PHP_MINFO(fastbbcode),
#if ZEND_MODULE_API_NO >= 20010901
	"0.1", /* Replace with version number for your extension */
#endif
	STANDARD_MODULE_PROPERTIES
};
/* }}} */

#ifdef COMPILE_DL_FASTBBCODE
ZEND_GET_MODULE(fastbbcode)
#endif

/* {{{ PHP_INI
*/
/* Remove comments and fill if you need to have entries in php.ini
PHP_INI_BEGIN()
STD_PHP_INI_ENTRY("fastbbcode.global_value",      "42", PHP_INI_ALL, OnUpdateLong, global_value, zend_fastbbcode_globals, fastbbcode_globals)
STD_PHP_INI_ENTRY("fastbbcode.global_string", "foobar", PHP_INI_ALL, OnUpdateString, global_string, zend_fastbbcode_globals, fastbbcode_globals)
PHP_INI_END()
*/
/* }}} */

/* {{{ php_fastbbcode_init_globals
*/
/* Uncomment this function if you have INI entries
static void php_fastbbcode_init_globals(zend_fastbbcode_globals *fastbbcode_globals)
{
fastbbcode_globals->global_value = 0;
fastbbcode_globals->global_string = NULL;
}
*/
/* }}} */

/* {{{ PHP_MINIT_FUNCTION
*/
PHP_MINIT_FUNCTION(fastbbcode)
{
	/* If you have INI entries, uncomment these lines
	REGISTER_INI_ENTRIES();
	*/
	return SUCCESS;
}
/* }}} */

/* {{{ PHP_MSHUTDOWN_FUNCTION
*/
PHP_MSHUTDOWN_FUNCTION(fastbbcode)
{
	/* uncomment this line if you have INI entries
	UNREGISTER_INI_ENTRIES();
	*/
	return SUCCESS;
}
/* }}} */

/* {{{ PHP_MINFO_FUNCTION
*/
PHP_MINFO_FUNCTION(fastbbcode)
{
	php_info_print_table_start();
	php_info_print_table_header(2, "fastbbcode support", "enabled");
	php_info_print_table_end();

	/* Remove comments if you have entries in php.ini
	DISPLAY_INI_ENTRIES();
	*/
}
/* }}} */


void
do_bbcode(
char *          bb_str,
int             bb_str_len,
char **         pStr,
int  *          len);

/* Remove the following function when you have succesfully modified config.m4
so that your module can be compiled into PHP, it exists only for testing
purposes. */

/* Every user-visible function in PHP should document itself in the source */
/* {{{ proto string bbcode(string arg)
Return a string to confirm that the module is compiled in */
PHP_FUNCTION(bbcode)
{
	char *arg = NULL;
	int arg_len, len;
	char *strg;

	if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "s", &arg, &arg_len) == FAILURE) {
		return;
	}
	do_bbcode(arg, arg_len, &strg, &len);
	/*len = spprintf(&strg, 0, "Congratulations! You have successfully modified ext/%.78s/config.m4. Module %.78s is now compiled into PHP.", "fastbbcode", arg);*/
	RETURN_STRINGL(strg, len, 0);
}
/* }}} */
/* The previous line is meant for vim and emacs, so it can correctly fold and
unfold functions in source code. See the corresponding marks just before
function definition, where the functions purpose is also documented. Please
follow this convention for the convenience of others editing your code.
*/

#endif

/* -------------------------------- BEGIN --------------------------------- */



#define     RGB_COLOR_LENGTH        sizeof( " rgb( 00, 00, 00 ); " )

#define     BBCODE_SIZE_START       "[size="
#define     BBCODE_SIZE_END         "[/size]"
#define     BBHTML_SIZE_START       "<span style=\"font-size:%02dpt;\">"
#define     BBHTML_SIZE_END         "</span>"
#define     BBHTML_SIZE_LENGTH      ( sizeof( BBHTML_SIZE_START ) + sizeof( BBHTML_SIZE_END ) )

#define     BBCODE_B_START          "[b]"
#define     BBCODE_B_END            "[/b]"
#define     BBHTML_B_START          "<strong>"
#define     BBHTML_B_END            "</strong>"
#define     BBHTML_B_LENGTH         ( sizeof( BBHTML_B_START ) + sizeof( BBHTML_B_END ) )

#define     BBCODE_I_START          "[i]"
#define     BBCODE_I_END            "[/i]"
#define     BBHTML_I_START          "<em>"
#define     BBHTML_I_END            "</em>"
#define     BBHTML_I_LENGTH         ( sizeof( BBHTML_I_START ) + sizeof( BBHTML_I_END ) )

#define     BBCODE_U_START          "[u]"
#define     BBCODE_U_END            "[/u]"
#define     BBHTML_U_START          "<span style=\"text-decoration: underline;\">"
#define     BBHTML_U_END            "</span>"
#define     BBHTML_U_LENGTH         ( sizeof( BBHTML_U_START ) + sizeof( BBHTML_U_END ) )

#define     BBCODE_S_START          "[s]"
#define     BBCODE_S_END            "[/s]"
#define     BBHTML_S_START          "<del>"
#define     BBHTML_S_END            "</del>"
#define     BBHTML_S_LENGTH         ( sizeof( BBHTML_S_START ) + sizeof( BBHTML_S_END ) )

#define     BBCODE_QUOTE_START      "[quote]"
#define     BBCODE_QUOTE_END        "[/quote]"
#define     BBHTML_QUOTE_START      "<q>"
#define     BBHTML_QUOTE_END        "</q>"
#define     BBHTML_QUOTE_LENGTH     0   /* BBHTML_QUOTE_XXX shorter then BBCODE_QUOTE_XXX */

#define     BBCODE_CODE_START      "[code]"
#define     BBCODE_CODE_END        "[/code]"
#define     BBHTML_CODE_START      "<pre>"
#define     BBHTML_CODE_END        "</pre>"
#define     BBHTML_CODE_LENGTH      (  sizeof( BBHTML_CODE_START ) + sizeof( BBHTML_CODE_END ) )

#define     BBCODE_COLOR_START      "[color="
#define     BBCODE_COLOR_END        "[/color]"
#define     BBHTML_COLOR_CODE_START      "<span style=\"color: rgb(%d, %d, %d);\">"
#define     BBHTML_COLOR_NAME_START      "<span style=\"color:%s;\">"
#define     BBHTML_COLOR_END        "</span>"
#define     BBHTML_COLOR_LENGTH     ( sizeof( BBHTML_COLOR_CODE_START ) + sizeof( BBHTML_COLOR_END ) + RGB_COLOR_LENGTH )

#define     BBCODE_URL_SHORT_START  "[url]"
#define     BBCODE_URL_LONG_START   "[url="
#define     BBCODE_URL_END          "[/url]"
#define     BBHTML_URL              "<a target=\"_blank\" href=\"http://%s\">%s</a>"
#define     BBHTML_URL_LENGTH       sizeof( BBHTML_URL )
#define     BBHTML_URLS              "<a target=\"_blank\" href=\"https://%s\">%s</a>"
#define     BBHTML_URLS_LENGTH       sizeof( BBHTML_URL )

#define     BBCODE_IMAGE_START      "[img="
#define     BBHTML_IMAGE            "<img src=\"http://%s\"/>"
#define     BBHTML_IMAGE_LENGTH     sizeof( "<img src=\"http://\"/>" )

#define     BBHTML_IMAGES            "<img src=\"https://%s\"/>"
#define     BBHTML_IMAGES_LENGTH     sizeof( "<img src=\"https://\"/>" )

#define     BBCODE_WWW              "www"
#define     BBCODE_HTTP             "http://"
#define     BBCODE_HTTP_LENGTH      ( sizeof( BBCODE_HTTP ) - 1 )
#define     BBCODE_HTTPS            "https://"
#define     BBCODE_HTTPS_LENGTH     ( sizeof( BBCODE_HTTPS ) - 1 )

#define     IS_IMAGE( end )           ( !memcmp( end - 4, ".jpg", 4 ) || \
                         !memcmp( end - 5, ".jpeg", 5 ) || \
                         !memcmp( end - 4, ".png", 4 ) || \
                         !memcmp( end - 4, ".gif", 4 ) )


int conv_www = 1;

/* This will calculate new lengths
* for part of the text excluding
* [code] [/code] segments.
* Incoming string must be in lower case
*/
int
get_length(
char *			p,
int				len)
{
	int 	ret = len;
	char *  s = NULL;
	char	back = *(p + len);
	*(p + len) = '\0';

	/*  This calculates increase in size of tags similar to
	*  [img=blah-blah/] and [size=..]blah-blah[/size]
	*/
#define     ADD_SIZE_PARAMETER_WITHIN( STR, LEN, START_TAG, ADDED_LENGTH ) { \
	char * s						= STR; \
		while ( s ) {                       \
        char *  end                 = NULL; \
		s							= strstr( s, START_TAG ); \
		if ( NULL == s ) {                  \
			break;                  \
				}                           \
		s                           += sizeof ( START_TAG ) - 1; \
        end                         = strchr( s, ']' );     \
        if ( NULL == end ) {        \
            end                     = s + strlen( s );  \
		        }                           \
		LEN 						+= ADDED_LENGTH + ( end - s ); \
        s                           = end;  \
        if ( *s ) {                 \
            ++s;                    \
		        }                           \
			}                               \
	 }

	ADD_SIZE_PARAMETER_WITHIN(p, ret, BBCODE_IMAGE_START, BBHTML_IMAGE_LENGTH)
		ADD_SIZE_PARAMETER_WITHIN(p, ret, BBCODE_COLOR_START, BBHTML_COLOR_LENGTH)
		ADD_SIZE_PARAMETER_WITHIN(p, ret, BBCODE_SIZE_START, BBHTML_SIZE_LENGTH)

		/*
		s								= p;
		while ( s ) {

		s							= strstr( s, BBCODE_SIZE_START );
		if ( NULL == s ) {
		break;
		}
		s++;
		ret 						+= BBHTML_SIZE_LENGTH;
		}
		*/

		/*  This calculates increase in size of tags similar to
		*  [b], [/S] etc..
		*/
#define ADD_SIZE( STR, LEN, START_TAG, ADDED_LENGTH ) { \
	char * s						= STR; \
		while ( s ) { \
\
		s							= strstr( s, START_TAG );\
		if ( NULL == s ) {\
			break;\
				}\
		s++;\
		LEN 						+= ADDED_LENGTH;\
			}   \
	  }

		ADD_SIZE(p, ret, BBCODE_B_START, BBHTML_B_LENGTH)
		ADD_SIZE(p, ret, BBCODE_I_START, BBHTML_I_LENGTH)
		ADD_SIZE(p, ret, BBCODE_U_START, BBHTML_U_LENGTH)
		ADD_SIZE(p, ret, BBCODE_S_START, BBHTML_S_LENGTH)

		/* URL length calculation - take into account existense of quotes "" and http://
		*/
		s = p;
	while (s) {
		char 	*	p1 = s;
		s = strstr(s, BBCODE_URL_SHORT_START); /* [url] */
		if (NULL == s) {
			break;
		}
		p1 = s + sizeof(BBCODE_URL_SHORT_START) - 1;
		/* Because inside of URL may be image for example, we have to check for
		* BBCODE_URL_END [/url]
		*/
		s = strstr(p1, BBCODE_URL_END);
		if (NULL == s) {
			/* ret						+= strlen( p1 ); */
			s = p1 + strlen(p1);
		} /*else {
		  ret						+= ( s - p1 );
		  }*/
		ret += sizeof(BBHTML_URL) + (s - p1);
	}

	s = p;
	/* This is simplified loop
	*/
	while (s) {
		char 	*	p1 = s;
		s = strstr(s, BBCODE_URL_LONG_START); /* [url= */
		if (NULL == s) {
			break;
		}
		ret += BBHTML_URL_LENGTH;
		s += (sizeof(BBCODE_URL_LONG_START) - 1);
	}

	if (conv_www) {
		s = p;
		while (s) {
			s = strstr(s, BBCODE_WWW);
			if (NULL != s) {
				char *start = s;
				size_t  add = 0;
				size_t  more = 0;
				PRINTF("Line %d\n", __LINE__);
				if (((s - p) >= (BBCODE_HTTP_LENGTH)) &&
					!memcmp(s - (BBCODE_HTTP_LENGTH), BBCODE_HTTP, BBCODE_HTTP_LENGTH) &&
					(((s - (BBCODE_HTTP_LENGTH)) == p) ||
					(((s - p) > (BBCODE_HTTP_LENGTH)) && isspace(*(s - (BBCODE_HTTP_LENGTH)-1))))) {
					/*add          += sizeof( BBCODE_HTTP );  */
					/* Reserve space for http:// prefix in link name
					*/
					more = sizeof(BBCODE_HTTP);
					PRINTF("Line %d  www found, reserving space for http\n", __LINE__);
					/* start       -= ( BBCODE_HTTP_LENGTH ); */
				}
				else {
					if (((s - p) >= (BBCODE_HTTPS_LENGTH)) &&
						!memcmp(s - (BBCODE_HTTPS_LENGTH), BBCODE_HTTPS, BBCODE_HTTPS_LENGTH) &&
						(((s - (BBCODE_HTTPS_LENGTH)) == p) ||
						(((s - p) > (BBCODE_HTTPS_LENGTH)) && isspace(*(s - (BBCODE_HTTPS_LENGTH)-1))))) {
						/*add          += sizeof( BBCODE_HTTPS );  */
						/* Reserve space for http:// prefix in link name
						*/
						more = sizeof(BBCODE_HTTPS);
						PRINTF("Line %d www found, reserving space for https\n", __LINE__);
						/* start       -= ( BBCODE_HTTP_LENGTH ); */
					}
					else {
						if ((s == p) || isspace(*(s - 1))) {
							;
						}
						else {
							++s;
							continue;
						}
					}
				}
				add = strcspn(start, " [\t\n\r");
				if (add > 4) {
					if (IS_IMAGE(start + add)) {
						PRINTF("Line %d reserved space for image\n", __LINE__);
						ret += add + BBHTML_IMAGE_LENGTH;
						++s;
						continue;
					}
				}
				ret += 2 * add + more + BBHTML_URL_LENGTH;
				++s;
			}
		}
		s = p;
		while (s) {
			int secure = 0;
			char *s2 = s;
			s = strstr(s, "http://");
			if (s == NULL) {
				s = strstr(s2, "https://");
				secure = 1;
			}
			if (NULL != s) {
				PRINTF("Line %d, found http://\n", __LINE__);
				char *start = s;
				size_t  add = 0;
				size_t  more = 0;
				if (!memcmp(s, "http://www", sizeof("http://www") - 1) ||
					!memcmp(s, "https://www", sizeof("https://www") - 1)) {
					PRINTF("Line %d http[s]://wwww found\n", __LINE__);
					++s;
					continue;
				}
				if ((s - p) >= (BBCODE_HTTP_LENGTH) &&
					!memcmp(s - (BBCODE_HTTP_LENGTH), BBCODE_HTTP, BBCODE_HTTP_LENGTH) &&
					(isspace(*(s - (BBCODE_HTTP_LENGTH))) || ((s - (BBCODE_HTTP_LENGTH)) == p))) {
					/*add          += sizeof( BBCODE_HTTP );  */
					/* Reserve space for http:// prefix in link name
					*/
					PRINTF("Line %d http://wwww\n", __LINE__);
					more = sizeof(BBCODE_HTTP);
					/* start       -= ( BBCODE_HTTP_LENGTH ); */
				}
				else {
					if ((s - p) >= (BBCODE_HTTPS_LENGTH) &&
						!memcmp(s - (BBCODE_HTTPS_LENGTH), BBCODE_HTTPS, BBCODE_HTTPS_LENGTH) &&
						(isspace(*(s - (BBCODE_HTTPS_LENGTH))) || ((s - (BBCODE_HTTPS_LENGTH)) == p))) {
						/*add          += sizeof( BBCODE_HTTPS );  */
						/* Reserve space for http:// prefix in link name
						*/
						PRINTF("Line %d http://wwww\n", __LINE__);
						more = sizeof(BBCODE_HTTPS);
						/* start       -= ( BBCODE_HTTPS_LENGTH ); */
					}
					else {
						if ((s == p) || isspace(*(s - 1))) {
							;
						}
						else {
							++s;
							continue;
						}
					}
				}
				add = strcspn(start, " [\t\n\r");
				if (add > 4) {
					if (IS_IMAGE(start + add)) {
						PRINTF("Line %d space for image reserved\n", __LINE__);
						ret += add + BBHTML_IMAGES_LENGTH;
						++s;
						continue;
					}
				}
				ret += 2 * add + more + BBHTML_URL_LENGTH;
				++s;
			}
		}
	}

	*(p + len) = back;
	return ret;
}

void
do_bbcode(
char *	        bb_str,
int				bb_str_len,
char **			pStr,
int	 *			len)
{
	char *	code_start = NULL;
	char *	code_end = NULL;
	char *			bb_str_lwr = NULL;
	char *			p = NULL;
	char *			pRead = NULL;
	char *			pWrite = NULL;
	int				new_len = 0;
	int				i = 0;
	int             wrote = 0;

	*len = 0;

	bb_str_lwr = estrdup(bb_str);
	p = bb_str_lwr;


	while (*p) {
		*p = tolower(*p);
		p++;
	}
	/* First iteration - calc new length */
	p = bb_str_lwr;
	do {
		code_start = strstr(p, BBCODE_CODE_START);
		if (NULL != code_start) {
			new_len += get_length(p, code_start - p);
			code_end = strstr(code_start, BBCODE_CODE_END);
			if (NULL == code_end) {
				code_end = code_start + strlen(code_start);
			}
			new_len += code_end - code_start + BBHTML_CODE_LENGTH;
			p = code_end;
		}
	} while (NULL != code_start);
	new_len += get_length(p, strlen(p));
	++new_len;
	p = bb_str_lwr;
	/*new_len							= get_length( p, bb_str_len ); */
	*pStr = (char *)emalloc(new_len);
	memset(*pStr, 0, new_len);
	pWrite = *pStr;
	pRead = bb_str;
	PRINTF("Allocated %d bytes for string with length %d bytes\n", new_len, bb_str_len);

	while (i < bb_str_len) {
		if (*(p) == '[') {
			if (!memcmp(p, BBCODE_B_START, sizeof(BBCODE_B_START) - 1)) {
				i += sizeof(BBCODE_B_START) - 1;
				pRead += sizeof(BBCODE_B_START) - 1;
				p += sizeof(BBCODE_B_START) - 1;
				strcpy(pWrite, BBHTML_B_START);
				pWrite += sizeof(BBHTML_B_START) - 1;
				wrote += sizeof(BBHTML_B_START) - 1;
				continue;
			}
#define 	BB_REPLACE( WHAT, BY_WHAT ) \
			if ( !memcmp( p, WHAT, sizeof( WHAT ) - 1 ) ) {\
                i                   += sizeof( WHAT ) - 1;\
                pRead               += sizeof( WHAT ) - 1;\
                p                   += sizeof( WHAT ) - 1;\
                strcpy( pWrite, BY_WHAT );\
                pWrite              += sizeof( BY_WHAT ) - 1;\
                wrote               += sizeof( BY_WHAT ) - 1;\
                continue;\
			            }	
			BB_REPLACE(BBCODE_B_END, BBHTML_B_END)
				BB_REPLACE(BBCODE_I_START, BBHTML_I_START)
				BB_REPLACE(BBCODE_I_END, BBHTML_I_END)
				BB_REPLACE(BBCODE_S_START, BBHTML_S_START)
				BB_REPLACE(BBCODE_S_END, BBHTML_S_END)
				BB_REPLACE(BBCODE_U_START, BBHTML_U_START)
				BB_REPLACE(BBCODE_U_END, BBHTML_U_END)
				BB_REPLACE(BBCODE_CODE_START, BBHTML_CODE_START)
				BB_REPLACE(BBCODE_CODE_END, BBHTML_CODE_END)
				BB_REPLACE(BBCODE_QUOTE_START, BBHTML_QUOTE_START)
				BB_REPLACE(BBCODE_QUOTE_END, BBHTML_QUOTE_END)
				BB_REPLACE(BBCODE_SIZE_END, BBHTML_SIZE_END)
				BB_REPLACE(BBCODE_COLOR_END, BBHTML_COLOR_END)

				if (!memcmp(p, BBCODE_SIZE_START, sizeof(BBCODE_SIZE_START) - 1)) {
					char * pEnd = NULL;
					unsigned int size = 0;
					int add = 0;
					char * pFound = NULL;
					p += sizeof(BBCODE_SIZE_START) - 1;
					pRead += sizeof(BBCODE_SIZE_START) - 1;
					size = strtoul(p, &pEnd, 10);
					add = snprintf(pWrite, new_len - wrote, BBHTML_SIZE_START, size);
					pWrite += add;
					wrote += add;
					pFound = strchr(p, ']');
					if (NULL == pFound) {
						pFound = p + strlen(p);
					}
					else {
						++pFound;
					}
					pRead += pFound - p;
					p = pFound;
					continue;
				}
			if (!memcmp(p, BBCODE_COLOR_START, sizeof(BBCODE_COLOR_START) - 1)) {
				char    * pNext = NULL;
				int		r = 0;
				int 	g = 0;
				int 	b = 0;
				p += sizeof(BBCODE_COLOR_START) - 1;
				pRead += sizeof(BBCODE_COLOR_START) - 1;
				char * pEnd = strchr(p, ']');
				char * pNextRead = pRead;

				/*char * pSpace				= strchr( p, ' ' );
				if ( pSpace && pSpace < pEnd ) {
				pEnd					= pSpace;
				}
				*/
				if (!pEnd) {
					pEnd = p + strlen(p);
					pNext = pEnd;
				}
				else {
					pNext = pEnd + 1;
				}
				pNextRead += pNext - p;
				if (*p == '#') {
					int rgb_len;
					p++;
					pRead++;
					rgb_len = pEnd - p;
					i += rgb_len;
					if (rgb_len > 6) {
						rgb_len = 6;
						pEnd = p + 6;
					}

					do {
						int hex;

						hex = 0;
						if (*pEnd >= '0' && *pEnd <= '9') {
							hex = *pEnd - '0';
						}
						else if (*pEnd >= 'a' && *pEnd <= 'f') {
							hex = *pEnd - 'a' + 10;
						}
						b = hex;
						--pEnd;
						if (pEnd == p) {
							break;
						}
#define	GET_HEX( COLOR, VAL ) \
						hex			= 0;\
						if ( *pEnd >= '0' && *pEnd <= '9' ) {\
							hex			= *pEnd - '0';\
												} else if ( *pEnd >= 'a' && *pEnd <= 'f' ) {\
							hex			= *pEnd - 'a' + 10;\
												}\
						COLOR				= VAL;\
						--pEnd;\
						if ( pEnd == p ) {\
							break;\
												}						

						GET_HEX(b, hex * 16)
							GET_HEX(g, hex)
							GET_HEX(g, hex * 16)
							GET_HEX(r, hex)
							GET_HEX(r, hex * 16)

					} while (0);

						int add = snprintf(pWrite, new_len - wrote, BBHTML_COLOR_CODE_START, r, g, b);
						pWrite += add;
						wrote += add;
						p = pNext;
						pRead = pNextRead;
				}
				else {
					size_t  off = pEnd - p;
					char back = *(pRead + off);
					*(pRead + off) = '\0';
					int add = snprintf(pWrite, new_len - wrote, BBHTML_COLOR_NAME_START, pRead);
					pWrite += add;
					wrote += add;
					*(pRead + off) = back;
					pRead += off + 1;
					p = pEnd + 1;
				}
				continue;
			}
			if (!memcmp(p, BBCODE_IMAGE_START, sizeof(BBCODE_IMAGE_START) - 1)) {
				{
					PRINTF("Line %d, IMAGE_START detected\n", __LINE__);
					p += sizeof(BBCODE_IMAGE_START) - 1;
					pRead += sizeof(BBCODE_IMAGE_START) - 1;
					i += sizeof(BBCODE_IMAGE_START) - 1;
				}
				int secure = 0; /* for https:// distinguishing */
				char * pEnd = p + strcspn(p, " \r\n\t]");
				char * pNext = strchr(p + sizeof(BBCODE_IMAGE_START) - 1, ']');
				char back;
				int add;
				size_t   off;
				if (NULL == pNext) {
					pNext = p + strlen(p);
				}
				else {
					++pNext;
				}
				if (NULL == pEnd) {
					pEnd = p + strlen(p);
				}
				if (!memcmp(p, BBCODE_HTTPS, BBCODE_HTTPS_LENGTH)) {
					p += BBCODE_HTTPS_LENGTH;
					pRead += BBCODE_HTTPS_LENGTH;
					i += BBCODE_HTTPS_LENGTH;
					secure = 1;
				}
				else if (!memcmp(p, BBCODE_HTTP, BBCODE_HTTP_LENGTH)) {
					p += BBCODE_HTTP_LENGTH;
					pRead += BBCODE_HTTP_LENGTH;
					i += BBCODE_HTTP_LENGTH;
				}
				off = pEnd - p;
				back = *(pRead + off);
				*(pRead + off) = '\0';
				if (!secure) {
					add = snprintf(pWrite, new_len - wrote, BBHTML_IMAGE, pRead);
				}
				else {
					add = snprintf(pWrite, new_len - wrote, BBHTML_IMAGES, pRead);
				}
				pWrite += add;
				wrote += add;
				*(pRead + off) = back;
				pRead += pNext - p;
				p = pNext;
				continue;
			}
			if (!memcmp(p, BBCODE_URL_SHORT_START, sizeof(BBCODE_URL_SHORT_START) - 1)) {
				p += sizeof(BBCODE_URL_SHORT_START) - 1;
				pRead += sizeof(BBCODE_URL_SHORT_START) - 1;
				i += sizeof(BBCODE_URL_SHORT_START) - 1;
				char * pUrl = pRead;
				int secure = 0; /* for https:// distinguishing */

				if (!memcmp(p, BBCODE_HTTPS, BBCODE_HTTPS_LENGTH)) {
					pUrl += BBCODE_HTTPS_LENGTH;
					secure = 1;
				} else if (!memcmp(p, BBCODE_HTTP, BBCODE_HTTP_LENGTH)) {
					pUrl += BBCODE_HTTP_LENGTH;
				}
				char * pStart = p;
				/*char * pEnd					= strchr( pStart, ' ' ); */
				char * pEnd = strstr(p, "[/url]");
				char back;
				int add;
				size_t off;
				if (NULL == pEnd) {
					pEnd = p + strlen(p);
					i += pEnd - p;
					p = pEnd;
				}
				else {
					i += pEnd + sizeof("[/url]") - p;
					p = pEnd + sizeof("[/url]");
				}
				/*p                           = pStart;*/
				off = pEnd - pStart;
				back = *(pRead + off);
				*(pRead + off) = '\0';

				if (!secure)
					add = snprintf(pWrite, new_len - wrote, BBHTML_URL, pUrl, pRead);
				else 
					add = snprintf(pWrite, new_len - wrote, BBHTML_URLS, pUrl, pRead);

				pWrite += add;
				wrote += add;
				*(pRead + off) = back;
				pRead += off + sizeof("[/url]");
				continue;
			}

			if (!memcmp(p, BBCODE_URL_LONG_START, sizeof(BBCODE_URL_LONG_START) - 1)) {
				int secure = 0; /* for https:// distinguishing */
				p += sizeof(BBCODE_URL_LONG_START) - 1;
				i += sizeof(BBCODE_URL_LONG_START) - 1;
				pRead += sizeof(BBCODE_URL_LONG_START) - 1;
				if (!memcmp(p, BBCODE_HTTPS, BBCODE_HTTPS_LENGTH)) {
					p += BBCODE_HTTPS_LENGTH;
					pRead += BBCODE_HTTPS_LENGTH;
					i += BBCODE_HTTPS_LENGTH;
					secure = 1;
				} else if (!memcmp(p, BBCODE_HTTP, BBCODE_HTTP_LENGTH)) {
					p += BBCODE_HTTP_LENGTH;
					i += BBCODE_HTTP_LENGTH;
					pRead += BBCODE_HTTP_LENGTH;
				}
				char * pEndUrl = strchr(p, ']');
				if (NULL == pEndUrl) {
					p++;
					i++;
					pRead++;
					continue;
				}
				char * pTitle = pEndUrl + 1;
				char * pTitleEnd = strstr(pTitle, BBCODE_URL_END);
				char back1, back2;
				int add;
				size_t  offUrl, offTitle;

				if (NULL == pTitleEnd) {
					pTitleEnd = pTitle + strlen(pTitle);
				}

				offUrl = pEndUrl - p;
				offTitle = pTitleEnd - p;
				back1 = *(pRead + offUrl);
				back2 = *(pRead + offTitle);
				*(pRead + offUrl) = '\0';
				*(pRead + offTitle) = '\0';
				pTitle = pRead + offUrl + 1;
				if (!secure) {
					add = snprintf(pWrite, new_len - wrote, BBHTML_URL, pRead, pTitle);
				} else {
					add = snprintf(pWrite, new_len - wrote, BBHTML_URLS, pRead, pTitle);
				}
				wrote += add;
				pWrite += add;
				*(pRead + offTitle) = back2;
				*(pRead + offUrl) = back1;
				offTitle += sizeof(BBCODE_URL_END) - 1;
				i += offTitle;
				p += offTitle;
				pRead += offTitle;
				continue;
			}
		}
		if (conv_www) {
			int add;
			PRINTF("Line %d conv_www\n", __LINE__);
			if (!memcmp(p, "www", 3)) {
				if (i == 0 || isspace(*(p - 1))) {
					int     l = strcspn(p, " \t\r\n[");
					char back = *(pRead + l);
					PRINTF("Line %d www\n", __LINE__);
					*(pRead + l) = '\0';
					if ((l > 4) && IS_IMAGE(p + l)) {
						add = snprintf(pWrite, new_len - wrote, BBHTML_IMAGE, pRead);
					}
					else {
						add = snprintf(pWrite, new_len - wrote, BBHTML_URL, pRead, pRead);
					}
					*(pRead + l) = back;
					p += l;
					pWrite += add;
					wrote += add;
					pRead += l;
					i += l;
					continue;
				}
			}
			else if (!memcmp(p, BBCODE_HTTPS, BBCODE_HTTPS_LENGTH)) {
				PRINTF("Line %d BBCODE_HTTPS\n", __LINE__);
				if (i == 0 || isspace(*(p - 1))) {
					int     l = strcspn(p, " \t\r\n[");
					char back = *(pRead + l);
					*(pRead + l) = '\0';
					if ((l > 4) && IS_IMAGE(p + l)) {
						add = snprintf(pWrite, new_len - wrote, BBHTML_IMAGES, pRead + BBCODE_HTTPS_LENGTH);
					}
					else {
						add = snprintf(pWrite, new_len - wrote, BBHTML_URLS, pRead + BBCODE_HTTPS_LENGTH, pRead);
					}
					*(pRead + l) = back;
					p += l;
					pWrite += add;
					wrote += add;
					pRead += l;
					i += l;
					continue;
				}
			}
			else if (!memcmp(p, BBCODE_HTTP, BBCODE_HTTP_LENGTH)) {
				PRINTF("Line %d BBCODE_HTTP\n", __LINE__);
				if (i == 0 || isspace(*(p - 1))) {
					int     l = strcspn(p, " \t\r\n[");
					char back = *(pRead + l);
					*(pRead + l) = '\0';
					if ((l > 4) && IS_IMAGE(p + l)) {
						add = snprintf(pWrite, new_len - wrote, BBHTML_IMAGE, pRead + BBCODE_HTTP_LENGTH);
					}
					else {
						add = snprintf(pWrite, new_len - wrote, BBHTML_URL, pRead + BBCODE_HTTP_LENGTH, pRead);
					}
					*(pRead + l) = back;
					p += l;
					pWrite += add;
					wrote += add;
					pRead += l;
					i += l;
					continue;
				}
			}
		}
		*pWrite++ = *pRead++;
		wrote++;
		i++;
		p++;
	}
	*pWrite = '\0';
	efree(bb_str_lwr);
	*len = strlen(*pStr);
}


#ifdef DEBUG

#define BUFFER_SIZE 2048

int main(int argc, char *argv[]) {

	char *in = (char*)malloc(BUFFER_SIZE);
	char *p = in;
	int len = 0;
	char *out = NULL;
	int len_out = 0;

	memset(in, 0, sizeof(in));

#ifndef TEST
	while (gets(p)) {
		p += strlen(p);
	}
#else
	strcpy(in, TEST);
#endif
	len = strlen(in);
	do_bbcode(in, len, &out, &len_out);

	printf("Parameters: [in len=%d out len=%d]\n", len, len_out);
	if (out != NULL) {
		printf("%s\n", out);
	}
	return 0;
}

#endif

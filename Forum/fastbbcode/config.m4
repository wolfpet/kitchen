dnl $Id$
dnl config.m4 for extension fastbbcode

dnl Comments in this file start with the string 'dnl'.
dnl Remove where necessary. This file will not work
dnl without editing.

dnl If your extension references something external, use with:

dnl PHP_ARG_WITH(fastbbcode, for fastbbcode support,
dnl Make sure that the comment is aligned:
dnl [  --with-fastbbcode             Include fastbbcode support])

dnl Otherwise use enable:

PHP_ARG_ENABLE(fastbbcode, whether to enable fastbbcode support,
dnl Make sure that the comment is aligned:
[  --enable-fastbbcode           Enable fastbbcode support])

if test "$PHP_FASTBBCODE" != "no"; then
  dnl Write more examples of tests here...

  dnl # --with-fastbbcode -> check with-path
  dnl SEARCH_PATH="/usr/local /usr"     # you might want to change this
  dnl SEARCH_FOR="/include/fastbbcode.h"  # you most likely want to change this
  dnl if test -r $PHP_FASTBBCODE/$SEARCH_FOR; then # path given as parameter
  dnl   FASTBBCODE_DIR=$PHP_FASTBBCODE
  dnl else # search default path list
  dnl   AC_MSG_CHECKING([for fastbbcode files in default path])
  dnl   for i in $SEARCH_PATH ; do
  dnl     if test -r $i/$SEARCH_FOR; then
  dnl       FASTBBCODE_DIR=$i
  dnl       AC_MSG_RESULT(found in $i)
  dnl     fi
  dnl   done
  dnl fi
  dnl
  dnl if test -z "$FASTBBCODE_DIR"; then
  dnl   AC_MSG_RESULT([not found])
  dnl   AC_MSG_ERROR([Please reinstall the fastbbcode distribution])
  dnl fi

  dnl # --with-fastbbcode -> add include path
  dnl PHP_ADD_INCLUDE($FASTBBCODE_DIR/include)

  dnl # --with-fastbbcode -> check for lib and symbol presence
  dnl LIBNAME=fastbbcode # you may want to change this
  dnl LIBSYMBOL=fastbbcode # you most likely want to change this 

  dnl PHP_CHECK_LIBRARY($LIBNAME,$LIBSYMBOL,
  dnl [
  dnl   PHP_ADD_LIBRARY_WITH_PATH($LIBNAME, $FASTBBCODE_DIR/lib, FASTBBCODE_SHARED_LIBADD)
  dnl   AC_DEFINE(HAVE_FASTBBCODELIB,1,[ ])
  dnl ],[
  dnl   AC_MSG_ERROR([wrong fastbbcode lib version or lib not found])
  dnl ],[
  dnl   -L$FASTBBCODE_DIR/lib -lm -ldl
  dnl ])
  dnl
  dnl PHP_SUBST(FASTBBCODE_SHARED_LIBADD)

  PHP_NEW_EXTENSION(fastbbcode, fastbbcode.c, $ext_shared)
fi

@echo off

@setlocal

set SPIDER_PATH=%~dp0

if "%PHP_COMMAND%" == "" set PHP_COMMAND=php.exe

"%PHP_COMMAND%" "%SPIDER_PATH%work" %*

@endlocal

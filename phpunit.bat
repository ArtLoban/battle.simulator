@ECHO OFF
setlocal DISABLEDELAYEDEXPANSION
SET BIN_TARGET=%~dp0vendor\bin\phpunit
php "%BIN_TARGET%" %*
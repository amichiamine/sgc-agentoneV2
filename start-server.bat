@echo off
cd /d %~dp0
php -S 0.0.0.0:5000 extensions\webview\index.html
pause

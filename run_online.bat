@echo off
title Digital Attendance Online Tunnel
echo ============================================================
echo   DIGITAL ATTENDANCE - PT SON DUCT SEJAHTERA
echo   Menjalankan Layanan Online Gratis (HTTPS Cloudflare)
echo ============================================================
echo Pastikan Laragon / PHP server (port 8000) sudah berjalan.
echo.
D:\laragon\bin\cloudflared.exe tunnel --url http://127.0.0.1:8000
pause

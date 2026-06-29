# Start all three Tamboli sites locally (PHP built-in servers)
# Usage: .\start-local.ps1

$php = Get-ChildItem "C:\laragon\bin\php" -Recurse -Filter "php.exe" -ErrorAction SilentlyContinue |
    Select-Object -First 1 -ExpandProperty FullName

if (-not $php) {
    Write-Error "PHP not found. Install Laragon or add php to PATH."
    exit 1
}

$root = Split-Path -Parent $MyInvocation.MyCommand.Path
Set-Location $root

Write-Host ""
Write-Host "Tamboli Samaj — local dev servers" -ForegroundColor Cyan
Write-Host "  Main website : http://127.0.0.1:8001"
Write-Host "  Student portal: http://127.0.0.1:8000"
Write-Host "  Admin panel  : http://127.0.0.1:8002"
Write-Host ""
Write-Host "For Laragon .test URLs, restart Laragon (Stop All -> Start All):" -ForegroundColor Yellow
Write-Host "  http://tamoli-main.test"
Write-Host "  http://tamoli-prathibha-samman.test"
Write-Host "  http://tamoli-admin.test"
Write-Host ""
Write-Host "Press Ctrl+C to stop all servers."
Write-Host ""

$jobs = @(
    @{ Port = 8000; Dir = "portal"; Name = "portal" },
    @{ Port = 8001; Dir = "main";   Name = "main" },
    @{ Port = 8002; Dir = "admin";  Name = "admin" }
)

foreach ($j in $jobs) {
    Start-Process -FilePath $php -ArgumentList "-S", "127.0.0.1:$($j.Port)", "-t", $j.Dir -WindowStyle Minimized
}

try {
    while ($true) { Start-Sleep -Seconds 60 }
} finally {
    Get-Process php -ErrorAction SilentlyContinue | Stop-Process -Force -ErrorAction SilentlyContinue
}
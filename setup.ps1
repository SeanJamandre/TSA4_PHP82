$ErrorActionPreference = 'Stop'
Set-Location $PSScriptRoot
function Step { param([scriptblock]$Command) & $Command; if ($LASTEXITCODE -ne 0) { throw 'Command failed. Read the error above.' } }
try {
    if (-not (Get-Command php -ErrorAction SilentlyContinue)) {
        if (Test-Path 'C:\xampp\php\php.exe') { $env:Path = 'C:\xampp\php;' + $env:Path }
    }
    Step { php preflight.php }
    if (-not (Get-Command composer -ErrorAction SilentlyContinue)) { throw 'Composer is missing. Install Composer using C:\xampp\php\php.exe, then reopen this window.' }
    if (-not (Test-Path 'app/artisan')) {
        if (Test-Path app) { throw 'An incomplete app folder exists. Rename it before retrying.' }
        Step { composer create-project laravel/laravel app '^12.0' --no-install --no-scripts --no-interaction }
    }
    Set-Location app
    Step { composer config platform.php 8.2.12 }
    Step { composer require laravel/breeze '^2.*' --dev --with-all-dependencies --no-interaction }
    if (-not (Test-Path .env)) { Copy-Item .env.example .env }
    Write-Host 'Generating Breeze authentication. Frontend build output is recorded in breeze-install.log; Node is not required for this package.'
    cmd /c 'php artisan breeze:install blade --no-interaction > breeze-install.log 2>&1'
    if ($LASTEXITCODE -ne 0) { Get-Content breeze-install.log; throw 'Breeze installation failed.' }
    Copy-Item "$PSScriptRoot/source/*" . -Recurse -Force
    if (-not (Test-Path database/database.sqlite)) { New-Item database/database.sqlite -ItemType File | Out-Null }
    Step { php configure-local.php }
    Step { php artisan config:clear }
    if (-not (Select-String -Path .env -Pattern '^APP_KEY=base64:' -Quiet)) { Step { php artisan key:generate } }
    Step { php artisan migrate --seed --force }
    Step { php artisan view:cache }
    Step { php artisan test --compact }
    Write-Host 'SETUP COMPLETE. Open http://127.0.0.1:8000 after the server starts.' -ForegroundColor Green
    Step { php artisan serve --host=127.0.0.1 --port=8000 }
} catch {
    Write-Host $_ -ForegroundColor Red
    Read-Host 'Setup stopped. Take a screenshot of the error. Press Enter to close'
    exit 1
}

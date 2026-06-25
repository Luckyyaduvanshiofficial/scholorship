$paths = Get-ChildItem -Path app, public, storage -Recurse -Include *.php -ErrorAction SilentlyContinue
foreach ($p in $paths) {
    $content = Get-Content $p.FullName -Raw -ErrorAction SilentlyContinue
    if ($content -match 'app/Config/|app/Views/|app/Core/|app/Controllers/|app/Models/|app/Middleware/|app/Routes/') {
        Write-Host "MATCH: $($p.FullName.Substring((Get-Location).Path.Length + 1))"
    }
}
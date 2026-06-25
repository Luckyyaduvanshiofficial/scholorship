$paths = Get-ChildItem -Path app, public, storage -Recurse -Include *.php -ErrorAction SilentlyContinue
foreach ($p in $paths) {
    $content = Get-Content $p.FullName -Raw -ErrorAction SilentlyContinue
    if ($content -match 'app/[A-Z]|/Config|/Core|/Controllers|/Models|/Middleware|/Routes|/Views') {
        Write-Host "=== $($p.FullName.Substring((Get-Location).Path.Length + 1)) ==="
        Select-String -Path $p.FullName -Pattern 'app/[A-Z][a-z]+|/Config|/Core|/Controllers|/Models|/Middleware|/Routes|/Views' -AllMatches | ForEach-Object {
            Write-Host "  Line $($_.LineNumber): $($_.Line.Trim())"
        }
    }
}
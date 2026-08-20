$ProjectRoot = (Get-Item $PSScriptRoot).Parent.FullName
Set-Location $ProjectRoot

$PluginSlug = "wpss-ultimate-user-management"
$BuildFolder = ".build"
$BuildBase  = "$BuildFolder\build_test"
$BuildDir   = "$BuildBase\$PluginSlug"
$ZipFile    = "$BuildFolder\$PluginSlug.zip"

If (Test-Path $BuildBase) { Remove-Item -Recurse -Force $BuildBase -ErrorAction SilentlyContinue }
If (Test-Path $ZipFile)   { Remove-Item -Force $ZipFile -ErrorAction SilentlyContinue }

New-Item -ItemType Directory -Path $BuildDir -Force | Out-Null

$IgnoreList = [System.Collections.Generic.List[string]]::new()

If (Test-Path ".distignore") {
    Get-Content ".distignore" | ForEach-Object {
        $line = $_.Trim()
        if ($line -and -not $line.StartsWith("#")) {
            $cleanLine = $line.TrimStart("/").TrimEnd("/").Replace("/", "\")
            $IgnoreList.Add($cleanLine)
        }
    }
}

$IgnoreList.Add(".build")

Write-Host "Copying production files..." -ForegroundColor Green

Get-ChildItem -Path $ProjectRoot -Recurse | ForEach-Object {
    $Item = $_
    $RelativePath = $Item.FullName.Substring($ProjectRoot.Length + 1)

    $ShouldExclude = $false
    foreach ($Rule in $IgnoreList) {
        if ($RelativePath -eq $Rule -or $RelativePath.StartsWith("$Rule\") -or $RelativePath -like "*\$Rule\*" -or $RelativePath -like "*\$Rule") {
            $ShouldExclude = $true
            break
        }
    }

    if (-not $ShouldExclude) {
        $Destination = Join-Path $BuildDir $RelativePath

        if ($Item.PSIsContainer) {
            If (-not (Test-Path $Destination)) {
                New-Item -ItemType Directory -Path $Destination -Force | Out-Null
            }
        } else {
            $ParentDir = Split-Path $Destination -Parent
            If (-not (Test-Path $ParentDir)) {
                New-Item -ItemType Directory -Path $ParentDir -Force | Out-Null
            }
            Copy-Item -Path $Item.FullName -Destination $Destination -Force
        }
    }
}

Write-Host "Compressing final package ($ZipFile)..." -ForegroundColor Green
If (Test-Path $BuildDir) {
    Compress-Archive -Path "$BuildDir" -DestinationPath "$ZipFile" -Force
}

Write-Host "------------------------------------------------" -ForegroundColor Yellow
Write-Host "Build completed successfully!" -ForegroundColor Yellow
Write-Host "Test folder: .\.build\build_test\$PluginSlug" -ForegroundColor Yellow
Write-Host "ZIP:         .\.build\$PluginSlug.zip" -ForegroundColor Yellow
Write-Host "------------------------------------------------" -ForegroundColor Yellow

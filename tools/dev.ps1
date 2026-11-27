[CmdletBinding()]
param(
    [Parameter(Position = 0)]
    [ValidateSet('help', 'up', 'build', 'down', 'stop', 'start', 'restart', 'logs', 'ps', 'shell', 'test', 'coverage', 'db-shell', 'db-reset', 'doctor', 'open')]
    [string]$Command = 'help',

    [Parameter(Position = 1)]
    [ValidateSet('ogspy-web', 'db')]
    [string]$Service = 'ogspy-web',

    [switch]$Yes
)

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'
[Console]::OutputEncoding = [System.Text.UTF8Encoding]::new()
$OutputEncoding = [Console]::OutputEncoding

$projectRoot = Split-Path -Parent $PSScriptRoot
$composeFile = Join-Path $projectRoot '.devcontainer\docker-compose.yml'
$envFile = Join-Path $projectRoot '.devcontainer\.env'
$composeArguments = @('compose')

if (Test-Path -LiteralPath $envFile) {
    $composeArguments += @('--env-file', $envFile)
}

$composeArguments += @('-f', $composeFile)

function Invoke-DockerCompose {
    param([string[]]$Arguments)

    & docker @composeArguments @Arguments
    if ($LASTEXITCODE -ne 0) {
        throw "Docker Compose a echoue avec le code $LASTEXITCODE."
    }
}

function Get-DockerComposePort {
    param(
        [string]$Service,
        [int]$ContainerPort,
        [int]$FallbackPort
    )

    $binding = & docker @composeArguments port $Service $ContainerPort 2>$null
    if ($LASTEXITCODE -eq 0 -and $binding) {
        $firstBinding = @($binding)[0]
        if ($firstBinding -match ':(\d+)$') {
            return [int]$Matches[1]
        }
    }

    return $FallbackPort
}

function Show-DevelopmentEndpoints {
    $httpPort = Get-DockerComposePort -Service 'ogspy-web' -ContainerPort 80 -FallbackPort 8080
    $databasePort = Get-DockerComposePort -Service 'db' -ContainerPort 3306 -FallbackPort 3306

    Write-Host ''
    Write-Host 'OGSpy est disponible :' -ForegroundColor Green
    Write-Host "  Web       http://localhost:$httpPort"
    Write-Host "  MariaDB   localhost:$databasePort"
}

function Test-DockerCommand {
    param([string[]]$Arguments)

    $previousErrorActionPreference = $ErrorActionPreference
    $ErrorActionPreference = 'SilentlyContinue'
    try {
        & docker @Arguments *> $null
        return $LASTEXITCODE -eq 0
    }
    finally {
        $ErrorActionPreference = $previousErrorActionPreference
    }
}

function Assert-DockerAvailable {
    if (-not (Get-Command docker -ErrorAction SilentlyContinue)) {
        throw 'Docker est introuvable. Installe Docker Desktop puis reessaie.'
    }

    if (-not (Test-DockerCommand -Arguments @('info'))) {
        throw 'Le moteur Docker ne repond pas. Demarre Docker Desktop puis reessaie.'
    }

    if (-not (Test-DockerCommand -Arguments @('compose', 'version'))) {
        throw 'Docker Compose est indisponible. Mets Docker Desktop a jour puis reessaie.'
    }
}

if ($Command -ne 'help') {
    Assert-DockerAvailable
}

Push-Location $projectRoot
try {
    switch ($Command) {
        'help' {
            @'
OGSpy - environnement de developpement

Usage :
  dev.cmd <commande> [service]
  .\tools\dev.ps1 <commande> [service]

Commandes :
  up          Demarrer OGSpy et MariaDB
  build       Reconstruire les images puis demarrer
  down        Arreter et retirer les conteneurs
  stop        Arreter les conteneurs sans les retirer
  start       Redemarrer les conteneurs arretes
  restart     Redemarrer les services
  logs        Suivre les logs (ogspy-web par defaut, ou db)
  ps          Afficher l'etat des services
  shell       Ouvrir un shell (ogspy-web par defaut, ou db)
  test        Lancer la suite PHPUnit sans couverture
  coverage    Generer coverage/index.html et coverage.xml
  db-shell    Ouvrir le client MariaDB
  db-reset    Supprimer et recreer la base locale
  doctor      Verifier Docker, Compose et PHP
  open        Ouvrir http://localhost:8080
  help        Afficher cette aide

Exemples :
  dev.cmd up
  dev.cmd logs db
  dev.cmd shell
  dev.cmd test
  dev.cmd db-reset
'@ | Write-Host
        }
        'up' {
            Invoke-DockerCompose @('up', '-d')
            Show-DevelopmentEndpoints
        }
        'build' {
            Invoke-DockerCompose @('up', '--build', '-d')
            Show-DevelopmentEndpoints
        }
        'down' {
            Invoke-DockerCompose @('down')
        }
        'stop' {
            Invoke-DockerCompose @('stop')
        }
        'start' {
            Invoke-DockerCompose @('start')
            Show-DevelopmentEndpoints
        }
        'restart' {
            Invoke-DockerCompose @('restart')
            Show-DevelopmentEndpoints
        }
        'logs' {
            Invoke-DockerCompose @('logs', '--tail', '150', '--follow', $Service)
        }
        'ps' {
            Invoke-DockerCompose @('ps')
        }
        'shell' {
            if ($Service -eq 'ogspy-web') {
                Invoke-DockerCompose @('exec', '--user', 'www-data', 'ogspy-web', 'bash')
            }
            else {
                Invoke-DockerCompose @('exec', 'db', 'bash')
            }
        }
        'test' {
            Invoke-DockerCompose @('exec', '-T', '--user', 'www-data', '-e', 'XDEBUG_MODE=off', 'ogspy-web', 'composer', 'test')
        }
        'coverage' {
            Invoke-DockerCompose @('exec', '-T', '--user', 'www-data', '-e', 'XDEBUG_MODE=off', 'ogspy-web', 'composer', 'test:coverage')
        }
        'db-shell' {
            Invoke-DockerCompose @('exec', 'db', 'sh', '-lc', 'exec mariadb -u"$MARIADB_USER" -p"$MARIADB_PASSWORD" "$MARIADB_DATABASE"')
        }
        'db-reset' {
            if (-not $Yes) {
                $confirmation = Read-Host 'Cette commande supprime la base locale. Saisis OUI pour continuer'
                if ($confirmation -cne 'OUI') {
                    Write-Host 'Reinitialisation annulee.'
                    break
                }
            }

            Invoke-DockerCompose @('down', '--volumes')

            $generatedFiles = @(
                (Join-Path $projectRoot 'install\install.lock'),
                (Join-Path $projectRoot 'config\id.php')
            )
            foreach ($generatedFile in $generatedFiles) {
                if (Test-Path -LiteralPath $generatedFile) {
                    Remove-Item -LiteralPath $generatedFile -Force
                }
            }

            Invoke-DockerCompose @('up', '--build', '-d')
            Show-DevelopmentEndpoints
        }
        'doctor' {
            & docker version
            if ($LASTEXITCODE -ne 0) {
                throw 'Le moteur Docker ne repond pas.'
            }
            Invoke-DockerCompose @('config', '--quiet')
            Invoke-DockerCompose @('ps')
            Invoke-DockerCompose @('exec', '-T', 'ogspy-web', 'php', '--version')
        }
        'open' {
            $httpPort = Get-DockerComposePort -Service 'ogspy-web' -ContainerPort 80 -FallbackPort 8080
            Start-Process "http://localhost:$httpPort"
        }
    }
}
finally {
    Pop-Location
}

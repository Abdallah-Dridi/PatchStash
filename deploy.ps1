Param(
    [Parameter(Position = 0)]
    [string]$Command,
    [Parameter(ValueFromRemainingArguments = $true)]
    [string[]]$Args
)

Set-StrictMode -Version Latest
$ErrorActionPreference = "Stop"

function Show-Help {
    @"
Usage: .\deploy.ps1 [command] [args...]

Commands:
  build        Build all containers
  up           Start containers in detached mode
  down         Stop and remove containers
  reset        Stop containers, remove volumes, rebuild from scratch
  refresh      Rebuild php + nginx without touching database
  logs         Tail logs for all containers
  shell        Open a bash shell inside the php container
  composer …   Run Composer inside the php container
  migrate      Run Doctrine migrations inside the php container
"@
}

function Get-Compose {
    if (Get-Command docker -ErrorAction SilentlyContinue) {
        try {
            docker compose version *> $null
            return "docker compose"
        } catch { }
    }

    if (Get-Command docker-compose -ErrorAction SilentlyContinue) {
        return "docker-compose"
    }

    throw "Docker Compose not found. Install Docker Desktop (with WSL2 integration enabled if using WSL) or docker-compose."
}

if (-not $Command) {
    Show-Help
    exit 1
}

$Root = Split-Path -Parent $MyInvocation.MyCommand.Path
$ComposeFile = Join-Path $Root "infra/docker-compose.yml"
$Project = "patchstash"
$ComposeBin = Get-Compose

function Run-Compose([string[]]$ComposeArgs) {
    & $ComposeBin -p $Project -f $ComposeFile @ComposeArgs
}

switch ($Command.ToLower()) {
    "build"   { Run-Compose @("build", "--pull") }
    "up"      { Run-Compose @("up", "-d") }
    "down"    { Run-Compose @("down") }
    "reset"   { Run-Compose @("down", "-v", "--remove-orphans"); Run-Compose @("build", "--no-cache") }
    "refresh" { Run-Compose @("build", "--no-cache", "php", "nginx"); Run-Compose @("up", "-d", "php", "nginx") }
    "logs"    { Run-Compose @("logs", "-f") }
    "shell"   { Run-Compose @("exec", "php", "bash") }
    "composer" {
        if (-not $Args -or $Args.Count -eq 0) {
            Write-Host "Usage: .\deploy.ps1 composer <args>"
            exit 1
        }
        Run-Compose @("exec", "php", "composer") + $Args
    }
    "migrate" { Run-Compose @("exec", "php", "php", "bin/console", "doctrine:migrations:migrate", "--no-interaction", "--allow-no-migration") }
    default   { Show-Help; exit 1 }
}

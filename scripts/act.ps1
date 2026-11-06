$script:Base = "http://127.0.0.1:8000/api"
if (-not $script:Tokens) { $script:Tokens = @{} }

function Act {
  param(
    [string]$As,
    [string]$Method = "GET",
    [string]$Path = "/auth/me",
    $Body = $null,
    [string]$Password = "password"
  )
  $H = @{ Accept = "application/json" }

  if (-not $script:Tokens.ContainsKey($As)) {
    $login = Invoke-RestMethod -Method Post -Uri "$script:Base/auth/login" -Headers $H `
      -ContentType "application/json" `
      -Body (@{ email = $As; password = $Password } | ConvertTo-Json)
    $t = $login.token
    if (-not $t) { $t = $login.data.token }
    if (-not $t) { $login | ConvertTo-Json -Depth 5; throw "Token tidak ketemu" }
    $script:Tokens[$As] = $t
    Write-Host "Login OK: $As" -ForegroundColor Green
  }

  $H.Authorization = "Bearer $($script:Tokens[$As])"
  $p = @{ Method = $Method; Uri = "$script:Base$Path"; Headers = $H }
  if ($Body) { $p.Body = ($Body | ConvertTo-Json -Depth 6); $p.ContentType = "application/json" }

  try {
    Invoke-RestMethod @p
  }
  catch {
    $code = $null
    if ($_.Exception.Response) { $code = [int]$_.Exception.Response.StatusCode }
    Write-Host "GAGAL $Method $Path (HTTP $code)" -ForegroundColor Red
    if ($_.ErrorDetails.Message) {
      Write-Host $_.ErrorDetails.Message -ForegroundColor Yellow
    } else {
      Write-Host $_.Exception.Message -ForegroundColor Yellow
    }
  }
}

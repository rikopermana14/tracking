$oldToken = '93yNUVTUEAfyxFsIfCBi7ZZunQ5JUYCobzkmzM7CoVaQU6LCq1j0QVDJaPZJKDYyAqspQ6vFYAQ-BTADcw0aw7Izn3WeDgUpcCtpMGBle_pKYxKVCHwQT9xiIlFIoY1k2XIfeAN7pq2-w3NikuKNWroI4FR5gibtLWGIDnP_R48awksSSwhe4qir5KeUQERaAi2jGF6NdZLK6W9CIMPXiVdcgGGdaZurF6lF3obze_D1r-rEWx6t919ViyiX2nICnDgefdN38POCh2KIc8paf_DnVz38b7Z7y_L79OkP9rgHChwR2PTekHso83SYvs3m1CcGhtX1acL-4B8yWbKQn6PF0L4LXDTKBth1YrLKFcK8JIo88_PYallplCdVe1gc88FdUu_cnqqdDsw-Dkg4OnGh-Ik'

$headers = @{
    Accept = 'application/json'
    Authorization = "Bearer $oldToken"
}

$start = Get-Date

while ($true) {

    $now = Get-Date

    try {
        $r = Invoke-WebRequest `
            -Uri 'https://app2.indotrack.com/vesselpro/Track/Asset/Map' `
            -Headers $headers `
            -Method GET `
            -UseBasicParsing `
            -ErrorAction Stop

        $status = $r.StatusCode
    }
    catch {
        $status = $_.Exception.Response.StatusCode.value__
    }

    $elapsed = $now - $start

    Write-Host "$now | HTTP $status | Aktif selama: $($elapsed.Days) hari $($elapsed.Hours) jam $($elapsed.Minutes) menit"

    if ($status -eq 401 -or $status -eq 403) {
        Write-Host ""
        Write-Host "TOKEN SUDAH TIDAK VALID"
        Write-Host "Total umur pengujian: $elapsed"
        break
    }

    Start-Sleep -Seconds 3600
}
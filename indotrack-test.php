<?php

date_default_timezone_set('Asia/Jakarta');

/*
|--------------------------------------------------------------------------
| KONFIGURASI
|--------------------------------------------------------------------------
*/

$apiUrl = 'https://app2.indotrack.com/vesselpro/Track/Asset/Map';

/*
|--------------------------------------------------------------------------
| TOKEN
|--------------------------------------------------------------------------
|
| Jangan tulis token asli di sini kalau file akan di-upload ke GitHub.
|
*/
function getIndoTrackToken()
{
    $url = 'https://app2.indotrack.com/vesselpro/token';

    $username = getenv('INDOTRACK_USERNAME');
    $password = getenv('INDOTRACK_PASSWORD');
    $db       = getenv('INDOTRACK_DB') ?: 'JKT_LIVE';
    $ipAdd    = getenv('INDOTRACK_IP');

    $postData = http_build_query([
        'grant_type' => 'password',
        'username'   => $username,
        'password'   => $password,
        'db'         => $db,
        'IPAdd'      => $ipAdd,
    ]);

    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $postData,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => [
            'Accept: */*',
            'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
            'Origin: https://app2.indotrack.com',
            'Referer: https://app2.indotrack.com/vesselpro/track/login/',
            'X-Requested-With: XMLHttpRequest',
        ],
        CURLOPT_TIMEOUT => 30,
    ]);

    $response = curl_exec($ch);

    if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);

        throw new Exception("Login IndoTrack gagal: {$error}");
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    if ($httpCode !== 200) {
        throw new Exception(
            "Login IndoTrack HTTP {$httpCode}: {$response}"
        );
    }

    $data = json_decode($response, true);

    if (!is_array($data)) {
        throw new Exception("Response login bukan JSON valid.");
    }

    if (empty($data['access_token'])) {
        throw new Exception(
            "access_token tidak ditemukan dalam response."
        );
    }

    return [
        'access_token' => $data['access_token'],
        'token_type'   => $data['token_type'] ?? 'bearer',
        'expires_in'   => (int)($data['expires_in'] ?? 0),
    ];
}
$tokenData = getIndoTrackToken();

$token = $tokenData['access_token'];

$tokenObtainedAt = time();

$tokenExpiresIn = $tokenData['expires_in'];





/*
|--------------------------------------------------------------------------
| DATABASE
|--------------------------------------------------------------------------
*/

$dbHost = '127.0.0.1';
$dbName = 'test';
$dbUser = 'root';
$dbPass = ''; // isi jika MySQL Anda menggunakan password


/*
|--------------------------------------------------------------------------
| CONNECT MYSQL
|--------------------------------------------------------------------------
*/

try {

    $pdo = new PDO(
        "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4",
        $dbUser,
        $dbPass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );

} catch (PDOException $e) {

    die(
        date('Y-m-d H:i:s') .
        " | Database ERROR | " .
        $e->getMessage() .
        PHP_EOL
    );
}


/*
|--------------------------------------------------------------------------
| AMBIL DATA INDOTRACK
|--------------------------------------------------------------------------
*/

function getIndoTrackData($apiUrl, $token)
{
    $ch = curl_init($apiUrl);

    curl_setopt_array($ch, [

        CURLOPT_RETURNTRANSFER => true,

        CURLOPT_HTTPHEADER => [
            'Accept: application/json',
            'Authorization: Bearer ' . $token,
        ],

        CURLOPT_TIMEOUT => 30,

        CURLOPT_CONNECTTIMEOUT => 10,

        CURLOPT_SSL_VERIFYPEER => true,

        CURLOPT_SSL_VERIFYHOST => 2,

    ]);

    $response = curl_exec($ch);

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    $curlError = curl_error($ch);

    curl_close($ch);

    if ($response === false) {

        throw new Exception(
            "cURL ERROR: " . $curlError
        );
    }

    if ($httpCode !== 200) {

        throw new Exception(
            "HTTP " . $httpCode . " | Response: " . substr($response, 0, 500)
        );
    }

    $json = json_decode($response, true);

    if ($json === null) {

        throw new Exception(
            "JSON ERROR: " . json_last_error_msg()
        );
    }

    return $json;
}


/*
|--------------------------------------------------------------------------
| NORMALISASI RESPONSE
|--------------------------------------------------------------------------
*/

function extractAssets($json)
{
    /*
     * API bisa membungkus array dalam Data/data/result.
     */

    if (isset($json['Data']) && is_array($json['Data'])) {
        return $json['Data'];
    }

    if (isset($json['data']) && is_array($json['data'])) {
        return $json['data'];
    }

    if (isset($json['Result']) && is_array($json['Result'])) {
        return $json['Result'];
    }

    if (isset($json['result']) && is_array($json['result'])) {
        return $json['result'];
    }

    /*
     * Kalau response langsung berupa array.
     */

    if (is_array($json) && array_is_list($json)) {
        return $json;
    }

    return [];
}


/*
|--------------------------------------------------------------------------
| FUNGSI BACA FIELD
|--------------------------------------------------------------------------
*/

function field($row, $keys, $default = null)
{
    foreach ($keys as $key) {

        if (array_key_exists($key, $row)) {
            return $row[$key];
        }
    }

    return $default;
}


/*
|--------------------------------------------------------------------------
| LOOP
|--------------------------------------------------------------------------
*/

echo "=========================================\n";
echo " IndoTrack Moving Collector\n";
echo " Interval : 5 menit\n";
echo " Database : test\n";
echo "=========================================\n\n";


while (true) {

    $capturedAt = date('Y-m-d H:i:s');

    try {
        echo date('Y-m-d H:i:s') .
     " | LOGIN OK | Token berlaku {$tokenExpiresIn} detik\n";
     if (time() >= ($tokenObtainedAt + $tokenExpiresIn - 300)) {

    echo date('Y-m-d H:i:s') .
         " | TOKEN AKAN EXPIRED | Login ulang...\n";

    $tokenData = getIndoTrackToken();

    $token = $tokenData['access_token'];

    $tokenObtainedAt = time();

    $tokenExpiresIn = $tokenData['expires_in'];

    echo date('Y-m-d H:i:s') .
         " | TOKEN BARU BERHASIL DIDAPATKAN\n";
}

        /*
         * Ambil API
         */

        $json = getIndoTrackData(
            $apiUrl,
            $token
        );

        $assets = extractAssets($json);

        $totalAssets = count($assets);

      $allAssets = [];

$movingCount = 0;
$idlingCount = 0;
$otherCount = 0;

foreach ($assets as $asset) {

    $allAssets[] = $asset;

    $status = strtoupper(
        trim(
            (string) field(
                $asset,
                ['Status', 'status'],
                ''
            )
        )
    );

    if ($status === 'MOVING') {

        $movingCount++;

    } elseif ($status === 'IDLING') {

        $idlingCount++;

    } else {

        $otherCount++;
    }
}

        /*
         * SIMPAN POLLING
         */

        $stmtPoll = $pdo->prepare("
            INSERT INTO indotrack_polls
            (
                poll_time,
                total_assets,
                moving_count,
                idling_count,
                other_count
            )
            VALUES
            (
                :poll_time,
                :total_assets,
                :moving_count,
                :idling_count,
                :other_count
            )
        ");

        $stmtPoll->execute([

            ':poll_time'    => $capturedAt,

            ':total_assets' => $totalAssets,

            ':moving_count' => $movingCount,

            ':idling_count' => $idlingCount,

            ':other_count'  => $otherCount,

        ]);


        $pollId = $pdo->lastInsertId();


        /*
         * SIMPAN SEMUA KAPAL MOVING
         */

        $stmtMoving = $pdo->prepare("
            INSERT INTO indotrack_moving
            (
                poll_id,
                asset_id,
                vessel_name,
                gps_time,
                latitude,
                longitude,
                speed,
                direction_degrees,
                direction_cardinal,
                status,
                captured_at
            )
            VALUES
            (
                :poll_id,
                :asset_id,
                :vessel_name,
                :gps_time,
                :latitude,
                :longitude,
                :speed,
                :direction_degrees,
                :direction_cardinal,
                :status,
                :captured_at
            )
        ");


        foreach ($allAssets as $asset) {

            $assetId = field(
                $asset,
                ['AssetID', 'asset_id', 'AssetId'],
                null
            );

            $vesselName = field(
                $asset,
                ['Name', 'name', 'VesselName'],
                null
            );

           $gpsTimeRaw = field(
    $asset,
    ['GPSTime', 'gps_time', 'GpsTime'],
    null
);

$gpsTime = null;

if (!empty($gpsTimeRaw)) {
    try {
        $gpsTime = (new DateTime($gpsTimeRaw))
            ->setTimezone(new DateTimeZone('Asia/Jakarta'))
            ->format('Y-m-d H:i:s');
    } catch (Exception $e) {
        $gpsTime = null;
    }
}

            $latitude = field(
                $asset,
                ['Latitude', 'latitude'],
                null
            );

            $longitude = field(
                $asset,
                ['Longitude', 'longitude'],
                null
            );

            $speed = field(
                $asset,
                ['Speed', 'speed'],
                null
            );

            $directionDegrees = field(
                $asset,
                ['DirectionDegrees', 'direction_degrees'],
                null
            );

            $directionCardinal = field(
                $asset,
                ['DirectionCardinal', 'direction_cardinal'],
                null
            );


            // ============================================================
// CEK DUPLIKAT KOORDINAT
// Maksimal 5 record dengan koordinat yang sama
// untuk kapal yang sama.
// ============================================================

$duplicateCheck = $pdo->prepare("
    SELECT COUNT(*) 
    FROM indotrack_moving
    WHERE asset_id = :asset_id
      AND ROUND(latitude, 6) = ROUND(:latitude, 6)
      AND ROUND(longitude, 6) = ROUND(:longitude, 6)
");

$duplicateCheck->execute([
    ':asset_id'  => $assetId,
    ':latitude'  => $latitude,
    ':longitude' => $longitude,
]);

$duplicateCount = (int) $duplicateCheck->fetchColumn();

if ($duplicateCount >= 5) {

    echo "   SKIP DUPLIKAT | {$vesselName} | {$latitude}, {$longitude} | sudah {$duplicateCount}x\n";

    continue;
}

            $stmtMoving->execute([

                ':poll_id'            => $pollId,

                ':asset_id'           => $assetId,

                ':vessel_name'        => $vesselName,

                ':gps_time'           => $gpsTime,

                ':latitude'           => $latitude,

                ':longitude'          => $longitude,

                ':speed'              => $speed,

                ':direction_degrees'  => $directionDegrees,

                ':direction_cardinal' => $directionCardinal,

                ':status'             => field(
    $asset,
    ['Status', 'status'],
    null
),

                ':captured_at'        => $capturedAt,

            ]);
        }


        /*
         * TAMPILKAN HASIL
         */

        echo sprintf(
            "%s | HTTP 200 | Total: %d | MOVING: %d | IDLING: %d\n",
            $capturedAt,
            $totalAssets,
            $movingCount,
            $idlingCount
        );


        foreach ($allAssets as $asset) {

    $name = field(
        $asset,
        ['Name', 'name'],
        '-'
    );

    $status = field(
        $asset,
        ['Status', 'status'],
        '-'
    );

    $speed = field(
        $asset,
        ['Speed', 'speed'],
        '-'
    );

    echo "   {$status} | {$name} | {$speed} knot\n";
}


    } catch (Throwable $e) {

        echo sprintf(
            "%s | ERROR | %s\n",
            $capturedAt,
            $e->getMessage()
        );
    }


    /*
     * TUNGGU 5 MENIT
     */

  /*
 * COUNTDOWN 10 MENIT
 */

for ($remaining = 600; $remaining > 0; $remaining--) {

    $minutes = intdiv($remaining, 60);
    $seconds = $remaining % 60;

    echo sprintf(
        "\rNext polling dalam %02d:%02d",
        $minutes,
        $seconds
    );

    sleep(1);
}

echo "\n\n";

}
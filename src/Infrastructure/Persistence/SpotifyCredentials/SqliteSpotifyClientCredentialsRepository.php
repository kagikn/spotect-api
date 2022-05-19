<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\SpotifyCredentials;

use App\Domain\SpotifyCredentials\SpotifyCredentials;
use App\Domain\SpotifyCredentials\SpotifyCredentialsRepository;
use App\Domain\SpotifyCredentials\SpotifyGenericCredentials;
use PDO;

class SqliteSpotifyClientCredentialsRepository implements SpotifyCredentialsRepository
{
    public function __construct()
    {
    }

    /**
     * @return bool
     */
    public function store(SpotifyCredentials $credentials): bool
    {
        $password = $_ENV['PASSWORD_FOR_SPOTIFY_TOKEN_DATABASE'];
        $tokenAndTimestampJson = json_encode([
            'token' => $credentials->getAccessToken(),
            'token_expiration_timestamp' => $credentials->getAccessTokenExpirationTimestamp()
        ]);

        $iv = random_bytes(16);
        $tokenDataEncrypted = openssl_encrypt(
            $tokenAndTimestampJson,
            'aes-256-cbc',
            $password,
            iv: $iv
        );

        $dbh = new PDO(
            'sqlite::memory:',
            options: [PDO::ATTR_PERSISTENT => true]
        );

        $retVal = $dbh->exec('CREATE TABLE IF NOT EXISTS spotify_client_token
                                (
                                    id INTEGER PRIMARY KEY CHECK (id = 0),
                                    token_data BLOB,
                                    iv BLOB
                                );');

        $stmt = $dbh->prepare('REPLACE INTO spotify_client_token (id, token_data, iv) 
                                        VALUES (0, :token_data, :iv);');
        $stmt->bindValue(':token_data', $tokenDataEncrypted, PDO::PARAM_LOB);
        $stmt->bindValue(':iv', $iv, PDO::PARAM_LOB);

        return $stmt->execute();
    }

    public function get(): ?SpotifyCredentials
    {
        $password = $_ENV['PASSWORD_FOR_SPOTIFY_TOKEN_DATABASE'];

        $dbh = new PDO(
            'sqlite::memory:',
            options: [PDO::ATTR_PERSISTENT => true]
        );

        $tableCountStmt = $dbh->query('SELECT COUNT(*) 
                                                    FROM sqlite_master 
                                                    WHERE type="table" AND name="spotify_client_token";',
            PDO::FETCH_NUM);
        if (!$tableCountStmt || $tableCountStmt->fetch()[0] == 0) {
            return null;
        }

        $selectStmt = $dbh->query('SELECT token_data, iv FROM spotify_client_token;', PDO::FETCH_ASSOC);
        if (!$selectStmt) {
            return null;
        }

        $result = $selectStmt->fetch();

        $iv = $result['iv'];
        $tokenDataEncrypted = $result['token_data'];
        $tokenAndTimestampDataJson = json_decode(openssl_decrypt(
            $tokenDataEncrypted,
            'aes-256-cbc',
            $password,
            iv: $iv
        ), true);

        return new SpotifyGenericCredentials(
            $tokenAndTimestampDataJson['token'],
            $tokenAndTimestampDataJson['token_expiration_timestamp']
        );
    }
}

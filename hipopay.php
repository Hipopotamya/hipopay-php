<?php


class HipopayIntegration
{
    protected string $apiKey;
    protected string $apiSecret;
    protected string $apiUrl = 'https://www.hipopotamya.com/api/v1/merchants';
    protected int $userId;
    protected string $userEmail;
    protected string $username;
    protected array $commissionTypes = [1, 2, 3];
    protected array $product;

    public function setApiKey(string $apiKey): void
    {
        $this->apiKey = $apiKey;
    }

    public function setApiSecret(string $apiSecret): void
    {
        $this->apiSecret = $apiSecret;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function setUserEmail(string $userEmail): void
    {
        $this->userEmail = $userEmail;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function setProduct(array $product): void
    {
        $this->product = $product;
    }

    public function createStoreSession()
    {
        $data = [
            'api_key' => $this->apiKey,
            'api_secret' => $this->apiSecret,
            'user_id' => $this->userId,
            'email' => $this->userEmail,
            'username' => $this->username,
            'ip_address' => $this->getClientIpAddress(),
            'hash' => base64_encode(
                hash_hmac('sha256',$this->userId.$this->userEmail.$this->username.$this->apiKey,$this->apiSecret ,true)
            )
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl.'/store/token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json'
        ]);

        $result = @curl_exec($ch);

        curl_close($ch);

        return json_decode($result , true);
    }

    /**
     * @throws \Src\Exception
     */
    public function createPaymentSession()
    {
        if ($this->product['commission_type'] && !in_array($this->product['commission_type'], $this->commissionTypes)) {
            throw new \Src\Exception('Invalid commission type');
        }

        $data = [
            'api_key' => $this->apiKey,
            'api_secret' => $this->apiSecret,
            'user_id' => $this->userId,
            'email' => $this->userEmail,
            'username' => $this->username,
            'ip_address' => $this->getClientIpAddress(),
            'hash' => base64_encode(
                hash_hmac('sha256',$this->userId.$this->userEmail.$this->username.$this->apiKey,$this->apiSecret ,true)
            ),
            'pro' => true,
            'product[name]' => $this->product['name'],
            'product[price]' => $this->product['price'],
            'product[reference_id]' => $this->product['reference_id'],
            'product[commission_type]' => $this->product['commission_type'],
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl.'/payment/token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json'
        ]);

        $result = @curl_exec($ch);

        curl_close($ch);

        return json_decode($result , true);
    }

    public function getIpnStatusIsSuccess(): bool
    {
        $hash = base64_encode(
            hash_hmac('sha256',$_POST["transaction_id "].$_POST["user_id"].$_POST["email"].$_POST["name"].$_POST["status"].$this->apiKey,$this->apiSecret ,true)
        );

        return $hash === $_POST["hash"];
    }

    public function getClientIpAddress()
    {
        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
            $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
            $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
        }
        $client = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote = @$_SERVER['REMOTE_ADDR'];
        if (filter_var($client, FILTER_VALIDATE_IP)) {
            $ip = $client;
        } elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
            $ip = $forward;
        } else {
            $ip = $remote;
        }

        return $ip;
    }
}

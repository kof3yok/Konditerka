// sendJson: Это пользовательская функция, которая принимает три параметра и один необязательный параметр:
// $status (int): Это HTTP-статус ответа.
// $message (string): Это сообщение, которое будет включено в ответ, если оно передано.
// $extra (array): Это дополнительные данные, которые можно включить в ответ (необязательно).
<?php
function sendJson(int $status, string $message, array $extra = []): void
{
    $response = ['status' => $status];
    if ($message) $response['message'] = $message;
    http_response_code($status);
    echo json_encode(array_merge($response, $extra));
    exit;
}

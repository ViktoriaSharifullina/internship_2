<?php

namespace App\Services;

use App\Models\User;
use InvalidArgumentException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserService
{
    /**
     * Регистрирует нового пользователя с учетом надежности пароля.
     *
     * @param array $userData Данные пользователя.
     * @return array
     */
    public function register(array $userData): array
    {
        $passwordStatus = $this->passwordStatus($userData['password']);

        if ($passwordStatus === 'weak') {
            throw new InvalidArgumentException('The password is weak.');
        }

        $userData['password'] = Hash::make($userData['password']);
        $user = User::create($userData);

        return [
            'user' => $user,
            'password_check_status' => $passwordStatus,
        ];
    }

    /**
     * Определяет статус пароля.
     *
     * @param string $password Пароль для проверки.
     * @return array
     */
    public function passwordStatus(string $password): string
    {
        $length = strlen($password);
        $hasLetters = preg_match('/[a-zA-Z]/', $password);
        $hasNumbers = preg_match('/[0-9]/', $password);
        $hasSpecialChars = preg_match('/[^a-zA-Z0-9]/', $password);

        if ($length > 12 && $hasLetters && $hasNumbers && $hasSpecialChars) {
            return 'perfect';
        }

        $conditionsMet = 0;
        if ($hasLetters) $conditionsMet++;
        if ($hasNumbers) $conditionsMet++;
        if ($hasSpecialChars) $conditionsMet++;

        if ($length > 8 && $conditionsMet >= 2) {
            return 'good';
        }

        return 'weak';
    }

    /**
     * Аутентифицирует пользователя и генерирует токен доступа
     * 
     * @param string $email 
     * @param string $password
     * @return array
     * @throws ValidationException 
     */
    public function authorize($email, $password)
    {
        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('authToken')->plainTextToken;

        return [
            'token' => $token,
            'user_id' => $user->id,
        ];
    }
}
